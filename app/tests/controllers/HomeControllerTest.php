<?php
class HomeControllerTest extends TestCase{

	public function testgetwords(){
		$this->client->request('POST','home/getwords',array(),array(),
			array('Content-Type'=>'text/json',),
			'{
				words: [foo,bar]
			}');
		$response = $this->client->getResponse();
		$this->assertEquals( 200, $response->getStatusCode() );
	}
}