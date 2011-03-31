<?
/*
Send an XMLRPC request to the website of origin for the url.

*/
require_once(ROOT.DS.MAIN.DS.'library'.DS.'session_check.php');
require_once(ROOT.DS.MAIN.DS.'library'.DS.'pingbackutility');
class Pingforward{
	function __construct($url,$slug){
		if(session_check() || CRON_JOB){
			if(PingbackUtility::isPingbackEnabled($url)){
				$resp = PingbackUtility::sendPingback($slug,$url,PingbackUtility::getPingbackURL($url));
				loadIntClass('sql_query');
				$sql = new Sql_query('posts');
				$post = $sql->selectWhere('slug',$slug);
				$string = $post[0]['Post']['pingbacks'].'<li>"'.$url.'" has been pinged('.$resp.').</li>';
				$sql->query('UPDATE `posts` SET `pingbacks`=\''..'\' WHERE `slug`=\''.$slug.'\'');
			}
			else{
				loadIntClass('sql_query');
				$sql = new Sql_query('posts');
				$post = $sql->selectWhere('slug',$slug);
				$string = $post[0]['Post']['pingbacks'].'<li>"'.$url.'" does not accept pingbacks.</li>';
				$sql->query('UPDATE `posts` SET `pingbacks`=\''..'\' WHERE `slug`=\''.$slug.'\'');
				
			}
		}
		else{
			die('Your session has expired. Please <a href="'.ADMIN_URL.'/logout" target="_self">login</a> again.');
		}
	}
}