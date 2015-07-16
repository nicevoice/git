/**
 * LICENSE
 *
 * @copyright Copyright (c) 2008-2010 cmstop (http://cmstop.com)
 * @author    muqiao
 * @needs     backend.css & index.php & cmstop.js
 * @version   $Id: jquery.tabview.js 3 2009-03-24 04:01:20Z yanbingbing@cmstop.com $
 */
(function($){
	var CONFIGS = {
		saveSessUrl:'',
		saveRecentUrl:'',
		defaultTab:{url:'javascript:false',path:null},
		defaultTitle:'Untitled Tab',
		loaded:function(){},
		switched:function(){}
	};
	var CLASSES = {
		adder:'adder',
		closeOther:'close_other',
		leftbtn:'leftbtn',
		rightbtn:'rightbtn',
		tabList:'tab_list',
		iframeHolder:'iframe_div',
		hover:'hover',
		focus:'focus'
	};
	var _substr = function(str, len) {
		var l = 0;
		for (var i=0; i<str.length; i++) {
			l += str.charCodeAt(i) > 255 ? 2 : 1;
			if (l > len) {
				break;
			}
		}
		if (l <= len) {
			return str;
		}
		len = len - 3;
		while (i--) {
			l -= str.charCodeAt(i) > 255 ? 2 : 1;
			if (l <= len) {
				break;
			}
		}
		return str.substr(0,i+1) + '...';
	};
	var _url = function(url) {
		return url 
			? (url.indexOf('?') == 0 ? (location.protocol + '//'+location.host + location.pathname + url) : url)
			: location.protocol + '//'+location.host + location.pathname;
	};
	var IE7 = $.browser.msie && (parseInt($.browser.version) < 8);
	var _$ = function(){
		var div = document.createElement('div');
		return function(html){
			div.innerHTML = html;
			return $(div.firstChild);
		}
	}();
	var tabview = function(tabContainer, frameContainer, configs)
	{
		configs = $.extend({}, CONFIGS, configs||{});
		configs.defaultTab.url = _url(configs.defaultTab.url);
		var _ciframe = frameContainer.empty(),
		_ctab = _$('<ul class="'+CLASSES.tabList+'"></ul>'),
		_current = null,
		_tabTable = {},
		_logOrder = [],
		_t = this,
		// create adder
		_adder = _$('<div class="'+CLASSES.adder+'" title="&#x6DFB;&#x52A0;&#x65B0;&#x6807;&#x7B7E;"></div>')
		.click(function(){
		    _add(configs.defaultTab.url, configs.defaultTab.path);
		}),
		_closeOther = _$('<div class="'+CLASSES.closeOther+'" title="&#x5173;&#x95ED;&#x5176;&#x5B83;&#x6807;&#x7B7E;&#x9875;"></div>')
		.click(function(){
			confirm('确定关闭其它所有标签页吗？') && _closeOtherAll();
		}).dblclick(function(){return false;}),

		_stepstop = 0,
		dequeue = function(step)
		{
			var _queue = function(){
				if (_stepstop) {
					return;
				}
				var l = step.pop();
				l === undefined || _ctab.animate({left:l},'fast',_queue);
			};
			_queue();
		},

		// add move right
		_mright = _$('<div class="'+CLASSES.leftbtn+'"></div>')
		.hover(function(){
			_ctab.stop(1);
			_stepstop = 0;
			_mright.addClass(CLASSES.hover);
			var posLeft = _ctab.position().left,
				offsetLeft = _mright.outerWidth();
			if (posLeft >= offsetLeft) return;
			var childs = _ctab.children();
			var step = [];
			for (var i=0,j=childs.length;i<j;i++)
			{
				var l = $(childs[i]).position().left;
				if (l + posLeft < offsetLeft)
				{
					step.push(offsetLeft - l)
				}
				else {
					break;
				}
			}
			dequeue(step);
		},function(){
		    _stepstop = 1;
			_mright.removeClass(CLASSES.hover);
		}).dblclick(function(){
		    return false;
		}),

		// add move left
		_mleft = _$('<div class="'+CLASSES.rightbtn+'"></div>')
		.hover(function(){
			_ctab.stop(1);
			_stepstop = 0;
			_mleft.addClass(CLASSES.hover);
			var posLeft = _ctab.position().left,
				offsetLeft = _mleft.position().left;
			if (offsetLeft >= _ctab.outerWidth() + posLeft) return;
			var childs = _ctab.children();
			var step = [];
			for (var i=childs.length;i-->0;)
			{
				var b = $(childs[i]),
					l = b.position().left,
					w = b.outerWidth();
				if (l + w + posLeft  > offsetLeft)
				{
					step.push(offsetLeft - l - w);
				}
				else {
					break;
				}
			}
			dequeue(step);
		},function(){
		    _stepstop = 1;
			_mleft.removeClass(CLASSES.hover);
		}).dblclick(function(){
		    return false;
		}),
		
		// have ctrl btn for scroll?
		scrollable = 0,
		// scroll out btn
		_scrollTo = function(btn,callback){
		    callback || (callback = function(){});
			var l = btn.position().left,
				cl = _ctab.position().left,
				ml = scrollable ? _mright.outerWidth() : 0,nl;
			if (l + cl < ml) {
		        _ctab.animate({left:(ml - l)},'fast',callback);
		        return;
		    }
	        var w = btn.outerWidth(),
	            mr = scrollable ? _mleft.position().left : (tabContainer.innerWidth() - _closeOther.outerWidth());
	        if (l + cl + w > mr)
	        {
	            _ctab.animate({left:(mr - l - w)},'fast',callback);
	            return;
	        }
	        callback();
		},
		_widthAdapt = function(){
		    // calculate all btn total width
			var width = 3;
			_ctab.children().each(function(){
				width += $.css(this,'width',false,'border');
			});
			// set ul width
			_ctab.css('width', width);
			// width of container
			width = _ctab.outerWidth();
			var iWidth = tabContainer.innerWidth() - _closeOther.outerWidth(),
				aWidth = _adder.outerWidth();
			iWidth - aWidth > width ? (
				_mleft.hide(), _mright.hide(),
				_adder.css('left', width-3),
				(scrollable = 0)
			) : (
			    _mleft.show(), _mright.show(),
				_adder.css('left', iWidth - aWidth),
				(scrollable = 1)
			);
		},
		_scrollAdapt = function() {
		    var width = _ctab.outerWidth();
			if (scrollable)
			{
			    var cl = _ctab.position().left,
			        ml = _mleft.position().left, rw;
			    (width + cl < ml) ?
				_ctab.animate({left:ml - width}, 'fast') :
				cl > (rw = _mright.outerWidth() - parseInt(_ctab.css('padding-left')))
				    && _ctab.animate({left:rw},'fast');
			} else {
			    _ctab.animate({left:0}, 'fast');
			}
		};

		tabContainer.dblclick(function(){
			_adder.click();
		}).append(_mright).append(_ctab).append(_mleft).append(_adder).append(_closeOther);
		
		var _tabCount = 1, _guid = 1,
		_saveSession = function() {
		    var orders = [].concat(_logOrder);
			_current && orders.push(_current);
			var lis = _ctab.find('li');
			// occord sort save urls
			var tabs = {};
			var l = lis.length;
			for (var i=0,j = orders.length;i<j;i++) {
				var t = _tabTable[orders[i]], path, index, btn, title;
				t && (btn = t.btn) && (index = lis.index(btn[0])) !=-1 && (
				    (path = btn.data('path') || ''),
				    (title = btn.attr('title') || ''),
				    (tabs[index] = i+'#'+path+'#'+encodeURIComponent(title)
				        +'#'+encodeURIComponent(t.iframe.attr('src')))
				);
			}
			var atabs = [];
			for (var i=0; i<l; i++) {
				tabs[i] && atabs.push(tabs[i]);
			}
			$.post(configs.saveSessUrl,'data='+atabs.join('|'));
		},
		_createTab = function(url, path, title) {
		    _tabCount++;
			var tabguid = _guid++;
			title || (title = configs.defaultTitle);
			url = _url(url);
			// create tab button
			var btn = $('<li><span>'+_substr(title,19)+'</span></li>')
				.hover(function(){
				    $.className.add(this,CLASSES.hover);
				}, function(){
				    $.className.remove(this,CLASSES.hover);
				})
				.click(function(){
					_t.switchTo(tabguid);
				})
				.dblclick(function(e){
					// stop pass event
					e.preventDefault();
					e.stopPropagation();
					_close(tabguid);
				}).attr('url', url||'').attr('title',title||'')
				.data('path', $.isArray(path) ? [].concat(path) : [])
				.data('tabguid',tabguid);
			var closer = $(document.createElement('div'))
				.hover(function(){
    				$.className.add(this,CLASSES.hover);
				},function(){
				    $.className.remove(this,CLASSES.hover);
				})
				.click(function(e){
					e.stopPropagation();
					_close(tabguid);
				});
			btn.append(closer);

			var a = btn.find('span');
			IE7 ? a.focus(function(){this.blur()})
				: a.css('outline','none');

			// create tab iframe
			var iframe = _$('<iframe src="'+url+'" width="100%" height="100%" frameborder="0"></iframe>');
			var holder = _$('<div class="'+CLASSES.iframeHolder+'"></div>');
			

			// create tab stucture
			var ts = {
				'btn'    : btn,
				'iframe' : iframe,
				'holder' : holder
			};
			iframe.bind('load', function(){
			    if (iframe.data('toclose')) return;
			    try {
			    	var ifr = iframe[0];
				    var doc = ifr.contentDocument || ifr.contentWindow.document;
				    var win = ifr.contentWindow;
				    win && (win.__ASSOC_TABID__ = tabguid);
				    btn.attr('url', win.location.href);
					if (doc.title && doc.title.length) {
						a.html(_substr(doc.title,19));
						btn.attr('title',doc.title);
					} else {
						throw "no title";
					}
				}
				catch (e) {
				    var u = btn.attr('url');
					a.html(_substr(u, 19));
					btn.attr('title', u);
				}
				
				_widthAdapt();
				tabguid == _current && _scrollTo(btn);
				
				configs.loaded(ts);
			});
			_tabTable[tabguid] = ts;
            
			// log open order
			_logOrder.unshift(tabguid);
			
			// insertTo container
			_ctab.append(btn);
			_ciframe.append(holder);

			_widthAdapt();
			
			return tabguid;
		},
		_switchTo = function(guid, callback) {
		    if (guid == undefined || guid == 'last') {
				if (!_logOrder.length) {
				    _widthAdapt();
					return;
				}
				guid = _logOrder.pop();
			} else if (_current && guid == _current) {
			    return;
			} else {
				var i = _logOrder.indexOf(guid);
				if (i==-1) {
					return;
				}
				_logOrder.splice(i, 1);
			}
			
			if (_current) {
			    // log view history order
			    _logOrder.push(_current);
			    
			    // hide current
				var ct = _tabTable[_current];
				ct.btn.removeClass(CLASSES.focus);
				ct.holder.removeClass(CLASSES.focus);
			}

			// show target
			var ts = _tabTable[guid];
			ts.btn.addClass(CLASSES.focus);
			_widthAdapt();
			// scroll out current-opend-tab
			_scrollTo(ts.btn, callback);
			var _n = !ts.init;
			ts.init || (ts.init = 1, setTimeout(function(){
			    ts.holder.append(ts.iframe);
			}, 3));
			ts.holder.addClass(CLASSES.focus);

			// set current tab guid
			_current = guid;

			// trigger switched event
			configs.switched(ts);
			return _n;
		},
		_setLocation = function(tab, url, path) {
		    tab.btn.data('path', $.isArray(path) ? [].concat(path) : []).attr('url', url);
		    tab.iframe.attr('src', url);
		},
		_add = function(url, path)
		{
		    var guid = _createTab(url, path);
		    _switchTo(guid, _scrollAdapt);
            return guid;
		},
		_open = function(url, guid, path)
		{
		    /**
			 * url:
			 * target: 'newtab' 'current'
			 * target == undefined target = current
			 */
			(url = $.trim(url)).length || (url = 'javascript:false');
			url = _url(url);
			if (url != configs.defaultTab.url && url != 'javascript:false') {
				var btn = _ctab.find('li[url="'+url+'"]');
				if (btn.length) {
					guid = btn.data('tabguid');
					var ts = _tabTable[guid];
					if (ts) {
				        _switchTo(guid);
				        return;
				    }
				}
			}
			if (guid == undefined || guid == 'current') {
				if (_current) {
					var ts = _tabTable[_current];
					_setLocation(ts, url, path);
					return;
				}
			}
			if (typeof guid == 'number') {
			    var ts = _tabTable[guid];
			    if (ts) {
			        _switchTo(guid);
			        _setLocation(ts, url, path);
			        return;
			    }
			}
			return _add(url, path);
		},
		_close = function(guid) {
			/**
			 * target: current #tab_id|tab_element 
			 * target == undefined target = current
			 */
			// find target
			if (guid == undefined || guid == 'current') {
				if (!_current) {
					return;
				}
				guid = _current;
			}

			var ts = _tabTable[guid];
			if (ts == undefined) {
				return;
			}
			var t = ts.btn.attr('title'), u = ts.btn.attr('url');
			// remove element
			var next = function(){
				_saveRecent(t, u); //add by xuxu
    			ts.btn.remove();
    			ts.iframe.remove();
    			ts.holder.remove();
    
    			// delete tabTable
    			delete ts['btn'];
    			delete ts['iframe'];
    			delete ts['holder'];
    			delete ts['init'];
    			delete _tabTable[guid];
    			_tabCount--;
    			if (guid == _current) {
        			// set current
        			_current = null;
    				// switch to last view
    				_switchTo(null, _scrollAdapt);
    				return;
    			}
    			_widthAdapt();
    			if (_current)
    			{
    			    var ct = _tabTable[_current];
    			    _scrollTo(ct.btn, _scrollAdapt);
    			}
    			else
    			{
    			    _scrollAdapt();
    			}
    			var i = _logOrder.indexOf(guid);
    			if (i!=-1) {
    				_logOrder.splice(i, 1);
    			}
			};
			if (ts.init) {
			    try {
			        var f = ts.iframe, win = f[0].contentWindow;
    		        f.data('toclose', 1);
    		        win.location = "about:blank";
    			    win.tryclose || (
    			        f.bind('load', function(){
    			            f.data('toclose') && setTimeout(next, 0);
    			        }),
    			        (win.tryclose = 1)
    			    );
			    } catch (e) {
			        return next();
			    }
			} else {
			    next();
			}
		},
		_closeOtherEnabled = 1,
		_closeOtherAll = function() {
			if (!_closeOtherEnabled) {
				return;
			}
			var tocloseNum = 0;
			function last() {
				_widthAdapt();
				if (_current) {
    			    var ct = _tabTable[_current];
    			    _tabTable = {};
    			    _tabTable[_current] = ct;
    			    _scrollTo(ct.btn, _scrollAdapt);
    			    _logOrder = [_current];
    			} else {
    				_tabTable = {};
    			    _scrollAdapt();
    			    _logOrder = [];
    			}
    			_closeOtherEnabled = 1;
			}
			function next(ts, t, u) {
				_saveRecent(t, u); //add by xuxu
    			ts.btn.remove();
    			ts.iframe.remove();
    			ts.holder.remove();
    
    			// delete tabTable
    			delete ts['btn'];
    			delete ts['iframe'];
    			delete ts['holder'];
    			delete ts['init'];
    			delete _tabTable[guid];
    			_tabCount--;
    			--tocloseNum == 0 && last();
			}
			function c(guid) {
				var ts = _tabTable[guid],
					t = ts.btn.attr('title'), u = ts.btn.attr('url');
				if (!ts.init) {
					return next(ts, t, u);
				}
			    try {
			        var f = ts.iframe, win = f[0].contentWindow;
    		        f.data('toclose', 1);
    		        win.location = "about:blank";
    			    win.tryclose || (
    			        f.bind('load', function(){
    			            f.data('toclose') && setTimeout(function(){
    			            	next(ts, t, u);
    			            }, 0);
    			        }),
    			        (win.tryclose = 1)
    			    );
			    } catch (e) {
			        return next(ts, t, u);
			    }
			}
			var temp = {};
			for (var guid in _tabTable) {
				guid == _current || (tocloseNum++, temp[guid] = 1);
			}
			if (tocloseNum) {
				_closeOtherEnabled = 0;
				for (var guid in temp) {
					c(guid);
				}
			}
		},
		_get = function(guid) {
		    guid === undefined && (guid = _current);
		    return guid && _tabTable[guid];
		},
		// add by xuxu
		_saveRecent = function(title, url) {
			var nu = configs.defaultTab.url;
			url = _url(url);
			if (url.substr(0, nu.length) == nu) {
				return;
			}
			$.post(configs.saveRecentUrl, {title:title, url:url});
		};
		this.initTabs = function(tabs) {
			if (!$.isArray(tabs)) return;
			var _ordertab = {}, max = 0, min = 0;
			for (var i=0,l=tabs.length;i<l;i++)
			{
				var t = tabs[i];
			    if (!$.isArray(t) || t.length < 4) {
					continue;
				}
				var order = parseInt(t[0]),
				    path = $.trim(t[1]),
				    title = t[2],
				    src = t[3];
				order > max && (max = order);
				order < min && (min = order);
				path = path.length ? path.split(',') : [];
				_ordertab[order] = _createTab(src, path, title);
			}
            _logOrder = [];
			for (var i=min; i<=max; i++)
			{
				_ordertab[i] && _logOrder.push(_ordertab[i]);
			}
			
			_switchTo(null, _scrollAdapt);
		};

		window.onunload = _saveSession;
		// open public method
		this.close = _close;
		this.open = _open;
		this.get = _get;
		this.reload = function(target) {
		    var tab = _get(target), win;
		    tab && (
		      (win = tab.iframe[0].contentWindow) ? win.location.reload()
		          : tab.iframe.attr('src', tab.btn.attr('url'))
		    );
		};
		this.switchTo = function(guid) {
			if (guid == _current) {
				return;
			}
		    _switchTo(guid);
		};
		this.adaptWidth = function() {
		    _widthAdapt();
		    _ctab.stop(1);
		    var tab = _get();
		    tab ? _scrollTo(tab.btn, _scrollAdapt) : _scrollAdapt();
		};
	};
	tabview.setClasses = function(classes){
		classes && $.extend(CLASSES, classes);
	};
	tabview.setConfigs = function(configs){
		configs && $.extend(CONFIGS, configs);
	};
	$.tabview = tabview;
	
	$.fn.tabview = function(configs){
		configs || (configs = {});
		var tab = new tabview(this, $(this.attr('target')),configs)
		this.data('tab', tab);
		return tab;
	};
})(jQuery);