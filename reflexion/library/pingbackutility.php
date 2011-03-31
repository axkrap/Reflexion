<?
/*
Send an XMLRPC request to the website of origin for the url.

*/
require_once(ROOT.DS.MAIN.DS.'library'.DS.'xmlrpc'.DS.'xmlrpc.php');
require_once(ROOT.DS.MAIN.DS.'library'.DS.'xmlrpc'.DS.'xmlrpcs.php');
require_once(ROOT.DS.MAIN.DS.'library'.DS.'simple_html_dom.php');
class PingbackUtility{
  const URL = '~^(https?|ftps?)://(([a-z0-9-]+\.)+[a-z]{2,6}|\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})(:[0-9]+)?(/?|/\S+)$~ix';
  const PINGBACK_LINK = '<link rel="pingback" href="([^"]+)" ?/?>';
  
  public static function isURL($url){
    return preg_match(self::URL, $url) ? true : false;
  }
  
  public static function isPingbackEnabled($url){
    return self::getPingbackURL($url) ? true : false;
  }
  
  public static function getPingbackURL($url){
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, true);
    curl_setopt($curl, CURLOPT_NOBODY, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $headers = self::parse_headers(curl_exec($curl));
    curl_close($curl);
    
    if(isset($headers['X-Pingback'])){
      return $headers['X-Pingback'];
    }
    
    $response = file_get_contents($url);
    
    return preg_match(self::PINGBACK_LINK, $response, $match) ? $match[1] : false;
  }
  
  public static function isBacklinking($url, $to){
    $doc=file_get_html($url);
	$arr = $doc->find('a');
    foreach($arr as $link){
      if($link->href == $to){
        return true;
      }
    }
    return false;
  }
  
  public static function sendPingback($from, $to, $server){
    $request = xmlrpc_encode_request('pingback.ping', array($from,  $to));
    $curl = curl_init($server);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
  }
  
  private static function parse_headers($header){
    $retVal = array();
    $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header));
    foreach( $fields as $field ) {
        if( preg_match('/([^:]+): (.+)/m', $field, $match) ) {
            $match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match[1])));
            if( isset($retVal[$match[1]]) ) {
                $retVal[$match[1]] = array($retVal[$match[1]], $match[2]);
            } else {
                $retVal[$match[1]] = trim($match[2]);
            }
        }
    }
    return $retVal;
  }
}