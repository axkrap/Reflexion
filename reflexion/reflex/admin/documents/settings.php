<?php require(ROOT.DS.MAIN.DS.'reflex'.DS.'admin'.DS.'documents'.DS.'headnavfoot'.DS.'header.php');
require(ROOT.DS.MAIN.DS.'config'.DS.'posts.php');
require(ROOT.DS.MAIN.DS.'config'.DS.'comments.php');
require(ROOT.DS.MAIN.DS.'config'.DS.'rss.php');
loadIntClass('sql_query');
$sql1 = new Sql_query('templates');
$templates = $sql1->selectAll();
$sql2 = new Sql_query('categories');
$categories = $sql2->selectAll();
$sql3 = new Sql_query('authors');
$authors = $sql3->selectAll();
$moderationStr = '';
for($i = 0; $i < count($moderate); ++$i){
	if($i !== 0 ) $moderationStr .= ', ';
	$moderationStr .= $moderate[$i];
}
$spamStr = '';
for($i = 0; $i < count($spam); ++$i){
	if($i !== 0 ) $spamStr .= ', ';
	$spamStr .= $spam[$i];
}
$opt_temp = '
<option value="'.DEFAULT_TEMPLATE.'">'.DEFAULT_TEMPLATE.'</option>
';
$opt_cat = '
<option value="'.DEFAULT_CATEGORY.'">'.DEFAULT_CATEGORY.'</option>
';
$opt_aut = '
<option value="'.DEFAULT_AUTHOR.'">'.DEFAULT_AUTHOR.'</option>
';
for($i = 0; $i < count($templates); ++$i){
	$value = $templates[$i]['Template']['name'];
	if($value === DEFAULT_TEMPLATE) continue;
	$opt_temp .= '
	<option value="'.$value.'">'.$value.'</option>
	';
}
for($i = 0; $i < count($categories); ++$i){
	$value = $categories[$i]['Categorie']['category'];
	if($value === DEFAULT_CATEGORY) continue;
	$opt_cat .= '
	<option value="'.$value.'">'.$value.'</option>
	';
}
for($i = 0; $i < count($authors); ++$i){
	$value = $authors[$i]['Author']['author'];
	if($value === DEFAULT_AUTHOR) continue;
	$opt_aut .= '
	<option value="'.$value.'">'.$value.'</option>
	';
}
?>
           	<h3 class="post_head closed" id="post_set"><span></span>Master Settings</h3>
            <div class="closed">
            	<form id="master_form" id="post_set">
                	 <div>It is recommended that you not change any Master Settings after a site has been launched. You can cause serious errors to occur on your website, as well as confuse your users and search engines. Remember: these words will become reserved words, you will not be able to name posts after them.</div><br />
                     <label for="admin_url">Admin URL:</label><br />
                    <input type="text" name="admin_url" id="admin_url" class="set_text" value="<? echo ADMIN_URL;?>" /><br />
                    <label for="external_url">External Server Prefix:</label><br />
                    <input type="text" name="external_url" id="external_url" class="set_text" value="<? echo ACTION_VAR;?>" /><br />
                    <label for="internal_url">Internal Server Prefix:</label><br />
                    <input type="text" name="internal_url" id="internal_url" class="set_text" value="<? echo INTERNAL_ACTION;?>" /><br />
                    <label for="ping_url">Pingback URI:</label><br />
                    <input type="text" name="ping_url" id="ping_url" class="set_text" value="<? echo PINGBACK;?>" /><br />
                    <label for="timezone">Set Timezone:</label><br />
                    <select name="timezone" id="timezone">
      					<option value="Kwajalein">(GMT -12:00) Eniwetok, Kwajalein</option>
      					<option value="Pacific/Midway">(GMT -11:00) Midway Island, Samoa</option>
      					<option value="Pacific/Honolulu">(GMT -10:00) Hawaii</option>
      					<option value="America/Anchorage">(GMT -9:00) Alaska</option>
      					<option value="America/Los_Angeles">(GMT -8:00) Pacific Time (US &amp; Canada)</option>
      					<option value="America/Denver">(GMT -7:00) Mountain Time (US &amp; Canada)</option>
      					<option value="America/Tegucigalpa">(GMT -6:00) Central Time (US &amp; Canada), Mexico City</option>
      					<option value="America/New_York">(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
                        <option value="America/Caracas">(GMT -4:30) Venezuela, Caracas</option>
      					<option value="America/Halifax">(GMT -4:00) Atlantic Time (Canada), La Paz</option>
      					<option value="America/St_Johns">(GMT -3:30) Newfoundland</option>
      					<option value="America/Sao_Paulo">(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>
      					<option value="Atlantic/South_Georgia">(GMT -2:00) Mid-Atlantic</option>
      					<option value="Atlantic/Azores">(GMT -1:00 hour) Azores, Cape Verde Islands</option>
      					<option value="Europe/Dublin">(GMT) Western Europe Time, London, Lisbon, Casablanca</option>
      					<option value="Europe/Belgrade">(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris</option>
      					<option value="Europe/Minsk">(GMT +2:00) Kaliningrad, South Africa</option>
      					<option value="Asia/Kuwait">(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
      					<option value="Asia/Tehran">(GMT +3:30) Tehran</option>
      					<option value="Asia/Muscat">(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
      					<option value="Asia/Kabul">(GMT +4:30) Kabul</option>
      					<option value="Asia/Yekaterinburg">(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
      					<option value="Asia/Kolkata">(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</option>
      					<option value="Asia/Katmandu">(GMT +5:45) Kathmandu</option>
      					<option value="Asia/Dhaka">(GMT +6:00) Almaty, Dhaka, Colombo</option>
      					<option value="Asia/Rangoon">(GMT +7:00) Bangkok, Hanoi, Jakarta</option>
      					<option value="Asia/Brunei">(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>
      					<option value="Asia/Seoul">(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
      					<option value="Australia/Darwin">(GMT +9:30) Adelaide, Darwin</option>
      					<option value="Australia/Canberra">(GMT +10:00) Eastern Australia, Guam, Vladivostok</option>
      					<option value="Asia/Magadan">(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>
      					<option value="Pacific/Fiji">(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
					</select><br />
                    <label for="rss_url">RSS feed URI:</label><br />
                    <input type="text" name="rss_url" id="rss_url" class="set_text" value="<? echo RSS_URI;?>" /><br />              	<input type="checkbox" id="development_mode" name="any" <? if(DEVELOPMENT_ENVIRONMENT) echo 'checked="checked"';?> /><label for="development_mode">Developer Mode On (print server errors - not recommended)</label><br />
                    <input type="button" class="button" id="master_btn" title="master" value="Save Changes" />
                </form>
            </div><br />
            <h3 class="post_head closed"><span></span>Posts Settings</h3>
            <div class="closed">
            	<form id="posts_form">
                	<label for="siteTag">Website Tagline:</label><br />
                    <input type="text" name="siteTag" id="sitetag" class="set_text" value="<? echo SITE_TAG;?>" /><br />
                    <label for="default_temp">Default Template:</label><br />
                    <select name="default_temp" id="default_temp">
                    	<? echo $opt_temp;?>
                    </select><br />
                    <label for="default_cat">Default Category:</label><br />
                    <select name="default_cat" id="default_cat">
                    	<? echo $opt_cat;?>
                    </select><br />
                    <label for="default_aut">Default Author:</label><br />
                    <select name="default_aut" id="default_aut">
                    	<? echo $opt_aut;?>
                    </select><br />
                    <input type="button" class="button" id="posts_btn" title="posts" value="Save Changes" />
                </form>
            </div><br />
            <h3 class="post_head closed"><span></span>Comments Settings</h3>
            <div class="closed">
            	<form id="comment_form">
                <label for="email">Email Address to Notify about comments:</label><br />
                <input type="text" class="set_text" name="email" id="email" value="<? echo COMMENT_EMAIL;?>" /><br />
                <label>Email Me When:</label><br />
                <input type="checkbox" class="check" id="email_any" name="any" <? if(COMMENT_EMAIL_ANY) echo 'checked="checked"';?> /><label for="email_any">Anyone comments</label><br />
                <input type="checkbox" class="check" id="email_held" name="held" <? if(COMMENT_EMAIL_MOD) echo 'checked="checked"';?> /><label for="email_held">A comment is held for moderation</label><br />
                <label>Before a comment appears:</label><br />
                <input type="checkbox" class="check" id="comm_admin" name="admin" <? if(COMMENT_APPROV_ADMIN) echo 'checked="checked"';?> /><label for="comm_admin">An administrator must approve the comment</label><br />
                <input type="checkbox" class="check" id="comm_author" name="aut" <? if(COMMENT_APPROV_AUTHOR) echo 'checked="checked"';?> /><label for="comm_author">The comment author must have a previously approved comment</label><br />
                <label>Comment Moderation</label><br />
                <div class="check">1. Hold a comment in moderation if it contains more than <input type="text" class="small" id="links" value="<? echo COMMENT_APPROV_LINKS;?>" /> links in it.</div>
                <div class="check"><label for="moderate_words" >2. Hold a comment in moderation if it contains the following words, phrases, html tags, or urls (seperate with commas)</label></div>
                <textarea class="check" id="moderate_words"><? echo $moderationStr;?></textarea><br /><br />
                <div class="check"><label for="spam_words"> 3. Mark a comment as spam if it contains the following words, phrases, html tags, or urls (seperate with commas)</label></div>
                <textarea class="check" id="spam_words"><? echo $spamStr;?></textarea><br /><br />
                <input type="button" class="button" id="comments_btn" title="comments" value="Save Changes" />
                </form>
            </div><br />
            <h3 class="post_head closed"><span></span>RSS</h3>
            <div id="rss" class="closed">
            	<form id="rss_form">
                	<input type="checkbox" class="check" id="rss_direct" name="rss_direct" <? if(RSS_REDIRECT) echo 'checked="checked"';?> /><label for="rss_direct">RSS redirects to an RSS distribution site (like feedburner).</label><br />
                	<label for="rss_where">URL to redirect to:</label><br />
                    <input type="text" name="rss_where" id="rss_where" class="set_text" value="<? echo RSS_WHERE;?>" /><br />
                    <label for="rss_regex">REGEXP to check for RSS service:</label><br />
                    <input type="text" name="rss_regexp" id="rss_regexp" class="set_text" value="<? echo AGENT_REGEXP;?>" /><br />
                    <label for="rss_title">RSS title:</label><br />
                    <input type="text" name="rss_title" id="rss_title" class="set_text" value="<? echo RSS_TITLE;?>" /><br />
                    <label for="rss_description">RSS description:</label><br />
                    <input type="text" name="rss_description" id="rss_description" class="set_text" value="<? echo RSS_DESCRIPTION;?>" /><br />
                    <input type="button" class="button" id="rss_btn" title="rss" value="Save Changes" />
                </form>
            </div><br />
            <h3 class="post_head closed"><span></span>Privacy Settings</h3>
            <div id="search_engine" class="closed">
            	
                <? 
					if(SEARCHABLE){
						echo '<span class="check">Your website is now visible to search engines. This is irreversible.</span>';
					}
					else{
						echo '<form id="privacy">
									<input type="checkbox" class="check" id="robots" /><label for="robots">Make website Visible to search engines (turning this on is irreversible)</label><br />
                					<input type="button" class="button" id="privacy_btn" value="Save Changes" />
                				</form>';
					}
				?>
                	
            </div>
            <div id="shim"></div>
 
<?php require(ROOT.DS.MAIN.DS.'reflex'.DS.'admin'.DS.'documents'.DS.'headnavfoot'.DS.'footer.php');?>

<script type="text/javascript">
//$(document).ready(function(){
/*********************Settings PAGE SPECIFIC*************************/
	//The tables might be small. The nice thing here is that there doens't have to be a window resize listener
	$('#timezone').val('<? echo TIME_ZONE;?>');
	$('#shim').height($('#navigation').height());
	$('.post_head').click(function(){
		if($(this).hasClass('closed')){
			$(this).removeClass('closed').addClass('open').next('div').removeClass('closed').addClass('open');
		}
		else{
			$(this).removeClass('open').addClass('closed').next('div').removeClass('open').addClass('closed');
		}
	});
	
	(function(){
		working = false;
		/*$('#master_btn').click(function(){
			if(working){
				return;
			}
			working = true;
			var sendObj = {'action':'master'};
			$('#master_form input').each(function(){
				if($(this).attr('type') === 'checkbox'){
					sendObj[this.id] = $(this).is(':checked');
				}
				else{
					sendObj[this.id] = $(this).val();
				}
			});
			ajaxObjSend(sendObj, internal_action+'_settings_change', 'POST', function(success,msg){
				if(success){
					if(msg.match(/^SUCCESS/)){
						working = false;
						alert('Settings successfully changed.');
					}
					else{
						working = false;
						alert(msg);
					}
				}
				else{
					working = false;
					alert('There was an error communicating with the server. It is possible that you have a bad internet connection.');
				}
			});
		});
		$('#posts_btn').click(function(){
			if(working){
				return;
			}
			working = true;
			var sendObj = {'action':'posts'};
			$('#posts_form input,select').each(function(){
				sendObj[this.id] = $(this).val();
			});
			ajaxObjSend(sendObj, internal_action+'_settings_change', 'POST', function(success,msg){
				if(success){
					if(msg.match(/^SUCCESS/)){
						working = false;
						alert('Settings successfully changed.');
					}
					else{
						working = false;
						alert(msg);
					}
				}
				else{
					working = false;
					alert('There was an error communicating with the server. It is possible that you have a bad internet connection.');
				}
			});
		});
		
		$('#comments_btn').click(function(){
			if(working){
				return;	
			}
			working = true;
			var sendObj = {'action':'comments'};
			$('#comment_form input,select,textarea').each(function(){
				if($(this).attr('type') === 'checkbox'){
					sendObj[this.id] = $(this).is(':checked');
				}
				else{
					sendObj[this.id] = $(this).val();
				}
			});
			ajaxObjSend(sendObj,internal_action+'_settings_change', 'POST', function(success,msg){
				if(success){
					if(msg.match(/^SUCCESS/)){
						working = false;
						alert('Settings changed successfully.');
					}
					else{
						working = false;
						alert(msg);
					}
				}
				else{
					working = false;
					alert('There was an error communicating with the server. It is possible that you have a bad internet connection.');
				}
			});
		});*/
		$('.button').click(function(){
			var id =  $(this).attr('title');
			if(working){
				return;	
			}
			working = true;
			var sendObj = {'action':id};
			$('#'+id+'_form input,select,textarea').each(function(){
				if($(this).attr('type') === 'checkbox'){
					sendObj[this.id] = $(this).is(':checked');
				}
				else{
					sendObj[this.id] = $(this).val();
				}
			});
			ajaxObjSend(sendObj,internal_action+'_settings_change', 'POST', function(success,msg){
				if(success){
					if(msg.match(/^SUCCESS/)){
						working = false;
						alert('Settings changed successfully.');
					}
					else{
						working = false;
						alert(msg);
					}
				}
				else{
					working = false;
					alert('There was an error communicating with the server. It is possible that you have a bad internet connection.');
				}
			});
		})
	<? if(!SEARCHABLE){
		?>$('#privacy_btn').click(function(){
			if(working){
				return;	
			}
			working = true;
			confirm('Are you sure you want to make your site visible to search engines? Remember: you cannot reverse this decision.', function(bool){
				if(bool){
					var sendObj = {'action':'privacy'};
					ajaxObjSend(sendObj,internal_action+'_settings_change', 'POST', function(success,msg){
						if(success){
							$('#search_engine').html('<span class="check">Your website is now visible to search engines. This is irreversible.</span>');
							alert('Your website is now visible to search engines.');
							working = false;
						}
						else{
							alert('There was an error communicating with the server. You may have a bad internet connection.');			
							working = false;
						}
					});
				}
				else{
					working = false;
				}
			});
		});<?
	}
	?>
	})();
	
//});
</script>
</body>
</html>
