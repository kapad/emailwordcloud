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
	private $id;

	public function getHeaders() {
		return $this->headers;
	}

	public function getFrom() {
		return $this->from;
	}

	public function getTo() {
		return $this->to;
	}

	public function getTime() {
		return $this->time;
	}

	public function getSubject() {
		return $this->subject;
	}

	public function getCC() {
		return $this->cc;
	}

	public function getBody() {
		return $this->body;
	}

	public function getId() {
		return $id;
	}

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

		$str = preg_replace('/[^a-z0-9\']+/i', '', $this->body);
		Log::debug($str);
		return $str;

	}

	public function storeToGraph() {

		$id = hash('md5', $this->toString());

		$neo = new Neo4jInterface();
		if(FALSE !== $neo->isEmailNodeExists($id)) {
			Log::debug("email node not found. should get created");
			$neo->storeEmailNode($id);
		}

		$text = explode(' ', $this->strippedBody());
		Log::debug(var_export($text, true));

		foreach($text as $word) {
			if(strlen($word) > 3) {
				if(FALSE !== $neo->isWordNodeExists($word)) {
					$neo->storeWordNode($word);
				}
				$neo->storeWordEmailRelation($word, $id);
			}
		}
		$this->id = $id;
		return $id;
	}


}



?>
