<?php
require_once(ROOT.DS.MAIN.DS.'library'.DS.'session_check.php');
class Delete_post {
	
	protected $_uri;
	protected $_loc;
	function __construct() {
		if(session_check()) {
			if(array_key_exists('uri',$_POST)){
				
				$this->_uri = $_POST['uri']==='_index_' ? '_index_' : str_replace('_','/',$_POST['uri']);
				$this->_loc = $_POST['uri']==='_index_' ? '_index_' : str_replace('_',DS,$_POST['uri']);
				$this->delete();
			}
			else{
				die('Your browser did not send the right information. If you are working on an older browser please switch to a modern browser.');
			}
		}
		else{
			die('Your session has expired, please log back in.');
		}
	}
	
	function delete(){
		loadIntClass('sql_query');
		$posts = new Sql_query('posts');
		$num =  $posts->numRows('slug',$this->_uri);
		if($num !== 0){
			$post_arr = $posts->selectWhere('slug',$this->_uri);
			$post_arr = $post_arr['Post'];
			if($post_arr['category'] == 'none'){
				@unlink(ROOT.DS.MAIN.DS.'reflex'.DS.'documents'.DS.$this->_uri.'.php');
				$posts->simpleQuery('DELETE FROM `posts` WHERE `slug`=\''.$this->_uri.'\'');
				die('yes');
			}
			else{
				@unlink(ROOT.DS.MAIN.DS.'reflex'.DS.'documents'.DS.'_categories_'.DS.$this->_uri.'.php');
				$posts->simpleQuery('DELETE FROM `posts` WHERE `slug`=\''.$this->_uri.'\'');
				die('yes');
			}
		}
		else{
			die('The server could not find the post that you selected.');
		}
	}
}