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
	// public function testEmailNodeStore(){
	// 	$interface = new Neo4jInterface() ;
	// 	$emailNode = $interface->storeEmailNode('test123');
	// 	print_r($emailNode);
	// 	$this->assertTrue(true);
	// }
	
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
}