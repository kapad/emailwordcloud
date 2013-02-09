<?php


class PushReceiver {
	
	/**
	 * 
	 */
	public function doStore($params) {

		if(isset($this->params['hmtl']) && !empty($this->params['html'])) {
			$params['body'] = $this->convertHTMLBodyToText($params['html']);
		}

		unset($this->params['html']);

		//send the params array to arpits code here. 
		
		return;

	}

	private function convertHTMLBodyToText($html) {

		//

	}

}


?>