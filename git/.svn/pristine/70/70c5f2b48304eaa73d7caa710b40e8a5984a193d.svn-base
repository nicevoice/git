if (!window.cmstop_toolbox) {
	// 初始化 cmstop_toolbox
	var cmstop_toolbox = window.cmstop_toolbox = {};

	// 返回窗口宽度
	cmstop_toolbox.getWidth	= window.innerWidth | document.body.clientWidth;

	// 返回窗口高度
	cmstop_toolbox.getHeight = window.innerHeight | document.body.clientHeight;

	// 按条件生成div并返回生成的dom对象
	cmstop_toolbox.divMaker	= function(attribute, style, parentObj) {
		var obj = document.createElement('div');
		for (var key in attribute) {
			if (key == 'class') {
				obj.setAttribute('class', attribute[key]);
				obj.setAttribute('className', attribute[key]);	// 兼容IE
			} else {
				obj.setAttribute(key, attribute[key]);
			}
		}
		for (var key in style) {
			obj.style[key] = style[key];
		}
		if (!parentObj) {
			parentObj = document.body;
		}
		parentObj.appendChild(obj);
		return obj;
	};

	// 事件绑定
	cmstop_toolbox.bind = function bind(obj, action, func) {
		if (window.addEventListener) {
			obj.addEventListener( action, function(event) {
				func(obj, event);
			}, false);
		} else if (window.attachEvent) { //IE
			obj.attachEvent('on' +action, function(event) {
				func(obj, event);
			});
		}
	};

	// 事件解绑
	cmstop_toolbox.unbind = function(obj, action, func) {
		if (window.removeEventListener) {
			obj.removeEventListener(action, func , false);
		} else if (window.detachEvent) { //IE
			obj.detachEvent(action, func);
		}
	};

	// 拖拽类
	cmstop_toolbox.drag = function(dragObj, moveObj) {
		var isDrag = false;
		var x = 0, y = 0;
		dragObj.style.cursor = 'move';
		// 拖动鼠标时
		var _mousemove = function(obj, event) {
			if (!isDrag) {
				return
			}
			moveObj.style.left	= x +  event.clientX + 'px';
			moveObj.style.top	= y +  event.clientY + 'px';
			parseInt(moveObj.style.top) < 0 && (moveObj.style.top = '0');
			cmstop_toolbox.bind(document.body, 'mouseup', _mouseup);
			return false;
		};
		// 松开鼠标时
		var _mouseup = function() {
			if (!isDrag) {
				return
			}
			cmstop_toolbox.unbind(document.body, 'mousemove', _mousemove);
			cmstop_toolbox.unbind(document.body, 'mouseup', _mouseup);
			isDrag = false;
			return false;
		};
		var _mousedown = function(obj, event) {
			if (isDrag) {
				return;
			}
			isDrag = true;
			x	= parseInt(moveObj.style.left) - event.clientX;
			y	= parseInt(moveObj.style.top)  - event.clientY;
			cmstop_toolbox.bind(document.body, 'mousemove', _mousemove);
		};
		// 按下鼠标时
		cmstop_toolbox.bind(dragObj, 'mousedown', _mousedown);
	};

	// 获得浏览器宽度
	cmstop_toolbox.getWidth	= function() {
		var width	= window.innerWidth;
		if (width == undefined) { // IE
			width	= document.documentElement.clientWidth;
		}
		return width;
	};

	// 获得浏览器高度
	cmstop_toolbox.getHeight = function() {
		var height	= window.innerHeight;
		if (height == undefined) { //IE
			height	= document.documentElement.clientHeight;
			height	= ((window.screen.height - 100) < height) ? window.screen.height - 100 : height;
		}
		return height;
	};

	// 工具窗口
	cmstop_toolbox.toolWin	= function() {
		var self = this;
		self.isopen = false;
		var cmstopToolbar		=  null;
		var cmstopToolbarBody	= null;
		var openStatus			= true;
		var cmdButton	= [
			{'class':'ico1', 'title':'\u8f6c\u8f7d', 'event':'reproduce','condition':'true'},
			{'class':'ico2', 'title':'\u7f16\u8f91', 'event':'edit', 'condition':'cmstop_toolbox.isMySite'},
			{'class':'ico4', 'title':'\u5220\u9664', 'event':'delete', 'condition':'cmstop_toolbox.isMySite'},
			{'class':'ico5', 'title':'\u7ba1\u7406', 'event':'admin','condition':'true'},
			{'class':'ico6', 'title':'\u9000\u51fa', 'event':'logout','condition':'true'}
		];
		self.open = function() {
			if (self.isopen) {
				return false;
			}
			self.isopen = true;
			var a, btn, logo, width;
			// 生成UI
			width	= cmstop_toolbox.getWidth();
			cmstopToolbar	= cmstop_toolbox.divMaker({"class":"cmstop-toolbar"}, {'top':'20px', 'left':(width-120+'px')});
			logo = cmstop_toolbox.divMaker({"class":"cmstop-toolbar-logo"}, {}, cmstopToolbar);
			cmstopToolbarBody = cmstop_toolbox.divMaker({"class":"cmstop-toolbar-body"}, {}, cmstopToolbar);
			var cmstopToolbarFoot = cmstop_toolbox.divMaker({"class":"cmstop-toolbar-foot"}, {}, cmstopToolbar);
			cmstop_toolbox.divMaker({"class":"cmstop-toolbar-bg"}, {}, cmstopToolbarBody);
			cmstopToolbarFoot.innerHTML	 = '<a id="cmstop_openstatus" class="cmstop-toolbar-size-switch-drop cmstop-toolbar-open-status" href="javascript:void(0);" onclick="cmstop_toolbox.toolWin.sizeToggle()" target="_self"></a>';
			cmstopToolbarFoot.innerHTML += '<a class="cmstop-toolbar-size-switch-close" href="javascript:void(0);" onclick="cmstop_toolbox.toolWin.close();" target="_self"></a>';
			cmstopToolbarFoot.innerHTML += '<div class="cmstop-toolbar-shadow-radius"></div>';
			// 图片绑定禁止拖动
			logo.ondragstart=function (){return false;};
			// 生成按钮
			for (var i in cmdButton) {
				btn	= cmdButton[i];
				if (!eval(btn['condition'])) {
					continue;
				}
				a = document.createElement('a');
				a.setAttribute('href'		, "javascript:void((function(){cmstop_toolbox_domain_admin='"+cmstop_toolbox.adminUrl+"';cmstop_toolbox_ver=2;cmstop_toolbox_cmd='"+btn['event']+"';if(typeof(cmstop_toolbox)!='undefined'){cmstop_toolbox.ready(cmstop_toolbox_cmd);return}var%20e=document.createElement('script');e.setAttribute('src',cmstop_toolbox_domain_admin+'js/cmstop.toolbox.js');e.setAttribute('charset','utf-8');document.body.appendChild(e)})())");
				a.setAttribute('class'		, 'cmstop-toolbar-btn cmstop-toolbar-' + btn['class']);
				a.setAttribute('className'	, 'cmstop-toolbar-btn cmstop-toolbar-' + btn['class']);
				a.setAttribute('title'		, btn['title']);
				a.setAttribute('id'			, 'cmstop_toolbox_menu_' + btn['event']);
				a.setAttribute('onclick'	, 'cmstop_toolbox.ready("' + btn['event'] + '");return false;');
				a.setAttribute('target'		, '_self');
				cmstopToolbarBody.appendChild(a);
			}
			cmstop_toolbox.drag(logo, cmstopToolbar);
		};
		self.sizeToggle = function() {
			var displayValue, btns = cmstopToolbarBody.getElementsByTagName('a');
			if (openStatus) {
				document.getElementById('cmstop_openstatus').setAttribute('class', 'cmstop-toolbar-size-switch-drop cmstop-toolbar-min-status');
				document.getElementById('cmstop_openstatus').setAttribute('className', 'cmstop-toolbar-size-switch-drop cmstop-toolbar-min-status');
				displayValue = 'none';
				openStatus = false;
			} else {
				document.getElementById('cmstop_openstatus').setAttribute('class', 'cmstop-toolbar-size-switch-drop cmstop-toolbar-open-status');
				document.getElementById('cmstop_openstatus').setAttribute('className', 'cmstop-toolbar-size-switch-drop cmstop-toolbar-open-status');
				displayValue = 'block';
				openStatus = true;
			}
			for (var a in btns) {
				if (typeof (btns[a]) == 'object') {
					 btns[a].style.display = displayValue;
				}
			}
		};
		self.close = function() {
			document.body.removeChild(cmstopToolbar);
			self.isopen = false;
		};
	};

	// 主窗口
	cmstop_toolbox.mainWin	= function() {
		var self = this;
		self.isopen = false;
		self.miniwin = false;
		var messageboxContainer = null;
		var messageboxHd	= null;
		var messageboxBd	= null;
		var messageboxFt	= null;
		var messageMainWin	= null;
		var option	= {};
		self.open = function(o) {
			if (self.isopen) {
				return false;
			}
			self.isopen = true;
			var headContent,headTitle,sizeControl,sizeControlItem,a,ifm,left;
			option = o || {};
			option.width	= option.width || 850;
			option.height	= option.height || 400;
			option.title	= option.title || '';
			left = (cmstop_toolbox.getWidth() - option.width) / 2;
			if (left < 120) {
				left = 0;
			} else {
				left += 'px';
			}
			messageboxContainer = cmstop_toolbox.divMaker({'class':'cmstop-messagebox'}, {'width':(option.width+12)+'px','top':0, 'left':left});
			messageboxHd	= cmstop_toolbox.divMaker({'class':'cmstop-messagebox-head'}, {}, messageboxContainer);
			messageboxBd	= cmstop_toolbox.divMaker({'class':'cmstop-messagebox-body'}, {}, messageboxContainer);
			messageboxFt	= cmstop_toolbox.divMaker({'class':'cmstop-messagebox-foot'}, {}, messageboxContainer);
			headContent		= cmstop_toolbox.divMaker({'class':'cmstop-messagebox-head-content'}, {}, messageboxHd);
			headTitle		= cmstop_toolbox.divMaker({'class':'cmstop-messagebox-head-title'}, {}, headContent);
			headTitle.innerHTML	+= '<div class="cmstop-messagebox-head-ico"></div>';
			headTitle.innerHTML	+= '<h2>' + option.title + '</h2>';
			sizeControl		= cmstop_toolbox.divMaker({'class':'cmstop-messagebox-size-control'}, {}, messageboxHd);
			sizeControlItem	= ['minsize','closepanel'];
			for (var i=0; i < sizeControlItem.length; i++) {
				a = document.createElement('a');
				a.setAttribute('class', 'cmstop-messagebox-head-' + sizeControlItem[i]);
				a.setAttribute('className', 'cmstop-messagebox-head-' + sizeControlItem[i]);
				a.setAttribute('href', 'javascript:;');
				a.setAttribute('target', '_self');
				sizeControl.appendChild(a);
				cmstop_toolbox.bind(a, 'click' , cmstop_toolbox.messageBox[sizeControlItem[i]]);
				cmstop_toolbox.bind(a, 'mousedown', function(){return false;});
				a.ondragstart = function() {return false;}
				a.cancelBubble = true;
				a = undefined;
			}
			cmstop_toolbox.divMaker({'class':'cmstop-messagebox-head-left'}, {}, messageboxHd);
			cmstop_toolbox.divMaker({'class':'cmstop-messagebox-head-right'}, {}, messageboxHd);
			messageMainWin	= cmstop_toolbox.divMaker({'class':'cmstop-messagebox-body-content'}, {'width':(option.width+'px'), 'height':(option.height-40+'px')}, messageboxBd);
			if (option.url) {
				var ifm	= document.createElement('iframe');
				ifm.src	= option.url;
				ifm.style.width		= '100%';
				ifm.style.height	= '100%';
				ifm.frameBorder		= 0;
				messageMainWin.appendChild(ifm);
			}
			if (typeof (option.content) == 'object') {
				messageMainWin.appendChild(option.content);
			}
			if (typeof (option.content) == 'string') {
				messageMainWin.innerHTML = option.content;
			}				
			cmstop_toolbox.divMaker({'class':'cmstop-messagebox-foot-center'},{}, messageboxFt);
			cmstop_toolbox.divMaker({'class':'cmstop-messagebox-foot-left'},{}, messageboxFt);
			cmstop_toolbox.divMaker({'class':'cmstop-messagebox-foot-right'},{}, messageboxFt);
			cmstop_toolbox.drag(messageboxHd, messageboxContainer);
			setInterval(function(){_isClose();}, 1000);
		};
		self.minsize = function() {
			if (!self.miniwin) {
				messageboxBd.style.display	= 'none';
				self.miniwin = true;
			} else {
				messageboxBd.style.display	= 'block';
				self.miniwin = false;
			}
			return false;
		};
		self.fullsize = function() {
			//	没这功能
		};
		self.closepanel = function() {
			document.body.removeChild(messageboxContainer);
			self.isopen = false;
			return false;
		};
		var _isClose = function() {
			if (location.hash == "#close") {
				self.closepanel();
				location.hash = '';
			}
		}
	};

	// 加载css
	var _loadCSS = function() {
		var cssUrl	= cmstop_toolbox.adminUrl + 'css/cmstop-toolbox.css';
		try {
			window.document.head.innerHTML += '<link type="text/css" rel="stylesheet" href="' + cssUrl + '" />';
		} catch (e) {	// 兼容IE
			window.document.createStyleSheet(cssUrl);
		}
	};

	// 初始化
	cmstop_toolbox.ready	= function(cmd) {
		if (typeof (cmstop_toolbox_ver) == 'undefined' || cmstop_toolbox_ver != 2) {
			alert('\u60a8\u7684\u7f51\u7f16\u5de5\u5177\u680f\u8fc7\u65e7,\n\u8bf7\u91cd\u65b0\u4e0b\u8f7d');
			return;
		}
		switch (cmd) {
		case 'start':
			cmstop_toolbox.adminUrl	= cmstop_toolbox_domain_admin;
			var temp_arr = cmstop_toolbox.adminUrl.split('.');
			temp_arr.shift();
			cmstop_toolbox.domain	= temp_arr.join('.').replace(/\/+/, '');
			cmstop_toolbox_domain_admin = undefined;
			cmstop_toolbox.isMySite = (function(){var r = new RegExp(cmstop_toolbox.domain);return r.test(location.host);})()
			_loadCSS();
			try {
				cmstop_toolbox.toolWin = new cmstop_toolbox.toolWin();
			}
			catch (e) {}
			if (!cmstop_toolbox.toolWin.isopen) {
				cmstop_toolbox.toolWin.open();
			}
			if (cmstop_toolbox.isMySite) {
				break;
			}
		case 'reproduce':
			try {
				cmstop_toolbox.messageBox = new cmstop_toolbox.mainWin();
			}
			catch (e) {}
			if (!cmstop_toolbox.messageBox.isopen) {
				var url = cmstop_toolbox.adminUrl;
				url += '?app=article&controller=article&action=miniadd';
				url += '&source=' + encodeURIComponent(window.location.href);
				url += '&sourcetitle=' + encodeURIComponent(window.document.title);
				var height = cmstop_toolbox.getHeight() || 400;
				height -= 32;
				cmstop_toolbox.messageBox.open({
					'width'	: 850,
					'height': height,
					'title'	: '\u4e00\u952e\u8f6c\u8f7d',
					'url'	: url
				});
			}
			break;
		case 'edit':
			try {
				cmstop_toolbox.messageBox = new cmstop_toolbox.mainWin();
			}
			catch (e) {}
			if (!cmstop_toolbox.messageBox.isopen) {
				var url	 = cmstop_toolbox.adminUrl;
				url		+= '?app=article&controller=article&action=miniedit';
				url		+= '&contentid=' + (contentid || '') + '&url=' + location.href;
				var height = cmstop_toolbox.getHeight() || 400;
				height -= 32;
				cmstop_toolbox.messageBox.open({
					'width'	: 800,
					'height': height,
					'title'	: '\u7f16\u8f91\u6587\u7ae0',
					'url'	: url
				});
			}
			break;
		case 'delete':
			try {
				cmstop_toolbox.messageBox = new cmstop_toolbox.mainWin();
			}
			catch (e) {}
			if (!cmstop_toolbox.messageBox.isopen) {
				var contentIfm, statusBar, okBtn, canelBtn;
				contentIfm	= document.createElement('div');
				contentIfm.style.textAlign	= 'center';
				contentIfm.innerHTML	= '<p style="padding: 12px 0; font-size: 16px;">\u786e\u5b9a\u8981\u5220\u9664\u8fd9\u7bc7\u6587\u7ae0\u4e48?</p>';
				statusBar	= cmstop_toolbox.divMaker({'class':'cmstop-messagebox-body-statusbar'}, {}, contentIfm);
				canelBtn	= document.createElement('a');
				canelBtn.setAttribute('class', 'cmstop-messagebox-body-statusbar-cancel');
				canelBtn.setAttribute('className', 'cmstop-messagebox-body-statusbar-cancel');
				canelBtn.href	= 'javascript:;';
				canelBtn.innerHTML = '\u53d6\u6d88';
				statusBar.appendChild(canelBtn);
				okBtn	= document.createElement('input');
				okBtn.setAttribute('class', 'cmstop-messagebox-body-statusbar-ok');
				okBtn.setAttribute('className', 'cmstop-messagebox-body-statusbar-ok');
				okBtn.type	= 'button';
				okBtn.value	= '\u786e\u5b9a';
				okBtn.style.cursor	= 'pointer';
				statusBar.appendChild(okBtn);
				cmstop_toolbox.bind(okBtn, 'click', function() {
					var ifm = document.createElement('iframe');
					ifm.src	= cmstop_toolbox.adminUrl + '?app=article&controller=article&action=remove&contentid='+contentid;
					ifm.style.display	= 'none';
					document.body.appendChild(ifm);
					cmstop_toolbox.bind(ifm, 'load', function() {
						location.href='http://'+location.host;
					});
				});
				cmstop_toolbox.bind(canelBtn, 'click', function() {
					cmstop_toolbox.messageBox.closepanel();
				});
				cmstop_toolbox.messageBox.open({
					'width'	: 240,
					'height': 120,
					'title'	: '\u662f\u5426\u5220\u9664?',
					'content': contentIfm
				});
			}
			break;
		case 'admin':
			var a = document.createElement('a');
			a.setAttribute('href', cmstop_toolbox.adminUrl);
			a.setAttribute('target', '_blank');
			document.body.appendChild(a);
			try {
				a.click();
			} catch (e) {
				try {
					var e = document.createEvent('MouseEvents');
					e.initEvent( 'click', true, true );
					a.dispatchEvent(e);
				} catch (e) {
					location.href = cmstop_toolbox.adminUrl;
				}
			}
			break;
		case 'logout':
			var ifm = document.createElement('iframe');
			ifm.src	= cmstop_toolbox.adminUrl + '?app=system&controller=admin&action=logout';
			ifm.style.display	= 'none';
			document.body.appendChild(ifm);
			cmstop_toolbox.bind(ifm, 'load', function() {
				document.location.reload();
			});
			break;
		}

	};
}
window.cmstop_toolbox.ready(cmstop_toolbox_cmd);