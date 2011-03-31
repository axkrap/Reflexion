<?php require(ROOT.DS.MAIN.DS.'reflex'.DS.'admin'.DS.'documents'.DS.'headnavfoot'.DS.'header.php');
$sql_temp = new Sql_query('templates');
$template_arr = $sql_temp->selectAll();
$temp_table = '';
for($i=0; $i < count($template_arr); ++$i){
	$value = $template_arr[$i]['Template'];
	$temp_table .= '
	<tr id="'.$value['name'].'">
						<td>'.$value['name'].'</td>
						<td class="date">'.date('m/d/Y',intval($value['date'])).'</td>
					</tr>';
}
?>
            <p class="in_main">All you need to do, in order to add a new template, is to upload the file from your computer, and add the relevant media files later. When you upload a new template the file name will be the name of the template. If you would like to replace a template, simply upload a file of the same name.</p><br />
 			 <form id="add_temp">
             <div id="swfupload-control"></div>
                <input type="button" class="button" id="new_temp" />
             </form>
             <table id="templates_table" class="tablesorter">
				<thead>
                	<tr>
                        <th>Name</th>
                        <th>Date Last Modified</th>
                    </tr>
                </thead>
        		<tbody id="table_body">
                	<? echo $temp_table;?>
               </tbody>
             </table>
<? require(ROOT.DS.MAIN.DS.'reflex'.DS.'admin'.DS.'documents'.DS.'headnavfoot'.DS.'footer.php');?>
<script type="text/javascript" src="scripts/admin/swfupload.js"></script>
<script type="text/javascript" src="scripts/admin/jquery.swfupload.js"></script>
<script type="text/javascript" src="scripts/admin/tablesorter.jquery.min.js"></script>
<script type="text/javascript">
//$(document).ready(function(){
/*********************Templates PAGE SPECIFIC*************************/
	//Main Body Size
	$('#main_body').append('<div id="sizeShim" style="background:transparent;height:'+(($('#navigation').height()-10) - $('#templates_table').height())+'px;width:660px;"></div>');
	
	function resizeShim(){
		var height = ($('#navigation').height()-10) - $('#templates_table').height();
		$('#sizeShim').height(height);
	}
	//UPLOAD FUNCTION THINGY
	(function(){
			 var aBid;
			 var i = 0;
		$('#swfupload-control').swfupload({
			upload_url: 'http://<? echo THIS_DOMAIN.'/'?>'+internal_action+'_upload_template',
			file_size_limit : "10240",
			file_types : "*.*",
			file_types_description : "All Files",
			file_upload_limit : "0",
			file_queue_limit:'1',
			flash_url : "scripts/admin/swfupload.swf",
			button_text : '<span class="text">Upload Template</span>',
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
			button_placeholder : $('#new_temp')[0],
			debug: false
		})
		.bind('fileQueued', function(event, file){
			i+=1;
			aBid = alert('File '+file.name+' '+file.size+'bytes, uploading to server...<br/><span id="percent'+i+'">'+0+'</span>% uploaded.', false);
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
			$('#'+aBid).remove();
			if(serverData.match(/^REPLACE\//)){
				var file = serverData.split('/')[1];
				confirm('Would you like to replace the template "'+file+'"?',function(rep){
					var obj = {'replace':rep,'template':file};
					ajaxObjSend(obj, 'http://<? echo THIS_DOMAIN.'/';?>'+internal_action+'_template_confirm', 'POST', function(success,msg){
						if(success){
							if(msg.match(/^SUCCESS\//)){
								var arr = msg.split('/'),
								id = arr[1],
								date = new Date();
								date = (date.getUTCMonth()+1)+'/'+date.getUTCDate()+'/'+date.getFullYear();
								alert('The template was successfully replaced.');
								$('#'+id+' td.date').html(date);
							}
							else if(!msg.match(/^NOTHING$/)){
								alert(msg, 5e3);
							}
						}
						else{
							if(rep){
							alert('There was an error communicating with the server. It looks like you might have a bad internet connection try uploading the template again once you have a better connection.');
							};
						}
					});
				});
			}
			else if(serverData.match(/^SUCCESS\//)){
				var arr = serverData.split('/'),
				file = arr[1],
				date = new Date();
				date = (date.getUTCMonth()+1)+'/'+date.getUTCDate()+'/'+date.getFullYear();
				alert(file+' was successfully added as a template.');
				$('tbody').append('<tr><td>'+file+'</td><td>'+date+'</td></tr>');
			}
			else{
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
	//Table sorter
	$("#templates_table").tablesorter({
		widgets: ['zebra']
	});
//});

</script>
</body>
</html>
