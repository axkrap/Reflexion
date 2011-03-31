<?php
require_once(ROOT.DS.MAIN.DS.'library'.DS.'session_check.php');
class Add_new_author {
	protected $_author;
	
	function __construct(){
		if(session_check()){	
			if(array_key_exists('author',$_POST)){
				$this->_author = $_POST["author"];
			}
			else{
				die('There was an error on the client. Your browser didn\'t send the appropriate information. Try refreshing the page.');
			}
		}
		else{
			die('Your session has expired. Please login again in order to add an author.');
		}
	}
	
	function __destruct(){
		loadIntClass('sql_query');
		$Aut = new Sql_query('authors');
		$aut_arr = $Aut->selectAll();
		for($i=0; $i < count($aut_arr); ++$i){
			if($this->_author === $aut_arr[$i]['Author']['author']){
				die('This author already exists. If they are not showing up in your slection menu check the documentation for add_new_author.');
			}
		}
		$Aut->simpleQuery('INSERT INTO `authors` (author) VALUES(\''.$this->_author.'\')');
		die('yes');
	}
}