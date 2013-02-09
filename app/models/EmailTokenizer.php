<?php

class EmailTokenizer {

	private $email;
	private $body;
	private $headers;
	private $from;
	private $to;
	private $time;
	private $cc;
	private $subject;

	public function __construct($email) {
		$this->email = $email;
		$this->initialize();
	}

	private function initialize() {

		if(isset($email->body)) {
			$this->body = $email->body;
		} else {
			$this->body = '';
		}

		if(isset($email->subject)) {
			$this->subject = $email->subject;
		} else {
			$this->subject = '';
		}

		if(isset($email->headers)) {
			$this->headers = $email->headers;
		}else {
			$this->headers = '';
		}

		if(isset($email->from)) {
			$this->from = $email->from;
		} else {
			$this->from = '';
		}

		if(isset($email->to)) {
			$this->to = $email->to;
		} else {
			$this->to = '';
		}

		if(isset($email->cc)) {
			$this->cc = $email->cc;
		} else {
			$this->cc = '';
		}

		$this->time = new DateTime();

	}

	public function toString() {
		return 
		$this->headers . ' ' .
		$this->time->format(DateTime::RSS) . ' ' .
		$this->subject .  ' ' .
		$this->from .  ' ' .
		$this->to .  ' ' .
		$this->cc . ' ' .
		$this->body;

	}

	private function strippedBody() {

		return preg_replace('/[^a-z0-9\']+/i', '', $this->body);

	}

	public function storeToGraph() {

		$id = hash('md5', $tokenizer->toString());

		$neo = new Neo4jInterface();
		if(!$neo->isEmailNodeExists($id)) {
			$neo->storeEmailNode($id);
		}

		$text = explode(' ', $this->strippedBody());

		foreach($text as $word) {
			if(strlen($word) > 3) {
				if(!$neo->isWordNodeExists($word)) {
					$neo->storeWordNode($word);
				}
				$neo->storeWordEmailRelation($word, $id);
			}
		}
		return $id;
	}


}



?>
