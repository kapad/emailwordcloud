<?php


class PushControllerTest extends TestCase {


	public function testMailPush() {

		// $this->refreshApplication();

		$response = $this->call( 'POST', 'push/', array(
				'headers'=>'test header',
				'text'=>'testing',
				'from'=>'test@unittest.com',
				'to'=>'tester@unittest.com',
				'subject'=>'test',
				'charsets'=>'UTF-8',
			) );

		$this->assertEquals( 200, $response->getStatusCode() );

	}

	public function testFailPush() {

		// $this->refreshApplication();

		$response = $this->call( 'POST', 'push/', array(
				'text'=>'testing',
				'from'=>'test@unittest.com',
				'to'=>'tester@unittest.com',
				'subject'=>'test',
				'charsets'=>'UTF-8',
			) );

		$this->assertEquals( 400, $response->getStatusCode() );
		unset( $response );

		// $this->refreshApplication();

		$response = $this->call( 'POST', 'push/', array(
				'headers'=>'test header',
				'text'=>'testing',
				'to'=>'tester@unittest.com',
				'subject'=>'test',
				'charsets'=>'UTF-8',
			) );

		$this->assertEquals( 400, $response->getStatusCode() );
		unset( $response );

		// $this->refreshApplication();

		$response = $this->call( 'POST', 'push/', array(
				'headers'=>'test header',
				'text'=>'testing',
				'from'=>'test@unittest.com',
				'subject'=>'test',
				'charsets'=>'UTF-8',
			) );

		assertEquals( 400, $response->getStatusCode() );
		unset( $response );

	}


}


?>
