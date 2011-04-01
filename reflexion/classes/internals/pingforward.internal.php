<?php
/*
Send an XMLRPC request to the website of origin for the url.

*/
require_once(ROOT.DS.MAIN.DS.'library'.DS.'session_check.php');
require_once(ROOT.DS.MAIN.DS.'library'.DS.'xmlrpc'.DS.'xmlrpc.inc');
require_once(ROOT.DS.MAIN.DS.'library'.DS.'simple_html_dom.php');
loadIntClass('Sql_query')
class Pingforward{
	protected $_pingURL;
	protected $_slug;
	protected $_url;
	function __construct($url,$slug){
		if((session_check() || CRON_JOB)){
			$file = file_get_contents($url);
			$this->_url = $url;
			$this->_slug = $slug;
			if($file !== false){
				$html = new simple_html_dom();
				$html->load_file($file);
				$pingEl = $html->find('link[rel=pingback]',0);
				if($pingEl !== null){
					$this->_pingURL = $pingEl->href;
					$this->ping();
				}
				else{
					$this->checkHeaders();
				}
			}
			else{
				$this->erase();
			}
		}
	}
	function erase(){
		$sql = new Sql_query('posts');
		$sql->query('UPDATE `posts` SET `pingbacks`=REPLACE(`pingbacks`,\''.$this->_url.'\',\'\') WHERE `slug`=\''.$this->_slug.'\'');
	}
	function checkHeaders(){
		$urlParse = parse_url($this->_url);

        if(!isset($urlParse['scheme'])) {
			$this->erase();
			return;
		}
        if($parts['scheme'] != 'http'){
			$this->erase();
			return;
		}
        if(!isset($parts['host'])){
			$this->erase();
			return;
		}
        $host = $urlParse['host'];
        if (isset($urlParse['port'])) $port = $urlParse['port'];
        $path = "/";
        if (isset($urlParse['path'])) $path = $urlParse['path'];
        if (isset($urlParse['query'])) $path .="?".$urlParse['query'];
        if (isset($urlParse['fragment'])) $path .="#".$urlParse['fragment'];
		$file = fsockopen($host, 80);
		if($file === false){
			$this->erase();
			return;
		}
		fwrite($file, 'GET '.$path.' HTTP/1.0\r\nHost: '.$host.'\r\n\r\n');
		$response = '';
		while(is_resource($file) && $file && (!feof($file))){
                $response .= fread($file, 1024);
		}
		fclose($file);
		$found = false;
		$headers = explode("\r\n", $response);
		foreach($headers as $header){
        	if(ereg("X-Pingback: ", $header)){
				$found = true;
				list($pburl) = sscanf($line, "X-Pingback: %s");
				$this->_pingURL = $pburl;
				$this->ping();
				return;
			}
        }
		if(!$found){
			$this->erase();
		}
	}
	
	function ping(){
		if(preg_match('/^http:///',$this->_pingURL)){
			$parsePing = parse_url($this->_pingURL);
        	if(!isset($parsePing['scheme'])){
                $this->erase();
				return;
			}
        	if($parsePing['scheme'] != 'http'){
                $this->erase();
				return;
			}
       		 if (!isset($parsePing['host'])) {
                $this->erase();
				return;
			}
       		$host = $parsePing['host'];
        	if (isset($parsePing['port'])) $port = $parsePing['port'];
        	$path = "/";
        	if (isset($parsePing['path'])) $path = $parsePing['path'];
        	if (isset($parsePing['query'])) $path .="?".$parsePing['query'];
        	if (isset($parsePing['fragment'])) $path .="#".$parsePing['fragment'];
			
			$sql = new Sql_query('posts');
			$postArr = $sql->query('SELECT * FROM `posts` WHERE `slug`=\''.$this->_slug.'\'');
			$category = $postArr[0]['category'] === 'none' ? '' : $postArr[0]['category'].'/';
			$domain = 'http://'.THIS_DOMAIN.'/'.$category.$this->_slug;
			
			$rpcMessage = new xmlrpcmsg("pingback.ping", array(new xmlrpcval($domain, "string"), new xmlrpcval($this->_url, "string")));
			
			$pingServer = new xmlrpc_client($path, $host, 80);
        	$pingServer->setRequestCompression(null);
        	$pingServer->setAcceptedCompression(null);
			$response = $pingServer->send($rpcMessage);
			if (!$response->faultCode()){
				$sql->query('INSERT INTO `pingbacks` (slug, url, path, type) VALUES (
					\''.$this->_slug.'\',
					\''.$this->_url.'\',
					\''.$domain.'\',
					\'outbound\'
				)');
				return;
			}
			else{
				$this->erase();
				return;
			}
		}
		else{
			$this->erase();
		}
	}
}