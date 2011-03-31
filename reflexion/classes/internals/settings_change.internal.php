<?php
require_once(ROOT.DS.MAIN.DS.'library'.DS.'session_check.php');
class Settings_change{
	protected $_action;
	protected $_dir;
	protected $_file;
	protected $_string;
	function __construct(){
		if(session_check()){
			if(array_key_exists('action',$_POST)){
        			$this->_dir = ROOT.DS.MAIN.DS.'config'.DS;
					$this->_string = '<?php';
					$this->$_POST['action']();
			}
			else{
				die('The browser failed to send the right information. It is possible that you have a bad internet connection.');	
			}
		}
		else{
			die('Your session has expired.');	
		}
	}
	function master(){
		if(!array_key_exists('development_mode', $_POST) || !array_key_exists('admin_url', $_POST) || !array_key_exists('external_url', $_POST) || !array_key_exists('internal_url', $_POST) || !array_key_exists('ping_url', $_POST) || !array_key_exists('rss_url', $_POST) || !array_key_exists('timezone',$_POST)){
			die('The browser failed to send the right information. It is possible that you have a bad internet connection.');
		}
		$this->_file = 'config.php';
		$this->_string .= ' $url = parse_url(\'http://\'.THIS_DOMAIN.$_SERVER[\'REQUEST_URI\']); ';
		$this->_string .=' $uri = substr($url[\'path\'], 1); ';
		$this->_string .= ' define(\'DEVELOPMENT_ENVIRONMENT\','.$_POST['development_mode'].'); ';
		$arr = array();
		$arr['THIS_DOMAIN'] = THIS_DOMAIN;
		$arr['ADMIN_URL'] = $_POST['admin_url'];
		$arr['ACTION_VAR'] = $_POST['external_url'];
		$arr['INTERNAL_ACTION'] = $_POST['internal_url'];
		$arr['PINGBACK'] = $_POST['ping_url'];
		$arr['RSS_URI'] = $_POST['rss_url'];
		$arr['URL_STATE'] = URL_STATE;
		$arr['REFLEX_VERSION'] = REFLEX_VERSION;
		$arr['TIME_ZONE'] = $_POST['timezone'];
		while(list($k,$v) = each($arr)){
			$this->_string .= ' define(\''.$k.'\',\''.$v.'\'); ';
		}
		$this->publish();
		die('SUCCESS');
	}
	function posts(){
		if(!array_key_exists('sitetag',$_POST) || !array_key_exists('default_temp',$_POST) || !array_key_exists('default_cat',$_POST) || !array_key_exists('default_aut',$_POST)){
			die('The browser failed to send the right information. It is possible that you have a bad internet connection.');
		}
		$this->_file = 'posts.php';
		require(ROOT.DS.MAIN.DS.'config'.DS.'posts.php');
       	$arr = array();
       	$arr['SITE_TAG'] = $this->stringQ($_POST['sitetag']);
       	$arr['DEFAULT_TEMPLATE'] = $this->stringQ($_POST['default_temp']);
       	$arr['DEFAULT_CATEGORY'] = $this->stringQ($_POST['default_cat']);
       	$arr['DEFAULT_AUTHOR'] = $this->stringQ($_POST['default_aut']);
       	$arr['SEARCHABLE'] = SEARCHABLE;
		while(list($k,$v) = each($arr)){
			$this->_string .= ' define(\''.$k.'\',\''.$v.'\'); ';
		}
		$this->publish();
		die('SUCCESS');
		
	}
	function comments(){
		if(!array_key_exists('email',$_POST) || !array_key_exists('email_any',$_POST) || !array_key_exists('email_held',$_POST) || !array_key_exists('comm_admin',$_POST) || !array_key_exists('comm_author',$_POST) || !array_key_exists('links',$_POST) || !array_key_exists('moderate_words',$_POST) || !array_key_exists('spam_words',$_POST)){
			die('The browser failed to send the right information. It is possible that you have a bad internet connection.');
		}
		$this->_file = 'comments.php';
        $arr = array();
        $arr['COMMENT_EMAIL'] = '\''.$this->stringQ($_POST['email']).'\'';
      	$arr['COMMENT_EMAIL_ANY'] =  $_POST['email_any'];
        $arr['COMMENT_EMAIL_MOD'] = $_POST['email_held'];
        $arr['COMMENT_APPROV_ADMIN'] = $_POST['comm_admin'];
        $arr['COMMENT_APPROV_AUTHOR'] = $_POST['comm_author'];
        $arr['COMMENT_APPROV_LINKS'] = $_POST['links'];
        $moderate = explode(',',$this->stringQ($_POST['moderate_words']));
        $spam = explode(',',$this->stringQ($_POST['spam_words']));
		while(list($k,$v) = each($arr)){
			$this->_string .= ' define(\''.$k.'\','.$v.'); ';
		}
		$this->_string .= ' $moderate = array(';
		$b1 = true;
		for($i = 0; $i < count($moderate); ++$i){
			if(!$b1) $this->_string .= ', ';
			$b1 = false;
			$this->_string .= '\''.trim($moderate[$i]).'\'';
		}
		$this->_string .= '); $spam = array(';
		$b2 = true;
		for($i = 0; $i < count($spam); ++$i){
			if(!$b2) $this->_string .=', ';
			$b2 = false;
			$this->_string .= '\''.trim($spam[$i]).'\'';
		}
		$this->_string .= ');';
		$this->publish();
		die('SUCCESS');
	}
	function rss(){
		if(!array_key_exists('rss_direct',$_POST) || !array_key_exists('rss_where',$_POST) || !array_key_exists('rss_regexp',$_POST) || !array_key_exists('rss_title',$_POST) || !array_key_exists('rss_description',$_POST)){
			die('The browser failed to send the right information. It is possible that you have a bad internet connection.');
		}
		$this->_file = 'rss.php';
		$arr = array();
		$arr['RSS_REDIRECT'] = $_POST['rss_direct'];
		$arr['RSS_WHERE'] = '\''.$this->stringQ($_POST['rss_where']).'\'';
		$arr['AGENT_REGEXP'] = '\''.$_POST['rss_regexp'].'\'';
		$arr['RSS_TITLE'] = '\''.$this->stringQ($_POST['rss_title']).'\'';
		$arr['RSS_DESCRIPTION'] = '\''.$this->stringQ($_POST['rss_description']).'\'';
		while(list($k,$v) = each($arr)){
			$this->_string .= ' define(\''.$k.'\','.$v.'); ';
		}
		$this->publish();
		die('SUCCESS');
	}
	function privacy(){
		$this->_file = 'posts.php';
		require(ROOT.DS.MAIN.DS.'config'.DS.'posts.php');
		$arr = array();
       	$arr['SITE_TAG'] = $this->stringQ(SITE_TAG);
       	$arr['DEFAULT_TEMPLATE'] = $this->stringQ(DEFAULT_TEMPLATE);
       	$arr['DEFAULT_CATEGORY'] = $this->stringQ(DEFAULT_CATEGORY);
       	$arr['DEFAULT_AUTHOR'] = $this->stringQ(DEFAULT_AUTHOR);
       	$arr['SEARCHABLE'] = 'true';
		while(list($k,$v) = each($arr)){
			$this->_string .= ' define(\''.$k.'\',\''.$v.'\'); ';
		}
		$fileloc = fopen(ROOT.DS.'robots.txt', 'w');
		fwrite($fileloc,'User-agent: *');
		fclose($fileloc);
		$this->publish();
		die('SUCCESS');
	}
	function stringQ($string){
		$temp = str_replace('\'','\\\'',$string);
		return $temp;
	}
	function publish(){
		$fileloc = fopen($this->_dir.$this->_file, 'w');
		fwrite($fileloc,$this->_string);
		fclose($fileloc);
	}
}