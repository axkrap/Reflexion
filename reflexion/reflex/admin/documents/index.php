<?php require(ROOT.DS.MAIN.DS.'reflex'.DS.'admin'.DS.'documents'.DS.'headnavfoot'.DS.'header.php');
require(ROOT.DS.MAIN.DS.'config'.DS.'posts.php');
$sql1 = new Sql_query('templates');
$template_arr = $sql1->selectAll();
$sql2 = new Sql_query('categories');
$cat_arr = $sql2->selectAll();
$sql3 = new Sql_query('authors');
$aut_arr = $sql3->selectAll();
if(array_key_exists('edit',$_GET)){
	$slug = $_GET['edit'] !== '_index_' ? str_replace('_','/',$_GET['edit']) : $_GET['edit'];
	if($slug !== ''){
		$sql4 = new Sql_query('posts');
		$post_arr = $sql4->selectWhere('slug',$slug);
		$post_arr = $post_arr['Post'];
	}
}
$post = isset($post_arr) ? true : false;
$cat = '';
if(isset($post_arr) && $post_arr['category'] !== 'none'){
	$slug = explode('/',$slug);
	$cat = $slug[0];
	array_shift($slug);
	$slug = $slug[0];
}
$template_table = '';
$category_table = '';
$author_table = '';
if($post){
	$template_table = '
				<option value="'.$post_arr['template'].'">'.$post_arr['template'].'</option>
				';
	for($i=0; $i < count($template_arr); ++$i){
		$value = $template_arr[$i]['Template']['name'];
		if($value != $post_arr['template']){
			$template_table .= '
				<option value="'.$value.'">'.$value.'</option>
				';
		}
	}
}
else{
	$template_table = '
		<option value="'.DEFAULT_TEMPLATE.'">'.DEFAULT_TEMPLATE.'</option>
		';
	for($i=0; $i < count($template_arr); ++$i){
		$value = $template_arr[$i]['Template']['name'];
		if($value === DEFAULT_TEMPLATE) continue;
			$template_table .= '
				<option value="'.$value.'">'.$value.'</option>
			';
	}
}
//Category
if(URL_STATE === 'day-name' || URL_STATE === 'month-name'){
	$category_table = '
	<div class="left ten">
    <label for="category" class="label">Category:</label><br />
    <select class="select" id="category" name="category">
	<option value="none">none</option>
	';
	if(URL_STATE === 'day-name'){
		$category_table .= '
		<option value="'.date('m/d/Y',time()).'">'.date('m/d/Y',time()).'</option>
		';
	}
	else{
		$category_table .= '
		<option value="'.date('m/Y',time()).'">'.date('m/Y',time()).'</option>
		';
	}
	$category_table .='
	</select>
	 </div>
	';
}
else if(URL_STATE !== 'name'){
	$category_table = '
	<div class="left ten">
	<label for="category" class="label">Category:</label><br />
    <select class="select" id="category" name="category">
	';
	if($post){
		$category_table .= '
		<option value="'.$post_arr['category'].'">'.$post_arr['category'].'</option>
		'; 
		for($i=0; $i < count($cat_arr); ++$i){
			$value = $cat_arr[$i]['Categorie']['category'];
			if($value != $post_arr['category']){
				$category_table.= '
					<option value="'.$value.'">'.$value.'</option>
				';
			}
		}
	}
	else{
		$category_table .=  '
			<option value="'.DEFAULT_CATEGORY.'">'.DEFAULT_CATEGORY.'</option>
			';
		for($i=0; $i < count($cat_arr); ++$i){
			$value = $cat_arr[$i]['Categorie']['category'];
			if($value === DEFAULT_CATEGORY) continue;
				$category_table.= '
					<option value="'.$value.'">'.$value.'</option>
					';
		}
	}
	$category_table .='
	<option value="NEW_CAT">+Add New Category...</option>
	</select>
	 </div>
	';
}

if($post){
	$author_table = '
		<option value="'.$post_arr['author'].'">'.$post_arr['author'].'</option>
	';
	for($i=0; $i < count($aut_arr); ++$i){
		$value = $aut_arr[$i]['Author']['author'];
		if($value != $post_arr['author']){
			$author_table.= '
				<option value="'.$value.'">'.$value.'</option>
			';
		}
	}
}
else{
	$author_table= '
	<option value="'.DEFAULT_AUTHOR.'">'.DEFAULT_AUTHOR.'</option>
	';
	for($i=0; $i < count($aut_arr); ++$i){
		$value = $aut_arr[$i]['Author']['author'];
		if($value === DEFAULT_AUTHOR) continue;
		$author_table .= '
			<option value="'.$value.'">'.$value.'</option>
		';
	}
}
?>
       		<form accept-charset="utf-8" id="np_postings">
       			<input type="text" class="text" id="np_title"<? echo ($post ? 'value="'.$post_arr['title'].'"' : 'title="Enter Title Here"' ); ?>  /><br />
            	<span id="url" title="<? echo 'http://'.THIS_DOMAIN.'/'.($post ? $cat :'');?>"><? echo 'http://'.THIS_DOMAIN.'/'.($post ? $cat : '');?></span><input type="text" class="text" id="np_post" <? echo $post ? 'value="'.$slug.'"' : 'title="Enter Post-Slug Here"';?> /><br />
                <input type="text" class="text" id="np_description" <? echo ($post ? 'value="'.$post_arr['description'].'"': 'title="Write a brief description of this post"');?> /><br />
                <input type="text" class="text" id="np_pingbacks" <? echo ($post ? 'value="'.$post_arr['pingbacks'].'"' : 'title="Enter pingbacks here; seperate urls with spaces"');?>  />
                <div class="left ten">
                <label for="template" class="label">Template:</label><br />
                <select class="select" id="template" name="template">
                <? echo $template_table;?>
                </select>
                </div>
                   <? echo $category_table;?>
                <div class="left ten">
                <label for="author" class="label">Author:</label><br />
                <select class="select" id="author" name="author">
                     <? echo $author_table;?>
                    <option value="NEW_AUT">+Add New Author...</option>
                </select>
                </div>
                <div class="left ten">
                <label for="comments_on" class="label" id="label_comments" >Comments On:</label>
                <input type="checkbox" id="comments_on" name="comments_on" <? 
					if(!$post || intval($post_arr['commentbool']) != 0){
						echo 'checked="checked"';
					}
				?> /><br />
                <label for="trackbacks_on" class="label" id="label_trackbacks">Pingbacks On:</label>
                <input type="checkbox" id="trackbacks_on" name="pingbacks_on" <? 
					if(!$post || intval($post_arr['pingbool']) != 0){
						echo 'checked="checked"';
					}
				?> />
                </div>
       		</form><br /><br /><br />
            <div id="areacontainer">
       			<span id="htmTog_btn" class="Tog_btn desel">HTML</span>
                <span id="visTog_btn" class="Tog_btn">Visual</span>
            	<span id="mediacontainer">
            		<span class="Stext">Insert/Upload Media:</span>
       				<span id="media" class="ico" title="Add Media"></span> 
            	</span>
       		</div><!--AreaContainer-->
            <div id="editor_container">
            	<textarea id="MCEeditor" class="editor"><? echo ($post ? $post_arr['post'] : '');?></textarea>
            </div>
            <form id="np_misc">
                	<input type="button" id="save_btn" value="Save as Draft" class="left button" />
                    <input type="button" id="publish_btn" value="Publish" class="right button" />
            </form><br /><br /><br />
            <h3 class="post_head closed"><span></span>Advanced Options</h3>
            <div class="closed">
            <input type="checkbox" id="cachepub" name="any" /><label for="cachepub">Cache Post on the publication of new Posts (recommended for index, archive, et al pages).</label><br />
            <label for="postdate">If saved as draft, publish on this date:</label><br />
            <input type="text" name="postdate" id="postdate" class="cal_text" title="mm/dd/YYYY" />
            <select id="post_hour">
            	<option value="0">Hour</option>
        	    <option value="0">00:00</option> 
				<option value="1">01:00</option> 
				<option value="2">02:00</option> 
				<option value="3">03:00</option> 
				<option value="4">04:00</option> 
				<option value="5">05:00</option> 
				<option value="6">06:00</option> 
				<option value="7">07:00</option> 
				<option value="8">08:00</option> 
				<option value="9">09:00</option> 
				<option value="10">10:00</option> 
				<option value="11">11:00</option> 
				<option value="12">12:00</option> 
				<option value="13">13:00</option> 
				<option value="14">14:00</option> 
				<option value="15">15:00</option> 
				<option value="16">16:00</option> 
				<option value="17">17:00</option> 
				<option value="18">18:00</option> 
				<option value="19">19:00</option> 
				<option value="20">20:00</option> 
				<option value="21">21:00</option> 
				<option value="22">22:00</option> 
				<option value="23">23:00</option>
            </select>
            <select id="post_min">
            	<option value="0">Minute</option>
    	        <option value="0">00</option> 
				<option value="1">01</option> 
				<option value="2">02</option> 
				<option value="3">03</option> 
				<option value="4">04</option> 
				<option value="5">05</option> 
				<option value="6">06</option> 
				<option value="7">07</option> 
				<option value="8">08</option> 
				<option value="9">09</option> 
				<option value="10">10</option> 
				<option value="11">11</option> 
				<option value="12">12</option> 
				<option value="13">13</option> 
				<option value="14">14</option> 
				<option value="15">15</option> 
				<option value="16">16</option> 
				<option value="17">17</option> 
				<option value="18">18</option> 
				<option value="19">19</option> 
				<option value="20">20</option> 
				<option value="21">21</option> 
				<option value="22">22</option> 
				<option value="23">23</option> 
				<option value="24">24</option> 
				<option value="25">25</option> 
				<option value="26">26</option> 
				<option value="27">27</option> 
				<option value="28">28</option> 
				<option value="29">29</option> 
				<option value="30">30</option> 
				<option value="31">31</option> 
				<option value="32">32</option> 
				<option value="33">33</option> 
				<option value="34">34</option> 
				<option value="35">35</option> 
				<option value="36">36</option> 
				<option value="37">37</option> 
				<option value="38">38</option> 
				<option value="39">39</option> 
				<option value="40">40</option> 
				<option value="41">41</option> 
				<option value="42">42</option> 
				<option value="43">43</option> 
				<option value="44">44</option> 
				<option value="45">45</option> 
				<option value="46">46</option> 
				<option value="47">47</option> 
				<option value="48">48</option> 
				<option value="49">49</option> 
				<option value="50">50</option> 
				<option value="51">51</option> 
				<option value="52">52</option> 
				<option value="53">53</option> 
				<option value="54">54</option> 
				<option value="55">55</option> 
				<option value="56">56</option> 
				<option value="57">57</option> 
				<option value="58">58</option> 
				<option value="59">59</option>
            </select>
            <br />
            <label for="commentsdate">Keep comments open until:</label><br />
            <input type="text" name="commentsdate" id="commentsdate" class="cal_text" title="mm/dd/YYYY"/>
            <select id="comm_hour">
        	    <option value="0">Hour</option>
        	    <option value="0">00:00</option> 
				<option value="1">01:00</option> 
				<option value="2">02:00</option> 
				<option value="3">03:00</option> 
				<option value="4">04:00</option> 
				<option value="5">05:00</option> 
				<option value="6">06:00</option> 
				<option value="7">07:00</option> 
				<option value="8">08:00</option> 
				<option value="9">09:00</option> 
				<option value="10">10:00</option> 
				<option value="11">11:00</option> 
				<option value="12">12:00</option> 
				<option value="13">13:00</option> 
				<option value="14">14:00</option> 
				<option value="15">15:00</option> 
				<option value="16">16:00</option> 
				<option value="17">17:00</option> 
				<option value="18">18:00</option> 
				<option value="19">19:00</option> 
				<option value="20">20:00</option> 
				<option value="21">21:00</option> 
				<option value="22">22:00</option> 
				<option value="23">23:00</option>
            </select>
            <select id="comm_min">
    	        <option value="0">Minute</option>
    	        <option value="0">00</option> 
				<option value="1">01</option> 
				<option value="2">02</option> 
				<option value="3">03</option> 
				<option value="4">04</option> 
				<option value="5">05</option> 
				<option value="6">06</option> 
				<option value="7">07</option> 
				<option value="8">08</option> 
				<option value="9">09</option> 
				<option value="10">10</option> 
				<option value="11">11</option> 
				<option value="12">12</option> 
				<option value="13">13</option> 
				<option value="14">14</option> 
				<option value="15">15</option> 
				<option value="16">16</option> 
				<option value="17">17</option> 
				<option value="18">18</option> 
				<option value="19">19</option> 
				<option value="20">20</option> 
				<option value="21">21</option> 
				<option value="22">22</option> 
				<option value="23">23</option> 
				<option value="24">24</option> 
				<option value="25">25</option> 
				<option value="26">26</option> 
				<option value="27">27</option> 
				<option value="28">28</option> 
				<option value="29">29</option> 
				<option value="30">30</option> 
				<option value="31">31</option> 
				<option value="32">32</option> 
				<option value="33">33</option> 
				<option value="34">34</option> 
				<option value="35">35</option> 
				<option value="36">36</option> 
				<option value="37">37</option> 
				<option value="38">38</option> 
				<option value="39">39</option> 
				<option value="40">40</option> 
				<option value="41">41</option> 
				<option value="42">42</option> 
				<option value="43">43</option> 
				<option value="44">44</option> 
				<option value="45">45</option> 
				<option value="46">46</option> 
				<option value="47">47</option> 
				<option value="48">48</option> 
				<option value="49">49</option> 
				<option value="50">50</option> 
				<option value="51">51</option> 
				<option value="52">52</option> 
				<option value="53">53</option> 
				<option value="54">54</option> 
				<option value="55">55</option> 
				<option value="56">56</option> 
				<option value="57">57</option> 
				<option value="58">58</option> 
				<option value="59">59</option>
            </select>
            <br />
            </div>
            
<?php require(ROOT.DS.MAIN.DS.'reflex'.DS.'admin'.DS.'documents'.DS.'headnavfoot'.DS.'footer.php');?>

<script type="text/javascript" src="scripts/tiny_mce/jquery.tinymce.js"></script>
<script type="text/javascript">
/*TO DO
1. Make flash uplaoder and html uploader work together $().tinymce().execCommand('mceInsertContent',false,'<b>Hello world!!</b>'); works!
*/

/*********************INDEX SPECIFIC CODE***************************/
$(document).ready(function(){
	var title69 = true, description = true, index = true, admin='<? echo ADMIN_URL;?>', ping='<? echo PINGBACK;?>',rss='<? echo RSS_URI;?>';
	var validatePost =  function(){
		var urlExp = /^[a-z0-9]+[a-z0-9-]*$/, url = $('#np_post').val();
		if($('#np_title').val() === $('#np_title').attr('title')){
			alert('Please enter a title for your post.');
			return false;
		}
		else if($('#np_title').val().length > 69 && title69){
			alert('It is recommended for SEO purposes that you use less than 69 characters (including spaces) in your titles. Search engines like Google will penalize you for having more. If you know what you\'re doing though, have at it: We won\'t show you this notice again during this session.', 8e3);
			title69 = false;
			return false;
		}
		else if($('#category option:selected').val() !== 'none' && url === $('#np_post').attr('title')){
			alert('You cannot have an empty url-slug for a category. You can name a slug after a category though.')
			return false;
		}
		else if(url === $('#np_post').attr('title') && index){
			alert('Please enter a URL-slug for your post. If you don\'t enter a URL-slug the index file of the entire website will be replaced. We won\'t show you this notice again. If the index file exists already, we will warn you of that.', 8e3);
			index = false;
			return false;
		}
		else if(url === admin || url ===  ping || url === rss){
			var string = url === admin ? 'admin-url' : url === ping ? 'pingback-url' : 'rss-url';
			alert('You cannot name your post the same as the '+string+', which is "'+url+'". If you really desire to have your post\'s url-slug be "'+url+'", then you can go into administrative settings and change the '+string+', but you will never be able to share a url-slug with the '+string+'.', 8e3);
			return false;
		}
		else if($('#category option:selected').val() !== 'none' && url.match(/^<? echo ACTION_VAR;?>/)){
			alert('No uncategorized post can start with the prefix "<? echo ACTION_VAR;?>_", as this is the external class prefix.');
			return false;
		}
		else if($('#category option:selected').val() !== 'none' && url.match(/^<? echo INTERNAL_ACTION;?>/)){
			alert('No uncategorized post can start with the prefix "<? echo INTERNAL_ACTION;?>_", as this is the internal class prefix.');
			return false;
		}
		else if(!url.match(urlExp) && index){
			if(url.toLowerCase().match(urlExp)){
				alert('Your URL-slug must be all lower-case. For more information on this please look at the documentation.', 5e3);
				return false;
			}
			else{
				alert('Your URL-slug can only be made up of lower-case alphanumeric characters and the En Dash(-). Many browsers cannot recognize non-ASCII characters in an HTTP request.', 6e3);
				return false;
			}
		}
		else if($('#np_description').val() === $('#np_description').attr('title') && description){
			description = false;
			alert('We recommend that you enter a description of your posts. Do you wish to publish anyways',6e3);
			return false;
		}
		else{
			return true;
		}
	};
//This object stores the css files, etc of the templates, it is here from a server-side include
	var templateObj =  <? 
					echo '{';
					$first = true;
					for($i=0; $i < count($template_arr); ++$i){
						echo ($first?'':','),'"',$template_arr[$i]['Template']['name'],'":{"css":"',$template_arr[$i]['Template']['css'],'"}';
						$first = false;
					}
					echo '}';
					?>, previousTemp = 'index';
//Change the width of the url input based on the static url's width
	$('#np_post').css('width', 660-$('#url').width());
//All text forms get inline values to help the user
	<? if(!$post){ ?>//start php bracket
	$('.text, .cal_text').each(function(){
		this.value = this.title;
	
		$(this).focus(function(){
			if(this.value == this.title){
				this.value = '';
				$(this).css('color','#495b6c');
			}
		});
		
		$(this).blur(function(){
			if(this.value == ''){
				this.value = this.title;
				$(this).css('color','#9eb2c4');
			}
		});
	});<? //End php bracket
	}
	?>
//If the template changes load up 
	$('#template').change(function(){
		$('#MCEeditor').tinymce().remove();
		initEditor('#MCEeditor');
	});
//If the author changes to NEW_AUT
	$('#author').change(function(){
		if($('#author option:selected').val() === 'NEW_AUT'){
			prompt('Please enter a new author. If you keep the field empty, no new author will be added', function(msg){
				if(msg == 'none'){
					alert('"none" is a reserved word for the author category. Why...just, why?');
					return;
				}
				if(msg != ''){
					ajaxObjSend(
						{'author':msg},
						internal_action+'_add_new_author',
						'POST',
						function(bool, txt){
							if(!bool){
								alert('A server error occurred, and "'+msg+'" was not added as a new author.');
							} 
							else if(txt=='yes'){
								alert('"'+msg+'" was added successfully.');
								$('#author').prepend('<option value="'+msg+'">'+msg+'</option>');
							}
							else{
								alert(txt);
							}
						}
					);//EndAjax
				}//end if
			});//end prompt
		}
	});
//If the category changes reflect it in the URL
	$('#category').change(function(){
		if($('#category option:selected').val() === 'NEW_CAT'){
			prompt('Please enter a new category. If you keep the field empty, no new category will be added', function(msg){
				if(msg == ''){
					return;	
				}
				if(!msg.match(/^[a-z0-9]+[a-z0-9-]*$/)){
					alert('Your category has to be url friendly. No non-ASCII characters, and no upper-case characters.');
					return;
				}
				if(msg == 'none'){
					alert('"None" is a reserved word for the category section. Do you really want a category called "none"? Weirdo.');
					return;
				}
				else{
					ajaxObjSend(
						{'category':msg},
						internal_action+'_add_new_category',
						'POST',
						function(bool,txt){
							if(!bool){
								alert('A server error occurred, and "'+msg+'" was not added as a new category.');
							}
							else if(txt=='yes'){
								alert('"'+msg+'" was added successfully');
								$('#category').prepend('<option value="'+msg+'">'+msg+'</option>');
							}
							else {
								alert(txt);
							}
						}
					);//end ajax
				}//end if
			});//end prompt
		}
		else{
			if($('#category option:selected').val() === 'none'){
				$('#url').text($('#url').attr('title'));
				$('#np_post').css('width', 660 - $('#url').width());
			}
			else{
				$('#url').text($('#url').attr('title')+$('#category option:selected').val()+'/');
				$('#np_post').css('width', 660 - $('#url').width());
			}
		}
	});
//Initialize WYSIWYG editor function
	function initEditor(element){
		if(!templateObj[$('#template option:selected').val()].css==='none'){
			$(element).tinymce({
				script_url : 'scripts/tiny_mce/tiny_mce.js',
		   		theme : "advanced",
	 			 plugins:"advhr,advimage,advlink,inlinepopups,preview,media,searchreplace,contextmenu,paste,fullscreen,visualchars,nonbreaking,wordcount,advlist,save,xhtmlxtras",
	  			theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,bullist,numlist,blockquote,|,outdent,indent,|,justifyleft,justifycenter,justifyright,justifyfull,|,link,unlink,charmap,media,removeformat,|,formatselect,|,undo,redo,|,fullscreen",
				theme_advanced_buttons2 : "",
        		theme_advanced_buttons3 : "",
		  		theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left"
	   		});
		}
		else{
			$(element).tinymce({
				script_url : 'scripts/tiny_mce/tiny_mce.js',
		   		theme : "advanced",
	 			 plugins:"advhr,advimage,advlink,inlinepopups,preview,media,searchreplace,contextmenu,paste,fullscreen,visualchars,nonbreaking,wordcount,advlist,save,xhtmlxtras",
	  			theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,bullist,numlist,blockquote,|,outdent,indent,|,justifyleft,justifycenter,justifyright,justifyfull,|,link,unlink,charmap,media,removeformat,|,formatselect,|,undo,redo,|,fullscreen",
				theme_advanced_buttons2 : "",
        		theme_advanced_buttons3 : "",
		  		theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left",
				content_css : templateObj[$('#template option:selected').val()].css
	   		});
		}
	};
		
	initEditor('#MCEeditor');
//Toggle HTML view or WYSIWYG view
	$('#visTog_btn').click(function(){
		id = 'MCEeditor';
		if($('#visTog_btn').hasClass('desel'))
		{
			if(tinyMCE.get(id)){
				alert('There was an error with Tiny MCE, please reload the page. If the problem persists your effort to report it will be greatly appreciated.');
			}
			else{
				initEditor('#MCEeditor');
				$('#visTog_btn').removeClass('desel');
				$('#htmTog_btn').addClass('desel');
				$('#mediacontainer').css('display','inherit');
			}
		}
	});
	
	$('#htmTog_btn').click(function(){
		id = 'MCEeditor';
		if($('#htmTog_btn').hasClass('desel'))
		{
			if(tinyMCE.get(id)){
				$('#MCEeditor').tinymce().remove();
				$('#htmTog_btn').removeClass('desel');
				$('#visTog_btn').addClass('desel');
				$('#mediacontainer').css('display','none');
			}
			else{
				alert('There was an error with Tiny MCE, please reload the page. If the problem persists your effort to report it will be greatly appreciated.');
			}
		}
	});
//Publsih or save
	//HTML encoder for any weird characters in the title, or description
	function htmlEncode(string){
  		var el = document.createElement('i');
 		$(el).append(string);
		string = $(el).html();
		$(el).remove();
		return  string;
	};
	
	function resetPage(bool,msg){
		if(bool){
			if(msg == 'file_exists'){
				alert('The post you are publishing exists already. Go ahead and publish it if you want to, but you will replace that post and it will be lost forever. We won\'t show you this message again for this slug name.', 8e3);
				sendObj["rewrite"] = true;
			}
			else if(msg == 'failed_write'){
				alert('The server failed to write your file. This is likely a server error and not an error of the Reflexion software. If the problem persists look up error#0003 in the Reflexion documentation for a fuller explanation', 8e3);
			}
			else if(msg != 'yes'){
				alert(msg);
			}
			else{
				$('.text').each(function(){
					this.value = this.title;
					$(this).css('color','#9eb2c4');
				});
				alert(msg,6e3);
				if(sendObj["rewrite"]){
					delete sendObj["rewrite"];
				}
			}
		}
		else{
			alert('Your browser failed to request to the server. Your internet connection may be bad.');
		}
	};
	//The object to send to the server
	var sendObj = {
		"post":function(){return $('#MCEeditor').val();},
		"title":function(){ return htmlEncode($('#np_title').val());},
		"slug": function(){
			var string = $('#np_post').val() === $('#np_post').attr('title') ? '_index_' : $('#np_post').val();
			return string;
		},
		"description":function(){
			var string = $('#np_description').val() === $('np_description').attr('title') ? '' : htmlEncode($('#np_description').val());
			return string;
		},
		"template":function(){ return $('#template option:selected').val();},
		"category":function(){ return $('#category option:selected').val();},
		"author":function(){ return $('#author option:selected').val();},
		"commentbool":function(){ return $('#comments_on').is(':checked');},
		"pingbacks": function(){
			var string = $('#np_pingbacks').val() === $('#np_pingbacks').attr('title') ? '' : $('#np_pingbacks').val();
			return string;
		},
		"publish":null, 
		"pingbool":function(){ return $('#pingbacks_on').is(':checked');},
		"cachepub":function(){ return $('#cachepub').is(':checked');},
		"postdate":function(){  var date = $('#postdate').val() === 'mm/dd/YYYY' ? '01/01/2030' : $('#postdate').val(); 
		return date+'/'+ $('#post_hour').val()+'/'+$('#post_min').val();},
		"comments_date":function(){ var date = $('#commentsdate').val() ==='mm/dd/YYYY' ? '01/01/2030' :$('#commentsdate').val();
			return date+'/'+ $('#comm_hour').val()+'/'+$('#comm_min').val();}
		<? echo ($post ? ', "rewrite":true' : '');?>
	};
		
	$('#publish_btn').click(function(){
		if(validatePost()){
			sendObj["publish"] = true;
			ajaxObjSend(sendObj, internal_action+'_create_new_post', 'POST',function(bool,msg){resetPage(bool,msg);});
		}
	});
	
	$('#save_btn').click(function(){
		if(validatePost()){
			sendObj["publish"] = false;
			ajaxObjSend(sendObj, internal_action+'_create_new_post', 'POST',function(bool,msg){resetPage(bool,msg);}); 
		}
	});
	
	$('.post_head').click(function(){
		if($(this).hasClass('closed')){
			$(this).removeClass('closed').addClass('open').next('div').removeClass('closed').addClass('open');
		}
		else{
			$(this).removeClass('open').addClass('closed').next('div').removeClass('open').addClass('closed');
		}
	});
	

});
</script>
</body>
</html>