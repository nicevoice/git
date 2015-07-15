jQuery(document).ready(function(){
	
	//pagination
	jQuery("div.pagination .left:empty").append("&laquo;&nbsp;Prev Page");
	jQuery("div.pagination .right:empty").append("Next Page&nbsp;&raquo;");
	
	jQuery("(div.pagination > span):has(a)").removeAttr("style");
	
	//input focus
	jQuery(":text, textarea").focus(function(){
		jQuery(this).parent().addClass("currentFocus");
		jQuery(".currentFocus .desc").css({"color" : "#3188c7"});
		jQuery(".currentFocus .message-input, .currentFocus #author, .currentFocus #email, .currentFocus #url").css({"border-color" : "#3188c7", "color" : "#000", "background-color" : "#fff"});
	});
	
	//input blur
	jQuery(":text,textarea").blur(function(){
		jQuery(this).parent().removeClass("currentFocus");
		jQuery(".message-input, .desc, #author, #email, #url").removeAttr("style");
	});
	
	//img hover background
	jQuery("ol.commentlist li.depth-1").hover(function(){
		jQuery(this).find("div>div>div>img").addClass("imghover")}, function(){
		jQuery(this).find("div>div>div>img").removeClass("imghover");
	});
	
	jQuery("ol.commentlist li.depth-1").hover(function(){
		jQuery(this).find("span.reply").removeClass("hide")}, function(){
		jQuery(this).find("span.reply").addClass("hide");
	});
	
	//reply button
	jQuery(".reply").click(function(){
		jQuery("#respond h3").addClass("remove_h3");
		jQuery(".remove_h3").css("display", "none");
	});
	
	jQuery("a#cancel-comment-reply-link").click(function(){
		jQuery("#respond h3").removeAttr("Style");
	});
	
	//scrollTo
	jQuery(".meta-lac a").click(function(){
		jQuery.scrollTo("#respond", 800);return false;
	});
	
	jQuery(".btt").hover(function(){
		jQuery(".btt").css({"cursor": "pointer"});
	});
	
	jQuery(".btt").click(function(){
		jQuery.scrollTo("#topbar", 800);
		return false;
	});
	
	//separate pings
	jQuery("li.pingback .right span.reply, li.trackback .right span.reply").remove();
	
	//remember visiotr info
	if(jQuery("#respond h3 .notyou").length > 0){
		jQuery("#respond .user_info").css("display", "none");
	}
	
	jQuery("#respond h3 .notyou").click(function(){
		jQuery("#respond .user_info").removeAttr("Style");
		jQuery("#respond h3").replaceWith("<h3>Leave a comment</h3>");
		return false;
	});
	
});
