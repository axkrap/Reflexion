<?php 
$loginAction = ' ';
if($this->_postName === '' || $this->_postName === 'login' || $this->_postName === 'logout') {
	$loginAction = ADMIN_URL;
}
else {
	$loginAction = ADMIN_URL.'/'.$this->_postName;
}?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<base href="<? echo 'http://'.THIS_DOMAIN.'/' ?>" />
<title>Reflexion - Log In</title>
</head>
<body>
<? if(isset($loggedout) && $loggedout) echo'logged out successfully:';?>
<form id="login" method="post" action="<?php echo $loginAction?>">
<label for="username">Username:</label><br />
<input type="text" name="username" /><br />
<label for="password">Password:</label><br />
<input type="password" name="password" /><br />
<input type="submit" value="log in" />
</form>
</body>
</html>