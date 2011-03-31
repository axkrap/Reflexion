<?php
require_once(ROOT.DS.MAIN.DS.'library'.DS.'session_check.php');
class Republish_post {
	protected $_post;
	protected $_title;
	protected $_slug;
	protected $_description;
	protected $_template;
	protected $_category;
	protected $_author;
	protected $_comments;
	protected $_pingbacks;
	protected $_pingbool;
	protected $_publish;
	protected $_rewrite;
	protected $_cachepub;
	protected $_uri;
	protected $_loc;
	function __construct($post = 'none') {
		if(session_check() || CRON_JOB) {
			if(array_key_exists('uri',$_POST) && $post === 'none'){
				$this->_uri = $_POST['uri'] !== '_index_' ? str_replace('_','/',$_POST['uri']) : '_index_';
				$this->_loc = $_POST['uri'] !== '_index_' ? str_replace('_',DS,$_POST['uri']) : '_index_';
				$this->republish();
			}
			else if($post !== 'none'){
				$this->_uri = $post;
				$this->_loc = str_replace('/',DS,$post);
			}
			else{
				die('Your browser did not send the right information. If you are working on an older browser please switch to a modern browser.');
			}
		}
		else{
			die('Your session has expired, please log back in.');
		}
	}
	
	function republish() {
		loadIntClass('sql_query');
		$posts = new Sql_query('posts');
		$num =  $posts->numRows('slug',$this->_uri);
		if($num !== 0){
			$post_arr = $posts->selectWhere('slug',$this->_uri);
			$post_arr = $post_arr['Post'];
			$this->_post = $post_arr['post'];
			$this->_title = $post_arr['title'];
			$this->_slug = $post_arr['slug'];
			$this->_description = $post_arr['description'];
			$this->_template = $post_arr['template'];
			$this->_category = $post_arr['category'];
			$this->_author = $post_arr['author'];
			$this->_commentbool = $post_arr['commentbool'];
			$this->_pingbacks = $post_arr['pingbacks'];
			$this->_pingbool = $post_arr['pingbool'];
			$this->_publish = $post_arr['publish'];
			$this->_cachepub = $post_arr['cachepub'];
				
			ob_start();
			require(ROOT.DS.MAIN.DS.'reflex'.DS.'templates'.DS.$this->_template.'.php');
			$html_string = ob_get_clean();
			$html_string = str_replace('<php>','<?php ',$html_string);
			$html_string = str_replace('</php>',' ?>',$html_string);
			
			if($this->_category == 'none'){
				$fileloc = fopen(ROOT.DS.MAIN.DS.'reflex'.DS.'documents'.DS.$this->_slug.'.php', 'w');
				fwrite($fileloc,$html_string);
				fclose($fileloc);
			}
			else{
				$fileloc = fopen(ROOT.DS.MAIN.DS.'reflex'.DS.'documents'.DS.'_categories_'.$this->_category.DS.$this->_slug.'.php', 'w');
				fwrite($fileloc,$html_string);
				fclose($fileloc);	
			}
			$posts->simpleQuery('UPDATE `posts` SET `publish`=\'1\' WHERE `slug`=\''.$this->_slug.'\'');
			die('yes');	
		}
		else{
			die('The server could not find the post that you selected.');
		}
	}
}