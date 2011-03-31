<?php if($this->_pingbool) echo '<php>header(\'X-Pingback: http://nathansweet.me/_pingback_\');</php>'?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<? if($this->_pingbool) echo '<link rel="pingback" href="http://nathansweet.me/_pingback_" />'; ?>
<title><? echo $this->_title;?></title>
<link rel="stylesheet" href="css/admin/reset.css" />
</head>
<body><? echo $this->_post;?>

<php> echo time();</php>
<button id="some_btn">A Button</button>
<script type="text/javascript">
window.onload = function(){
	
	document.getElementById('some_btn').onclick = function(e){
		alert('whatup');
	}
}
</script>
</body>
</html>