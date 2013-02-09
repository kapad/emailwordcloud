<?php

class PushController extends BaseController {

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store() {

		try {
			$params = Input::json();
			// $json = file_get_contents('php://input');
			// Log::debug(var_export($json, true));
			// $params = json_decode($json, true);
			Log::debug(var_export($params, true));
			// Log::debug(var_export($_POST, true));
			$this->validateStoreParams( $params );
			$pushReceiver = App::make( 'PushReceiver');
			$pushReceiver->doStore($params);
			$response = Response::make();
			$response->setStatusCode(200)->send();
		} catch( Exception $e ) {
			Log::debug($e->getMessage());
			// $response = Response::make();
			// $response->setStatusCode(400)->send();
			Response::make(null, 400)->send();
		}

	}

	private function validateStoreParams( $params ) {

		if ( !isset( $params->headers ) || empty( $params->headers ) ||
			!isset( $params->from ) || empty( $params->from ) ||
			!isset( $params->to ) || empty( $params->to ) ) {

			throw new Exception('Incorrect post params');

		}
		return;

	}

}
