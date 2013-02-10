<?php

class PushController extends BaseController {

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store() {

		try {
			Log::debug(var_export(apache_request_headers(), true));
			// Log::debug(var_export(file_get_contents('php://input'), true));
			$post = Input::all();
			Log::debug(var_export($post, true));
			$params = $this->parseOutParams($post);
			$this->validateStoreParams( $params );
			$pushReceiver = App::make( 'PushReceiver');
			$emailToken = $pushReceiver->doStore($params);
			$response = Response::make();
			$response->setStatusCode(200)->send();
		} catch( Exception $e ) {
			Log::debug($e->getMessage());
			$response = Response::make();
			$response->setStatusCode(200)->send();
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

	private function parseOutParams( $post ) {

		$params = new stdClass();
		if ( isset( $post['subject'] ) ) {
			$params->subject = $post['subject'];
		}
		if ( isset( $post['headers'] ) ) {
			$params->headers = $post['headers'];
		}
		if ( isset( $post['from'] ) ) {
			$params->from = $post['from'];
		}
		if ( isset( $post['to'] ) ) {
			$params->to = $post['to'];
		}
		if ( isset( $post['text'] ) ) {
			$params->text = $post['text'];
		}
		if ( isset( $post['html'] ) ) {
			$params->html = $post['html'];
		}
		if ( isset( $post['cc'] ) ) {
			$params->cc = $post['cc'];
		}

		return $params;
	}

}
