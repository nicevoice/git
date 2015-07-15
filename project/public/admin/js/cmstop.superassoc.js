(function($){
function innerWidth(){
	return document.documentElement.clientWidth;
}
function innerHeight(){
	return document.documentElement.clientHeight;
}
var menuPrefix = 'menu',
headHeight = 61,
tabHeight = 88,
tabContainer = null,
tabObject = null,
iframeContainer = null,
menuNameContainer = null,
routerContainer = null,
treeBrowser = null,
currentRoot = null,
headerContainer = null,
rightContainer = null,
IE7 = $.browser.msie && (parseInt($.browser.version) < 8),
treeOptions = {
	url:"?app=system&controller=index&action=menu&root=%s",
	paramId:"id",
	paramHaschild:"hasChildren",
	active:function(div, id, item){
		if (item.url) {
			var path = _path(div);
			routerContainer.empty();
			while (1) {
				var a = $('<a href="javascript:;">'+div.text()+'</a>').click(function(){
					superAssoc.show($.data(this, 'path'), true);
				}).data('path',[].concat(path));
				routerContainer.prepend(a);
				path.pop();
				if (path.length == 0) {
					break;
				}
				div = div.closest('ul').prev();
			}
		}
		menuNameContainer.html(div.text());
	},
	click:function(div, id, item){
		item.url && tabObject.open(item.url, 'current', _path(div));
	}
},
_path = function(div) {
	var path = div.data('path');
	if (path && path.length) {
		return [].concat(path);
	}
	var li = div[0].parentNode;
	path = [li.getAttribute('idv')];
	while(1){
		li = li.parentNode.parentNode;
		path.unshift(li.getAttribute('idv'));
		if (!$.nodeName(li, 'LI')) {
			break;
		}
	}
	div.data('path', path);
	return path;
},
_scan = function(container,path){
	$('>li[id^='+menuPrefix+']',container).each(function(){
		var id = this.id.substr(menuPrefix.length);
		var _p = path ? [].concat(path) : [];
		_p.push(id);
		(IE7 ? $('>a',this).focus(function(){this.blur()})
			 : $('>a',this).css('outline','none'))
		.click(function(e){
			superAssoc.show(_p, true);
		});
		var ul = $('>ul',this);
		ul.length && ul.each(function(){
			_scan(this, _p);
		});
	});
},
_adapt = function(){
	try{
		var w = innerWidth(), h = innerHeight();
		if (w > 920) {
			headerContainer.width(w);
			rightContainer.width(w-150);
			tabObject && tabObject.adaptWidth();
		}
		treeBrowser.css('height', h - (headHeight + parseInt(treeBrowser.css('padding-top')) + parseInt(treeBrowser.css('padding-bottom'))));
		iframeContainer.css('height', h - tabHeight);
	} catch (e) {}
};
var _popupFunc = {
	fillData:function(popup, form) {
		var tab = tabObject.get();
		if (tab) {
			var t = tab.btn.attr('title'), u = tab.btn.attr('url');
			form[0].name.value = t;
			form[0].url.value = u;
		}
	},
	getNote:function(popup,form) {
		form[0].note.disabled = true;
		$.getJSON("?app=system&controller=my&action=getNote",function(json){
			popup.find('.time').text(json.time);
			form[0].note.value = json.note;
			form[0].note.disabled = false;
		});
	}
};
function _initshortcuts() {
	var popups = $('div.popup');
	$('#shortcut>span[target]').each(function(i){
		var span = $(this);
		var popup = popups.filter('[name="'+span.attr('target')+'"]');
		var form = popup.find('form');
		var visable = 0;
		var Ihide = function(){
			if (!visable) return;
			visable = 0;
			$(document.body).unbind('mousedown', Iblur);
			popup.css('display','none');
			span.removeClass('sc_now');
		};
		var Iblur = function(e){
			var el  = e.target;
			el == span[0] || span.find('*').index(el) != -1
			|| el == popup[0] || popup.find('*').index(el) != -1
			|| Ihide();
		};
		form.submit(function(){
			form.ajaxSubmit({
                dataType:'json',
        		type:'post',
        		success: function(json) {
        			json.state ? ct.ok('保存成功') : ct.error('保存失败');
        		},
        		beforeSubmit:Ihide
			});
			return false;
		});
		// callback fill data
		var cb = span.attr('callback');
		cb = cb && (cb in _popupFunc) ? _popupFunc[cb] : function(){};
		popup.find('.closer').click(Ihide);
		span.click(function(){
			cb(popup, form);
			if (visable) return;
			visable = 1;
			span.addClass('sc_now');
			popup.css({'visibility':'hidden','display':'block'});
			var offset = span.offset();
			popup.css({
				top : span.outerHeight(true) + offset.top + 1,
				left: offset.left - popup.outerWidth() + span.outerWidth(),
				visibility:'visible'
			});
			$(document.body).mousedown(Iblur);
		});
	});
}
window.superAssoc = {
	init:function() {
		menuNameContainer = $("#root_menu_name");
		tabContainer = $('#tab_container');
		iframeContainer = $("#frame_container");
		routerContainer = $('#position');
		treeBrowser = $("#browser");
		headerContainer = $('#head');
		rightContainer = $('#right');
		_adapt();
		_scan('#menu');
		var _keydown = function(e) {
			e || (e = window.event);
			var keycode = e.keyCode ? e.keyCode : e.charCode;
			if (keycode == 116 || e.ctrlKey && keycode==82)
		    {
		        if(document.all) {
					e.keyCode = 0;
				}
				if ( e.preventDefault ) {
        			e.preventDefault();
        		}
        		e.returnValue = false;
        		if ( e.stopPropagation ) {
        			e.stopPropagation();
        		}
        		e.cancelBubble = true;
				superAssoc.reload();
		    }
		};
		var _resetf5 = function(doc) {
			if ( doc.addEventListener ) {
				doc.addEventListener('keydown', _keydown, false );
			} else if ( doc.attachEvent ) {
				doc.attachEvent( 'onkeydown', _keydown );
			}
		};
		
		tabObject = tabContainer.tabview({
			defaultTab:{url:'?app=system&controller=index&action=newtab',path:null},
			saveSessUrl:'?app=system&controller=index&action=savetab',
			saveRecentUrl:'?app=system&controller=index&action=recent',
			switched:function(tab){
				var path = tab.btn.data('path');
				path && path.length && superAssoc.show(path);
			},
			loaded:function(ct){
				var iframe = ct.iframe[0];
				try {
					var doc = iframe.contentDocument || iframe.contentWindow.document;
					doc && _resetf5(doc);
				}
				catch (e) {}
			}
		});
		_resetf5(document);
		$.event.add(window, 'keydown', function(e){
			if (e.keyCode == 32) return false;
		});
		// click first tab
		$.ajax({
		    url:'?app=system&controller=index&action=gettab',
		    dataType:'json',
		    success:function(json){
		        json.length
		          ? tabObject.initTabs(json)
		          : $('#menu>li:eq(0)>a').click();
		    },
		    error:function(){
		        $('#menu>li:eq(0)>a').click();
		    }
		});
		$(window).bind('resize', _adapt);
		
		_initshortcuts();
	},
	refresh:function(path, click){
		if (! path && currentRoot) {
			var div = currentRoot.find('div.active');
			path = div.length ? _path(div) : [currentRoot.attr('idv')];
		}
		treeBrowser.empty();
		currentRoot = null;
		superAssoc.show($.isArray(path) ? [].concat(path) : [], click);
	},
	reload:function(target){
		tabObject.reload(target);
	},
	get:function(target){
	    tabObject.get(target);
	},
	show:function(path, click) {
		if (!($.isArray(path) && path.length)) {
			return;
		}
		var root = path[0], cid,
		_show = path.length > 1 ? (function(){
			currentRoot.tree('open', path.slice(1), click)
		}) : (function(){
			currentRoot.triggerHandler(click ? 'clk' : 'active');
		});
		if (currentRoot &&
			root == (cid = currentRoot.attr('idv')))
		{
			return _show();
		}
		if (cid) {
			currentRoot.hide();
			$('#'+menuPrefix+cid).removeClass('focused');
		}
		var root_li = $('#'+menuPrefix+root);
		root_li.addClass('focused');
		currentRoot = $('#treemenu_'+root);
		if (currentRoot.length) {
			currentRoot.show();
			return _show();
		}
		var txt = $('>a', root_li).text(),
			url = root_li.attr('url');
		
		currentRoot = $('<div class="tree" id="treemenu_'+root+'" idv="'+root+'"><div style="display:none">'+txt+'</div></div>')
		.appendTo(treeBrowser)
		.tree($.extend({
			prepared:_show
		}, treeOptions))
		.bind('clk',function(){
			currentRoot.triggerHandler('active');
			url && tabObject.open(url, 'current', [root]);
		})
		.bind('active', function(){
			currentRoot.tree('resetActive');
			var a = $('<a href="javascript:;">'+txt+'</a>').click(function(){
				superAssoc.show([root], true);
			});
			routerContainer.html(a);
			menuNameContainer.html(txt);
		});
	},
	open:function(url,target,path) {
		if (!$.isArray(path)) {
			var tab = tabObject.get();
			tab && (path = tab.btn.data('path'));
		}
		tabObject.open(url, target, path);
	},
	close:function(target){
	    tabObject.close(target);
	},
	call:function(function_name, args, target) {
        var tab = tabObject.get(target);
        tab && ct.func(function_name,tab.iframe[0].contentWindow)(args);
    }
};
})(jQuery);
