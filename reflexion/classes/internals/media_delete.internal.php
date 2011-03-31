<?php
require_once(ROOT.DS.MAIN.DS.'library'.DS.'session_check.php');
class Media_delete{
	
	protected $_loc;
	
	function __construct() {
		if(session_check()) {
			$this->_loc = str_replace('/',DS,$_POST['media']);
			$this->delete();
		}
		else{
			die('Your session has expired, please log back in.');
		}
	}
	
	function delete(){
		if(unlink(ROOT.DS.$this->_loc)){
			die('SUCCESS');
		}
		else{
			die('ERROR');
		}
	}
}