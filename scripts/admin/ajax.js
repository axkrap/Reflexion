/**Custom server call**/
function ajaxObjSend(obj, action, method, callBack){
	var data = '', size = 0;
	for (var key in obj){
		if(obj.hasOwnProperty(key)) size+=1;
	}
	
	$.each(obj, function(index, value){
		size-=1;
		value = typeof value !== 'function' ? value : value();
		if(size===0){
			data+= index+'='+value;
		}
		else{
			data+= index+'='+value+'&';
		}
	});
	
	$.ajax({
		type: method,
		url: action,
		data: data,
		failure: function(msg){
			callBack(false, msg);
		},
		success: function(msg){
			callBack(true, msg);
		},
		error:function(msg){
			callBack(false, msg);
		}
	 });
};
/**Alert, prompt, and confirm overrides**/
(function(w){
	//IIFE variables and functions
	var i = 0;
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
	
	alert = w.alert = function(msg,time){
		i+=1;
		var settime = time==undefined ? 4e3 : time===false ? false : time;
		var aB = 'alert_box'+i, aBid = '#'+aB;
		var html = '<div id="'+aB+'" class="alert_box"><p>'+msg+'</p></div>';
		$('body').append(html);
		alertBoxSet(true, aBid);
		
		$(window).resize(function(){
			alertBoxSet(false,aBid);
		});
	
		$(aBid).animate({
   			opacity: 1.00,
			top: '+=10'
			},
			1e3,
			function(){
				if(settime !== false){
					animateOut(settime,aBid);
				}
			}
		);
		return aB;
	};
	
	var confirm = w.confirm = function(msg,callBack){
		i+=1;
		var aB = 'alert_box'+i, aBid = '#'+aB;
		var html = '<div id="'+aB+'" class="alert_box"><p>'+msg+'</p><button id="cancel'+i+'" class="button left">Cancel</button><button id="okay'+i+'" class="button right">Okay</button></div>';
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
 
		$('#cancel'+i).click(function(){
			if(!clicked){
				clicked = true;
				callBack(false);
				animateOut(0, aBid);
			}
		});
		$('#okay'+i).click(function(){
			if(!clicked){
				clicked = true;
				callBack(true);
				animateOut(0, aBid);
			}
		});
	};
	

	
	var prompt = w.prompt = function(msg, callBack){
		i+=1;
		var aB = 'alert_box'+i, aBid = '#'+aB;
		var html = '<div id="'+aB+'" class="alert_box"><p>'+msg+'</p><input style="width:90%;" class="prompt_put" id="prompt'+i+'" type="text"/><br/><button style="align:center;" class="button" id="enter'+i+'">Enter</button></div>';
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
				callBack($('#prompt'+i).val());
				animateOut(0,aBid);
			}
		});
	};
})(window);