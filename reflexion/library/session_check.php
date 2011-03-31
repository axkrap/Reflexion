<?php
/*
Return a boolean based on whether the admin session is still valid

In session_create one cookie is set with a username. This cookie
is meant to last for a long time. The function checks for this
cookie, and checks if the value of the cookie exists in the user
database. If the value is in the database it checks if the user
is still logged in, if that is true, it checks if the UTC value
of when they last logged in is less than an hour, if that is not
true it invalidates the loggedin boolean and the session, forcing
the user to sign in again.
*/
function session_check() {
	if(array_key_exists('username', $_COOKIE)){
		loadIntClass('sql_query');
		$pName =  hash("sha512",$_COOKIE['username']);
		$sql = new Sql_query('users');
		$thisDB = $sql->selectAll();
		$userexists = false;
		$int = 0;
		for($i = 0; $i<count($thisDB); ++$i){
			if($pName === $thisDB[$i]['User']['user']) $userexists = true;
			$int = $i;
		}
		if($userexists) {
			if(intval($thisDB[$int]['User']['loggedin'])===1) {
				if(intval($thisDB[$int]['User']['logtime'])>time()){
					return true;
				}
				else{
					$sql->simpleQuery("UPDATE `users` SET `loggedin`='0', `logtime`='0'' WHERE `user`='".$pName."'");
					$sql->disconnect();
					return false;
					
				}
			}
			else{
				$sql->simpleQuery("UPDATE `users` SET `logtime`='0' WHERE `user`='".$pName."'");
				$sql->disconnect();
				return false;
				
			}
		}
		else{
			setcookie('username',$_COOKIE['username'], time()-60*60*24);
			return false;	
			
		}
	}
	else{
		return false;
		
	}
}