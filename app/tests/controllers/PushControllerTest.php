<?php


class PushControllerTest extends TestCase {


	public function testMailPush() {

		$this->client->request( 'POST', 'push/', array(), array(), 
			array('Content-Type'=>'text/json',),
			'{
			"headers":"test header",
			"text":"testing",
			"from":"test@unittest.com",
			"to":"tester@unittest.com",
			"subject":"test",
			"charsets":"UTF-8"
		}' );

		$response = $this->client->getResponse();

		$this->assertEquals( 200, $response->getStatusCode() );

	}

	public function testFailPush() {

		unset( $response );
		$this->client->request( 'POST', 'push/', array(), array(), 
			array('Content-Type'=>'text/json',),
			'{
			"text":"testing",
			"from":"test@unittest.com",
			"to":"tester@unittest.com",
			"subject":"test",
			"charsets":"UTF-8"
		}' );
		$response = $this->client->getResponse();
		$this->assertEquals( 400, $response->getStatusCode() );

		// $this->refreshApplication();

		unset( $response );
		$this->client->request( 'POST', 'push/', array(), array(), 
			array('Content-Type'=>'text/json',),
			'{
			"headers":"test header",
			"text":"testing",
			"to":"tester@unittest.com",
			"subject":"test",
			"charsets":"UTF-8"
		}' );
		$response = $this->client->getResponse();
		$this->assertEquals( 400, $response->getStatusCode() );

		// $this->refreshApplication();

		unset( $response );
		$this->client->request( 'POST', 'push/', array(), array(), 
			array('Content-Type'=>'text/json',),
			'{
			"headers":"test header",
			"text":"testing",
			"from":"test@unittest.com",
			"subject":"test",
			"charsets":"UTF-8"
		}' );
		$response = $this->client->getResponse();
		assertEquals( 400, $response->getStatusCode() );

	}


}


?>
