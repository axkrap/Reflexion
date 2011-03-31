<?php 
/*
Checks if a user has input a correct username and password.

The function checks if the posted values username and password
were actually posted. If they were it checks if they line up
correctly with the user database. If they do it sets a cookie,
called 'username', with the inputs user name as a value, it 
sets the 'loggedin' boolean and 'logtime' integer (both of which
the session_check function checks) to their appropriate values.
*/
function session_create(){
	if(array_key_exists('username', $_POST) && array_key_exists('password', $_POST)){
		$pName = hash("sha512",$_POST['username']);
		$pPass = hash("sha512",$_POST['password']);
		loadIntClass('sql_query');
		$sql = new Sql_query('users');
		$thisDB = $sql->selectAll();
		$userexists = false;
		$int = 0;
		for($i = 0; $i<count($thisDB); ++$i){
			if($pName === $thisDB[$i]['User']['user']) $userexists = true;
			$int = $i;
		}
		if($userexists){
			if($thisDB[$int]['User']['password'] === $pPass){
				if(!array_key_exists('username', $_COOKIE)){
					//One year
					setcookie('username',$_POST['username'],time()+60*60*24*365,'/');
				}
				$time = time()+60*60;
				$sql->simpleQuery("UPDATE `users` SET `loggedin`='1', `logtime`='".$time."' WHERE `user`='".$pName."'");
				$sql->disconnect();
				return true;
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}
	else{
		return false;	
	}
}