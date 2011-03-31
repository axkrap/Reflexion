<?php
require_once(ROOT.DS.MAIN.DS.'library'.DS.'session_check.php');
class Admin_comments{
	protected $_post;
	protected $_comments;
	function __construct(){
		if(array_key_exists('post',$_POST) && session_check()) {
			$this->_post = $_POST['post'] === 'AP' ? 'ALL' : $_POST['post'];
			if($this->_post !== '_index_'){
				str_replace('_','/',$this->_post);
			}
			$filter = $_POST['post'] === 'AP' ? array('pending','spam') : 'ALL';
			loadIntClass('get_comments');
			$comm = new Get_comments($this->_post, $filter);
			$this->_comments = $comm->commentsArr();
			echo $this->write();
			exit;
		}
		else{
			die('Your session has expired. Please <a href="'.ADMIN_URL.'/logout" target="_self">login</a> again in order to add your category.');
		}
	}
	function write(){
		$temp = '';
		$count = count($this->_comments);
		$int = 0;
		for($i = 0; $i < $count; ++$i){
			$int += 1;
			$temp .= '
					<tr id="'.$this->_comments[$i]['id'].'" class="'.$this->_comments[$i]['approved'].'" title="'.$this->_comments[$i]['type'].'">
                    	<td><input title="'.$this->_comments[$i]['id'].'" type="checkbox" class="table_check" /></td>
                        <td class="auth">'.$this->_comments[$i]['author_name'].'</td>
                        <td class="comm">'.$this->_comments[$i]['content'].'</td>
                        <td class="resp">'.$this->_comments[$i]['post_slug'].'</td>
                        <td class="date">'.date('m/d/Y',intval($this->_comments[$i]['time'])).'</td>
                    </tr>
					';
		}
		if($int === 0){
			$str = $this->_post === 'ALL' ? 'There are no comments pending or marked as spam' : 'There are no comments for '.THIS_DOMAIN.'/'.$this->_post.'.';
			die($str);
		}
		return $temp;
	}
}