<?php
require_once(ROOT.DS.MAIN.DS.'library'.DS.'session_check.php');
class Cron_job{
	
	
	function __construct($arr){
		if(session_check()){
			require(ROOT.DS.MAIN.DS.'library'.DS.'cron_jobs.php');
			array_push($cronJobs,$arr);
			$this->write($cronJobs);
		}
		else{
			die('Your session has expired, please log back in.');
		}
	}
	
	function write($arr){
		$str = '<?php $cronJobs = array(';				   
		$first = true;
		for($i = 0; $i <count($arr); ++$i){
			if(!$first) $str .= ',';
			$first = false;
			$str .= 'array(\'time\'=>'.$arr[$i]['time'].',\'command\'=>\''.$arr[$i]['command'].'\',\'parameters\'=>array(';																				 
			$first2 = true;																							
			for($z = 0; $z < count($arr[$i]['parameters']);++$z){
				if(!$first2) $str .=',';
				$first2 = false;
				$str .= '\''.$arr[$i]['parameters'][$z].'\'';
			}
			$str .= '))';
		}
		$str .=');';
		$fileloc = fopen(ROOT.DS.MAIN.DS.'library'.DS.'cron_jobs.php', 'w');
		fwrite($fileloc,$str);
		fclose($fileloc);
	}
}