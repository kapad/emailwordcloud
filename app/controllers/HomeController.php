<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function showWelcome()
	{
		return View::make('hello');
	}

	public function saveSendGridEmail()
	{
		return View::make('hello');
	}

	public function showWordCloud()
	{
		return View::make('showcloud');
	}

	public function getWords() {
		// $wordsAndWeights = array(
		// 	array('text'=>"Lorem","weight"=>15),
		// 	array('text'=>"Rohan","weight"=>9),
		// 	array('text'=>"Invalid","weight"=>6),
		// 	array('text'=>"Client","weight"=>7),
		// 	array('text'=>"Credentials","weight"=>5),
		// 	array('text'=>"Error","weight"=>10),
		// 	array('text'=>"Exception","weight"=>8),
		// 	array('text'=>"API","weight"=>16),
		// 	array('text'=>"Key","weight"=>30),
		// 	array('text'=>"Secret","weight"=>25)
		// );
		$neoModel = new Neo4jModel();
		//TODO: Take a word as an input and use that to filter the word count as well.
		$result = $neoModel->getWordCount() ;
		return Response::json($result);
	}

}