<?php
/*
Creates a new post, checks to see if the post exists already or not.
*/
require_once(ROOT.DS.MAIN.DS.'library'.DS.'session_check.php');
class Create_new_post {
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
	protected $_html_string;
	protected $_rewrite;
	protected $_cachepub;
	protected $_postdate;
	protected $_commentsdate;
	function __construct() {
		if(session_check()) {
			if(array_key_exists('post',$_POST) && array_key_exists('title',$_POST) && array_key_exists('slug',$_POST) && array_key_exists('description',$_POST) && array_key_exists('template',$_POST) && array_key_exists('category',$_POST) && array_key_exists('author',$_POST) && array_key_exists('commentbool',$_POST) && array_key_exists('pingbacks',$_POST) && array_key_exists('publish',$_POST) && array_key_exists('pingbool',$_POST) && array_key_exists('cachepub',$_POST) && array_key_exists('postdate', $_POST) && array_key_exists('comments_date',$_POST)) {
				$this->_unlink = false;
				$this->_post = $_POST['post'];
				$this->_title = $_POST['title'];
				$this->_slug = $_POST['slug'] === '' ? '_index_' : $_POST['slug'] ;
				$this->_description = $_POST['description'];
				$this->_template = $_POST['template'];
				$this->_category = $_POST['category'];
				$this->_author = $_POST['author'];
				$this->_commentbool = $_POST['commentbool'] === 'true' ? true : false;
				$this->_pingbacks = $_POST['pingbacks'];
				$this->_publish = $_POST['publish'] === 'true' ? true : false;
				$this->_pingbool = $_POST['pingbool'] === 'true' ? true : false;
				$this->_rewrite = array_key_exists('rewrite',$_POST) ? true : false;
				$this->_cachepub = $_POST['cachepub'] === 'true' ? true : false;
				$pdate = explode('/',$_POST['postdate']);
				reset($pdate);
				$this->_postdate = mktime(intval($pdate[3]),intval($pdate[4]),0,intval($pdate[0]),intval($pdate[1]),intval($pdate[2]));
				$cdate = explode('/',$_POST['comments_date']);
				reset($cdate);
				$this->_commentsdate = mktime(intval($cdate[3]),intval($cdate[4]),0,intval($cdate[0]),intval($cdate[1]),intval($cdate[2]));
				$this->initialize();
			}
			else {
				die('The program failed to accurately POST data to the server. Error#0001');
			}
		}
		else {
			die('Your session has expired. Please <a href="'.ADMIN_URL.'/logout" target="_self">login</a> again in order to submit your post.');
		}
	}
	
	function initialize() {
		if(file_exists(ROOT.DS.MAIN.DS.'reflex'.DS.'templates'.DS.$this->_template.'.php')) {
			ob_start();
			require(ROOT.DS.MAIN.DS.'reflex'.DS.'templates'.DS.$this->_template.'.php');
			$this->_html_string = ob_get_clean();
			$this->_html_string = str_replace('<php>','<?php ',$this->_html_string);
			$this->_html_string = str_replace('</php>',' ?>',$this->_html_string);
			loadIntClass('sql_query');
			$this->cron_set();
			$this->directory_create();
		}
		else {
			die('The template you specified cannot be found. This could be a serious error you should report it or resolve as soon as you can. Error#0002');
		}
	}
	function cron_set(){
		loadIntClass('cron_job');
		if(!$this->_publish && $this->_postdate > time()){
			$arr1 = array('time'=>$this->_postdate,'command'=>'republish_post','parameters'=>array($this->_slug));
			$cron1 = new Cron_job($arr1);
		}
		$cache = new Sql_query('posts');
		$cacheNum = $cache->query('SELECT * FROM `posts` WHERE `cachepub`=\'1\'');
		for($i = 0; $i < count($cacheNum); ++$i){
			$arr2 = array('time'=>time(),'command'=>'republish_post','parameters'=>array($cacheNum[$i]['Post']['slug']));
			$cron2 = new Cron_job($arr2);
		}
	}
	function directory_create() {
		if($this->_category === 'none') {
			if(!file_exists(ROOT.DS.MAIN.DS.'reflex'.DS.'documents'.DS.$this->_slug.'.php')){
				$this->db_create(ROOT.DS.MAIN.DS.'reflex'.DS.'documents'.DS.$this->_slug.'.php');
			}
			else if($this->_rewrite){
				$this->db_create(ROOT.DS.MAIN.DS.'reflex'.DS.'documents'.DS.$this->_slug.'.php');
			}
			else {
				die('file_exists');
			}
		}
		else if(!file_exists(ROOT.DS.MAIN.DS.'reflex'.DS.'documents'.DS.'_categories_'.DS.$this->_category.DS.$this->_slug.'.php')) {
			$this->db_create(ROOT.DS.MAIN.DS.'reflex'.DS.'documents'.DS.'_categories_'.DS.$this->_category.DS.$this->_slug.'.php');
		}
		else if($this->_rewrite){
			$this->db_create(ROOT.DS.MAIN.DS.'reflex'.DS.'documents'.DS.'_categories_'.DS.$this->_category.DS.$this->_slug.'.php');
		}
		else {
			die('file_exists');
		}
	}
	
	function db_create($directory) {
		$commentbool = $this->_commentbool ? 1 : 0;
		
		$publish = $this->_publish ? 1 : 0;
		
		$pingbool = $this->_pingbool ? 1 : 0;
		
		$cachepub = $this->_cachepub ? 1 : 0;
		
		$time = time();
		$posts = new Sql_query('posts');
		$num =  $posts->numRows('slug',$this->_slug);
		if($num !== 0){
			$posts->simpleQuery('UPDATE `posts`
				SET `title`=\''.$this->_title.'\',
				`description`=\''.$this->_description.'\,
				`template`=\''.$this->_template.'\',
				`category`=\''.$this->_category.'\',
				`author`=\''.$this->_author.'\',
				`commentbool`=\''.$commentbool.'\',
				`publish`=\''.$publish.'\',
				`post`=\''.$this->_post.'\',
				`pingbool`=\''.$pingbool.'\',
				`publishdate`=\''.$time.'\',
				`cachepub`=\''.$cachepub.'\,
				`comments_off`=\''.$this->_commentsdate.'\'
			WHERE `slug`=\''.$this->_slug.'\'');
			$posts->disconnect();
		}
		else{
			$posts->simpleQuery('INSERT INTO `posts` (title, slug, description, template, category, author, commentbool, publish, post, pingbool, publishdate, cachepub, comments_off) VALUES (
				\''.$this->_title.'\',
				\''.$this->_slug.'\',
				\''.$this->_description.'\',
				\''.$this->_template.'\',
				\''.$this->_category.'\',
				\''.$this->_author.'\',
				\''.$commentbool.'\',
				\''.$publish.'\',
				\''.$this->_post.'\',
				\''.$pingbool.'\',
				\''.$time.'\',
				\''.$cachepub.'\',
				\''.$this->_commentsdate.'\')');
			$posts->disconnect();
		}
	
		$string_url = 'http://'.THIS_DOMAIN.'/';
		$string_url .= $this->_category === 'none' ? ($this->_slug ==='_index_' ? '' : $this->_slug) : $this->_category.'/'.$this->_slug;
		if($this->_publish=='true'){
			$this->publish($directory,$string_url);
		}
		die('Your new post has been saved. Simply go to Posts, when you\'re ready to publish it. When it is published its url will be "'.$string_url.'".');
	}
	
	function publish($directory, $string_url) {
		$fileloc = fopen($directory, 'w');
		fwrite($fileloc,$this->_html_string);
		die('Your new post has been published. It can be viewed <a href="'.$string_url.'" target="_blank">here</a>; its url is "'.$string_url.'".');
	}
}