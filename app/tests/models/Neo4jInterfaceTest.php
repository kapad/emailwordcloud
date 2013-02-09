<?php

class Neo4jInterfaceTest extends TestCase {
	
	public function testConnection(){
		$interface = new Neo4jInterface() ;
		$client = $interface->getClient();
		$serverInfo = $client->getServerInfo();
		$this->assertNotContains('Body',$serverInfo);
	}

	public function testEmailIndex(){
		$interface = new Neo4jInterface() ;
		$emailIndex = $interface->getEmailIndex() ;
		print_r($emailIndex);
		$this->assertTrue(true);
		// $this->assertNotContains('Body',$serverInfo);	
	}
	
	// public function testEmailNodeStore(){
	// 	$interface = new Neo4jInterface() ;
	// 	$emailNode = $interface->storeEmailNode('test123');
	// 	print_r($emailNode);
	// 	$this->assertTrue(true);
	// }
}