<?php
require_once(ROOT.DS.MAIN.DS.'library'.DS.'session_check.php');
class Upload_template{
	
	protected $_filename;
	protected $_templateName;
	protected $_extension;
	protected $_path;
	protected $_css;
	function __construct(){
		if(!array_key_exists('username',$_POST)){
			die('You browser failed to send the right information to the server. This could be due to a bad internet connection.');
		}
		if(session_check($_POST['username'])){
			$this->_filename = basename($_FILES['Filedata']['name']);
			$this->_path = ROOT.DS.MAIN.DS.'reflex'.DS.'templates'.DS;
			if(substr_count($this->_filename, '.') === 1){
				$this->checkString();
			}
			else{
				$string = substr_count($this->_filename, '.') > 0 ? 'Please upload a file with only one extension.' : 'Your file must have at least one extension.';
				die($string);
			}
		}
		else{
			die('Your session has expired. Please <a href="'.ADMIN_URL.'/logout" target="_self">login</a> again in order to upload your template.');
		}
	}
	
	function checkString(){
		$arr = explode('.',$this->_filename);
		$this->_templateName = $arr[0];
		array_shift($arr);
		$this->_extension = $arr[0];
		if(!preg_match('/^[[:alnum:]]+_*[[:alnum:]_]*$/', $this->_templateName)){
			die('The filename can only be alphanumeric, with the exception of underscore, "_"');
		}
		if($this->_extension !== 'php'){
			die('The file must be a php file.');
		}
		$this->checkDB();
	}
	
	function checkDB(){
		loadIntClass('sql_query');
		$sql = new Sql_query('templates');
		$num =  $sql->numRows('name', $this->_templateName);
		if($num === 1 && file_exists($this->_path.$this->_filename)){
			$sql->simpleQuery('UPDATE `templates` SET `rep`=\'1\' WHERE `name`=\''.$this->_templateName.'\'');
			if($this->copyFile($_FILES['Filedata']['tmp_name'], $this->_path.$this->_templateName.'.temp.php')){
				die('REPLACE/'.$this->_templateName);	
			}
			else{
				die('The file exists already, and the server was unable to temporarily save the file to assess if you wanted to replace. Please try uploading again.');
			}
		}
		else if($this->copyFile($_FILES['Filedata']['tmp_name'], $this->_path.$this->_filename)){					
				$sql->simpleQuery('INSERT INTO `templates` (name, css, rep, date) VALUES (
					\''.$this->_templateName.'\',
					\'0\',
					\'0\',
					\''.time().'\')');
				$sql->disconnect();
				$this->getCSS();
		}
		else{
			die('The file was not successfully saved. Please try uploading again.');
		}
	}
	
	function getCSS(){
		require(ROOT.DS.MAIN.DS.'library'.DS.'simple_html_dom.php');
		$html = file_get_html($this->_path.$this->_filename);
		$stylesheets = $html->find('link[rel=stylesheet]');
		$this->_css = '';
		$first = true;
		foreach($stylesheets as $value){
			$this->_css .= $first ? '' : ',';
			$this->_css .= $value->href;
			$first = false;
		}
		loadIntClass('sql_query');
		$sql = new Sql_query('templates');
		$sql->simpleQuery('UPDATE `templates` SET `css`=\''.$this->_css.'\' WHERE `name`=\''.$this->_templateName.'\'');
		die('SUCCESS/'.$this->_templateName.'/'.time());
	}
	
	function copyFile($copyName,$path){
		if(move_uploaded_file($copyName, $path)){
			return true;
		}
		else{
			return false;
		}
	}
}