<?php
require_once(ROOT.DS.MAIN.DS.'library'.DS.'session_check.php');
class Template_confirm{
	protected $_template;
	protected $_replace;
	protected $_path;
	protected $_css;
	function __construct(){
		if(session_check()){
			if(array_key_exists('replace',$_POST) && array_key_exists('template', $_POST)){
				$this->_template = $_POST['template'];
				$this->_replace = $_POST['replace'] === 'true' ? true : false;
				$this->_path = ROOT.DS.MAIN.DS.'reflex'.DS.'templates'.DS;
				$this->db_check();
			}
			else{
				die('The browser failed to appropriately communicated with the server. Try reloading the page');
			}
		}
		else{
			die('Your session has expired. Please <a href="'.ADMIN_URL.'/logout" target="_self">login</a> again in order to replace your template.');
		}
	}
	function db_check(){
		loadIntClass('sql_query');
		$sql = new Sql_query('templates');
		$sql->simpleQuery('SELECT * FROM `templates` WHERE `name`=\''.$this->_template.'\' AND `rep`=\'1\'');
		$num = $sql->getNumRows();
		$sql->freeResult();
		if($num === 0){
			die('The server was unable to find any templates approved for replacement');
		}
		else if($this->_replace){
			$sql1 = new Sql_query('templates');
			$sql1->simpleQuery('UPDATE `templates` SET `rep`=\'0\' WHERE `name`=\''.$this->_template.'\'');
			$this->replace_template();
		}
		else{
			$sql2 = new Sql_query('templates');
			$sql2->simpleQuery('UPDATE `templates` SET `rep`=\'0\' WHERE `name`=\''.$this->_template.'\'');
			unlink($this->_path.$this->_template.'.temp.php');
			die('NOTHING');
		}
	}
	
	function replace_template(){
		if(file_exists($this->_path.$this->_template.'.php') && file_exists($this->_path.$this->_template.'.temp.php')){
			if(unlink($this->_path.$this->_template.'.php')){
				if(copy($this->_path.$this->_template.'.temp.php',$this->_path.$this->_template.'.php')){
					unlink($this->_path.$this->_template.'.temp.php');
					$this->getCSS();
				}
				else{
					die('The server was able to delete the old template, but was unable to add the new one. You can fix this manually, consult the documentation.');
				}
			}
			else{
				die('The server was unable to delete the old template. Please consult the documentation for this error.');
			}
		}
		else{
			die('The server was unable to find a template with which to replace the approved template to replace');
		}
	}
	
	function getCSS(){
		require(ROOT.DS.MAIN.DS.'library'.DS.'simple_html_dom.php');
		$html = file_get_html($this->_path.$this->_template.'.php');
		$stylesheets = $html->find('link[rel=stylesheet]');
		$this->_css = '';
		$first = true;
		foreach($stylesheets as $value){
			$this->_css .= $first ? '' : ',';
			$this->_css .= $value->href;
			$first = false;
		}
		$sql = new Sql_query('tempaltes');
		$sql->simpleQuery('UPDATE `templates` SET `css`=\''.$this->_css.'\', `date`=\''.time().'\' WHERE `name`=\''.$this->_template.'\'');
		die('SUCCESS/'.$this->_template.'/'.time());
	}
	
}