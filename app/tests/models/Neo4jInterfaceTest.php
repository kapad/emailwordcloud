<?php
use Everyman\Neo4j\Client,
	Everyman\Neo4j\Index\NodeIndex,
	Everyman\Neo4j\Relationship,
	Everyman\Neo4j\Index\RelationshipIndex,
	Everyman\Neo4j\Node,
	Everyman\Neo4j\Path,
	Everyman\Neo4j\Cypher\Query,
	Everyman\Neo4j\Cypher;

class Neo4jInterfaceTest extends TestCase {
	
	private $_interface ;
	public function setUp(){
		$this->_interface = new Neo4jInterface() ;
	}

	public function testConnection(){
		$client = $this->_interface->getClient();
		$serverInfo = $client->getServerInfo();
		$this->assertNotContains('Body',$serverInfo);
	}

	public function teststoreEmailNodeByToken(){
		$emailNode = $this->_interface->storeEmailNodeByToken('testNode');
		$this->assertTrue($emailNode instanceof Node);
	}

	public function testStoreWordNode(){
		$wordNode = $this->_interface->storeWordNode('testWord');
		$this->assertTrue($wordNode instanceof Node);
	}

	public function testIsEmailNodeNotExists(){
		$this->assertNull($this->_interface->isEmailNodeExists('token','NotEmailNode'));
	}

	public function testIsEmailNodeExists(){
		$result = $this->_interface->isEmailNodeExists('token','testNode');
		$this->assertTrue(is_array($result));
	}

	public function testIsWordNodeNotExists(){
		$this->assertNull($this->_interface->isWordNodeExists('NotWordNode'));
	}

	public function testIsWordNodeExists(){
		$result = $this->_interface->isWordNodeExists('testWord');
		$this->assertTrue($result instanceof Node);
	}

	public function testgetRelationshipBetweenEmailAndWord(){
		$wordNode = $this->_interface->isWordNodeExists('testWord');
		$emailNode = $this->_interface->isEmailNodeExists('token','testNode');
		$rel = $this->_interface->getRelationshipBetweenEmailAndWord($emailNode[0],$wordNode);
		$this->assertTrue($rel instanceof Relationship);
	}

	public function teststoreWordEmailRelation(){
		$result = $this->_interface->storeWordEmailRelation('testWord','testNode');
		$this->assertTrue($result);
	}
	
	public function testgetWordCountByWordsFilterNoWords(){
		$result = $this->_interface->getWordCountByWordsFilter() ;
		$this->assertNotNull($result);
	}

	public function testgetWordCountByWordsFilterWordArray(){
		$result = $this->_interface->getWordCountByWordsFilter(array('testWord'));
		$this->assertNotNull($result);
	}

	public function teststoreEmailNode(){
		$email = array(
			'id' => '12345',
			'headers' => 'email header', 
			'subject'=>'unit test email', 
			'from'=>'rohan@gharpay.in',
			'to'=>'rohankapadia@gmail.com',
			'cc'=>'all@gharpay.in',
			'body'=>'Verpa bohemica is a species',
			);
		$emailTokenizer = new EmailTokenizer((object) $email);
		$emailNode = $this->_interface->storeEmailNode($emailTokenizer);
		$this->assertTrue($emailNode instanceof Node);
	}
}