<?php
function logout(){
	$pName = hash("sha512",$_COOKIE['username']);
	loadIntClass('sql_query');
	$class = new Sql_query('users');
	$userDb = $class->selectWhere('user',$pName);
	$userDb = $userDb['User'];
	if($pName === $userDb['user']) {
		$class->simpleQuery("UPDATE `users` SET `loggedin`='0', `logtime`='0'' WHERE `user`='".$pName."'");
		$class->disconnect();
		return true;
	}
	else{
		return false;
	}
}
if(logout()) {
	$loggedout = true;
	require(ROOT.DS.MAIN.DS.'reflex'.DS.'admin'.DS.'documents'.DS.'login.php');
}
else {
	$loggedout = false;
	require(ROOT.DS.MAIN.DS.'reflex'.DS.'admin'.DS.'documents'.DS.'login.php');
}