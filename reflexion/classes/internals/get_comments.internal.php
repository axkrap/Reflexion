<?php
require_once(ROOT.DS.MAIN.DS.'library'.DS.'session_check.php');
class Get_comments{
	protected $_comments;
	protected $_sort;
	protected $_respondents;
	protected $_approved;
	protected $_DESC;
	function __construct($post, $filter = 'ALL', $sort = 'none', $DESC = true){
		if(session_check()) {
			$this->_sort = $sort;
			$this->_DESC = $DESC;
			loadIntClass('sql_query');
			$sql = new Sql_query('comments');
			$this->_comments =  $sql->getComments($post,$filter);
		}
		else{
			die('Your session has expired. Please <a href="'.ADMIN_URL.'/logout" target="_self">login</a> again in order to submit your post.');	
		}
	}
	function commentsArr(){
		$this->_sorter();
		return $this->_comments;
	}
	function _sorter(){
		$temp = array();
		if($this->_sort !== 'none'){
			for($i = 0; $i < count($this->_comments); ++$i){
				$temp[$this->_comments[$i][$this->_sort]] = $this->_comments[$i];
			}
			if($this->_DESC){
				krsort($temp);
			}
			else{
				ksort($temp);
			}
			$this->_comments = $temp;
		}
	}
}