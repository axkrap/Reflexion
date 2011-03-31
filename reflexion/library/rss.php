<?php
require(ROOT.DS.MAIN.DS.'config'.DS.'rss.php');
class Rss_feed{
	function __construct(){
		if(RSS_REDIRECT && !preg_match(AGENT_REGEXP,$_SERVER['HTTP_USER_AGENT'])){
			header ('HTTP/1.1 302 Found');
  			header ('Location: '.RSS_WHERE);
		}
		else{
			$this->writeRSS();
		}
	}
	
	function writeRSS(){
		$rsshead = '<?xml version="1.0" encoding="UTF-8" ?>
					<rss version="2.0">
					<channel>
					<title>'.RSS_TITLE.'</title>
					<description>'.RSS_DESCRIPTION.'</description>
					<language>en-us</language>
					<copyright>Copyright (C) '.date('Y').' '.THIS_DOMAIN.'</copyright>
					<link>http://'.THIS_DOMAIN.'/'.RSS_URI.'</link>
					<lastBuildDate>'.date('D, d M Y H:i:s T').'</lastBuildDate>';
		loadIntClass('sql_query');
		$sql = new Sql_query('posts');
		$postArr = $sql->query('SELECT * FROM `posts` WHERE `publish`=\'1\'');
		$count = count($postArr);
		if($count === 0){
			die('There are no posts');
		}
		reset($postArr);
		$latest =$postArr[0]['Post']['publishdate'];
		$rssbody='';
		for($i = 0; $i < $count;++$i){
			$p = $postArr[$i]['Post'];
			if($p['slug'] === '_index_') $p['slug'] ='';
			$link = 'http://'.THIS_DOMAIN.'/'.($p['category'] !=='none'?$p['category'].'/':'').$p['slug'];
			$rssbody .= '
			<item>
			<title>'.$p['title'].'</title>
			<description>'.$p['description'].'</description>
			<link>'.$link.'</link>
			<guid isPermaLink="true">'.$link.'</guid>
			<pubDate>'.date('D, d M Y H:i:s T',intval($p['publishdate'])).'</pubDate>
			</item>
			';
			if(intval($latest)<intval($p['publishdate'])){
				$latest = $p['publishdate'];
			}
		}
		$rsshead.='
		<pubDate>'.date('D, d M Y H:i:s T',intval($latest)).'</pubDate>';
		$rssbody.='</channel>
			</rss>';
		header("Content-Type: application/rss+xml");
		echo $rsshead,$rssbody;
		exit;
	}
}