<?php
require_once(ROOT.DS.MAIN.DS.'library'.DS.'session_check.php');
class Unpublish_post {
	
	protected $_uri;
	protected $_loc;
	
	function __construct() {
		if(session_check()) {
			if(array_key_exists('uri',$_POST)){
				$this->_uri = $_POST['uri'] !== '_index_' ? str_replace('_','/',$_POST['uri']) : '_index_';
				$this->_loc = $_POST['uri'] !== '_index_' ? str_replace('_',DS,$_POST['uri']) : '_index_';
				$this->unpublish();
			}
		}
	}
	function unpublish(){
		loadIntClass('sql_query');
		$posts = new Sql_query('posts');
		$num =  $posts->numRows('slug',$this->_uri);
		if($num != 0){
			if(file_exists(ROOT.DS.MAIN.DS.'reflex'.DS.'documents'.DS.$this->_loc.'.php') || file_exists(ROOT.DS.MAIN.DS.'reflex'.DS.'documents'.DS.'_categories_'.DS.$this->_loc.'.php')){
				$post_arr = $posts->selectWhere('slug',$this->_uri);
				$post_arr = $post_arr['Post'];
				if($post_arr['category'] == 'none'){
					if(unlink(ROOT.DS.MAIN.DS.'reflex'.DS.'documents'.DS.$this->_loc.'.php')){
						$posts->simpleQuery('UPDATE `posts` SET `publish`=\'0\' WHERE `slug`=\''.$this->_uri.'\'');
						die('yes');
					}
					else{
						die('The server failed to delete the post you selected.');	
					}
				}
				else{
					if(unlink(ROOT.DS.MAIN.DS.'reflex'.DS.'documents'.DS.'_categories_'.DS.$this->_loc.'.php')){
						$posts->simpleQuery('UPDATE `posts` SET `publish`=\'0\' WHERE `slug`=\''.$this->_uri.'\'');
						die('yes');
					}
					else{
						die('The server failed to delete the post you selected.');	
					}
				}
			}
			else{
				$post_arr = $posts->selectWhere('slug',$this->_uri);
				$post_arr = $post_arr['Post'];
				if($post_arr['publish'] == 0){
					die('yes');	
				}
				else{
					$posts->simpleQuery('UPDATE `posts` SET `publish`=\'0\' WHERE `slug`=\''.$this->_uri.'\'');
					die('yes');
				}
				
			}
		}
		else{
			die('The server could not find the post that you selected.');	
		}
	}
}