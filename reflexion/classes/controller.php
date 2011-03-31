<?php
/*
Check which file is appropriate to load and load it.

The Controller checks the post-slug and category of the url.
It will appropriately load it, or throw a 404 error if the
file does not exist. If the file exists if both words are
brought to lower case, then the Controller will throw a 301
response header and send the client to the url with lower
case url. This effectively negates case sensitivity in the
MVC. Also of note, while it does look like the 301 behavior
is being unnecessarily repeated in the following code, each
implementation differs slightly from the last, making it
unworthy of its own function.
*/
class Controller {
	protected $_postName;
	
	protected $_category;
	
	function __construct($postName, $category) {
		$this->_postName = $postName;
		
		$this->_category = $category;
	}
	
	
	function __destruct() {
		if($this->_postName === 'GO_TO_404_ERROR_PAGE') {
			require(ROOT.DS.MAIN.DS.'reflex'.DS.'_404_'.DS.'404.php');
		}
		else if($this->_category === ADMIN_URL || strtolower($this->_category ) === ADMIN_URL) {
			if($this->_category !== ADMIN_URL) {
				$pageURL = 'http';
				$pageURL .= '://'.THIS_DOMAIN.'/'.ADMIN_URL.'/'.strtolower($this->_postName);
				header ('HTTP/1.1 301 Moved Permanently');
  				header ('Location: '.$pageURL);
				exit;
			}
			else {
				require(ROOT.DS.MAIN.DS.'reflex'.DS.'admin'.DS.'index.php');	
			}
		}
		else if(!$this->_category) {
			if(file_exists(ROOT.DS.MAIN.DS.'reflex'.DS.'documents'.DS.$this->_postName.'.php')) {
				require(ROOT.DS.MAIN.DS.'reflex'.DS.'documents'.DS.$this->_postName.'.php');
			}
			else {
				if(file_exists(strtolower(ROOT.DS.MAIN.DS.'reflex'.DS.'documents'.DS.$this->_postName.DS.'index.php'))) {
					$pageURL = 'http';
					$pageURL .= '://'.THIS_DOMAIN.'/'.strtolower($this->_postName);
					header ('HTTP/1.1 301 Moved Permanently');
  					header ('Location: '.$pageURL);
					exit;
				}
				else{
					require(ROOT.DS.MAIN.DS.'reflex'.DS.'_404_'.DS.'404.php');
				}
			}
		}
		else {
			if(file_exists(ROOT.DS.MAIN.DS.'reflex'.DS.'documents'.DS.'_categories_'.DS.$this->_category.DS.$this->_postName.'.php')) {
				require(ROOT.DS.MAIN.DS.'reflex'.DS.'documents'.DS.'_categories_'.DS.$this->_category.DS.$this->_postName.'.php');
			}
			else {
				if(file_exists(strtolower(ROOT.DS.MAIN.DS.'reflex'.DS.'documents'.DS.'_categories_'.DS.$this->_category.DS.$this->_postName.'.php'))){
					$pageURL = 'http';
					$this->_category = str_replace('_' , '/' , $this->_category);
					$pageURL .= '://'.THIS_DOMAIN.'/'.strtolower($this->_category).'/'.strtolower($this->_postNmae);
					header ('HTTP/1.1 301 Moved Permanently');
  					header ('Location: '.$pageURL);
					exit;
				}
				else{
					require(ROOT.DS.MAIN.DS.'reflex'.DS.'_404_'.DS.'404.php');
				}
			}
		}
	}	
}