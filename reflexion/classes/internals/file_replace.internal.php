<?php
require_once(ROOT.DS.MAIN.DS.'library'.DS.'session_check.php');
class File_replace{
	protected $_path;
	protected $_filename;
	function __construct(){
		if(session_check()){
			if(array_key_exists('parent',$_POST) && array_key_exists('child',$_POST) && array_key_exists('replace',$_POST)){		$this->_path = str_replace('.',DS,$_POST['parent']);
				$this->_filename = $_POST['child'];
				if($_POST['replace'] === 'true'){
					$this->replace();
				}
				else{
					if(unlink(ROOT.DS.$this->_path.DS.$this->_filename.'.temp')){
						die('KILLED');
					}
				}
			}
			else{
				die('The browser didn\'t send the right information. It is possible that you have a choppy internet connection.');
			}
		}
		else{
			die('Your session has expired. Please <a href="'.ADMIN_URL.'/logout" target="_self">login</a> again in order to replace your file.');
		}
	}
	
	function replace(){
		if(copy(ROOT.DS.$this->_path.DS.$this->_filename.'.temp', ROOT.DS.$this->_path.DS.$this->_filename)){
			unlink(ROOT.DS.$this->_path.DS.$this->_filename.'.temp');
			die('SUCCESS');	
		}
		die('The file was successfully uploaded, but the temporary file was not deleted. If this happens more than 10 times you could start to have a problem');
	}
}