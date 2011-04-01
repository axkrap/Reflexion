<?php require(ROOT.DS.MAIN.DS.'reflex'.DS.'admin'.DS.'documents'.DS.'headnavfoot'.DS.'header.php');
$Post = new Sql_query('posts');
$post_arr = $Post->selectAll();
$table = '';
$p_temp_opts ='';
$p_cat_opts = '';
$p_aut_opts = '';
$pub_i = 0;
$unpub_i = 0;

$Temp = new Sql_query('templates');
$temp_arr = $Temp->selectAll();
$temp_options_js = '';
for($i=0; $i < count($temp_arr); ++$i){
	$temp_options_js .= '<option value="'.$temp_arr[$i]['Template']['name'].'">'.$temp_arr[$i]['Template']['name'].'</option>';
}
$p_temp_opts = $temp_options_js;
$cat = new Sql_query('categories');
$catarr = $cat->selectAll();
for($i=0; $i < count($catarr); ++$i){
	$p_cat_opts .= '<option value="'.$catarr[$i]['Categorie']['category'].'">'.$catarr[$i]['Categorie']['category'].'</option>';
}
$aut = new Sql_query('authors');
$autarr = $aut->selectAll();
for($i=0; $i < count($autarr); ++$i){
	$p_aut_opts .= '<option value="'.$autarr[$i]['Author']['author'].'">'.$autarr[$i]['Author']['author'].'</option>';
}
for($i=0; $i < count($post_arr); ++$i){
	$value = $post_arr[$i]['Post'];
	$local = str_replace('/','_',$value['slug']);
	if($value["publish"] == 1){
		$pub_i+=1;
	}
	else{
		$unpub_i+=1;	
	}
	$table.=
		'
		<tr id="'.$local.'" published="'.($value["publish"] == 1?'true':'false').'" author="'.str_replace(" ","_",$value["author"]).'" template="'.str_replace(" ","_",$value["template"]).'" category="'.$value["category"].'">
			<td><input type="checkbox" class="table_check" name="'.$local.'"/></td>
			<td>'.$value["title"].'</td>
			<td>'.$value["template"].'</td>
			<td>'.$value["category"].'</td>
			<td>'.$value["author"].'</td>
			<td>'.date('m/d/Y',intval($value["publishdate"])).'</td>
		</tr>
		<tr class="meta off" title="'.$local.'">
			<td colspan="3">
				<ul id="'.$local.'_ul1'.'">
					'.($value["publish"] == 1 ? '' : '<li id="'.$local.'_pub'.'">THIS POST IS NOT PUBLISHED</li>
					').'<li>'.$value["description"].'</li>
					<li><a href="'.ADMIN_URL.'?edit='.$local.'" target="_self">Edit</a> | <a href="'.ADMIN_URL.'/preview?view='.$local.'" target="_blank">Preview</a></li>
					<li>There are''</li>
				</ul>
			</td>
			<td colspan="3">
				<ul>
					<li>The relative uri is: "'.$value["slug"].'"</li>
					<li>'.($value["commentbool"] ?'There are: <a href="'.ADMIN_URL.'/comments?post='.$value['slug'].'" target="_self">'.$value['approved_comments'].' approved comments</a>' : 'No comments are allowed in this post.').'</li>
					'.($value["commentbool"] ? '<li>There are: <a href="'.ADMIN_URL.'/comments?post='.$value['slug'].'" target="_self">'.$value['pending_comments'].' unapproved comments</a></li>' : '').'
					<li>'.($value["pingbool"] ? 'There are : <a href="'.ADMIN_URL.'/comments?post='.$value['slug'].'" target="_self">'.$value["pings"].' pingbacks</a>.':'Pingbacks are disabled in this post.').'</li>
				</ul>
			</td>
		<tr/>';
}
?>
            <p id="pub_filter" class="in_main">
            	<a id="filter_all" title="<? echo $pub_i+$unpub_i;?>" class="selected">All(<? echo $pub_i+$unpub_i;?>)</a>|<a id="filter_published" title="<? echo $pub_i;?>">Published(<? echo $pub_i;?>)</a>|<a id="filter_unpublished" title="<? echo $unpub_i;?>">Unpublished(<? echo $unpub_i;?>)</a>
            </p>
            <form id="cat_filter" class="in_main">
            	<select id="bulk_actions">
                	<option value="">Actions...</option>
                    <option value="unpublish">Unpublish</option>
                    <option value="template">New Template</option>
                    <option value="republish">(Re)publish</option>
                    <option value="delete">Delete</option>
                </select>
                <input type="button" class="button" id="bulk_apply_btn" value="apply" />
                <select id="filter_template">
                	<option value="">All templates...</option>
                	 <? echo $p_temp_opts;?>
                    
                </select>
                <select id="filter_category">
                	<option value="">All categories...</option>
                	<? echo $p_cat_opts;?>
                    
                </select>
                <select id="filter_author">
                	<option value="">All authors...</option>
                    <? echo $p_aut_opts;?>
                    
                </select>
                <input type="button" class="button" id="filter_btn" value="filter" />
            </form>
            <table id="posts_table" class="tablesorter">
				<thead>
                	<tr>
                    	<th><input type="checkbox" id="select_all" /></th>
                        <th>Title</th>
                        <th>Template</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>Date</th>
                    </tr>
                </thead>
        		<tbody>
                <? echo $table?>
                </tbody>
            </table>
<?php require(ROOT.DS.MAIN.DS.'reflex'.DS.'admin'.DS.'documents'.DS.'headnavfoot'.DS.'footer.php');?>
<script type="text/javascript" src="scripts/admin/tablesorter.jquery.min.js"></script>
<script type="text/javascript">
//$(document).ready(function(){
(function(w, undefined){
	/****ANIMATION STUFF***/
	var i = -1;
	function alertBoxSet(animateCall,aBid){
		if(animateCall){
			$(aBid).css('display','block');
		}
		var left = $(window).width()/2 - $(aBid).width()/2,
		topSalt = animateCall ? 10 : 0,
		top = $(window).height()/2 - $(aBid).height()/2 - topSalt;
		$(aBid).css({'left':left,'top':top});
	};
	
	function animateOut (time, aBid){
			setTimeout(function(){
				$(aBid).animate({
					opacity:0,
					top:'+=10'},
					1e3,
					function(){
						$(aBid).remove();
					}
				);//end of animate
			},time);//end of function and setTimeout
		};
		
	function ac_prompt(msg, callBack){
		i+=1;
		var aB = 'alert_box'+i, aBid = '#'+aB;
		html = '<div id="'+aB+'" class="alert_box"><p>'+msg+'</p><select style="width:90%;" id="prompt'+i+'" type="text"><? echo $temp_options_js;?></select><br/><button style="align:center;" class="button" id="enter'+i+'">Enter</button></div>';
		var clicked = false;
		$('body').append(html);
		alertBoxSet(true, aBid);
		$(window).resize(function(){
			alertBoxSet(false,aBid);
		});
		$(aBid).animate({
   			opacity: 1.00,
			top: '+=10'
			},
			1e3
		);
		$('#enter'+i).click(function(){
			if(!clicked){
				clicked = true;
				animateOut(0,aBid);
				processing = false;
				callBack(true, $('#prompt'+i+' option:selected').val());
			}
		});
	};
	
	var whiteout = false;
	function white(out){
		if(out){
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
		}
	}
	/**AJAXIAN STUFF**/
	//Is the function going? Because we only want on instance of actioner running at a time
	
	
	var actioner = w.actioner = function(){
		var processing = false,
		//Get template type from user
		template = undefined,
		//What bulk action are they doing
		action,
		//If there is more than one post being modified then an alert box pops up to inform the user of how many posts are left to modify
		aBid,
		//an array of the posts to modify
		arr_uri = [],
		//The INITIAL number of posts being modified
		arr_l = 0,
		//The current number of posts we have modified
		int=0,
		//The currentid of the table row representing the post we are modifying
		currentid,
		//No to delete?
		delegate_out = false;
		//If one instance of the function is going still
		function init (going, temp){
			if(processing){
				return false;
			}
			action = $('#bulk_actions option:selected').val();
			if(action ===''){
				return false;
			}
			processing = true;
			if(action === 'unpublish' && !going){
				processing = false;
				if(delegate_out){
					finisher(false);
					return;
				}
				delegate_out = true;
				confirm('Are you sure you want to unpublish this/these post(s)?',function(bool){init(bool);});
				return;
			}
			else if(action === 'unpublish' && going){
				arrayCreator('published','false');
			}
			else if(action === 'template' && !going){
				processing = false;
				if(delegate_out){
					finisher(false);
					return;
				}
				delegate_out = true;
				confirm('Are you wure you want to change the template in this/these post(s)?',function(bool){init(bool);});
				return;
			}
			else if(action === 'template' && going && !temp){
				processing = false;
				ac_prompt('Please select a template to change to:',function(bool, tem){init(bool,tem);});
				return;
			}
			else if(action === 'template' && going && temp){
				template = temp;
				arrayCreator('template',template);
			}
			else if(action === 'republish' && !going){
				processing = false;
				if(delegate_out){
					finisher(false);
					return;
				}
				delegate_out = true;
				confirm('Are you sure that you want to (Re)publish this/these post(s)?', function(bool){init(bool);});
				return;
			}
			else if(action === 'republish' && going){
				arrayCreator();
			}
			else if(action === 'delete' && !going){
				processing = false;
				if(delegate_out){
					finisher(false);
					return;
				}
				delegate_out = true;
				confirm('It is not recommended that you delete posts. If you delete posts you will not be able to recover them, and there is no way to undo this action. We recommend that you "unpublish" instead. Would you like to delete anyways?', function(bool){init(bool);});
				return;
			}
			else if(action === 'delete' && going){
				arrayCreator();
			}
			else{
				return;
			}
		//Execute
			if(arr_l > 1){
				white(true);
				aBid = alert('<span id="processing">'+i+' of '+arr_uri.length+' posts complete.</span>', false);
				arrayHandler();
			}
			else if(arr_l > 0){
				arrayHandler();
			}
			else{
				var a_string;
				if(action === 'template'){
					a_string = 'All of the posts you selected already have the template you specified';
				}
				if(action === 'unpublish'){
					a_string = 'None of the posts you selected are published.';
				}
				processing = false;
				finisher(false);
				alert(a_string, 5e3);
				return;
			}
		};
		
		function arrayCreator(attrwas,attris){
			$('.table_check:checked').each(function(){
				if(attrwas && attris){
					if($('#'+$(this).attr('name')).attr(attrwas) === attris){
						this.checked = false;
					}
					else{
						arr_uri.push($(this).attr('name'));
					}
				}
				else{
					arr_uri.push($(this).attr('name'));
				}
			});
			arr_l = arr_uri.length+0;
		};
	
	
		function arrayHandler(bool){
			if(bool === false){
				return;
			}
			currentid = arr_uri.shift();
			//obj to be sent
			var obj = {}
			//if there is a template present send it
			if(template !== undefined){
				obj["template"] = template;
			}
			//Number of posts modified
			int+=1;
			obj["uri"] = currentid;
			//the class in refelxion being used (internal)
			var act = internal_action+'_'+action+'_post';
			if(arr_uri.length === 0){
				//last ajax call
				ajaxObjSend(obj, act , 'POST', function(bool, msg){finisher(update(bool,msg));});
			}
			else{
				ajaxObjSend(obj, act , 'POST', function(bool, msg){arrayHandler(update(bool, msg));});
			}
		}
	
		function update(server, msg){
			if(!server){
				animateOut(0,'#'+aBid);
				white(false);
				var past = action == 'delete' || action == 'template' ? '' : 'e';
				alert('Something went wrong. '+int+' posts have been '+action+past+'d, and the post that failed has the uri "'+currentid.replace('_','/')+'"');
				finisher(false);
				return false;
			}
			else{
				if(msg != 'yes'){
					alert(msg);
					finisher(false);
					return false;
				}
				else{
					$('#processing').text(int+' of '+arr_l+' posts complete.');
					alterElement();
					return true;
				}
			}
		};
	
		function alterElement(){
			if(action === 'unpublish'){
				$('#'+currentid).attr('published','false');
				$('#'+currentid+'_ul1').prepend('<li id="'+currentid+'_pub'+'">THIS POST IS NOT PUBLISHED</li>');
				var pub = parseInt($('#filter_published').attr('title'))-1,
				unpub = parseInt($('#filter_unpublished').attr('title'))+1;
				$('#filter_published').attr('title',pub);
				$('#filter_unpublished').attr('title',unpub);
				$('#filter_published').text('Published('+pub+')');
				$('#filter_unpublished').text('Unpublished('+unpub+')');	
			}
			else if(action === 'republish'){
				if($('#'+currentid).attr('published') == 'false'){
					$('#'+currentid).attr('published','true');
					$('#'+currentid+'_pub').remove();
					var pub = parseInt($('#filter_published').attr('title'))+1,
					unpub = parseInt($('#filter_unpublished').attr('title'))-1;
					$('#filter_published').attr('title',pub);
					$('#filter_unpublished').attr('title',unpub);
					$('#filter_published').text('Published('+pub+')');
					$('#filter_unpublished').text('Unpublished('+unpub+')');
				}	
			}
			else if(action === 'template'){
				$('#'+currentid).attr('template',template);
				$('#'+currentid+':nth-child(3)').html(template);
			}
			else if(action === 'delete'){
				var pub_bool = $('#'+currentid).attr('published');
				var pub = parseInt($('#filter_published').attr('title')),
				unpub = parseInt($('#filter_unpublished').attr('title')),
				alli = parseInt($('#filter_all').attr('title'));
				if(pub_bool === 'true'){
					pub -= 1;
					alli -= 1;
					$('#filter_published').attr('title',pub);
					$('#filter_all').attr('title',alli);
					$('#filter_published').text('Published('+pub+')');
					$('#filter_all').text('All('+alli+')');
				}
				else{
					unpub -= 1;
					alli -= 1;
					$('#filter_unpublished').attr('title',unpub);
					$('#filter_all').attr('title',alli);
					$('#filter_unpublished').text('Unpublished('+unpub+')');
					$('#filter_all').text('All('+alli+')');
				}
				$('#'+currentid).next().addClass('delete');
				$('#'+currentid).addClass('delete');
				
			}
		};
	
		function finisher(upda){
			if(upda){
				animateOut(0,'#'+aBid);
				white(false);
				var tem_s = action === 'template' ? ' Remeber that the template function doesn\'t republish the posts it simply changes what template they will use when they are.' : '';
				var past = action === 'delete' || action == 'template'? '' : 'e';
				var string = arr_l > 1 ? 'All '+arr_l+' posts have been '+action+past+'d.' : 'The post has been '+action+past+'d.'+tem_s;
				alert(string, 8e3);
				
			}
			$("table").trigger("sorton",[[[1,0]]]);
		};
		init();
	};
	
})(window, undefined);
/*********************POSTS PAGE SPECIFIC*************************/
	//The table might be small.
	$('#main_body').append('<div id="sizeShim" style="background:transparent;height:'+(($('#navigation').height()-10) - $('#posts_table').height())+'px;width:660px;"></div>');
	
	function resizeShim(){
		var height = ($('#navigation').height()-10) - $('#posts_table').height();
		$('#sizeShim').height(height);
	}
//If a row is clicked and isn't a meta row, then open its meta row
	$('tbody tr').each(function(i){
		var tr = this;
		if(!$(tr).hasClass('meta')){
			$(tr).click(function(event){
				if(event.target.nodeName!=='INPUT' && event.target.nodeName!=='input'){
					resizeShim();
					var $target = $('[title='+$(tr).attr('id')+']');
					if($target.hasClass('off')){
						$(tr).css('cursor','n-resize');
					}
					else{
						$(tr).css('cursor','s-resize');
					}
					$target.toggleClass('off');
				}
			});
		}
	});
	//Checkboxes
	$('#select_all').click(function(){
		var checked = $(this).is(':checked');
		$('.table_check').each(function(){
			if(checked){
				if($(this).is(':visible')){
					this.checked = true;
				}
			}
			else{
				this.checked = false;
			}
		});
	});
	//Keep all filtered posts checked if the select all checkbox is on, turn off the ones that are filtered out
	function visCheck(){
		var checked = $('#select_all').attr('checked')
		$('.table_check').each(function(){
			if($(this).is(':visible')){
				this.checked = checked ? true : this.checked;
			}
			else{
				this.checked =  false;
			}
		});
	};
	var publish = null;
//Publish or unpublish filtering
	$('#filter_all').click(function(){
		resizeShim();
		if(!$(this).hasClass('selected')){
			$('tbody tr').each(function(){
				if(!$(this).hasClass('meta')){
					if($(this).hasClass('off')){
						$(this).removeClass('off');
					}
				}
			});
			//publish = 'tbody tr';
			publish = null;
			visCheck();
			$(this).addClass('selected');
			if($('#filter_published').hasClass('selected')){
				$('#filter_published').removeClass('selected');
			}
			else{
				$('#filter_unpublished').toggleClass('selected');
			}
			$("table").trigger("sorton",[[[1,0]]]); 
		}
	});
	
	$('#filter_published').click(function(){
		resizeShim();
		if(!$(this).hasClass('selected')){
			$('tbody tr[published="true"]').each(function(){
				if($(this).hasClass('off')){
					$(this).removeClass('off');
				}
			});
			$('tbody tr[published="false"]').each(function(){
				if(!$(this).hasClass('off')){
					$(this).addClass('off');
					$('[title="'+this.id+'"]').addClass('off');
				}
			});
			visCheck();
			publish = true;
			$(this).addClass('selected');
			if($('#filter_all').hasClass('selected')){
				$('#filter_all').removeClass('selected');
			}
			else{
				$('#filter_unpublished').toggleClass('selected');
			} 
        	$("table").trigger("sorton",[[[1,0]]]); 
		}
	});
	
	$('#filter_unpublished').click(function(){
		resizeShim();
		if(!$(this).hasClass('selected')){
			$('tbody tr[published="false"]').each(function(){
				if($(this).hasClass('off')){
					$(this).removeClass('off');
				}
			});	
			$('tbody tr[published="true"]').each(function(){
				if(!$(this).hasClass('off')){
					$(this).addClass('off');
					$('[title="'+this.id+'"]').addClass('off');
				}
			});
			//publish = ' [published="false"]';
			publish = false;
			visCheck();
			$(this).addClass('selected');
			if($('#filter_all').hasClass('selected')){
				$('#filter_all').removeClass('selected');
			}
			else{
				$('#filter_published').toggleClass('selected');
			}
			$("table").trigger("sorton",[[[1,0]]]);
		}
	});
	//Filter by lists
	$('#filter_btn').click(function(){
		resizeShim();
		var template = $('#filter_template option:selected').val(),
		category = $('#filter_category option:selected').val(),
		author = $('#filter_author option:selected').val(),
		search_str = '';
		if(template !== ''){
			search_str += '[template="'+template+'"]';
		}
		if(category !== ''){
			search_str += '[category="'+category+'"]';
		}
		if(author !== ''){
			search_str += '[author="'+author+'"]';
		}
		if(search_str === ''){
			search_str = 'tr:not(.meta)';
		}
		if(publish === null){
			$('tbody tr').each(function(){
				if($(this).is(search_str)){
					$(this).removeClass('off');
				}
				else if(!$(this).hasClass('meta')){
					$(this).addClass('off');
				}
			});
		}
		else if(publish){
			$('[publish="true"]').each(function(){
				if($(this).is(search_str)){
					$(this).removeClass('off');
				}
				else if(!$(this).hasClass('meta')){
					$(this).addClass('off');
				}
			});
		}
		else {
			$('[publish="false"]').each(function(){
				if($(this).is(search_str)){
					$(this).removeClass('off');
				}
				else if(!$(this).hasClass('meta')){
					$(this).addClass('off');
				}
			});
		}
		visCheck();
		$("table").trigger("sorton",[[[1,0]]]);
	});
	
	$('#bulk_apply_btn').click(function(){
		var i = 0;
		$('.table_check:checked').each(function(){
			i+=1;
		});
		if(i !== 0){
			actioner();
		}
	});
	
//Table sorter sortMeta is my own
	$("#posts_table").tablesorter({
		headers:{
			0:{sorter:false}
		},
		widgets: ['sortMeta','zebra','deleter']
	}); 
	$("table").trigger("sorton",[[[1,0]]]);
//});
</script>
</body>
</html>
