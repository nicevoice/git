(function($){
var OPTIONS = {
	multiple:false,
	picked:function(items){},
	url:'?app=system&controller=picker&action=pick'
};
$.datapicker = function(options){
	var o = $.extend({
		width:550
	}, OPTIONS, options||{});
	o.url = o.multiple ? (o.url + (o.url.indexOf('?') == -1 ? '?' : '&') + 'multi=1') : o.url;
	var d = ct.iframe(o, {
		ok:function(checked){
			o.picked(checked);
			d.dialog('close');
		}
	});
};
})(jQuery);


