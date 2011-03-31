<?php
require_once(ROOT.DS.MAIN.DS.'library'.DS.'session_check.php');
class Preview_post{
	protected $_post;
	protected $_title;
	protected $_slug;
	protected $_description;
	protected $_template;
	protected $_category;
	protected $_author;
	protected $_commentbool;
	protected $_pingbacks;
	protected $_pingbool;
	protected $_publish;
	protected $_rewrite;
	protected $_cachepub;
	
	protected $_uri;
	protected $_query;
	function __construct($uri){
		if(session_check()) {
			$this->_uri = $uri;
			loadIntClass('sql_query');
			$this->_query = new Sql_query('posts');
			$num =  $this->_query->numRows('slug',$this->_uri);
			if($num !== 0){
				$this->loadUp();
				$this->buffer();
				$this->outPut();
			}
			else{
				die('No such post exists in the database.');	
			}
		}
		else{
			die('Your session has expired. Please <a href="'.ADMIN_URL.'/logout" target="_self">login</a> again in order to submit your post.');	
		}
	}
	
	function loadUp(){
		$post_arr = $this->_query->selectWhere('slug', $this->_uri);
		$post_arr = $post_arr['Post'];
		$this->_post = $post_arr['post'];
		$this->_title = $post_arr['title'];
		$this->_slug = $post_arr['slug'];
		$this->_description = $post_arr['description'];
		$this->_template = $post_arr['template'];
		$this->_category = $post_arr['category'];
		$this->_author = $post_arr['author'];
		$this->_commentbool = intval($post_arr['commentbool']) === 1 ? true : false;
		$this->_pingbacks = $post_arr['pingbacks'];
		$this->_pingbool = intval($post_arr['pingbool']) === 1 ? true : false;
		$this->_publish = intval($post_arr['publish']) === 1 ? true : false;
		$this->_cachepub = intval($post_arr['cachepub'])  === 1 ? true : false;
	}
	
	function buffer(){
		ob_start();
		require(ROOT.DS.MAIN.DS.'reflex'.DS.'templates'.DS.$this->_template.'.php');
		$html_string = ob_get_clean();
		$html_string = str_replace('<php>','<?php ',$html_string);
		$html_string = str_replace('</php>',' ?>',$html_string);
		require(ROOT.DS.MAIN.DS.'library'.DS.'simple_html_dom.php');
		$html = new simple_html_dom();
		$html->load($html_string);
		$b = $html->find('base',0);
		if(!isset($b)){
			$html->find('head',0)->innertext = '<base href="http://'.THIS_DOMAIN.'/"/>'.$html->find('head',0)->innertext;
			$html_string = $html->save();
		}
		$fileloc = fopen(ROOT.DS.MAIN.DS.'reflex'.DS.'admin'.DS.'documents'.DS.'dont_touch_this_file.php', 'w');
		fwrite($fileloc,$html_string);
		fclose($fileloc);
	}
	
	function outPut(){
		require(ROOT.DS.MAIN.DS.'reflex'.DS.'admin'.DS.'documents'.DS.'dont_touch_this_file.php');
	}
}