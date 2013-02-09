<?php

use Html2Text\Html2Text;


class PushReceiver {

	/**
	 *
	 */
	public function doStore( $params ) {

		if ( isset( $this->params->html ) && !empty( $this->params->html ) ) {
			if ( isset( $params->body ) ) {
				$params->body = $params->body . $this->convertHTMLBodyToText( $params->html );
			}
			$params->body = $this->convertHTMLBodyToText( $params->html );
		}

		unset( $this->params->html );

		$tokenizer = new EmailTokenizer($params);

		$emailToken = $tokenizer->storeToGraph($id);

		return $emailToken;

	}

	private function convertHTMLBodyToText( $html ) {

		$converter = new Html2Text( $html );
		return $converter->get_text();

	}

}


?>
