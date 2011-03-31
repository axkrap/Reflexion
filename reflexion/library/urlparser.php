<?php
/*
This is the initialization for the entire CMS

1. setReporting allows all php errors to be printed at
runtime and logged. This ABSOLUTELY needs to be turned
off when you are ready to launch your website.

2. stripSlashesDeep takes away all forward slashes from any
posted, getted, and cookied vaules, as php can process the
esoteric string without them. This does create a problem
though, as there are some items that are posted that have
forward slashes, such as html files with javascript in them,
which require their forward slashes for their own purposes.

3. unregisterGlobals unsets all values from the GLOBAL array

4. callHook decomposes the url, and passes it to the appropriate
venue for processing, either as its own class or to the controller.
*
/** Set TimeZone**/
date_default_timezone_set(TIME_ZONE);
/** Toggles options for Developer Mode **/

function setReporting() {
	if(DEVELOPMENT_ENVIRONMENT) {	
		error_reporting(E_ALL);
		ini_set('display_errors','On');
	} 
	else {
		error_reporting(E_ALL);
		ini_set('display_errors', 'Off');
		ini_set('log_errors','On');
		ini_set('error_log', ROOT.DS.MAIN.DS.'tmp'.DS.'logs'.DS.'error.log');
	}
}

/** Check for Magic quotes and remove them **/
function stripSlashesDeep($value) {
	$value= is_array($value) ? array_map('stripSlashesDeep',$value) : stripslashes($value);
	return $value;
}

function removeMagicQuotes() {
	if(get_magic_quotes_gpc()) {
		$_GET = stripSlashesDeep($_GET);
		$_POST = stripSlashesDeep($_POST);
		$_COOKIE = stripSlashesDeep($_COOKIE);
	}
}

/** Check register for globals and remove them **/
function unregisterGlobals() {
	
	if(ini_get('register_globals')) {
		$array = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
		foreach($array as $value){
			
			foreach($GLOBALS[$value] as $key=>$var) {
				if($var === $GLOBALS[$key]) {
					unset($GLOBALS[$key]);
				}
			}
		}
	}
}

/** Autoload any classes that are required **/
function __autoload($className) {
	if(file_exists(ROOT.DS.MAIN.DS.'classes'.DS.strtolower($className).'.php')) {
		require_once(ROOT.DS.MAIN.DS.'classes'.DS.strtolower($className).'.php');
	}
}
/** Allow for internal class calls that are sensitive. Must be called and invoked maually.**/
function loadIntClass($className){
	if(file_exists(ROOT.DS.MAIN.DS.'classes'.DS.'internals'.DS.$className.'.internal.php')){
		require_once(ROOT.DS.MAIN.DS.'classes'.DS.'internals'.DS.$className.'.internal.php');
	}
}

/** Main Call and URL Decomposer Function **/
function callHook(){
	global $uri;
	if(preg_match('/^$/', $uri)) {
		$postName = '_index_';
	}
	else if(preg_match('/^'.ADMIN_URL.'$/',$uri)) {
		$uriArray = explode('/',$uri);
		$category = $uriArray[0];
		array_shift($uriArray);
		$postName = '';
	}
	else if(preg_match('/^'.ADMIN_URL.'\/[[:alnum:]]+-*[[:alnum:]-]*$/',$uri)) {
		$uriArray = explode('/',$uri);
		$category = $uriArray[0];
		array_shift($uriArray);
		$postName = $uriArray[0];
	}
	else if(preg_match('/^'.ACTION_VAR.'\_[[:alnum:]_]+_*[[:alnum:]_]*$/',$uri)) {
		$urlArray = explode('_',$uri,2);
		$actionName = $uriArray[0];
		array_shift($uriArray);
		$postName = $uriArray[0];
	}
	else if(preg_match('/^'.INTERNAL_ACTION.'\_[[:alnum:]_]+_*[[:alnum:]_]*$/',$uri)) {
		$uriArray = explode('_',$uri,2);
		$actionName = $uriArray[0];
		array_shift($uriArray);
		$postName = $uriArray[0];
	}
	else if(preg_match('/^'.PINGBACK.'$/', $uri) || preg_match('/^'.PINGBACK.'$/', strtolower($uri))) {
		$postName = '_pingback_';
		$pingUp = false;
		if(!preg_match('/^'.PINGBACK.'$/', $uri)) $pingUp = true;
	}
	else if(preg_match('/^'.RSS_URI.'$/', $uri) || preg_match('/^'.RSS_URI.'$/', strtolower($uri))){
		$postName = '_rssfeed_';
		$rssUp = false;
		if(!preg_match('/^'.RSS_URI.'$/', $uri)) $rssUp = true;
	}
	else if(preg_match('/^[[:alnum:]]+-*[[:alnum:]-]*$/', $uri)) {
		$uriArray = explode('/',$uri);
		$postName = $uriArray[0];
		$category = '';
	}
	/*else if(URL_STATE === 'day-name' && preg_match('/^[0-9]{4}\/{1}[0-9]{2}\/{1}[0-9]{2}\/[[:alnum:]]+-*[[:alnum:]-]*$/', $uri)) {	
		$uriArray = explode('/', $uri);
		$category = $uriArray[0].'_';
		array_shift($uriArray);
		$category .= $uriArray[0].'_';
		array_shift($uriArray);
		$category .= $uriArray[0];
		array_shift($uriArray);
		$postName = $uriArray[0];
	}
	else if(URL_STATE === 'month-name' && preg_match('/^[0-9]{4}\/{1}[0-9]{2}\/[[:alnum:]]+-*[[:alnum:]-]*$/', $uri)) {
		$uriArray = explode('/', $uri);
		$category = $uriArray[0].'_';
		array_shift($uriArray);
		$category .= $uriArray[0];
		array_shift($uriArray);
		$postName = $uriArray[0];
	}*/
	else if(URL_STATE === 'category-name' && preg_match('/^[[:alnum:]]+-*[[:alnum:]-]*\/[[:alnum:]]+-*[[:alnum:]-]*$/', $uri)) {
		$uriArray = explode('/', $uri);
		$category = $uriArray[0];
		array_shift($uriArray);
		$postName = $uriArray[0];
	}
	else {
		$postName = 'GO_TO_404_ERROR_PAGE';
	}
	
	if($postName === '_pingback_') {
		if($pingUp) {
			$pageURL = 'http';
			$pageURL .= '://'.THIS_DOMAIN.'/'.strtolower($uri);
			header ('HTTP/1.1 301 Moved Permanently');
  			header ('Location: '.$pageURL);
			exit;
		}
		else{
			require(ROOT.DS.MAIN.DS.'library'.DS.'pingbackserver.php');
			new PingbackServer();
		}
	}
	else if($postName === '_rssfeed_') {
		if($rssUp) {
			$pageURL = 'http';
			$pageURL .= '://'.THIS_DOMAIN.'/'.strtolower($uri);
			header ('HTTP/1.1 301 Moved Permanently');
  			header ('Location: '.$pageURL);
			exit;
		}
		else{
			require(ROOT.DS.MAIN.DS.'library'.DS.'rss.php');
			new Rss_feed();
		}
	}
	else if(isset($actionName) && $actionName == INTERNAL_ACTION) {
		loadIntClass($postName);
		$postName = ucwords($postName);
		new $postName;
	}
	else if (isset($actionName) && $actionName == ACTION_VAR)  {
		$postName = ucwords($postName);
    	new $postName;
	}
	else if(isset($category) && isset($postName)) {
		new Controller($postName, $category); 
	}
	else {
		new Controller($postName, false); 
	}
}

setReporting();
removeMagicQuotes();
unregisterGlobals();
callHook();