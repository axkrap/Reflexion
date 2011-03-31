<?php
require_once(ROOT.DS.MAIN.DS.'library'.DS.'session_check.php');
require_once(ROOT.DS.MAIN.DS.'library'.DS.'session_create.php');
if($this->_postName !=='login' && (session_check() || session_create())) {
	if($this->_postName === '') {
		header("Cache-Control: no-cache");
		require(ROOT.DS.MAIN.DS.'reflex'.DS.'admin'.DS.'documents'.DS.'index.php');
	}
	else if(file_exists(ROOT.DS.MAIN.DS.'reflex'.DS.'admin'.DS.'documents'.DS.$this->_postName.'.php')) {
		header("Cache-Control: no-cache");
		require(ROOT.DS.MAIN.DS.'reflex'.DS.'admin'.DS.'documents'.DS.$this->_postName.'.php');
	}
	else if(file_exists(strtolower(ROOT.DS.MAIN.DS.'reflex'.DS.'admin'.DS.'documents'.DS.$this->_postName.'.php'))) {
		$pageURL = 'http';
		$pageURL .= '://'.THIS_DOMAIN.'/'.ADMIN_URL.'/'.strtolower($this->_postName);
		header ('HTTP/1.1 301 Moved Permanently');
  		header ('Location: '.$pageURL);
		exit;
	}
	else {
		$pageURL = 'http';
		$pageURL .= '://'.THIS_DOMAIN.'/'.ADMIN_URL;
		header ('HTTP/1.1 301 Moved Permanently');
  		header ('Location: '.$pageURL);
		exit;
	}
}
else {
	if($this->_postName === 'login') {
		header("Cache-Control: no-cache");
		require(ROOT.DS.MAIN.DS.'reflex'.DS.'admin'.DS.'documents'.DS.'login.php');
	}
	else if($this->_postName === '') {
		header("Cache-Control: no-cache");
		require(ROOT.DS.MAIN.DS.'reflex'.DS.'admin'.DS.'documents'.DS.'login.php');
	}
	else if(file_exists(ROOT.DS.MAIN.DS.'reflex'.DS.'admin'.DS.'documents'.DS.$this->_postName.'.php')) {
		header("Cache-Control: no-cache");
		require(ROOT.DS.MAIN.DS.'reflex'.DS.'admin'.DS.'documents'.DS.'login.php');
	}
	else if(file_exists(strtolower(ROOT.DS.MAIN.DS.'reflex'.DS.'admin'.DS.'documents'.DS.$this->_postName.'.php'))) {
		$pageURL = 'http';
		$pageURL .= '://'.THIS_DOMAIN.'/'.ADMIN_URL.'/'.strtolower($this->_postName);
		header ('HTTP/1.1 301 Moved Permanently');
  		header ('Location: '.$pageURL);
		exit;
	}
	else {
		$pageURL = 'http://'.THIS_DOMAIN.'/'.ADMIN_URL.'/login';
		header ('HTTP/1.1 301 Moved Permanently');
  		header ('Location: '.$pageURL);
	}
	
}