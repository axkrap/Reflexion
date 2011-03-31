<?php
require_once(ROOT.DS.MAIN.DS.'library'.DS.'session_check.php');
class Template_post {
	
	protected $_uri;
	protected $_template;
	function __construct() {
		if(session_check()) {
			if(array_key_exists('uri',$_POST) && array_key_exists('template',$_POST)){
				$this->_uri = $_POST['uri'] !== '_index_' ? str_replace('_','/',$_POST['uri']): $_POST['uri'];
				$this->_template = $_POST['template'];
				$this->template();
			}
			else{
				die('Your browser did not send the right information. If you are working on an older browser please switch to a modern browser.');
			}
		}
		else{
			die('Your session has expired, please log back in.');
		}
	}
	
	function template(){
		loadIntClass('sql_query');
		$posts = new Sql_query('posts');
		$num =  $posts->selectWhere('slug',$this->_uri);
		if($num != 0){
			if(file_exists(ROOT.DS.MAIN.DS.'reflex'.DS.'templates'.DS.$this->_template.'.php')){
				$posts->simpleQuery('UPDATE `posts` SET `template`=\''.$this->_template.'\' WHERE `slug`=\''.$this->_uri.'\'');
				die('yes');
			}
			else{
				die('It seems that the template does not exist anymore.');
			}
		}
		else{
			die('The server could not find the post that you selected.');
		}
	}
}