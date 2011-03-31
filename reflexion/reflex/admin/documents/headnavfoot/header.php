<?php
$postKey = $this->_postName === '' ? 'index' : $this->_postName;
$docArray = array(
			'index' => array('title' => 'Create a New Post', 'css' => 'newpost'),
			'posts' => array('title'=>'Browse Your Posts','css'=>'posts'),
			'templates' => array('title'=>'Browse your Templates','css'=>'templates'),
			'upload-files' => array('title'=>'Upload/Browse Your Files','css'=>'media'),
			'comments' => array('title'=>'Browse Your Comments','css'=>'comments'),
			'settings' => array('title'=>'Adjust Your Settings','css'=>'settings'),
			);
loadIntClass('sql_query');
$header_posts = new Sql_query('posts');
$unpublished_i = $header_posts->numRows('publish','0');
$comments = new Sql_query('comments');
$unapproved_i = $comments->numRows('approved','pending');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Reflexion - <? echo $docArray[$postKey]['title']?></title>
<base href="<? echo 'http://'.THIS_DOMAIN.'/';?>" />
<link rel="stylesheet" href="css/admin/reset.css" />
<link rel="stylesheet" href="css/admin/<? echo $docArray[$postKey]['css']?>.css" />
</head>
<body>
    <div id="header">
    	<div id="logo"></div>
         <p class="alternate">
        	<?
				if($postKey !== 'comments'){
					echo '<a href="'.ADMIN_URL.'/comments" target="_self">',$unapproved_i,($unapproved_i === 0 || $unapproved_i > 1 ? ' Comments awaiting approval' : ' Comment awaiting approval'),'</a>';	
				}
				else{
					echo $unapproved_i,($unapproved_i === 0 || $unapproved_i > 1 ? ' Comments awaiting approval' : ' Comment awaiting approval');
				}
			?><br /><? 
			if($postKey !== 'posts'){
				echo '<a href="'.ADMIN_URL.'/posts" target="_self">',$unpublished_i,($unpublished_i === 0 || $unpublished_i > 1 ? ' Posts awaiting publication' :' Post awaiting publication'),'</a>';
			}
			else{
				echo $unpublished_i,($unpublished_i === 0 || $unpublished_i > 1 ? ' Posts awaiting publication' :' Post awaiting publication');
			}
			?></a>
        </p>
        <p class="headerP left"><? echo THIS_DOMAIN ?></p>
        <p class="headerP right">Welcome <? echo (array_key_exists('username', $_COOKIE) ? $_COOKIE['username'] : '');?> | <a href="<? echo ADMIN_URL.'/logout';?>" target="_self" id="logout">logout</a></p>
    </div><!--Header-->
      <div id="navigation">
    		<h2 id="posts" class="heading"><span id="posts_ico" class="ico"></span>Posts</h2>
            <ul>
            	<li<? echo  $postKey === 'index' ? ' class="here">&raquo; Add New Post &laquo;' : '><a href="'.ADMIN_URL.'" targe="_self">Add New Post</a>'?></li>
            	<li<? echo  $postKey === 'posts' ? ' class="here">&raquo; Posts &laquo;' : '><a href="'.ADMIN_URL.'/posts" targe="_self">Posts</a>'?></li>
                <li<? echo  $postKey === 'templates' ? ' class="here">&raquo; Templates &laquo;' : '><a href="'.ADMIN_URL.'/templates" targe="_self">Templates</a>'?></li>
            </ul>
            <h2 class="heading"><span id="tools_ico" class="ico"></span>Tools</h2>
            <ul id="tools_ul">
            	<li<? echo  $postKey === 'upload-files' ? ' class="here">&raquo; Upload Files &laquo;' : '><a href="'.ADMIN_URL.'/upload-files" targe="_self">Upload Files</a>'?></li>
                <li<? echo  $postKey === 'comments' ? ' class="here">&raquo; Comments &laquo;' : '><a href="'.ADMIN_URL.'/comments" targe="_self">Comments</a>'?></li>
                <li<? echo  $postKey === 'settings' ? ' class="here">&raquo; Settings &laquo;' : '><a href="'.ADMIN_URL.'/settings" targe="_self">Settings</a>'?></li>
            </ul>
   		</div><!--Navigation-->
        <div id="main_body">
       		<h2 class="main_heading"><? echo $docArray[$postKey]['title']?></h2><br />
            