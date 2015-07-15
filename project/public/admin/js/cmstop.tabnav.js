(function($){
	var CONFIGS = {
		classFocus:'active',
		initFocus:0,
		focused:function(){},
		dataType:'json',
		forceFocus:false
	};
	var IE7 = $.browser.msie && (parseInt($.browser.version) < 8);
	var tabnav = function(ul, data, configs) {
		configs = $.extend({},CONFIGS,configs || {});
		var locked = false;
		var focusedLi = null;
		var _focus = function() {
			if (locked) {
				return;
			}
			var li = $(this);
			if (!configs.forceFocus && li.hasClass(configs.classFocus)) {
				return;
			}
			locked = true;
			if (configs.dataType == null) {
				focusedLi && focusedLi.removeClass(configs.classFocus);
				focusedLi = li.addClass(configs.classFocus);
				configs.focused(li);
				locked = false;
				return;
			}
			$.ajax({
				url:li.attr('url'),
				type:'GET',
				dataType:configs.dataType,
				success:function(data){
					focusedLi && focusedLi.removeClass(configs.classFocus);
					focusedLi = li.addClass(configs.classFocus);
					configs.focused(li,data);
				},
				complete:function(){
					locked = false;
				}
			});
		};
		var createli = function(item) {
			var a = $('<a href="javascript:;">'+item.name+'</a>');
			var li = $('<li/>').append(a).click(_focus);
			IE7 ? a.focus(function(){this.blur()})
				: a.css('outline','none');
			configs.dataType == 'function' && li.attr('url',item.url);
			ul.append(li);
		}
		// find li s
		var lis = $('>li', ul);
		if (lis.length) {
			lis.each(function(){
				var li = $(this);
				var a = $('a',li);
				IE7 ? a.focus(function(){this.blur()})
					: a.css('outline','none');
				li.attr('url', a.attr('href')).click(_focus);
			});
		} else {
			for (var i=0,l=data.length; i<l; i++) {
				createli(data[i]);
			}
		}
		// focus first
		if (configs.initFocus>-1) {
			var f = ul.find('>li:eq('+configs.initFocus+')');
			// f.hasClass(configs.classFocus) || f.click();
			f.click();
		}
	};
	$.fn.tabnav = function(options,data) {
		data || (data = []);
		tabnav(this, data, options);
		return this;
	}
})(jQuery);