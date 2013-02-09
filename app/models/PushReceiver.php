<?php

use Html2Text\Html2Text;


class PushReceiver {

	/**
	 *
	 */
	public function doStore( $params ) {

		if ( isset( $params->html ) && !empty( $params->html ) ) {
			if ( isset( $params->text ) ) {
				$params->text = $params->text . $this->convertHTMLBodyToText( $params->html );
			}
			$params->text = $this->convertHTMLBodyToText( $params->html );
		}

		unset( $this->params->html );

		$tokenizer = new EmailTokenizer($params);

		$emailToken = $tokenizer->storeToGraph();

		return $emailToken;

	}

	private function convertHTMLBodyToText( $html ) {

		$converter = new Html2Text( $html );
		return $converter->get_text();

	}

}


?>
