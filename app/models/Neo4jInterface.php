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
	 * @param  Array $wordArray 
	 * @return Array            
	 */
	public function getWordCountByWordsFilter($wordArray = null){
		$wordNodeList = array(); 		
		if(is_array($wordArray)){
			foreach ($wordArray as $word) {
				$wordNode = $this->isWordNodeExists($word);
				if($wordNode)
					array_push($wordNodeList,$wordNode->getId());
			}
			$wordNodeString = (count($wordNodeList))?implode(',', $wordNodeList) : "*";
		}else{
			$wordNodeString = "*";
		}

		$queryString = "start n=node($wordNodeString)
						match n<-[r]-email-[s]->word
						where n.type?= 'word' and type(r) = 'CONTAINS' and type(s) = 'CONTAINS'
						return sum(s.count), word.value";
		$query = new Query($this->_client,$queryString);
		$result = $query->getResultSet() ;
		$resultArray = array();
		foreach ($result as $row) {
			$wordArray = array( 'weight' => $row[0],
								'text' => $row[1]);
			array_push($resultArray, $wordArray);
		}
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
		if(count($emailNodeArray)) {
			return $emailNodeArray[0];
		}

		$timestamp = ($email->getTime()) ? : null ;
		$from = ($email->getFrom()) ? : null;
		$to = ($email->getTo()) ? : null ;
		$cc = ($email->getCC()) ? : null;

		$emailNode = $this->_client->makeNode()
						  ->setProperty('type', 'email')
						  ->setProperty('token',$emailToken);
		if($timestamp)						  
			$emailNode->setProperty('time',$timestamp);
		if($from)
			$emailNode->setProperty('from', $from);
		if($to)
			$emailNode->setProperty('to',$to);
		if($cc)
			$emailNode->setProperty('cc',$cc);
		$emailNode->save();

		//Indexing the properties for the email node so that they can be searched easily later
		$this->_emailIndex->add($emailNode,'token',$emailNode->getProperty('token'));
		if($timestamp)						  
			$this->_emailIndex->add($emailNode,'time',$emailNode->getProperty('time'))
		if($from)
			$this->_emailIndex->add($emailNode,'from',$emailNode->getProperty('from'))
		if($to)
			$this->_emailIndex->add($emailNode,'to',$emailNode->getProperty('to'))
		if($cc)
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
		if(count($emailNodeArray)) {
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
		if(!count($emailNodeArray)){
			$emailNode = $this->storeEmailNodeByToken($emailToken);
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
	 * @param  Integer $emailNode
	 * @param  Integer $wordNode 
	 * @return Integer              
	 */
	public function getRelationshipBetweenEmailAndWord($emailNode,$wordNode){
		if(empty($emailNode) || empty($wordNode))
			throw new Exception("Empty email or word node ID given. Not allowed", 1);

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
	 * @return boolean / Node      
	 */
	public function isWordNodeExists($word){
		if(empty($word)) throw new Exception("Empty word given. Not allowed", 1000);
		
		$wordNode = $this->_wordIndex->findOne('value',$word );
		return (empty($wordNode)) ? false:$wordNode ;
	}

	/**
	 * This function checks if the email node exists in Neo4j DB
	 * @param  String  $key This is the key over which the index needs to be searched
	 * @param String $value This is the value of the property
	 * @return boolean / Node            
	 */
	public function isEmailNodeExists($key,$value){
		if(empty($emailToken)) throw new Exception("Empty email token given. Not allowed", 1001);
		
		$emailNode = $this->_emailIndex->query("$key:$value");
		return (empty($emailNode)) ? false:$emailNode ;
	}
}