// expired, do not use
(function(ct){
$.extend(ct,{
    ajax:function(title, url, width, height, load, ok, cancel)
    {
    	return ct.ajaxDialog({
    		title:title,
    		width:width,
    		height:height
    	}, url, load, ok, cancel);
    },
    form:function(title, url, width, height, submitBack, formReady, beforeSubmit, beforeSerialize)
    {
    	return ct.formDialog({
    		title:title,
    		width:width,
    		height:height
    	}, url, submitBack, formReady, beforeSubmit, beforeSerialize);
    }
});
})(cmstop);