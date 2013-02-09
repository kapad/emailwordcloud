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
		$neoModel = new Neo4jModel();
		//TODO: Take a word as an input and use that to filter the word count as well.
		$params = Input::json(); 
		print_r($params);
		$result = $neoModel->getWordCount($params) ;
		return Response::json($result);
	}

}