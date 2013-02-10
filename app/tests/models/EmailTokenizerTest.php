<?php

class EmailTokenizerTest extends TestCase {


	private $tokenizer;

	public function setUp() {
		parent::setUp();
		$email = array(
			'headers' => 'test header', 
			'subject'=>'unit test', 
			'from'=>'rohan@gharpay.in',
			'to'=>'rohankapadia@gmail.com',
			'cc'=>'tech@gharpay.in',
			'text'=>'Verpa bohemica is a species of fungus in the Morchellaceae family, commonly known as the early morel or the wrinkled thimble-cap. The species was first described in the scientific literature by the Czech physician and mycologist Julius Vincenz von Krombholz in 1828 bohemica refers to Bohemia now a part of the Czech Republic where Krombholz originally collected the species. The mushroom has a pale yellow or brown thimble-shaped cap that has a surface wrinkled and ribbed with brain-like convolutions. The cap hangs from the top of a lighter-colored, brittle stem that measures up to 12 cm 4.7 in long. It is one of several species known informally as a false morel. In the field, the mushroom is reliably distinguished from the true morels on the basis of cap attachment: V. bohemica has a cap that hangs completely free from the stem. Although widely considered edible, consumption is generally not advised due to reports of poisoning in susceptible individuals. Poisoning symptoms include gastrointestinal upset and lack of muscular coordination. V. bohemica is found in northern North America, Europe, and Asia.',
			);
		$this->tokenizer = new EmailTokenizer((object) $email);
	}

	public function testStoreToGraph() {
		$token = $this->tokenizer->storeToGraph();
	}

}

?>