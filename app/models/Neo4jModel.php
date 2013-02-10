<?php

class Neo4jModel{

	/**
	 * This function returns array of words and the count of their occurrence
	 * @param  Array $wordArray Array of words to filter the results on
	 * @return Array            
	 */
	public function getWordCount($wordArray = null){
		$neoInterface = new Neo4jInterface() ;
		return $neoInterface->getWordCountByWordsFilter($wordArray) ;
	}
}