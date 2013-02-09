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
		Log::debug(var_export($email, true));
		$this->initialize();
	}

	private function initialize() {

		if(isset($this->email->text)) {
			$this->body = $this->email->text;
		} else {
			$this->body = '';
		}

		if(isset($this->email->subject)) {
			$this->subject = $this->email->subject;
		} else {
			$this->subject = '';
		}

		if(isset($this->email->headers)) {
			$this->headers = $this->email->headers;
		}else {
			$this->headers = '';
		}

		if(isset($this->email->from)) {
			$this->from = $this->email->from;
		} else {
			$this->from = '';
		}

		if(isset($this->email->to)) {
			$this->to = $this->email->to;
		} else {
			$this->to = '';
		}

		if(isset($this->email->cc)) {
			$this->cc = $this->email->cc;
		} else {
			$this->cc = '';
		}

		$this->time = new DateTime();

	}

	public function toString() {
		return 
		$this->headers . ' ' .
		$this->subject .  ' ' .
		$this->time->format(DateTime::RSS) . ' ' .
		$this->from .  ' ' .
		$this->to .  ' ' .
		$this->cc . ' ' .
		$this->body;

	}

	private function strippedBody() {

		$str = preg_replace('/[^a-z0-9\'\s]+/i', '', $this->body);
		Log::debug($str);
		return $str;

	}

	public function storeToGraph() {

		$id = hash('md5', $this->toString());
		Log::debug($id);
		Log::debug($this->toString());
		Log::debug(var_export($this, true));

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
