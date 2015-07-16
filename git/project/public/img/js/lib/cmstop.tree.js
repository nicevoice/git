/**
 * based on jQuery 1.3.x
 *
 * @author     kakalong (firebing.cn & hi.baidu.com/emkiao)
 * @copyright  2010 (c) cmstop.com
 * @version    $Id: cmstop.tree.js 5425 2012-05-04 12:47:03Z liyawei $
 */
(function($){
var OPTIONS = {
	url:"?app=system&controller=index&action=menu&parent=%s",
	paramId:"id",
	paramHaschild:"hasChildren",
	async:true,
	expanded:false,
	renderTxt:function(div, id, item){
		return $('<span>'+item.text+'</span>');
	},
	active:function(){},
	click:function(){},
	prepared:function(){},
	itemReady:function(){}
},
CLASSES = {
	expandable:"expandable",
	expanded:"expanded",
	haschild:"haschild",
	itemarea:"itemarea",
	txtarea:"txtarea",
	hitarea:"hitarea",
	bitarea:"bitarea",
	active:"active",
	tree:'tree'
};
var tree = function(container, options){
	options = $.extend({}, OPTIONS, options||{});
	container.jquery || (container = $(container));
	this.container = container.addClass(CLASSES.tree);
	container.data('treeObj', this);
	this.actived = null;
	this.options = options;
	if (options.async) {
		this.branches(container, options.prepared);
		return;
	}
	var t = this;
	container.find('li').each(function(){
		t.prepareItem($(this));
	});
};
function nochild(li){
	li.removeClass(CLASSES.haschild)
		.children('.'+CLASSES.itemarea).removeClass(CLASSES.expandable)
		.children('.'+CLASSES.hitarea).unbind('.tree')
			.removeClass(CLASSES.hitarea).addClass(CLASSES.bitarea);
}
tree.prototype = {
	branches:function(li, callback){
		var t = this, o = t.options;
		li.data('locked', true);
		$.ajax({
			url:o.url.replace('%s', encodeURIComponent(li.attr('idv'))),
			success:function(json) {
				if (!json || !json.length) {
					nochild(li);
					return;
				}
				var ul = $(document.createElement('ul')).appendTo(li);
				for (var i=0,l=json.length; i<l; ++i) {
					t.buildItem(json[i], ul);
				}
				ul.show();
				li.children('.'+CLASSES.itemarea).addClass(CLASSES.expanded);
				typeof callback == 'function' && callback.call(t);
			},
			error:function(xhr, err){
				err == 'parsererror' && nochild(li);
			},
			complete:function(){
				li.data('locked', false);
			},
			dataType: 'json'
		});
	},
	prepareItem:function(li){
		var t = this, o = t.options,
			div = $('<div class="'+CLASSES.itemarea+'"></div>'),
			ul = li.children('ul');
		if (ul.length) {
			ul.prevAll().appendTo(div);
			div.prependTo(li);
			li.addClass(CLASSES.haschild)
			.bind('expand.tree',function(e, callback){
				if (li.data('locked')) {
					return;
				}
				if (! div.hasClass(CLASSES.expanded)) {
					div.addClass(CLASSES.expanded);
					ul.show();
				}
			});
			o.expanded && li.triggerHandler('expand.tree');
			div.addClass(CLASSES.expandable);
			var hitarea = $('<b class="'+CLASSES.hitarea+'"></b>').prependTo(div);
			hitarea.bind('click.tree', function(e){
				e.stopPropagation();
				e.preventDefault();
				if (div.hasClass(CLASSES.expanded)) {
					div.removeClass(CLASSES.expanded);
					ul.hide();
				} else {
					div.addClass(CLASSES.expanded);
					ul.show();
				}
			});
		} else {
			li.children().appendTo(div);
			div.prependTo(li);
			div.prepend('<b class="'+CLASSES.bitarea+'"></b>');
		}
		
		div.bind('active.tree', function(){
			t.actived && t.actived.removeClass(CLASSES.active);
			t.actived = div.addClass(CLASSES.active);
			o.active.apply(t, [div]);
		}).click(function(){
			div.triggerHandler('active.tree');
			li.triggerHandler('expand.tree');
			o.click.apply(t, [div]);
		});
		
		li.bind('active.tree',function(){
			div.triggerHandler('active.tree');
		}).bind('clk.tree',function(){
			div.click();
		});
	},
	buildItem:function(item, ul){
		var t = this, o = t.options, id = item[o.paramId],
			li = $('<li idv="'+id+'"></li>').appendTo(ul),
			div = $('<div class="'+CLASSES.itemarea+'"></div>').appendTo(li);
		o.renderTxt(div, id, item).addClass(CLASSES.txtarea).appendTo(div);
		if (item[o.paramHaschild]) {
			li.addClass(CLASSES.haschild)
			.bind('expand.tree',function(e, callback){
				if (li.data('locked')) {
					return;
				}
				var ul = $('>ul', li);
				if (ul.length) {
					if (! div.hasClass(CLASSES.expanded)) {
						div.addClass(CLASSES.expanded);
						ul.show();
					}
					typeof callback == 'function' && callback.call(t);
				} else if (li.hasClass(CLASSES.haschild)) {
					t.branches(li, callback);
				}
			});
			o.expanded && li.triggerHandler('expand.tree');
			div.addClass(CLASSES.expandable);
			var hitarea = $('<b class="'+CLASSES.hitarea+'"></b>').prependTo(div);
			hitarea.bind('click.tree', function(e){
				e.stopPropagation();
				e.preventDefault();
				if (li.data('locked')) {
					return;
				}
				var ul = $('>ul', li);
				if (! ul.length) {
					t.branches(li);
				} else if (div.hasClass(CLASSES.expanded)) {
					div.removeClass(CLASSES.expanded);
					ul.hide();
				} else {
					div.addClass(CLASSES.expanded);
					ul.show();
				}
			});
		} else {
			div.prepend('<b class="'+CLASSES.bitarea+'"></b>');
		}
		
		div.bind('active.tree', function(){
			t.actived && t.actived.removeClass(CLASSES.active);
			t.actived = div.addClass(CLASSES.active);
			o.active.apply(t, [div, id, item]);
		}).click(function(){
			div.triggerHandler('active.tree');
			li.triggerHandler('expand.tree');
			o.click.apply(t, [div, id, item]);
		});
		
		li.bind('active.tree',function(){
			div.triggerHandler('active.tree');
		}).bind('clk.tree',function(){
			div.click();
		});
		o.itemReady.apply(t, [li, ul, item]);
	},
	resetActive:function(){
		this.actived && this.actived.removeClass(CLASSES.active);
		this.actived = null;
	},
	open:function(path, click){
		typeof path == 'string' && (path = path.split(','));
		var n = 0, l = path.length - 1, li = this.container,
		open = function() {
			if (n > l) {
				return;
			}
			li = li.find('li[idv="'+path[n]+'"]');
			if (! li.length) {
				return;
			}
			if (n++ == l) {
				li.triggerHandler((click ? 'clk' : 'active')+'.tree');
				return;
			}
			li.triggerHandler('expand.tree', [open]);
		}
		open();
	}
};
$.tree = tree;
$.fn.tree = function(options) {
	if (typeof options == 'string') {
		var t = $(this[0]).data('treeObj');
		return t[options].apply(t, Array.prototype.slice.call(arguments, 1));
	} else {
		return this.each(function(){
			var opt = {}, t = $(this);
			for (var attr in OPTIONS) {
				var val = t.attr(attr);
				val && (typeof val == 'string') && (opt[attr] = val);
			}
			new tree(t, $.extend({}, opt, options||{}));
		});
	}
};

})(jQuery);

(function($){

var OPTIONS = {
	alt:'请选择',
	name:null,
	url:null,
	initUrl:null,
	paramVal:'value',
	paramTxt:'text',
	paramHaschild:'hasChildren',
	expanded:false,
	ending:false,
	multiple:false,
	width:200,
	selectMult:false,
	extraClass:null,
	render:function(checked, storedData, a, o){
		var title = [], l = checked.length, span = '<span>'+o.alt+'</span>';
		if (l) {
			$.each(storedData, function(i, k) {
				title.push(k.name);
			});
			var d = storedData[checked[0]];
			span = '<span>'+(d ? d[o.paramTxt] : '&nbsp;')+(l > 1 ? '<em>...</em>' : '')+'</span>';
		}
		title = title.join(',');
		$(span).attr('title', title);
		a.attr('title', title);
		a.html(span);
	}
},
CLASSES = {
	selectree:'selectree'
};
var doc = $();
// needs cmstop.tree.js
var selectree = function(elem, options){
	var o = $.extend({}, OPTIONS, options || {}),
		$elem = elem.jquery ? elem : $(elem);
	elem = $elem[0];
	for (var attr in OPTIONS) {
		var val = $elem.attr(attr);
		val && (o[attr] = val);
	}
	o.value = $elem.val() || o.value;
	var valBox = null,
		classes = (o.extraClass ? (o.extraClass+' ') : '') + CLASSES.selectree,
		multi = o.multiple,
		checked = $.isArray(o.value)
			? [].concat(o.value)
			: (o.value ? (typeof o.value == 'string' ? o.value.split(',') : [o.value]) : []),
		storedData = {},
		tree = null, treeInited = 0,
		type = (multi ? 'checkbox' : 'radio'),
		a = $('<a hideFocus tabindex="0" class="'+classes+'" style="text-decoration:none;"></a>');
	$elem.hide();
	if (o.name) {
		if (elem.nodeName == 'INPUT' || elem.nodeName == 'TEXTAREA') {
			valBox = $elem;
		} else {
			valBox = $('<input name="'+o.name+'" type="hidden" />').insertAfter(elem).hide();
			$elem.removeAttr('name');
			elem.nodeName == 'SELECT' && $elem.attr('disabled', 'true');
		}
	}
	$elem.bind('changed initd', function(){
		o.render(checked, storedData, a, o);
		if (valBox) {
			valBox.val(checked.join(','));
			valBox.change();
		}
	});

	a.mousedown(function(){
		treeInited || initTree();
		tree.is(':visible') || showTree();
		a.focus();
	}).keydown(function(e){
		// ENTER
		if (e.keyCode == 13 || e.keyCode == 32) {
			return false;
		}
		// TAB ESC
		if (e.keyCode == 9 || e.keyCode == 27) {
			hideTree();
		}
	}).insertAfter(elem);
	function check(input){
		checked.indexOf(input.value) == -1 && checked.push(input.value);
		input.checked = true;
	}
	function uncheck(input){
		var i = checked.indexOf(input.value);
			i != -1 && checked.splice(i, 1);
		input.checked = false;
	}
	function initTree() {
		if (treeInited) return;
		treeInited = 1;
		tree = $('<div class="'+classes+'" idv="tree"></div>')
		.bind('mousedown mouseup', function(){
			setTimeout(function(){a.focus();}, 0);
		}).css('width', parseInt(o.width) || 100).appendTo(document.body);
		new $.tree(tree, {
			paramId:o.paramVal,
			paramHaschild:o.paramHaschild,
			expanded:o.expanded,
			url:o.url,
			renderTxt:function(div, val, item){
				var	label;
				if (o.ending && item[o.paramHaschild]) {
					if (!multi || !o.selectMult) {
						return $('<label><span>'+item[o.paramTxt]+'</span></label>');
					}
				}
				storedData[val] = item;
				label = $('<label><input type="'+type+'" value="'+val+'" /> <span>'+item[o.paramTxt]+'</span></label>');
				var input = label.find('input');
				item.elem = label;
				var li = div.parent(), pinput = li.parent().prev().find('input');
				input.click(function(e){
					e.stopPropagation();
					if (multi) {
						if (this.checked) {
							li.children('ul').find('input').each(function(){
								this.disabled = true;
								uncheck(this);
								this.checked = true;
							});
							check(this);
						} else {
							uncheck(this);
							li.children('ul').find('input').attr('checked',false).attr('disabled', false).removeAttr('disabled');
						}
					} else {
						tree.find('input:checked').each(function(){
							uncheck(this);
						});
						checked = [];
						check(this);
					}
					$elem.triggerHandler('changed', [checked, storedData]);
				});
				if (multi && (pinput.attr('checked') || pinput.attr('disabled'))) {
					uncheck(input[0]);
					input[0].disabled = true;
					input[0].checked = true;
				} else {
					input[0].checked = checked.indexOf(val) != -1;
				}
				return label;
			}
		});
	}
	function hideTree(){
		doc.unbind('mousedown', blurTree);
		tree.hide();
	}
	function blurTree(e){
		var el = e.target, tag = el.tagName || '*';
		el == elem || $elem.find(tag).index(el) != -1 ||
		el == tree[0] || tree.find(tag).index(el) != -1 || hideTree();
	}
	function showTree(){
		var off = a.offset();
		tree.css({
			left:off.left,
			top:off.top + a.outerHeight(true) + 1,
			minWidth:a.outerWidth() - 2,
			display:'block'
		});
		setTimeout(function(){
			doc.bind('mousedown', blurTree);
		}, 0);
	}
	o.render([], {}, a, o);
	o.initUrl && checked.length &&
	$.getJSON(o.initUrl.replace('%s', encodeURIComponent(checked.join(','))),
	function(json){
		if (json.length) {
			checked = [];
			for (var i=0,t; t = json[i++];) {
				checked.push(t[o.paramVal]);
				storedData[t[o.paramVal]] = t;
			}
			$elem.triggerHandler('initd', [checked, storedData]);
		}
	});
};
$.selectree = selectree;
$.fn.selectree = function(options){
	return this.each(function(){
		selectree(this, options);
	});
};
})(jQuery);

(function($){

    var OPTIONS = {
            name:null,
            url:null,
            initUrl:null,
            paramVal:'value',
            paramTxt:'text',
            paramHaschild:'hasChildren',
            expanded:false,
            ending:false,
            multiple:false,
            selectMult:false,
            extraClass:null
        },
        CLASSES = {
            placetree:'placetree'
        };
    // needs cmstop.tree.js
    var placetree = function(elem, options){
        var o = $.extend({}, OPTIONS, options || {}),
            $elem = elem.jquery ? elem : $(elem);
        elem = $elem[0];
        for (var attr in OPTIONS) {
            var val = $elem.attr(attr);
            val && (o[attr] = val);
        }
        o.value = $elem.val() || o.value;
        var valBox = null,
            classes = (o.extraClass ? (o.extraClass+' ') : '') + CLASSES.placetree,
            multi = o.multiple,
            checked = $.isArray(o.value)
                ? [].concat(o.value)
                : (o.value ? (typeof o.value == 'string' ? o.value.split(',') : [o.value]) : []),
            storedData = {},
            tree = null, treeInited = 0,
            type = (multi ? 'checkbox' : 'radio');
        $elem.hide();
        if (o.name) {
            if (elem.nodeName == 'INPUT' || elem.nodeName == 'TEXTAREA') {
                valBox = $elem;
            } else {
                valBox = $('<input name="'+o.name+'" type="hidden" />').insertAfter(elem).hide();
                $elem.removeAttr('name');
                elem.nodeName == 'SELECT' && $elem.attr('disabled', 'true');
            }
        }
        $elem.bind('changed initd', function(){
            if (valBox) {
                valBox.val(checked.join(','));
                valBox.change();
            }
        });
        function check(input){
            checked.indexOf(input.value) == -1 && checked.push(input.value);
            input.checked = true;
        }
        function uncheck(input){
            var i = checked.indexOf(input.value);
            i != -1 && checked.splice(i, 1);
            input.checked = false;
        }
        function initTree() {
            if (treeInited) return;
            treeInited = 1;
            tree = $('<div class="'+classes+'" idv="tree"></div>').insertAfter(elem).show();
            new $.tree(tree, {
                paramId:o.paramVal,
                paramHaschild:o.paramHaschild,
                expanded:o.expanded,
                url:o.url,
                renderTxt:function(div, val, item){
                    var	label;
                    if (o.ending && item[o.paramHaschild]) {
                        if (!multi || !o.selectMult) {
                            return $('<label><span>'+item[o.paramTxt]+'</span></label>');
                        }
                    }
                    storedData[val] = item;
                    label = $('<label><input type="'+type+'" value="'+val+'" /> <span>'+item[o.paramTxt]+'</span></label>');
                    var input = label.find('input');
                    item.elem = label;
                    var li = div.parent(), pinput = li.parent().prev().find('input');
                    input.click(function(e){
                        e.stopPropagation();
                        if (multi) {
                            if (this.checked) {
                                li.children('ul').find('input').each(function(){
                                    this.disabled = true;
                                    uncheck(this);
                                    this.checked = true;
                                });
                                check(this);
                            } else {
                                uncheck(this);
                                li.children('ul').find('input').attr('checked',false).attr('disabled', false).removeAttr('disabled');
                            }
                        } else {
                            tree.find('input:checked').each(function(){
                                uncheck(this);
                            });
                            checked = [];
                            check(this);
                        }
                        $elem.triggerHandler('changed', [checked, storedData]);
                    });
                    if (multi && (pinput.attr('checked') || pinput.attr('disabled'))) {
                        uncheck(input[0]);
                        input[0].disabled = true;
                        input[0].checked = true;
                    } else {
                        input[0].checked = checked.indexOf(val) != -1;
                    }
                    return label;
                }
            });
        }
        o.initUrl && checked.length &&
        $.getJSON(o.initUrl.replace('%s', encodeURIComponent(checked.join(','))),
            function(json){
                if (json.length) {
                    checked = [];
                    for (var i=0,t; t = json[i++];) {
                        checked.push(t[o.paramVal]);
                        storedData[t[o.paramVal]] = t;
                    }
                    $elem.triggerHandler('initd', [checked, storedData]);
                }
            });
        initTree();
    };
    $.placetree = placetree;
    $.fn.placetree = function(options){
        return this.each(function(){
            placetree(this, options);
        });
    };
})(jQuery);
