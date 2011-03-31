<?php

if(array_key_exists('view',$_GET)){
	$post = $_GET['view'] !== '_index_' ? str_replace('_','/',$_GET['view']) : $_GET['view'];
	loadIntClass('preview_post');
	new Preview_post($post);
}
else{
	die('You need to specify a post to preview. The URL should look like this: "http://'.THIS_DOMAIN.'/'.ADMIN_URL.'/preview?view=example-post. If your url does look like that right now then there is something wrong with your server.');
}