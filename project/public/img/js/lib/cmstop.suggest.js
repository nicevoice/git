/**
 * based on jQuery 1.3.x
 *
 * @author     kakalong (firebing.cn & hi.baidu.com/emkiao)
 * @copyright  2010 (c) cmstop.com
 * @version    $Id: cmstop.suggest.js 1501 2010-11-03 07:40:25Z root $
 */
(function($){
var LANG = {
	removeItem:'移除此项',
	noSuggestItem:'未发现匹配项'
};
var OPTIONS = {
	single:false,
	width:300,
	url:'keyword=%s',
	paramVal:'value',
	paramTxt:'text',
	anytext:false,
	initUrl:'',
	listBridge:'scroll',
	listUrl:'',
	listExpanded:false,
	listParamHaschild:'hasChildren',
	getExtraData:function(){}
};
var CLASSES = {
	active:'active',
	blink:'blink',
	container:'suggestContainer',
	selectedItem:'selectedItem',
	closer:'closer',
	selectedContainer:'selectedContainer',
	listBtn:'listAllBtn',
	dropContainer:'dropContainer',
	dropItem:'dropItem',
	dropInfo:'dropInfo',
	listPanel:'listPanel'
};
function suggest(elem, options) {
	options = $.extend({}, OPTIONS, options||{});
	var listOptions = {};
	for (var k in options) {
		if (k.substr(0,4) == 'list') {
			var _k = k.substr(4,1).toLowerCase() + k.substr(5);
			listOptions[_k] = options[k];
		}
	}
	// container
	var container = $('<div class="'+CLASSES.container+'"></div>')
		.insertAfter(elem)
		.width(parseInt(options.width)||300),
	
	// input which contain values
	valBox = $(elem).hide(),
	
	// selected value stack
	selectedValue = [],
	activedDropItem = null,
	dropIsActive = 0,
	
	// list all items btn
	listBtn = $('<span class="'+CLASSES.listBtn+'"></span>')
	.click(function(){
		list.visible() ? list.hide() : showList();
	}).prependTo(container),
	
	list = new List($.extend(listOptions, {
		hide:function(){
			listBtn.removeClass(CLASSES.active);
		},
		show:function(){
			listBtn.addClass(CLASSES.active);
		},
		click:function(checked, val, item){
			checked ? addItem(item) : removeItem(item[options.paramVal]);
		},
		single:options.single,
		paramVal:options.paramVal,
		paramTxt:options.paramTxt,
		btn:listBtn
	})),
	
	// selected container width
	_w = container.width() - listBtn.outerWidth(true),
	// selected items and input container
	selectedContainer = $('<div class="'+CLASSES.selectedContainer+'"></div>')
		.click(function(){
			input.focus();
		}).dblclick(function(){
			list.visible() || showList();
		})
		.insertAfter(listBtn).width(_w||100),

	// active input
	input = $('<input type="text" style="width:20px" />')
		.keyup(function(e){
			// ENTER
			if (e.keyCode == 13) {
				if (options.anytext) {
					if (dropIsActive && dropContainer.find('>a.'+CLASSES.active).length) {
						dropSelect()
					} else {
						this.value && txtItem()
					}
				} else {
					dropIsActive && dropSelect();
				}
				return;
			}
			// DOWN UP LEFT RIGHT return
			if (e.keyCode > 36 && e.keyCode < 41) {
				return;
			}
			autoGrow();
			// suggest dropdown list
			drop();
		}).keydown(function(e){
			// ENTER return false
			if (e.keyCode == 13) {
				return false;
			}
			if (this.value == "") {
				switch (e.keyCode) {
				case 37:
					movePrev();
					return;
				case 39:
					moveNext();
					return;
				// BACKSPACE
				case 8:
					// delete prev added item
					removePrev();
					return;
				// DELETE
				case 46:
					// delete next added item
					removeNext();
					return;
				}
			}
			if (e.keyCode == 37 || e.keyCode == 39) {
				return;
			}
			if (e.keyCode == 8 || e.keyCode == 46) {
				autoGrow();
				return;
			}
			// DOWN
			if (e.keyCode == 40) {
				dropIsActive && dropDown();
				return;
			}
			// UP
			if (e.keyCode == 38) {
				dropIsActive && dropUp();
				return;
			}
			setTimeout(autoGrow, 0);
			// autoGrow(String.fromCharCode(e.keyCode));
		}).focus(function(){
			this.value = '';
			autoGrow();
			// hide all list
			list.hide();
			// set input style
			input.addClass(CLASSES.active);
		}).blur(function(){
			// set input style
			input.removeClass(CLASSES.active);
			// set time out hide dropdownlist
			setTimeout(hideDrop, 10);
		}).prependTo(selectedContainer).css('max-width',_w||100),
	// drop container with suggest list
	dropContainer = $('<div class="'+CLASSES.dropContainer+'"></div>')
		.appendTo(document.body);
	function autoGrow(){
		var val = input.val(), v;
		v = val ? val.replace(/[^\x00-\xff]/g,'**') : '';
		input.width( v == '' ? 1 : v.length * 7 + 10);
		input.val(val);
	}

	function clearStack() {
		selectedContainer.find('>a').remove();
		list.uncheck();
		selectedValue = [];
		valBox.val('');
	}
	function addItem(item) {
		var val = item[options.paramVal], txt = item[options.paramTxt];
		if (selectedValue.indexOf(val) !== -1) {
			// blink
			blink(val);
		} else {
			options.single && clearStack();
			var a = $('<a name="'+val+'" class="'+CLASSES.selectedItem+'"><b>'+txt+'</b></a>');
			var span = $('<span class="'+CLASSES.closer+'" title="'+LANG.removeItem+'">'+LANG.removeItem+'</span>')
			.click(function(e){
				e.preventDefault();
				removeItem(a);
			});
			input.before(a.append(span));
			selectedValue.push(val);
			valBox.val(selectedValue.join(','));
			list.check(val);
		}
	}
	function txtItem() {
		var item = {}, val = input.val();
		item[options.paramVal] = val;
		item[options.paramTxt] = val;
		addItem(item);
		input.hasClass(CLASSES.active) && input.blur();
		setTimeout(function(){input.focus();}, 0);
	}
	function blink(value) {
		var a = selectedContainer.find('a[name="'+value+'"]');
        a.addClass(CLASSES.blink);
		var i = 0;
		var ival = setInterval(function() {
			a.hasClass(CLASSES.blink)
				? a.removeClass(CLASSES.blink)
				: a.addClass(CLASSES.blink);
			if (++i > 2) {
				clearInterval(ival);
				ival = null;
			}
		}, 300);
	}
	function removeItem(a) {
		var value;
		if (a.jquery) {
			value = a.attr('name');
		} else {
			value = a;
			a = selectedContainer.find('a[name="'+value+'"]');
		}
		a.remove();
		var i = selectedValue.indexOf(value);
		if (i != -1) {
			selectedValue.splice(i, 1);
			valBox.val(selectedValue.join(','));
		}
		list.uncheck(value);
	}
	function movePrev(){
		var a = input.prev('a');
		if (a.length) {
			input.width(1).insertBefore(a);
			setTimeout(function(){
				input.focus();
			}, 0);
		}
	}
	function moveNext(){
		var a = input.next('a');
		if (a.length) {
			input.width(1).insertAfter(a);
			setTimeout(function(){
				input.focus();
			}, 0);
		}
	}
	function removePrev() {
		var a = input.prev('a');
		if (a.length) {
			removeItem(a);
		}
	}
	function removeNext() {
		var a = input.next('a');
		if (a.length) {
			removeItem(a);
		}
	}
	function getData(url, success, error) {
		var extraData = options.getExtraData();
		$.ajax({
			url:url,
			type:'POST',
			dataType:'json',
			data:extraData,
			success:success,
			error:error
		});
	}
	var val = valBox.val();
	if (val !== '') {
		if (options.initUrl) {
			getData(options.initUrl.replace('%s',encodeURIComponent(val)), function(json){
				if (json.length) {
					for (var i=0,t; t = json[i++]; addItem(t)) {}
				}
			});
		} else {
			var data = val.split(/\s*,\s*/);
			for (var i=0,l=data.length;i<l;i++) {
				var item = {};
				item[options.paramVal] = data[i];
				item[options.paramTxt] = data[i];
				addItem(item);
			}
		}
	}
	
	function drop() {
		var val = input.val();
		if (! val) {
			hideDrop();
			return;
		}
		getData(options.url.replace('%s', encodeURIComponent(input.val())),
		function(json){
			dropContainer.empty();
			if (json.length) {
				for (var i=0,l; l = json[i++]; buildDropItem(l)) {}
				if (! options.anytext) {
					var first = dropContainer.find('>a:first');
					first.length && activeDropItem(first);
				}
				showDrop();
			}
		});
	}
	function showDrop() {
		var offset = input.offset();
		dropContainer.css({
			'left':offset.left,
			'top':offset.top + input.outerHeight(true),
			'display':'block'
		});
		dropIsActive = !!dropContainer.find('>a').length;
	}
	function hideDrop() {
		dropIsActive = false;
		dropContainer.hide();
	}
	function buildDropItem(item) {
		var val = item[options.paramVal], txt = item[options.paramTxt];
		var a = $('<a class="'+CLASSES.dropItem+'" title="'+val+'" name="'+val+'">'+txt+'</a>')
		.mousedown(function(){
			addItem(item);
			input.hasClass(CLASSES.active) && input.blur();
			setTimeout(function(){input.focus();}, 0);
		}).mouseover(function(){
			activeDropItem(a);
		}).click(function(e){
			e.preventDefault();
		});
		dropContainer.append(a);
	}
	function activeDropItem(a) {
		activedDropItem && activedDropItem.removeClass(CLASSES.active);
		activedDropItem = a;
		a.addClass(CLASSES.active);
	}
	function dropDown() {
		var down = null;
		if (activedDropItem && (down = activedDropItem.next('a')).length) {
			activeDropItem(down);
		} else {
			down = dropContainer.find('>a:first');
			down.length && activeDropItem(down);
		}
	}
	function dropUp() {
		var up = null;
		if (activedDropItem && (up = activedDropItem.prev('a')).length) {
			activeDropItem(up);
		} else {
			up = dropContainer.find('>a:last');
			up.length && activeDropItem(up);
		}
	}
	function dropSelect() {
		activedDropItem && activedDropItem.mousedown();
	}
	
	function showList() {
		var offset = container.offset();
		list.show({
			left:offset.left,
			top:offset.top + listBtn.outerHeight(true),
			width:container.width()
		});
	}
}
var _bridge = {};
suggest.registerBridge = function(name, bridge) {
	_bridge[name] = bridge;
};
var List = function(options){
	var t = this;
	t.inited = false;
	t.options = options;
	t.panel = $('<div class="'+CLASSES.listPanel+'"></div>').appendTo(document.body);
	t.checked = [];
	var _init = _bridge[t.options.bridge];
	t.init = function(){
		if (! t.inited) {
			_init(t);
			t.inited = true;
		}
		return t;
	};
};
var doc = $();
List.prototype = {
	uncheck:function(value){
		var t = this;
		if (value == undefined) {
			t.checked = [];
			t.inited && t.panel.find('input').attr('checked', false);
		} else {
			var i = t.checked.indexOf(value);
			i != -1 && t.checked.splice(i, 1);
			t.inited && t.panel.find('input[value='+value+']').attr('checked', false);
		}
	},
	check:function(value){
		var t = this;
		t.checked.indexOf(value) == -1 && t.checked.push(value);
		t.inited && t.panel.find('input[value='+value+']').attr('checked', true);
	},
	hide:function(){
		var t = this;
		t.inited && t.panel.hide();
		doc.unbind('mousedown.suggest');
		t.options.hide();
		return t;
	},
	show:function(css){
		var t = this, panel = t.panel, o = t.options; btn = o.btn;
		t.init();
		setTimeout(function(){
			doc.bind('mousedown.suggest', function(e){
				var el = e.target;
				el == panel[0] || (btn && btn.index(el) != -1) || panel.find(el.tagName||'*').index(el) != -1 || t.hide();
			});
		}, 0);
		css && panel.css(css);
		panel.show();
		o.show();
		return t;
	},
	visible:function(){
		return this.inited && this.panel.is(':visible');
	}
};

$.suggest = suggest;

$.fn.suggest = function(options) {
	this.each(function(){
		var opt = {}, input = $(this);
		for (var attr in OPTIONS) {
			var val = input.attr(attr);
			val && (opt[attr] = val);
		}
		suggest(this, $.extend({}, opt, options||{}));
	});
	return this;
};

})(jQuery);

// Bridge
(function($){
var CLASSES = {
	container:'scrollContainer',
	pageinfo:'pageInfo',
	closer:'listCloser'
};
function loadPage(t){
	var o = t.options;
	t._lock = true;
	$.ajax({
		url:o.url.replace('%s', ++t._page),
		success:function(json) {
			var l;
			if (json.data && (l = json.data.length)) {
				t._total || (t._total = parseInt(json.total));
				t._count += l;
				t._pageinfo.text(t._count+' / '+t._total);
				for (var i=0;i<l;i++) {
					buildItem(json.data[i], t);
				}
			}
		},
		complete:function(){
			t._lock = false;
		},
		dataType: 'json'
	});
}
function buildItem(item, t){
	var o = t.options,
		val = item[o.paramVal], txt = item[o.paramTxt];
	var label = $('<label><input type="'+(o.single ? 'radio' : 'checkbox') +'" value="'+val+'" /><span title="'+val+'">'+txt+'</span></label>');
	label.find('input').click(function(){
		o.click(this.checked, val, item);
		this.checked ? t.check(val) : t.uncheck(val);
	}).attr('checked', t.checked.indexOf(val) != -1);
	t._container.append(label);
}
$.suggest.registerBridge('scroll', function(t){
	t._lock = false;
	t._count = 0;
	t._total = 0;
	t._page = 0;
	var panel = t.panel;
	panel.append('\
	<h3>\
		<span class="'+CLASSES.closer+'">close</span>\
		<span class="'+CLASSES.pageinfo+'">0 / 0</span>\
	</h3>\
	<div class="'+CLASSES.container+'" title="滚动翻页"></div>');
	
	t._pageinfo = panel.find('span.'+CLASSES.pageinfo);
	panel.find('span.'+CLASSES.closer).click(function(){
		t.hide();
	});
	var container = panel.find('div.'+CLASSES.container)
	.mousewheel(function(e, delta){
		if (delta < 0 && !t._lock && t._count < t._total 
			&& container.scrollTop() + container.height() > container[0].scrollHeight - 20)
		{
			loadPage(t);
		}
	});
	t._container = container;
	loadPage(t);
});

})(jQuery);

(function($){

var CLASSES = {
	container:'treeContainer',
	closer:'listCloser'
};
$.suggest.registerBridge('tree', function(t){
	var o = t.options, panel = t.panel;
	panel.append('\
	<h3>\
		<span class="'+CLASSES.closer+'">close</span>\
	</h3>\
	<div class="'+CLASSES.container+'" idv="tree"></div>');
	panel.find('span.'+CLASSES.closer).click(function(){
		t.hide();
	});
	var inputType = o.single ? 'radio' : 'checkbox';
	panel.find('div.'+CLASSES.container).tree({
		paramId:o.paramVal,
		paramHaschild:o.paramHaschild,
		expanded:o.expanded,
		url:o.url,
		renderTxt:function(div, id, item){
			var label = $('<label><input type="'+inputType+'" value="'+id+'" /><span title="'+id+'">'+item[o.paramTxt]+'</span></label>')
			label.find('input').click(function(){
				o.click(this.checked, this.value, item);
				this.checked ? t.check(id) : t.uncheck(id);
			}).attr('checked', t.checked.indexOf(id) != -1);
			return label;
		}
	});
	return t;
});

})(jQuery);