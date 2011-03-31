<?php
class Write_comments{
	protected $_post;
	protected $_html;
	protected $_sort;
	protected $_desc;
	protected $_filter;
	protected $_comments;
	function __construct($post,$html,$sort='date',$desc=true,$filter=array('approved')){
			$this->_post = $post;
			$this->_html = $html;
			$this->_sort = $sort;
			$this->_desc = $desc;
			$this->_filter = $filter;
			$this->getComments();
			$this->writeComments();
	}
	function getComments(){
		loadIntClass('get_comments');
		$comm = new Get_comments($this->_post,$this->_filter,$this->_sort,$this->_desc);
		$this->_comments = $comm->commentsArr();
	}
	function writeComments(){
		$temp = '';
		$h = $this->_html;
		$rep_arr = array('id'=>'/%id%/','post_slug'=>'/%post_slug%/','author_name'=>'/%author_name%/','author_email'=>'/%author_email%/','author_ip'=>'/%author_ip%/','author_url'=>'/%author_url%/','time'=>'/%time%/','content'=>'/%content%/','karma'=>'/%karma%/','approved'=>'/%approved%/','agent'=>'/%agent%/','parent'=>'/%parent%/','type'=>'/%type%/');
		$r_arr = array();
		while(list($k,$v) = each($rep_arr)){
			if(preg_match($v,$h)){
				$r_arr[$k]=$v;
			}
		}
		for($i = 0; $i < count($this->_comments); ++$i){
			$c = $this->_comments[$i]
			$string = $h;
			while(list($k,$v) = each($r_arr)){
				$string = str_replace($v,$c[$k],$string);
			}
			$temp .= $string;
		}
		echo $temp;
	}
}