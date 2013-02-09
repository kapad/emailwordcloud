<?php


class PushReceiver {
	
	/**
	 * 
	 */
	public function doStore($params) {

		if(isset($this->params->html) && !empty($this->params->html)) {
			if(isset($params->body)) {

			$params->body = $params->body . $this->convertHTMLBodyToText($params->html);
			}
			$params->body = $this->convertHTMLBodyToText($params->html);
		}

		unset($this->params->html);

		//send the params array to arpits code here. 
		
		return;

	}

	private function convertHTMLBodyToText($html) {

		//

	}

}


?>