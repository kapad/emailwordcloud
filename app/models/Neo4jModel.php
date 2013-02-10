<?php

class Neo4jModel{

	/**
	 * This function returns array of words and the count of their occurrence
	 * @param  Array Array $wordArray['words' => ['word',word],
	 *                          'startTime' => '<From Time>',
	 *                          'endTime' => '<End Time>',
	 *                          'text' => '<email id text>'
	 *                         ] 
	 * @return Array            
	 */
	public function getWordCount($wordArray = null){
		$neoInterface = new Neo4jInterface() ;
		return $neoInterface->getWordCountByFilter($wordArray) ;
	}
}