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

	public function getId(){
		return $this->id ;
	}
	
	public function __construct($email) {
		$this->email = $email;
		$this->initialize();
	}

	private function initialize() {

		if(isset($this->email->id)) {
			$this->id = $this->email->id;
		} else {
			$this->id = '';
		}

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

		$this->time = date('Y-m-d H:i:s');
		// $now = strtotime(date('Y-m-d H:i:s'));
		// $mt = mt_rand(1, 99999999);
		// $rand = floatval('0.' . $mt);
		// $this->time = date('Y-m-d H:i:s', ($now*$rand));
	}

	public function toString() {
		return 
		$this->headers . ' ' .
		$this->subject .  ' ' .
		$this->time. ' ' .
		$this->from .  ' ' .
		$this->to .  ' ' .
		$this->cc . ' ' .
		$this->body;

	}

	private function strippedBody() {

		$str = preg_replace('/[^a-z0-9\'\s]+/i', '', $this->body);
		return $str;

	}

	public function storeToGraph() {

		$id = hash('md5', $this->toString());

		$this->id = $id;
		Log::debug('Got the ID: '.$id);
		Log::debug(var_export($this, true));

		$neo = new Neo4jInterface();
		if(FALSE !== $neo->isEmailNodeExists('token',$id)) {
			Log::debug("email node not found. should get created");
			$neo->storeEmailNode($this);
		}

		$text = explode(' ', $this->strippedBody());

		foreach($text as $word) {
			if(strlen($word) > 3) {
				Log::debug("Processing word: $word");
				// $wordNode = $neo->isWordNodeExists($word);
				// if(!$neo->isWordNodeExists($word)) {
				// 	Log::debug("Storing the word: $word");
				// 	$neo->storeWordNode($word);
				// }
				Log::debug("Storing the relation between $word and $id");
				$neo->storeWordEmailRelation($word, $id);
			}
		}
		
		return $id;
	}


}



?>
