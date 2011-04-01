<?php
define('DS', '/');
define('ROOT', dirname(dirname(dirname(__FILE__))));
define('MAIN', 'reflexion');
define('CRON_JOB',true);
error_reporting(E_ALL);
ini_set('display_errors', 'Off');
ini_set('log_errors','On');
ini_set('error_log', ROOT.DS.MAIN.DS.'tmp'.DS.'logs'.DS.'error.log');

function loadIntClass($className){
	if(file_exists(ROOT.DS.MAIN.DS.'classes'.DS.'internals'.DS.$className.'.internal.php')){
		require_once(ROOT.DS.MAIN.DS.'classes'.DS.'internals'.DS.$className.'.internal.php');
	}
}
require(ROOT.DS.MAIN.DS.'library'.DS.'cron_jobs.php');
function checkSet(){
	global $cronJobs;
	$count = count($cronJobs);
	if($count === 0) return;
	for($i = 0; $i < $count; ++$i){
		$c = $cronJobs[$i];
		if(time()>$c['time']){
			$params = '';
			$first = true;
			for($t = 0; $t < count($c['parameters']); ++$t){
				if(!$first) $params .= ',';
				$first = false;
				$params .=	'\''.$c['parameters'][$t].'\'';
			}
			loadIntClass($c['command']);
			eval('new '.ucwords($c['command']).'('.$params.');');
			unset($cronJobs[$i]);
		}
	}
}
	
function setCron(){
	global $cronJobs;
	$string = '<?php $cronJobs = array(';
	$first2 = true;
	while(list($k,$v) = each($cronJobs)){
		if(!$first2) $string .= ',';
		$first2 = false;
		$string .= 'array(\'time\'=>'.$v['time'].',\'command\'=>\''.$v['command'].'\',\'parameters\'=>array(';
		$first3 = true;																							
		for($z = 0; $z < count($v['parameters']);++$i){
			if(!$first3) $string .=',';
			$first3 = false;
			$string .= '\''.$v['parameters'][$z].'\'';
		}
		$string .= '))';
	}
	$string .=');';
	$fileloc = fopen(ROOT.DS.MAIN.DS.'library'.DS.'cron_jobs.php', 'w');
	fwrite($fileloc,$string);
	fclose($fileloc);
}
checkSet();
setCron();
exit;