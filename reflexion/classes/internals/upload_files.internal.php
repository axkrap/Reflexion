<?php
require_once(ROOT.DS.MAIN.DS.'library'.DS.'session_check.php');
class Upload_files{
	
	protected $_filename;
	protected $_extension;
	protected $_path;
	function __construct(){
		
		if(!array_key_exists('username',$_POST)){
			die('The browser failed to send the right data. You may have a bad internet connection.');
		}
		if(session_check($_POST['username'])){
			$this->_filename = basename($_FILES['Filedata']['name']);
			if(!preg_match('/^[[:alnum:]]+_*\.*-*[[:alnum:]_\.-]*$/', $this->_filename)){
				die('You passed a file with an illegal filename. Try using only acii characters.');
			}
			$this->getExtension();
			$this->getPath();
			if(file_exists($this->_path.DS.$this->_filename)){
				if(move_uploaded_file($_FILES['Filedata']['tmp_name'], ROOT.DS.$this->_path.DS.$this->_filename.'.temp')){
					$path = str_replace(DS,'.',$this->_path);
					die('REPLACE/'.$path.'/'.$this->_filename);
				}
				else{
					die('The file '.$this->_filename.' already exists on the server. The server was unable to temporarily save the file to see if you wanted to replace the file. It is possible that you uploaded a file bigger than is allowed.');
				}
				
			}
			$this->copyFile();
		}
		else{
			die('Your session has expired. Please <a href="'.ADMIN_URL.'/logout" target="_self">login</a> again in order to upload your file.');
		}
	}
	
	function getExtension(){
		$ext = explode('.',$this->_filename);
		$count = count($ext);
		while($count>1){
			$count-=1;
			array_shift($ext);
		}
		$this->_extension = $ext[0];
	}
	
	function getPath(){
		if($this->_extension === 'css'){
			$this->_path .= 'css';
		}
		else{
			$image_arr = array('jpg', 'jpeg', 'png', 'gif','bmp','tiff','tif','psd');
			$scripts_arr = array('js','jar','php','swf','fla','vb');
			$sound_arr = array('mp3','ogg','m4p','wav');
			$video_arr = array('3gp','avi','mov','mp4','mpg','mpeg','wmv');
			$font_arr = array('eot','woff','ttf','svg','otf');
			foreach($image_arr as $v){
				if($v === $this->_extension){
					$this->_path = 'media'.DS.'images';
					return true;
				}
			}
			foreach($scripts_arr as $v){
				if($v === $this->_extension){
					$this->_path = 'scripts';
					return true;
				}
			}
			foreach($sound_arr as $v){
				if($v === $this->_extension){
					$this->_path = 'media'.DS.'sound';
					return true;
				}
			}
			foreach($video_arr as $v){
				if($v === $this->_extension){
					$this->_path = 'media'.DS.'video';
					return true;
				}
			}
			foreach($font_arr as $v){
				if(preg_match('/^'.$v.'/' , $this->_extension)){
					$this->_path = 'css'.DS.'fonts';
					return true;
				}
			}
			$this->_path .= 'media'.DS.'other';
		}
	}
	
	function copyFile(){
		if(move_uploaded_file($_FILES['Filedata']['tmp_name'], ROOT.DS.$this->_path.DS.$this->_filename)){
			$path = str_replace(DS,'.',$this->_path);
			die('SUCCESS/'.$path.'/'.$this->_filename.'/'.$this->_extension);
		}
		else{
			die('It looks like you uploaded a file that is bigger than allowed.');
		}
	}
}