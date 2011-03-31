<?php
require_once(ROOT.DS.MAIN.DS.'library'.DS.'session_check.php');
class Add_new_category {
	protected $_category;
	
	function __construct(){
		if(session_check()){	
			if(array_key_exists('category',$_POST)){
				$this->_category = $_POST["category"];
			}
			else{
				die('There was an error on the client. Your browser didn\'t send the appropriate information. Try refreshing the page.');
			}
		}
		else{
			die('Your session has expired. Please <a href="'.ADMIN_URL.'/logout" target="_self">login</a> again in order to add your category.');
		}
	}
	
	function __destruct(){
		loadIntClass('sql_query');
		$Cat = new Sql_query('categories');
		$cat_arr = $Cat->selectAll();
		for($i=0; $i < count($cat_arr); ++$i){
			if($this->_category === $cat_arr[$i]['Categorie']['category']){
				die('This category already exists. If it is not showing up in your slection menu check the documentation for add_new_category.');
			}
		}
		if(is_dir(ROOT.DS.MAIN.DS.'reflex'.DS.'documents'.DS.'_categories_'.DS.$this->_category)){
			die('This category already exists. If it is not showing up in your slection menu check the documentation for add_new_category.');
		}
		$Cat->simpleQuery('INSERT INTO `categories` (category) VALUES(\''.$this->_category.'\')');
		$Cat->disconnect();
		if(!mkdir(ROOT.DS.MAIN.DS.'reflex'.DS.'documents'.DS.'_categories_'.DS.$this->_category)){
			die('The server failed to write the folder. Check the documentation for add_new_category.');
		}
		die('yes');
	}
}