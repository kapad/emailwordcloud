<?php

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

	public function testIsEmailNodeNotExists(){
		$this->assertFalse($this->_interface->isEmailNodeExists('NotEmailNode'));
	}

	public function testIsEmailNodeExists(){
		$this->assertFalse($this->_interface->isEmailNodeExists('2'));
	}

	public function testIsWordNodeNotExists(){
		$this->assertFalse($this->_interface->isWordNodeExists('NotWordNode'));
	}

	public function testIsWordNodeExists(){
		$this->assertFalse($this->_interface->isWordNodeExists('foo'));
	}

	public function testStoreEmailNode(){
		$emailNode = $this->_interface->storeEmailNode('testNode');
		$this->assertNotNull($emailNode);
	}

	public function testStoreWordNode(){
		$wordNode = $this->_interface->storeWordNode('testWord');
		$this->assertNotNull($wordNode);
	}

	public function testgetRelationshipBetweenEmailAndWord(){
		$wordNode = $this->_interface->isWordNodeExists('testWord');
		$emailNode = $this->_interface->isEmailNodeExists('testNode');
		$this->_interface->getRelationshipBetweenEmailAndWord($emailNode,$wordNode);
	}

	public function teststoreWordEmailRelation(){
		$result = $this->_interface->storeWordEmailRelation('testWord','testNode');
		$this->assertTrue($result);
	}
	
	public function testgetWordCountNoWords(){
		$result = $this->_interface->getWordCount() ;
		$this->assertNotNull($result);
	}

	public function testgetWordCountWordArray(){
		$result = $this->_interface->getWordCount(array('testWord'));
		$this->assertNotNull($result);
	}
}