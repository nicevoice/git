(function(){

var doc = document, $doc = $(), win = window;
function fieldValue(el) {
	var n = el.name, t = el.type, tag = el.nodeName.toLowerCase();

	if (!n || el.disabled || t == 'reset' || t == 'button' ||
		(t == 'checkbox' || t == 'radio') && !el.checked ||
		tag == 'select' && el.selectedIndex == -1)
	{
		return null;
	}

	if (tag == 'select') {
		var index = el.selectedIndex;
		if (index < 0) return null;
		var a = [], ops = el.options;
		var one = (t == 'select-one');
		var max = (one ? index+1 : ops.length);
		for (var i= (one ? index : 0); i < max; i++) {
			var op = ops[i];
			if (op.selected) {
				var v = op.value;
				if (!v) {
					v = (op.attributes && op.attributes['value'] && !(op.attributes['value'].specified))
						? op.text
						: op.value;
				}
				if (one) return v;
				a.push(v);
			}
		}
		return a;
	}
	return el.value;
}
function formSerialize(form) {
    var els = form.elements;
	if (!els) return '';
	var a = [];
    for (var i=0, max=els.length; i < max; i++) {
        var el = els[i];
        var n = el.name;
        if (!n) continue;

        var v = fieldValue(el);
        if (v && v.constructor == Array) {
            for (var j=0, jmax=v.length; j < jmax; j++)
                a.push({name: n, value: v[j]});
        }
        else if (v !== null && typeof v != 'undefined')
            a.push({name: n, value: v});
    }
    return $.param(a);
}
var LANG = {
	BUTTON_OK:'确定',
	BUTTON_CANCEL:'取消'
};
function form(opt, url, jsonok, formReady, beforeSubmit, cancel) {
	var dialog = $(doc.createElement('DIV')),
	wrap, masker, warn, wival = null,
	form = null, buttons = {}, _buttons,
	submit = function(form) {
		_buttons.attr('disabled', 'disabled');
		masker.css({height:wrap.height(),width:wrap.width()}).show();
		if (beforeSubmit && beforeSubmit(form, dialog) === false) {
			complete();
			return;
		}
		var data = formSerialize(form[0]);
		$.ajax({
			url:url,
			type:'POST',
			dataType:'json',
			data:data,
			success:function(json){
				if (json.state) {
					jsonok(json);
					dialog.dialog("destroy").remove();
				} else {
					showwarn(json.error);
				}
			},
			error:function(){
				showwarn('请求异常');
			},
			complete:complete
		});
	},
	viewReady = function(form){
		wrap = dialog.parent();
		masker = $('<div class="masker"></div>').insertBefore(dialog);
		form.submit(function(e){
			e.preventDefault();
			e.stopPropagation();
			_buttons.eq(0).click();
		});
		formReady && formReady(form, dialog);
	},
	complete = function(){
		masker.hide();
		_buttons.attr('disabled', '').removeAttr('disabled');
	},
	showwarn = function(msg){
		warn || (warn = $('<div class="warning"></div>').prependTo(dialog));
		clearTimeout(wival);
		wival = null;
		warn.html(msg).show();
		wival = setTimeout(function(){
			warn.slideUp();
		}, 3000);
	};
	buttons[LANG.BUTTON_OK] = function() {
		submit(form, dialog);
	};
	buttons[LANG.BUTTON_CANCEL] = function() {
		dialog.dialog("close");
	};
	typeof opt == 'object' || (opt = {title:opt ? opt.toString() : ''});
	opt = $.extend({
		width : 450,
		height: 'auto',
		maxHeight: 500,
		resizable: false,
		modal : true
	}, opt, {
		autoOpen: false,
		buttons : buttons,
		close:function(){
			dialog.dialog("destroy").remove();
			cancel && cancel();
		}
	});
	dialog.dialog(opt).load(url, function(){
		form = dialog.find('form:first');
		viewReady(form, dialog);
		dialog.dialog('open');
	});
	_buttons = dialog.nextAll('div.btn_area').children('button');
	return dialog;
}

var currentPath = null;
var type = null;
var focused = null;
var container = null;
var nav = null;
function _focus(li){
	focused && focused.removeClass('checked');
	focused = li;
	focused && focused.addClass('checked');
}
function _Folder(item){
	var li = $('\
	<li class="dir" path="'+item.path+'" title="'+item.name+'">\
		<div></div>\
		<span>'+item.name+'</span>\
	</li>');
	var delyMet = null;
	li.click(function(){
		if (delyMet) {
			clearTimeout(delyMet);
			delyMet = null;
			// open
			PSN.load(li.attr('path'));
			return;
		}
		// focus
		_focus(li);
		delyMet = setTimeout(function(){
			delyMet = null;
		}, 500);
	}).bind('contextMenu',function(){
		_focus(li);
	});
	li.contextMenu('#'+(type=='dir' ? 'master-dir-menu' : 'slave-dir-menu'),
	function(action){
		PSN[action](li);
	});
	return li;
}
function _File(item) {
	var li = $('\
	<li class="file" path="'+item.path+'" title="'+item.name+'">\
		<div></div>\
		<span>'+item.name+'</span>\
	</li>');
	if (type != 'dir') {
		var delyMet = null;
		li.click(function(){
			if (delyMet) {
				clearTimeout(delyMet);
				delyMet = null;
				// check
				PSN.check(li);
				return;
			}
			// focus
			_focus(li);
			delyMet = setTimeout(function(){
				delyMet = null;
			}, 500);
		}).bind('contextMenu',function(){
			_focus(li);
		});
	} else {
		li.css('cursor', 'default');
		li.bind('contextMenu',function(){
			_focus(null);
		});
	}
	li.contextMenu('#'+(type=='dir' ? 'slave-file-menu' : 'master-file-menu'),
	function(action){
		PSN[action](li);
	});
	return li;
}
var PSN = {
	init:function(t, p) {
		type = t;
		container = $('#container');
		$('#center').mousedown(function(){
			_focus(null);
		}).contextMenu('#main-menu',function(action){
			PSN[action]();
		});
		$('#ctrl>span').click(function(){
			PSN[this.getAttribute('action')]();
		});
		var pos = /{PSN:(\d+)}(.*)/i.exec(p);
		var psnid = pos ? pos[1] : 0;
		var dirname = pos ? pos[2].replace(/(%2f)|(\\+)/ig,'/').replace(/^\/+|\/+$/g,'').replace(/\/+/g,'/').split('/') : [];
		var file = dirname.pop();
		dirname.unshift('{PSN:'+psnid+'}');
		dirname = dirname.join('/');
		
		nav = $('#navigator').navigator({
            dirUrl:'?app=system&controller=psn&action=dir',
            dirVar:'path'
        }).bind('cd', function(e, path){
        	PSN.load(path.replace(/^\/+|\/+$/g,''));
        });
        
		this.load(dirname, file && function(){
			var li = container.find('li[title="'+file+'"]');
			li.length && _focus(li);
		});
		
		$('#bottom>button').click(function(){
			PSN[this.getAttribute('action')](focused);
		});
	},
	check: function(li){
		var path;
		if (li) {
            !li.jquery && (li = $(li));
			path = li.attr('path');
			if (li.hasClass('dir') && type != 'dir') {
				this.load(path);
				return;
			}
		} else if (type == 'dir') {
			path = currentPath;
		} else {
			return;
		}
		dialogCallback.ok(path);
	},
	cancel:function(){
		dialogCallback.cancel();
	},
	open:function(li){
		this.load(li.attr('path'));
	},
	load:function(path, loaded){
		currentPath = path;
		focused = null;
		container.empty();
		$.getJSON('?app=system&controller=psn&action=load&path='+path, function(json){
			for (var i=0,k;k=json.dirs[i++];) {
				container.append(_Folder(k));
			}
			for (var i=0,k;k=json.files[i++];) {
				container.append(_File(k));
			}
			nav.trigger('setNav', ['/'+json.path, '/'+json.alias]);
			loaded && loaded();
		});
	},
	mkdir:function(){
		var url = '?app=system&controller=psn&action=mkdir&path='+currentPath;
		form({
			title:'新建目录',
			width:300
		}, url, function(json){
			var li = _Folder(json.data);
			container.append(li);
			_focus(li);
		});
	},
	mkfile:function(){
		var url = '?app=system&controller=psn&action=mkfile&path='+currentPath;
		form({
			title:'新建文件',
			width:300
		}, url, function(json){
			var li = _File(json.data);
			container.append(li);
			type != 'dir' && _focus(li);
		});
	},
	remove:function(li){
		ct.confirm('此操作不可恢复，确定要删除"'+li.attr('title')+'"吗？',function(){
			var url = '?app=system&controller=psn&action=remove&path='+li.attr('path');
			$.getJSON(url, function(json){
				if (json.state) {
					ct.ok('删除成功');
					focused && (li[0] == focused[0]) && _focus(null);
					li.fadeOut('fast',function(){
						li.remove();
					});
				} else {
					ct.error('删除失败');
				}
			});
		});
	},
	rename:function(li){
		var url = '?app=system&controller=psn&action=rename&path='+li.attr('path');
		form({
			title:'重命名',
			width:300
		}, url, function(json){
			li.attr('path', json.data.path);
			li.attr('title', json.data.name);
			li.find('span').text(json.data.name);
		});
	}
};
window.PSN = PSN;

})();