          </div><!--MainBody-->
  
  <div id="footer">
    	<p class="left"><a>Documentation</a> | <a>Feedback</a></p>
   		<p class="right">Version <? echo REFLEX_VERSION;?></p>
    </div><!--Footer-->
<!--<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>-->
<script type="text/javascript" src="scripts/admin/jquery.js"></script>
<script type="text/javascript" src="scripts/admin/ajax.js"></script>
<script type="text/javascript">
//These globals are a necessary evil
var internal_action = <? echo '\''.INTERNAL_ACTION.'\'';?>;
var external_action = <? echo '\''.ACTION_VAR.'\'';?>;
//CSS sucks, let's fix it:
$(document).ready(function(){
	
	$('#main_body').width($(document).width() - 280);
	
	$(window).resize(function(){
		$('#main_body').width($(document).width() - 280);
	});
});
</script>