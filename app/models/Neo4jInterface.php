<?php
use Everyman\Neo4j\Client,
	Everyman\Neo4j\Index\NodeIndex,
	Everyman\Neo4j\Relationship,
	Everyman\Neo4j\Index\RelationshipIndex,
	Everyman\Neo4j\Node,
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
		//Currently hard-coding the neo4j server to localhost port 7575.
		//This needs to be picked up from a config file.
		$this->_client = new Client('localhost', 7474) ;
		$this->_wordIndex = new NodeIndex($this->_client, 'words');
		$this->_emailIndex = new NodeIndex($this->_client, 'emails');
		$this->_containsIndex = new RelationshipIndex($this->_client,'contains');
	}

	/**
	 * This function stores the word node and links it to the email node
	 * @param  String $word       
	 * @param  String $emailToken 
	 * @return NodeObject $wordNode
	 */
	public function storeWordNode($word, $emailToken){
		if(empty($word) || empty($emailToken))
			throw new Exception("Empty word or email token passed. Throwing exception", 1);
			
		$emailNode = $this->storeEmailNode($emailToken);

		if(!$this->isWordNodeExists($word)){
			$wordNode = $client->makeNode() ;
			$wordNode->setProperty('type', 'word');
			$wordNode->setProperty('value', $word);
			$wordNode->save() ;

			$emailNode->relateTo($wordNode, 'CONTAINS')->save() ;
			//TODO: Set the count property of the relationship CONTAINS
		}else{
			//TODO: Get the word node and the relationship property count of CONTAINS
			//Increment the count
			//Set the relationship count with incremented value
		}

		return $wordNode;
	}

	/**
	 * This function stores the email as a node
	 * @param  String $emailToken 
	 * @return NodeObject $emailNode
	 */
	public function storeEmailNode($emailToken){
		if(empty($emailToken)) throw new Exception("Empty email token sent. Throwing exception", 1);
		
		if(is_null($this->_emailIndex)) throw new Exception("Unable to find the email index", 1);

		if($this->isEmailNodeExists($emailToken))		
			return $emailNode;

		return $client->makeNode()->setProperty('token',$emailToken)->save();
	}

	/**
	 * This function checks if the word node exists in Neo4j DB
	 * @param  String  $word 
	 * @return boolean       
	 */
	public function isWordNodeExists($word){
		$wordNode = $this->_wordIndex->queryOne('value: '.$word );
		return (is_null($wordNode)) ? false:true ;
	}

	/**
	 * This function checks if the email node exists in Neo4j DB
	 * @param  String  $emailToken 
	 * @return boolean             
	 */
	public function isEmailNodeExists($emailToken){
		$emailNode = $this->_emailIndex->queryOne('token: '.$emailToken);
		return (is_null($emailNode)) ? false:true ;
	}
}