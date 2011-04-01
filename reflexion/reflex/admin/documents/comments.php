<?php require(ROOT.DS.MAIN.DS.'reflex'.DS.'admin'.DS.'documents'.DS.'headnavfoot'.DS.'header.php');
$slug='ALL';
$filter = array('pending','spam');
if(array_key_exists('post',$_GET)){
	$slug = $_GET['post'] !== '_index_' ? str_replace('_','/',$_GET['post']) : '_index_';
	$filter = 'ALL';
}
loadIntClass('sql_query');
$sql = new Sql_query('comments');
$comments = $sql->getComments($slug,$filter);
$table = '';
$pending = 0;
$approved = 0;
$spam = 0;
for($i = 0; $i < count($comments); ++$i){
			$table .= '
					<tr id="'.$comments[$i]['id'].'" class="'.$comments[$i]['approved'].'" title="comment">
                    	<td><input title="'.$comments[$i]['id'].'" type="checkbox" class="table_check" /></td>
                        <td class="auth">'.$comments[$i]['author_name'].'</td>
                        <td class="comm">'.$comments[$i]['content'].'</td>
                        <td class="resp">'.$comments[$i]['post_slug'].'</td>
                        <td class="date">'.date('m/d/Y',intval($comments[$i]['time'])).'</td>
                    </tr>
					';
			switch($comments[$i]['approved']){
				case 'approved':
					$approved+=1;
					break;
				case 'pending':
					$pending+=1;
					break;
				case 'spam':
					$spam+=1;
					break;
			}
		}
$all = $pending + $approved + $spam;
$sql2 = new Sql_query('posts');
$posts = $sql2->query('SELECT * FROM `posts` WHERE `commentbool`=\'1\' OR `pingbool`=\'1\'');
?>
            <p id="para" class="in_main">You may not view all comments. You may view all the comments for a particular posts, or all pending, or spam comments from your entire site.</p>
            <form class="in_main">
            	<select id="posts_select">
                <?
					if(array_key_exists('post',$_GET)){
						echo '<option value="',$_GET['post'],'">',$_GET['post'],'</option>
						<option value="AP">All Pending and Spam</option>';
						for($i = 0; $i < count($posts); ++$i){
							if($posts[$i]['Post']['slug'] !== $_GET['post']){
								echo '<option value="',str_replace('/','_',$posts[$i]['Post']['slug']),'">',$posts[$i]['Post']['slug'],'</option>
								';
							}
						}
					}
					else{
						echo '<option value="AP">All Pending and Spam</option>
						';
						for($i = 0; $i < count($posts); ++$i){
							echo '
								<option value="',str_replace('/','_',$posts[$i]['Post']['slug']),'">',$posts[$i]['Post']['slug'],'</option>
							';
						}
					}
					
					
				?>
                </select>
                <input type="button" class="button" id="get_comments_btn" value="get" />
            </form>
            <p class="in_main" id="com_filter">
                <a id="filter_all" class="selected">All(<? echo $all;?>)</a>|<a id="filter_pending">Pending(<? echo $pending;?>)</a>|<a id="filter_approved" <? echo array_key_exists('posts',$_GET) ? '':'class="out"'; ?>>Approved(<? echo $approved;?>)</a>|<a id="filter_spam">Spam(<? echo $spam;?>)</a>
            </p>
            <form id="cat_filter" class="in_main">
            	<select id="bulk_actions">
                	<option value="">Bulk Actions</option>
                    <option value="unapprove">Unapprove</option>
                    <option value="approve">Approve</option>
                    <option value="spam">Mark as Spam</option>
                    <option value="delete">Delete</option>
                </select>
                <input type="button" class="button" id="bulk_apply_btn" value="apply" />
                <select id="filter_template">
                	<option value="all">Show All Types</option>
                	<option value="comments">Comments</option>
                    <option value="pingbacks">Pingbacks</option>
                 </select>
                <input type="button" class="button" id="filter_btn" value="filter" />
            </form>
            <table id="comments_table" class="tablesorter">
				<thead>
                	<tr>
                    	<th><input type="checkbox" id="select_all" class="table_check" /></th>
                        <th class="auth">Author</th>
                        <th class="comm">Comment</th>
                        <th class="resp">In Response to</th>
                        <th class="date">Date</th>
                    </tr>
                </thead>
        		<tbody id="table_body">
                	<? echo $table;?>
                </tbody>
            </table>
<? require(ROOT.DS.MAIN.DS.'reflex'.DS.'admin'.DS.'documents'.DS.'headnavfoot'.DS.'footer.php');?>
<script type="text/javascript" src="scripts/admin/tablesorter.jquery.min.js"></script>
<script type="text/javascript">
//$(document).ready(function(){
	
/*********************Comments PAGE SPECIFIC*************************/
	//The tables might be small. The nice thing here is that there doens't have to be a window resize listener
	if($('#main_body').height()<$('#navigation').height()){
		$('#main_body').height($('#navigation').height()+10);
	}
	//Checkboxes
(function(){
	
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
	
	var filter = 'tbody tr';
	var filterStat = function(filter){
		if(!filter){
			filter='';
		}
		$('#filter_all').text('All('+$('tbody tr'+filter).length+')');
		$('#filter_pending').text('Pending('+$('tbody tr.pending'+filter).length+')');
		$('#filter_approved').text('Approved('+$('tbody tr.approved'+filter).length+')');
		$('#filter_spam').text('Spam('+$('tbody tr.spam'+filter).length+')');
	};
//Publish or unpublish filtering
	$('#filter_all').click(function(){
		if(!$(this).hasClass('selected') && !$(this).hasClass('out')){
			$(filter).each(function(){
				$(this).removeClass('off');
			});
			visCheck();
			$(this).addClass('selected');
			if($('#filter_pending').hasClass('selected')){
				$('#filter_pending').removeClass('selected');
			}
			else if($('#filter_approved').hasClass('selected')){
				$('#filter_approved').removeClass('selected');
			}
			else{
				$('#filter_spam').removeClass('selected');
			}
			$("table").trigger("sorton",[[[1,0]]]); 
		}
	});
	
	$('#filter_pending').click(function(){
		if(!$(this).hasClass('selected') && !$(this).hasClass('out')){
			$(filter).each(function(){
				if($(this).hasClass('off') && $(this).hasClass('pending')){
					$(this).removeClass('off');
				}
				else if(!$(this).hasClass('off') && !$(this).hasClass('pending')){
					$(this).addClass('off');
				}
			});
			
			visCheck();
			$(this).addClass('selected');
			if($('#filter_all').hasClass('selected')){
				$('#filter_all').removeClass('selected');
			}
			else if($('#filter_approved').hasClass('selected')){
				$('#filter_approved').removeClass('selected');
			}
			else{
				$('#filter_spam').removeClass('selected');
			}
        	$("table").trigger("sorton",[[[1,0]]]); 
		}
	});
	
	$('#filter_spam').click(function(){
		if(!$(this).hasClass('selected') && !$(this).hasClass('out')){
			$(filter).each(function(){
				if($(this).hasClass('off') && $(this).hasClass('spam')){
					$(this).removeClass('off');
				}
				else if(!$(this).hasClass('off')  && !$(this).hasClass('spam')){
					$(this).addClass('off');
				}
			});
			
			visCheck();
			$(this).addClass('selected');
			if($('#filter_all').hasClass('selected')){
				$('#filter_all').removeClass('selected');
			}
			else if($('#filter_approved').hasClass('selected')){
				$('#filter_approved').removeClass('selected');
			}
			else{
				$('#filter_pending').removeClass('selected');
			}
        	$("table").trigger("sorton",[[[1,0]]]); 
		}
	});
	
	$('#filter_approved').click(function(){
		if(!$(this).hasClass('selected') && !$(this).hasClass('out')){
			$('tbody tr').each(function(){
				if($(this).hasClass('off') && $(this).hasClass('approved')){
					$(this).removeClass('off');
				}
				else if(!$(this).hasClass('off')  && !$(this).hasClass('approved')){
					$(this).addClass('off');
				}
			});
			
			visCheck();
			$(this).addClass('selected');
			if($('#filter_all').hasClass('selected')){
				$('#filter_all').removeClass('selected');
			}
			else if($('#filter_spam').hasClass('selected')){
				$('#filter_spam').removeClass('selected');
			}
			else{
				$('#filter_pending').removeClass('selected');
			}
        	$("table").trigger("sorton",[[[1,0]]]); 
		}
	});
	//Filter by lists
	$('#filter_btn').click(function(){
		var selectId = $('#com_filter a.selected').attr('id');
		if($('#filter_template option:selected').val() === 'pingbacks'){
			filter = 'tbody tr[title="pingback"]';
			filterStat('[title="pingback"]');
			$('tbody tr').each(function(){
				if(!$(this).hasClass('off') && $(this).attr('title') !== 'pingback'){
					$(this).addClass('off');
				}
				else if($(this).hasClass('off') && $(this).attr('title') === 'pingback'){
					$(this).removeClass('off');			
				}
			});
		}
		else if($('#filter_template option:selected').val() === 'comments'){
			filter = 'tbody tr[title="comment"]';
			filterStat('[title="comment"]');
			$('tbody tr').each(function(){
				if(!$(this).hasClass('off') && $(this).attr('title') !== 'comment'){
					$(this).addClass('off');
				}
				else if($(this).hasClass('off') && $(this).attr('title') === 'comment'){
					$(this).removeClass('off');			
				}
			});
		}
		else{
			filter = 'tbody tr';
			filterStat('');
			$('tbody tr').each(function(){
				if($(this).hasClass('off')){
					if(selectId === 'filter_all'){
						$(this).removeClass('off');
					}
					else if(selectId === 'filter_pending' && $(this).hasClass('pending')){
						$(this).removeClass('off');
					}
					else if(selectId === 'filter_approved' && $(this).hasClass('approved')){
						$(this).removeClass('off');
					}
					else if(selectId === 'filter_spam' && $(this).hasClass('spam')){
						$(this).removeClass('off');
					}
				}
			});
		}
		visCheck();
		$("table").trigger("sorton",[[[1,0]]]);
	});
//Getter
	var loading = false;
	var getSetComments = function(post){
		var obj = {
			'post':post
		}
		ajaxObjSend(obj, 'http://<? echo THIS_DOMAIN.'/';?>'+internal_action+'_admin_comments', 'POST', function(success,msg){
			if(success){
				white = new String(msg);
				if(!white.replace(/^\s+|\s+$/g, '').match(/^<tr/)){
					alert(msg);
					loading = false;
				}
				else{
					var i = 0;
					$('#table_body tr').each(function(){
						i+=1;
					})
					loading = false;
					if(i!==0){
						$('#table_body tr').addClass('delete');
						$("table").trigger("sorton",[[[1,0]]]);
					}
					$('#table_body').html(msg);
					filterStat('');
					visCheck();
					$('#in_main a').removeClass('selected');
					$('#in_main a').removeClass('out');
					$('#filter_all').addClass('selected');
					$('#filter_template').val('all');
					if(post === 'AP'){
						$('#filter_approved').addClass('out');	
					}
					else{
						$('#filter_approved').removeClass('out');		
					}
					$("table").trigger("sorton",[[[1,0]]]);
				}
			}
			else{
				alert('Your browser failed to connect to the server. You may have a bad internet connection right now.');
				loading = false;
			}
		});
	}
	$('#get_comments_btn').click(function(){
		if(!loading){
			loading = true;
			if($('#posts_select option:selected').val() === ''){
				loading = false;
				return;
			}
			getSetComments($('#posts_select option:selected').val());		
		}
	});

	var aBid;
	$('#bulk_apply_btn').click(function(){
		if(!loading){
			loading = true;
			var action = $('#bulk_actions option:selected').val(),
			actionNamed = action === 'spam' ? 'marked as '+action: action+'d',
			i = 0,
			num = 0,
			what = $('#filter_template option:selected').val();
			filterer = what === 'all' ? '' : what === 'comments' ? '[title="comments"]' : '[title="pingbacks"]',
			com_filter = $('#com_filter a.selected').attr('id');
			$('.table_check:checked').each(function(){
				i+=1;
			});
			aBid = alert('<span id="number">0</span> of '+i+' comments are '+actionNamed,false);
			$('.table_check:checked').each(function(){
				var obj = {
					'action':action,
					'id':$(this).attr('title')
				}
				ajaxObjSend(obj, 'http://<? echo THIS_DOMAIN.'/';?>'+internal_action+'_comments_actions', 'POST', function(success,msg){
					if(success){
						if(msg.match(/^SUCCESS/)){
							num+=1;
							$('number').text(num);
							$('#'+obj.id).removeClass('approved');
							$('#'+obj.id).removeClass('spam');
							$('#'+obj.id).removeClass('pending');
							switch(action){
								case 'approve':
									$('#'+obj.id).addClass('approved');
									if(com_filter !== 'filter_approved' && com_filter !== 'filter_all')
										$('#'+obj.id).addClass('off');
									break;
								case 'unapprove':
									$('#'+obj.id).addClass('pending');
									if(com_filter !== 'filter_pending' && com_filter !== 'filter_all')
										$('#'+obj.id).addClass('off');
									break;
								case 'spam':
									$('#'+obj.id).addClass('spam');
									if(com_filter !== 'filter_spam' && com_filter !== 'filter_all')
										$('#'+obj.id).addClass('off');
									break;
								case 'delete':
									$('#'+obj.id).addClass('delete');
									$("table").trigger("sorton",[[[1,0]]]);
									break;
							}
							filterStat(filterer);
							if(i === num){
								$('#'+aBid).remove();
								alert('All of the comments have been '+actionNamed);
							}
							loading = false;
							$("table").trigger("sorton",[[[1,0]]]);
						}
						else{
							loading = false;
							$('#'+aBid).remove();
							alert('There was an error communicating with the server');
						}
					}
					else{
						loading = false;
						$('#'+aBid).remove();
						alert('It seems that you might have a bad internet connection.');
					}
				});
			});
		}
	});
//Table sorter sortMeta is my own
var table = $("#comments_table").tablesorter({
		headers:{
			0:{sorter:false},
			2:{sorter:false}
		},
		widgets: ['zebra','deleter']
	});

})();
//});
</script>
</body>
</html>
