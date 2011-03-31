<?php
require_once(ROOT.DS.MAIN.DS.'library'.DS.'session_check.php');
class Comments_actions{
	protected $_action;
	protected $_id;
	function __construct(){
		if(session_check()) {
			if(array_key_exists('action',$_POST) && array_key_exists('id',$_POST)){
				$this->_action = $_POST['action'];
				$this->_id = $_POST['id'];
				$this->actionParse();
			}
		}
		else{
			die('Your session has expired. Please <a href="'.ADMIN_URL.'/logout" target="_self">login</a> again in order to submit your post.');
		}
	}
	function actionParse(){
		if($this->_action === 'unapprove'){
			$this->unapprove();
		}
		else if($this->_action === 'approve'){
			$this->approve();
		}
		else if($this->_action === 'spam'){
			$this->spam();
		}
		else if($this->_action === 'delete'){
			$this->delete();
		}
	}
	function unapprove(){
		loadIntClass('sql_query');
		$sql = new Sql_query('comments');
		$array = $sql->query('SELECT * FROM `comments` WHERE `id`=\''.$this->_id.'\'');
		$sql->simpleQuery('UPDATE `comments` SET `approved`=\'pending\' WHERE `id`=\''.$this->_id.'\'');
		$this->republish($array[0]['Comment']['post_slug']);
		die('SUCCESS');
	}
	function approve(){
		loadIntClass('sql_query');
		$sql = new Sql_query('comments');
		$array = $sql->query('SELECT * FROM `comments` WHERE `id`=\''.$this->_id.'\'');
		$sql->simpleQuery('UPDATE `comments` SET `approved`=\'approved\' WHERE `id`=\''.$this->_id.'\'');
		$this->republish($array[0]['Comment']['post_slug']);
		die('SUCCESS');
	}
	function spam(){
		loadIntClass('sql_query');
		$sql = new Sql_query('comments');
		$array = $sql->query('SELECT * FROM `comments` WHERE `id`=\''.$this->_id.'\'');
		$sql->simpleQuery('UPDATE `comments` SET `approved`=\'spam\' WHERE `id`=\''.$this->_id.'\'');
		$this->republish($array[0]['Comment']['post_slug']);
		die('SUCCESS');
	}
	function delete(){
		loadIntClass('sql_query');
		$sql = new Sql_query('comments');
		$array = $sql->query('SELECT * FROM `comments` WHERE `id`=\''.$this->_id.'\'');
		$sql->simpleQuery('DELETE FROM `comments` WHERE `id`=\''.$this->_id.'\'');
		$this->republish($array[0]['Comment']['post_slug']);
		die('SUCCESS');
	}
	function republish($post){
		loadIntClass('republish_post');
		new Republish_post($post);
	}
}