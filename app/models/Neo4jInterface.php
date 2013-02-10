<?php
use Everyman\Neo4j\Client,
	Everyman\Neo4j\Index\NodeIndex,
	Everyman\Neo4j\Relationship,
	Everyman\Neo4j\Index\RelationshipIndex,
	Everyman\Neo4j\Node,
	Everyman\Neo4j\Path,
	Everyman\Neo4j\Cypher\Query,
	Everyman\Neo4j\Cypher;
/**
 * This class interfaces with the Neo4j db
 */
class Neo4jInterface{
	private $_client;
	private $_wordIndex;
	private $_containsIndex; 
	private $_emailIndex;

	public function getClient(){
		return $this->_client ;
	}

	public function getWordIndex(){
		return $this->_wordIndex ;
	}

	public function getContainsIndex(){
		return $this->_containsIndex ;
	}

	public function getEmailIndex(){
		return $this->_emailIndex ;
	}
	
	public function __construct(){
		//TODO: Currently hard-coding the neo4j server to localhost port 7474.
		//This needs to be picked up from a config file.
		$this->_client = new Client('localhost', 7474) ;
		$this->_wordIndex = new NodeIndex($this->_client, 'words');
		$this->_wordIndex->save();
		$this->_emailIndex = new NodeIndex($this->_client, 'emails');
		$this->_emailIndex->save();
		$this->_containsIndex = new RelationshipIndex($this->_client,'contains');
		$this->_containsIndex->save();
	}

	/**
	 * This function returns the array of words and their occurrence count
	 * @param  Array $wordArray['words' => ['word',word],
	 *                          'startTime' => '<From Time>',
	 *                          'endTime' => '<End Time>',
	 *                          'text' => '<email id text>'
	 *                         ] 
	 * @return Array            
	 */
	public function getWordCountByFilter($filterArray = null){
		$queryString = '';
		$wordNodeString = '*';
		if(is_array($filterArray) && count($filterArray)){
			if(array_key_exists('words', $filterArray) && count($filterArray['words'])){
				$wordNodeString = implode(',', $filterArray['words']);
			}else{
				$wordNodeString = "*";	
			}
		}

		$queryString = "start n=node:words('value:($wordNodeString)') 
						match n<-[r]-email-[s]->word
						where n.type?= 'word' and type(r) = 'CONTAINS' and type(s) = 'CONTAINS' " ;

		if(is_array($filterArray) && count($filterArray)){
			if(array_key_exists('startTime', $filterArray)){
				$queryString.=" and email.date ?> ".$filterArray['startTime'];
			}
			if(array_key_exists('endTime', $filterArray)){
				$queryString.=" and email.date ?< ".$filterArray['endTime'];	
			}
			if(array_key_exists('text', $filterArray)) {
				$queryString.=" and (email.to ?=~ '.*".$filterArray['text'].".*'
							   or email.from ?=~ '.*".$filterArray['text'].".*'
							   or email.cc ?=~ '.*".$filterArray['text'].".*'
							  )";
			}
		}

		$queryString .= " with sum(s.count) as tagcount, word, count(email) as emailCount
						order by tagcount desc
						return tagcount, word.value, emailCount";

		Log::debug($queryString);
		
		$query = new Query($this->_client,$queryString);
		$query_time_start = microtime(true);
		$result = $query->getResultSet() ;
		$query_time_end = microtime(true);
		
		$resultArray = array();
		$wordArray = array() ;
		$resultArray['query_time'] = $query_time_end-$query_time_start ;
		foreach ($result as $row) {
			$wordData = array( 'weight' => $row[0],
								'text' => $row[1]);
			array_push($wordArray, $wordData);
			//Getting the e-mails count. This is the same value in all the rows
			$resultArray['email_count'] = $row[2];
		}
		$resultArray['words'] = $wordArray;
		$resultArray['word_count'] = count($resultArray['words']) ;
		return $resultArray;
	}

	/**
	 * This function stores the word node and links it to the email node
	 * @param  String $word       
	 * @param  String $emailToken 
	 * @return Node $wordNode
	 */
	public function storeWordNode($word){
		if(empty($word))
			throw new Exception("Empty word or email token passed. Throwing exception", 1);
			
		$wordNode = $this->isWordNodeExists($word);
		if(!$wordNode){
			$wordNode = $this->_client->makeNode()
							 ->setProperty('type', 'word')
							 ->setProperty('value', $word)
							 ->save() ;

			$this->_wordIndex->add($wordNode,'value',$wordNode->getProperty('value'));
		}
		return $wordNode;
	}

	/**
	 * This function stores the email as a node
	 * @param  EmailTokenizer $emailToken 
	 * @return Node $emailNode
	 */
	public function storeEmailNode($email){
		if(empty($email)) throw new Exception("Empty email token sent. Throwing exception", 1);
		
		if(is_null($this->_emailIndex)) throw new Exception("Unable to find the email index", 1);
		
		$emailToken = $email->getId() ;
		$emailNodeArray = $this->isEmailNodeExists('token',$emailToken) ;
		
		if($emailNodeArray && count($emailNodeArray)) {
			return $emailNodeArray[0];
		}

		$timestamp = $email->getTime() ;
		$from = $email->getFrom();
		$to = $email->getTo();
		$cc = $email->getCC();

		$emailNode = $this->_client->makeNode()
						  ->setProperty('type', 'email')
						  ->setProperty('token',$emailToken);
		if(!empty($timestamp))						  
			$emailNode->setProperty('time',$timestamp);
		if(!empty($from))
			$emailNode->setProperty('from', $from);
		if(!empty($to))
			$emailNode->setProperty('to',$to);
		if(!empty($cc))
			$emailNode->setProperty('cc',$cc);
		$emailNode->save();
		
		//Indexing the properties for the email node so that they can be searched easily later
		$this->_emailIndex->add($emailNode,'token',$emailNode->getProperty('token'));
		if(!empty($timestamp))						  
			$this->_emailIndex->add($emailNode,'time',$emailNode->getProperty('time'));
		if(!empty($from))
			$this->_emailIndex->add($emailNode,'from',$emailNode->getProperty('from'));
		if(!empty($to))
			$this->_emailIndex->add($emailNode,'to',$emailNode->getProperty('to'));
		if(!empty($cc))
			$this->_emailIndex->add($emailNode,'cc',$emailNode->getProperty('cc'));

		return $emailNode ;
	}

	/**
	 * This function stores the email as a node based on the token
	 * @param  String $emailToken 
	 * @return Node $emailNode
	 */
	public function storeEmailNodeByToken($emailToken){
		if(empty($emailToken)) throw new Exception("Empty email token sent. Throwing exception", 1);
		
		if(is_null($this->_emailIndex)) throw new Exception("Unable to find the email index", 1);
		
		$emailNodeArray = $this->isEmailNodeExists('token',$emailToken) ;
		if($emailNodeArray && count($emailNodeArray)) {
			return $emailNodeArray[0];
		}

		$emailNode = $this->_client->makeNode()
						  ->setProperty('type', 'email')
						  ->setProperty('token',$emailToken);
		$emailNode->save();

		//Indexing the properties for the email node so that they can be searched easily later
		$this->_emailIndex->add($emailNode,'token',$emailNode->getProperty('token'));

		return $emailNode ;
	}
	/**
	 * This function stores the relationship between a word and email node
	 * @param  String $word       
	 * @param  String $emailToken 
	 * @return boolean             
	 */
	public function storeWordEmailRelation($word,$emailToken){
		if(empty($word) || empty($emailToken)) 
			throw new Exception("Empty word or email token provided. Not allowed", 1);
		
		$emailNodeArray = $this->isEmailNodeExists('token',$emailToken);
		if(!$emailNodeArray && !count($emailNodeArray)){
			$emailNode = $this->storeEmailNodeByToken($emailToken);
		}else{
			$emailNode = $emailNodeArray[0];
		}

		$wordNode = $this->isWordNodeExists($word);
		if(!$wordNode)
			$wordNode = $this->storeWordNode($word);
		$relation = $this->getRelationshipBetweenEmailAndWord($emailNode,$wordNode);
		if(is_null($relation)){
			$emailNode->relateTo($wordNode,'CONTAINS')->setProperty('count',1)->save();	
		}else{
			$count = $relation->getProperty('count');
			$relation->setProperty('count',++$count)->save() ;
		}
		return true;
	}

	/**
	 * This function returns the relationship ID (if exists) between emailNode and wordNode
	 * @param  Node $emailNode
	 * @param  Node $wordNode 
	 * @return Relationship              
	 */
	public function getRelationshipBetweenEmailAndWord($emailNode,$wordNode){
		if(empty($emailNode) || empty($wordNode))
			throw new Exception("Empty email or word node given. Not allowed", 1);

		$path = $emailNode->findPathsTo($wordNode, 'CONTAINS', Relationship::DirectionOut)
						  ->setMaxDepth(1)
						  ->getSinglePath() ;
		if(is_null($path))
			return null ;

		$path->setContext(Path::ContextRelationship);
		foreach ($path as $rel) {
			return $rel ;
		}
	}

	/**
	 * This function checks if the word node exists in Neo4j DB
	 * @param  String  $word 
	 * @return Node      
	 */
	public function isWordNodeExists($word){
		if(empty($word)) throw new Exception("Empty word given. Not allowed", 1000);
		
		$wordNode = $this->_wordIndex->findOne('value',$word );
		return (empty($wordNode)) ? null:$wordNode ;
	}

	/**
	 * This function checks if the email node exists in Neo4j DB
	 * @param  String  $key This is the key over which the index needs to be searched
	 * @param String $value This is the value of the property
	 * @return Node            
	 */
	public function isEmailNodeExists($key,$value){
		if(empty($value) || empty($key)) throw new Exception("Empty key/value given. Not allowed", 1001);
		
		$emailNode = $this->_emailIndex->query("$key:$value");
		return (empty($emailNode)) ? null:$emailNode ;
	}
}