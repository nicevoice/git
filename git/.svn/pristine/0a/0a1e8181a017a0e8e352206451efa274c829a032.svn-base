/**
 * based on jQuery 1.3+
 *
 * @author     kakalong
 * @copyright  2010 (c) cmstop.com
 * @version    $Id: cmstop.autocomplete.js 4561 2012-03-16 03:51:13Z dengguanglei $
 */
(function($){
var OPTIONS = {
	url:'?app=system&controller=page&action=page&q=%s',
	cache:true,
	showEvent:'dblclick',
	autoFill:true,
	extraParams:function(){return ''},
	itemFormat:function(item, k){
		return k ? item.text.replace(new RegExp(k, 'ig'), function(k){
			return '<strong>' + k + '</strong>';
    	}) : item.text;
	},
	itemSelected:function(a, item, input){},
	jsonLoaded:function(item){},
	itemPrepared:function(a, item){}
};
var CLASSES = {
	active:'active',
	autocomplete:'autocomplete-box'
};
var toInt = function(o) {
	o = parseInt(o);
	return isNaN(o) ? 0 : o;
}
function autocomplete(input, options) {
	options = $.extend({}, OPTIONS, options||{});
	input.jquery || (input = $(input));
	input.attr("autocomplete", "off");
	var _cachedData = {},
	doc = $(document),
	// create container append to body
	div = $('<div class="'+CLASSES.autocomplete+'"></div>')
	.css({
		position:'absolute'
	})
	.appendTo(document.body),
	
    current = null,
    visible = false,
	ihide = function(){
		blurCurrent();
		if (!visible) return;
		visible = false;
		doc.unbind('mousedown', iblur);
		div.hide();
	},
	iblur = function(e){
		input[0] == e.target || div[0] == e.target || div.find(e.target.nodeName).index(e.target) != -1 || ihide();
    },
    blurCurrent = function(){
    	current && current.removeClass(CLASSES.active);
		current = null;
    };
    // listen input
    input.keyup(function(e){
		switch (e.keyCode) {
		// ENTER
		case 13:
			// select item and hide ul
			selectItem(current);
			return;
		case 9:
			return;
		// ESC LEFT UP RIGHT DOWN
		case 27: case 37: case 38: case 39: case 40:
			return false;
		default:
			// get items
			query();
		}
    }).keydown(function(e){
    	switch(e.keyCode) {
		// ENTER
		case 13: return false;
		// TAB
		case 9:
			// hide div
			ihide();
			return;
		// UP
		case 38:
			move();
			return;
		// DOWN
		case 40:
			move(1);
			return;
		}
    });
    options.showEvent && input.bind(options.showEvent, function(e){
    	// empty input than show no keywords suggest
    	this.value == '' ? query() : show();
    });
    function move(down){
    	if (!show()) {
    		return;
    	}
    	if (current) {
    		var a = current[down ? 'next' : 'prev']('a');
    		if (a.length) {
    			moveTo(a)
    		} else {
    			blurCurrent();
    			current = 0;
    			options.autoFill || input.val(input.attr('selftext'));
    		}
    	} else if (current == null) {
    		current = 0;
    	} else if (current == 0) {
    		moveTo(div.find('>a:'+(down ? 'first' : 'last')));
    	}
    }
    function moveTo(a){
    	a.mouseover();
    	var offset = 0;
		a.prevAll('a').each(function(){
			offset += this.offsetHeight;
		});
		if ((offset + a[0].offsetHeight - div.scrollTop()) > div[0].clientHeight) {
            div.scrollTop(offset + a[0].offsetHeight - div.innerHeight());
        } else if(offset < div.scrollTop()) {
            div.scrollTop(offset);
        }
    }
    function query(){
    	var k = input.val();
    	options.autoFill && input.attr('selftext', k);
    	if ($.trim(k) == '') {
    		k = '';
    	}
    	// find in cache
    	if (options.cache && (k in _cachedData)) {
    		display(_cachedData[k], k);
    		return;
    	}
    	// no cache than find in remote
    	var data = typeof options.extraParams == 'function'
    		? options.extraParams()
    		: options.extraParams;
    	$.ajax({
			url:options.url.replace('%s', encodeURIComponent(k)),
			type:'POST',
			dataType:'json',
			data:data,
			success:function(json){
				options.jsonLoaded(json);
				display(json, k);
				if (options.cache) {
					_cachedData[k] = json;
				}
			},
			error:function(){
				ihide();
    			div.empty();
			}
		});
    }
    function display(data, k) {
    	if (!data || !data.length) {
    		ihide();
    		div.empty();
    		return;
    	}
    	blurCurrent();
    	div.empty().css('height', data.length > 10 ? 200 : 'auto');
    	for (var i=0,item;item=data[i++];) {
    		addItem(item, k);
    	}
    	show();
    }
    function show() {
    	// set offset
    	if (visible || !div.find('>a').length) return visible;
    	visible = true;
    	var offset = input.offset();
    	div.css({
			left:offset.left,
			top:offset.top + input.outerHeight(true),
			display:'block'
		});
		var bw = toInt(div.css('border-left-width')) * 2;
		var pw = parseInt(div.css('padding-left'))
		div.width(
			input.outerWidth() - toInt(div.css('border-left-width')) * 2
			- toInt(div.css('padding-left')) - toInt(div.css('padding-right'))
		);
    	doc.mousedown(iblur);
    	return true;
    }
    function addItem(item, k){
    	var text = typeof options.itemFormat == 'function'
    		? options.itemFormat(item, k) : item.text;
    	var a = $('<a>'+text+'</a>')
		.mousedown(function(){
			selectItem(a);
		}).mouseover(function(){
			activeItem(a, item);
		}).data('itemdata', item);
		
    	options.itemPrepared(a, item);
		div.append(a);
    }
    function activeItem(a, item){
    	blurCurrent();
    	current = a;
    	a.addClass(CLASSES.active);
    	options.autoFill && input.val(item.text);
    }
    function selectItem(a){
    	ihide();
    	if (!a) return;
    	input.val(a.data('itemdata').text);
    	options.itemSelected(a, a.data('itemdata'), input);
    }
}
$.fn.autocomplete = function(options) {
	this.each(function(){
		var opt = {}, input = $(this);
		for (var attr in OPTIONS) {
			var val = input.attr(attr);
			val && (opt[attr] = val);
		}
		autocomplete(input, $.extend({},options||{},opt));
	});
	return this;
};
})(jQuery);