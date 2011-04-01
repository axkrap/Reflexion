<?php
header("Content-Type: application/xml");
require_once(ROOT.DS.MAIN.DS.'library'.DS.'simple_html_dom.php');
require_once(ROOT.DS.MAIN.DS.'library'.DS.'xmlrpc'.DS.'xmlrpc.inc');
require_once(ROOT.DS.MAIN.DS.'library'.DS.'xmlrpc'.DS.'xmlrpcs.inc');
loadIntClass('sql_query');
class PingbackServer{
	
	protected $_sourceURL;
	protected $_destinationURL;
	protected $_response;
	protected $_responseMessage;
	function __construct(){
		$pingFunction = array( "pingback.ping" => array( "function" => "PingbackServer::serverParser" ));
		$server  = new xmlrpc_server($pingFunction);
		$server->setdebug(3);
		$server->service();
	}
	
	function serverParser($rpcMessage){
		$param1 = $rpcMessage->getParam(0);
        $param2 = $rpcMessage->getParam(1);
        $this->_sourceURL = $param1->scalarval(); # their article
        $this->_destinationURL = $param1->scalarval(); # your article
		$parseDestination = parse_url($this->_destinationURL);
		if($parseDestination['host'] !== THIS_DOMAIN && 'www.'.$parseDestination['host'] !== THIS_DOMAIN && $parseDestination['host'] !== 'www.'.THIS_DOMAIN){
			$this->_response = 32;
			$this->_resonseMessage = 'Not only does the URI not exist, the domain is incorrect.';
			return $this->pingServer();
		}
		else if(!file_exists(ROOT.DS.MAIN.DS.'reflex'.DS.'documents'.DS.str_replace('/',DS,strtolower($parseDestination['path']))).'.php'){
			$this->_response = 32;
			$this->_responseMessage = 'The URI does not exist';
			return $this->pingServer();
		}
		else{
			$this->checkURI($parseDestination['path']);
		}
	}
	
	function checkURI($uri){
		$slug = explode('/',$uri);
		$slug = $slug[count($slug)-1];
		$sql = new Sql_query('posts');
		$postArr = $sql->query('SELECT * FROM `posts` WHERE `slug`=\''.$slug.'\'');
		if(intval($postArr[0]['pingbool']) === 0){
			$this->_resonse = 33;
			$this->_responseMessage = 'The URI does not accept pingbacks';
			return $this->pingServer();
		}
		else{
			$string = file_get_contents($this->_sourceURL)
			if($html !== false){
				$html = new simple_html_dom();
				$html->load($string);
				$anchors = $html->find('a[href]');
				$exists = false;
				$path = '';
				for($i = 0; $i < count($anchors); ++$i){
					$loc = parse_url($anchors[$i]->href);
					if(strtolower($loc['path']) === strtolower($uri)){
						$exists = true;
						$path = strtolower($loc['path']);
						break;
					}
				}
				if($exists){
					$pingArr = $sql->query('SELECT * FROM `pingbacks` WHERE `slug`=\''.$slug.'\'');
					$already = false;
					for($i = 0; $i < count($pingArr); ++$i){
						$pURL = parse_url($pingArr[$i]['url']);
						if(strtolower($pURL['path']) === $path){
							$this->_response = 48;
							$this->_responseMessage = 'This pingback has already been registered';
							return $this->pingServer();
						}
					}
					if(!$already){
						$this->_response = 'SUCCESS';
						$this->_responseMessage = 'Pingback successfully registered';
						$this->savePing($slug);
						return $this->pingServer();
					}
				}
				else{
					$this->_response = 17;
					$this->_responseMessage = 'The source URI does not appear to contain the link.';
					return $this->pingServer();
				}
			}
			else{
				$this->_response = 16;
				$this->_responseMessage = 'The source URI does not exist';
				return $this->pingServer();
			}
		}
	}
	function savePing($slug){
		$sql3 = new Sql_query('pingbacks');
		$sql3->query('INSERT INTO `pingbacks` (slug, url, path, type) VALUES (
			\''.$slug.'\',
			\''.$this->_sourceURL.'\',
			\''.$this->_destinationURL.'\',
			\'inbound\'
		)');
		$sql3->query('UPDATE `posts` SET `pings`=(1 + `pings`) WHERE `slug`=\''.$slug.'\'');
	}
	function pingServer(){
		if($this->_response === 'SUCCESS'){
			return new xmlrpcresp(new xmlrpcval($this->_responseMessage, "string"));
		}
		else{
			return new xmlrpcresp(0, $this->_response, $this->_responseMessage);	
		}
	}
}