<?php require(ROOT.DS.MAIN.DS.'reflex'.DS.'admin'.DS.'documents'.DS.'headnavfoot'.DS.'header.php');
function dirToArray($dir) {
    $contents = array();
    foreach (scandir($dir) as $node) {
        if ($node == '.')  continue;
        if ($node == '..') continue;
		if (preg_match('/^\./',$node)) continue;
        if (is_dir($dir.DS.$node)) {
            if($node == 'admin') continue;
			if($dir === ROOT.DS.'scripts' && $node === 'tiny_mce') continue;
			$contents[$node] = dirToArray($dir.DS.$node);
			continue;
        }
		else {
            $contents[] = $node;
        }
    }
    return $contents;
}

function listMaker($name,$arr){
	$string = '';
	foreach ($arr as $key => $value){
		if(is_array($value)){
			$string.='
			<li class="directory expanded"><a href="#" rel="'.$name.'/'.$key.'">'.$key.'</a>
				<ul id="'.$key.'" class="jqueryFileTree">';
			$string.= listMaker($name.'/'.$key,$value);
			$string.='
				</ul>
			</li>';
			continue;
		}
		else{
			$ext = explode('.',$value);
			$count = count($ext);
			while($count>1){
				$count-=1;
				array_shift($ext);
			}
			$ext = $ext[0];
			if($ext === 'temp') continue;
			$string.='
			<li class="file ext_'.$ext.'"><a href="#" rel="'.$name.'/'.$value.'">'.$value.'</a></li>';
		}
	}
	return $string;
}
$scripts = listMaker('scripts',dirToArray(ROOT.DS.'scripts'));
$css = listMaker('css',dirToArray(ROOT.DS.'css'));
$media = listMaker('media',dirToArray(ROOT.DS.'media'));
$string_info = ' The maximum file size you can upload is ';
$string_info .= strpos(php_sapi_name(),'apache') === false ? ini_get('upload_max_filesize').'B; this is because your server is not running php as an apache module.' : '100MB. If you want to increase this number you can do so by modifying the ".htaccess" file.';
?>
     		<p class="in_main" id="para">"Double-click" on a file to view it. You can select multiple files by holing the "shift" key. When you upload a file, the uploader will automatically figure out where to place your file.<br /><? echo $string_info?></p>
            <form class="in_main" id="media_sorter">
            	<div id="swfupload-control"></div>
            	<input type="button" class="button_alt" id="upload_btn" value="Upload New File" />
            	<input type="button" class="button_alt" id="delete_btn" value="Delete Forever" />
            </form>
            
            <div id="file_tree_parent">
            	<div id="file_tree" class="in_main">
                	<ul class="jqueryFileTree">
                		<li class="directory expanded"><a href="#" rel="css">css</a>
                			<ul  id="scripts" class="jqueryFileTree">
                				<? echo $css;?>
                			</ul>
                		</li>
                		<li class="directory expanded"><a href="#" rel="media">media</a>
                			<ul id="media" class="jqueryFileTree">
                				<? echo $media;?>
             				</ul>
             			</li>
             			<li class="directory expanded"><a href="#" rel="scripts">scripts</a>
             				<ul  id="scripts" class="jqueryFileTree">
                            	<? echo $scripts;?>
             				</ul>
             			</li>
             		</ul>
            	</div>
       		</div>
<? require(ROOT.DS.MAIN.DS.'reflex'.DS.'admin'.DS.'documents'.DS.'headnavfoot'.DS.'footer.php');?>
<script type="text/javascript" src="scripts/admin/swfupload.js"></script>
<script type="text/javascript" src="scripts/admin/jquery.swfupload.js"></script>
<script type="text/javascript">
//$(document).ready(function(){
/*********************Upload-Files PAGE SPECIFIC*************************/
	//Main Body Size
	$('#main_body').append('<div id="sizeShim" style="background:transparent;height:'+(($('#navigation').height()-10) - $('#file_tree').height())+'px;width:660px;"></div>');
	
	function resizeShim(){
		var height = ($('#navigation').height()-120) - $('#file_tree').height();
		$('#sizeShim').height(height);
	}
	resizeShim();
		
	(function(){
		var shiftdown = false;
		$(document).keydown(function(e){
			if(e.which === 16){
				shiftdown = true;
			}
		})
		.keyup(function(e){
			if(e.which === 16){
				shiftdown = false;
			}
		});
	    $('#file_tree a').each(function(e){
			$(this).click(function(){
				if($(this).parent().hasClass('directory')){
					if($(this).parent().hasClass('expanded')){
						$(this).parent().removeClass('expanded').addClass('collapsed');
						$(this).parent().find('ul.jqueryFileTree:first').css('display','none');
					}
					else{
						$(this).parent().removeClass('collapsed').addClass('expanded');
						$(this).parent().find('ul.jqueryFileTree:first').css('display','block');
					}
					resizeShim();
				}
				if($(this).parent().hasClass('file')){
					if(!shiftdown) {
  						$('.sel').each(function(){
							$(this).removeClass('sel');
						});
					}
					if($(this).hasClass('sel')){
						$(this).removeClass('sel');
					}
					else{
						$(this).addClass('sel');
					}
				}
				return false;
			});
			if($(this).parent().hasClass('file')){
				$(this).dblclick(function(){
					var url = '<? echo 'http://'.THIS_DOMAIN.'/'?>'+$(this).attr('rel');
					window.open(url,'_blank');
				});
			}
		});
	//Actions
			var deleting = false;
			//Delete click
			$('#delete_btn').click(function(){
				if(deleting){
					return false;
				}
				deleting = true;
				var send_array = [],
				any = false,
				element;
				$('.sel').each(function(){
					any = true;
					send_array.push($(this).attr('rel'));
					element = this;
				});
				if(any && send_array.length === 1){
					confirm('Are you sure you want to delete this file?',function(bool){
						if(bool){
							white();
							obj = {'media':send_array[0]};
							ajaxObjSend(obj, 'http://<? echo THIS_DOMAIN.'/'?>'+internal_action+'_media_delete', 'POST', function(success,msg){
								if(success){
									if(msg.match(/^SUCCESS/)){
										white();
										$(element).parent().remove();
										alert(send_array[0]+' was successfully deleted.');
									}
									else{
										white();
										alert('There was an error deleting the file. Try again later.');
									}
								}
								else{
									white();
									alert('It seems that there was an error connecting to the server. It may be due to a bad internet connection.');
								}
							});
						}
					});
					
				}
				else if(any && send_array.length > 1){
					confirm('Are you sure you want to delete these files?', function(bool){
						if(bool){
							white();
							var i = send_array.length,
							aid = alert('<span id="file_num">0</span> of '+i+' files deleted...',false);
							for (var key in send_array){
								i-=1;
								deleter(send_array[key]);
								if(i === 0 && successful_delete){
									$('#'+aid).remove();
									$('[rel="'+send_array[key]+'"]').parent().remove();
									white();
									alert('All of the files were successfully deleted');
								}
								else if(successful_delete){
									$('#file_num').text(send_array.length-i);
									$('[rel="'+send_array[key]+'"]').parent().remove();
								}
								else{
									$('#'+aid).remove();
									white();
									alert('There was an error deleting one of the files.');
									break;
								}
							}
						}
					});
				}
				deleting = false;
			});
			
			var successful_delete = true;
			function deleter (value){
				obj = {'media':value};
				return ajaxObjSend(obj, 'http://<? echo THIS_DOMAIN.'/'?>'+internal_action+'_media_delete', 'POST', function(success,msg){
					if(success){
						if(msg.match(/^SUCCESS/)){
							successful_delete = true;	
						}
						else{
							successful_delete =  false;
						}
					}
					else{
						successful_delete =  false;	
					}
				});
			}
			
			whiteout = false;
			function white(){
				if(!whiteout){
					$('body').append('<div id="white_out"></div>');
					$('#white_out').width($(document).width());
					$('#white_out').height($(document).height());
					$(window).resize(function(){
						$('#white_out').width($(document).width());
						$('#white_out').height($(document).height());
					});
					whiteout = true;
				}
				else{
					$('#white_out').remove();
					whiteout = false;
				}
			}
		//Swf click
		var aBid;
		var i;
		var ulim = <? echo intval(ini_get('upload_max_filesize'))?>*1024;
		$('#swfupload-control').swfupload({
			upload_url: 'http://<? echo THIS_DOMAIN.'/'?>'+internal_action+'_upload_files',
			file_size_limit : '"'+ulim+'"',
			file_types : "*.*",
			file_types_description : "All Files",
			file_upload_limit : "0",
			file_queue_limit:'1',
			flash_url : "scripts/admin/swfupload.swf",
			button_text : '<span class="text">Upload New Files</span>',
			button_image_url : '../media/admin/images/site_btn.png',
			button_text_style : ".text { color:#495b6c;font-size:14px;font-family:Verdana, Geneva, sans-serif;}",
			button_text_left_padding : 7,
			button_text_top_padding : 2,
			button_action : SWFUpload.BUTTON_ACTION.SELECT_FILES,
			button_disabled : false,
			button_cursor : SWFUpload.CURSOR.HAND,
			button_window_mode : SWFUpload.WINDOW_MODE.TRANSPARENT,
			button_width : 141,
			button_height : 32,
			post_params:{'username':'<? echo $_COOKIE['username'];?>'},
			button_placeholder : $('#upload_btn')[0],
			debug: false
		})
		.bind('fileQueued', function(event, file){
			i+=1;
			white();
			aBid = alert('File "'+file.name+'" is '+file.size+'bytes, and is uploading to the server...<br/><span id="percent'+i+'">'+0+'</span>% uploaded.', false);
			// start the upload since it's queued
			$(this).swfupload('startUpload');
		})
		.bind('fileQueueError', function(event, file, errorCode, message){
			$('#'+aBid).remove();
			alert('An error occurred with '+file.name+':<br/>'+message+'<br/>Error#:'+errorCode);
		})
		.bind('uploadProgress', function(event, file, bytesComp, bytesTot){
			var per = Math.ceil((bytesComp/bytesTot)*100);
			$('#percent'+i).text(per);
		})
		.bind('uploadSuccess', function(event, file, serverData){
			if(serverData.match(/^SUCCESS\//)){
				white();
				$('#'+aBid).remove();
				var arr = serverData.split('/');
				var parent = arr[1].replace('.','/');
				var child = arr[2];
				var ext = arr[3];
				alert('File uploaded successfully.');
				//ADD the ELEMENT and events
				$('[rel="'+parent+'"] + ul').append('<li class="file ext_'+ext+'"><a href="#" rel="'+parent+'/'+child+'">'+child+'</a></li>');
				$('[rel="'+parent+'/'+child+'"]').click(function(){
					if(!shiftdown) {
  						$('.sel').each(function(){
							$(this).removeClass('sel');
						});
					}
					if($(this).hasClass('sel')){
						$(this).removeClass('sel');
					}
					else{
						$(this).addClass('sel');
					}
					return false;
				});
				
				$('[rel="'+parent+'/'+child+'"]').dblclick(function(){
					var url = '<? echo 'http://'.THIS_DOMAIN.'/'?>'+$(this).attr('rel');
					window.open(url,'_blank');
				});
				//Element finished
			}
			else if(serverData.match(/^REPLACE\//)){
				var arr = serverData.split('/');
				var parent = arr[1].replace('.','/');
				var child = arr[2];
				$('#'+aBid).remove();
				confirm('Do you want to replace the file '+child+'?',function(bool){
					var obj = {
						'parent':parent,
						'child':child,
						'replace':bool
					};
					ajaxObjSend(obj, 'http://<? echo THIS_DOMAIN.'/';?>'+internal_action+'_file_replace', 'POST', function(success,msg){
						if(success){
							if(msg.match(/^SUCCESS/)){
								white();
								alert(child+' was successfully replaced.');
							}
							else if(!msg.match(/^KILLED/)){
								white();
								alert(msg);
							}
						}
						else{
							white();
							alert('There was a problem connecting to the server. You may have a bad internet connection.')
						}
					});
				});
			}
			else{
				white();
				$('#'+aBid).remove();
				alert(serverData);
			}
		})
		.bind('uploadComplete', function(event, file){
			$(this).swfupload('startUpload');
		})
		.bind('uploadError', function(event, file, errorCode, message){
			$('#'+aBid).remove();
			alert('An error occurred with '+file.name+':<br/>'+message+'<br/>Error#:'+errorCode);
		});
	})();
	
//});

</script>
</body>
</html>
