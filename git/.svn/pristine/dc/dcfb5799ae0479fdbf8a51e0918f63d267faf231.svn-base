/**
 * cmstop basework
 */
Array.prototype.indexOf || (Array.prototype.indexOf = function(item,i){
	// for ie
	i || (i = 0);
	var length = this.length;
	if(i < 0){
		i = length + i;
	}
	for(; i < length; i++){
		if(this[i] === item) return i;
	}
	return -1;
});
(function($, window, undefined){
var document = window.document, doc = $(document), doe = document.documentElement,
userAgent = navigator.userAgent.toLowerCase(),
IE = /opera/.test(userAgent) ? 0 : parseInt((/.+msie[\/: ]([\d.]+)/.exec(userAgent) || {1:0})[1]),
IEMode = IE > 7 ? document.documentMode : 0,
IE7 = (IE == 7 || IEMode == 7),
IE8 = IEMode == 8 && IE > 7,
IE9 = IEMode == 9 && IE > 8,
listenning = 1,
_loadingBox = null,
_tips = null,
httpRegex = /^http[s]?\:\/\//i;
function createTips(className, html) {
	var t;
	if (_tips) {
		t = _tips[0];
		var timer = _tips.data('timer');
		_tips.stop();
		timer && (clearTimeout(timer), clearInterval(timer));
	} else {
		t = document.createElement('div');
		_tips = $(t).appendTo(document.body);
	}
	t.className = className;
	t.style.cssText = 'position:fixed;visibility:hidden;z-index:1000000;';
	_tips.html(html);
	return _tips;
}

var bubble = function(){
	this.bubble = $(
	'<div class="bubble">'+
		'<div class="corner tl"></div>'+
		'<div class="corner tr"></div>'+
		'<div class="corner bl"></div>'+
		'<div class="corner br"></div>'+
		'<div class="top"></div>'+
		'<div class="cnt"></div>'+
		'<div class="bot"></div>'+
		'<div class="point"></div>'+
	'</div>').appendTo(document.body);
	this.pointer = this.bubble.find('.point');
	this.cnt = this.bubble.find('.cnt');
};
bubble.prototype = {
	pointTo:function(o){
		var x, y;
		if (o.nodeType == 1 ? (o = $(o)) : o.jquery) {
			var offset = o.offset();
			x = offset.left + parseInt(o[0].offsetWidth / 2);
			y = offset.top + parseInt(o[0].offsetHeight / 2);
		} else if (o.originalEvent) {
			x = o.pageX;
			y = o.pageY;
		} else {
			return;
		}

		var ww = doe.clientWidth, wh = doe.clientHeight, 
			sL = doc.scrollLeft(), sT = doc.scrollTop(),
			$b = this.bubble, b = $b[0], bw, bh,
			pclass, bTop, bLeft;
		b.style.cssText = '';
		bw = b.offsetWidth;
		bh = b.offsetHeight;
		if (!bw|| !bh) {
			b.style.display = 'block';
			bw = b.offsetWidth;
			bh = b.offsetHeight;
		}
		b.style.width = bw+'px';
		if ((wh / 2) > (y - sT)) {
			bTop = y + parseInt(this.pointer.height()) + 13;
			pclass = 'S';
		} else {
			bTop = y - bh - parseInt(this.pointer.height()) - 2;
			pclass = 'N';
		}
		if ((ww / 2) > (x - sL)) {
			bLeft = x - 13;
			pclass += 'W';
		} else {
			bLeft = x - bw + 10;
			pclass += 'E';
		}
		this.pointer[0].className = 'point '+pclass;
		$b.css({left:bLeft, top:bTop});
		return this;
	},
	setYellow:function(flag){
		this.bubble[flag ? 'addClass' : 'removeClass']('yellow');
		return this;
	},
	html:function(html){
		this.cnt.html(html);
		return this;
	},
	get:function(){
		return this.bubble;
	}
};
var cmstop = {
	IE : IE,
	IE7 : IE7,
	IE8 : IE8,
	pos:function (pos, width, height) {
		pos || (pos = 'right');
    	var sL = doc.scrollLeft(), sT = doc.scrollTop(),
    		iH = doe.clientHeight, iW = doe.clientWidth;
    	var style = {}, offset;
    	if (pos == 'top') {
    		style.top = 2;
    		style.left = (iW-width)/2;
    	} else if (pos == 'right') {
    		style.top = 2;
    		style.right = 2;
    	} else if (pos == 'center') {
    		style.top = (iH-height) * .382;
    		style.left = (iW-width)/2;
    	} else if (pos.nodeType == 1 ? (pos = $(pos)) : pos.jquery ) {
    		offset = pos.offset();
    		offset.left = offset.left - sL;
    		offset.top = offset.top - sT;
    		if (offset.left + width > iW) {
    			style.left = offset.left - width + pos.outerWidth();
    		} else {
    			style.left = offset.left;
    		}
    		var ph = pos.outerHeight();
    		if (offset.top + height + ph > iH) {
    			style.top = offset.top - height;
    		} else {
    			style.top = offset.top + pos.outerHeight();
    		}
    	} else if (pos.originalEvent) {
    		offset = {
    			left : pos.pageX - sL,
    			top  : pos.pageY - sT
    		};
    		if (offset.left + width > iW) {
    			style.left = offset.left - width;
    		} else {
    			style.left = offset.left;
    		}
    		if (offset.top + height > iH) {
    			style.top = offset.top - height;
    		} else {
    			style.top = offset.top;
    		}
    	}
    	return style;
	},
	/**
	 * get correct reference of function
	 */
	func:function(ns, context) {
        if (typeof ns == 'function') {
            return ns;
        }
        if (typeof ns == 'string') {
            ns = ns.split('.');
            var o = (context || window)[ns[0]], w = null;
            if (!o) return null;
            for (var i=1,l;l=ns[i++];) {
                if (!o[l]) {
                    return null;
                }
                w = o;
                o = o[l];
            }
            return o && (function(){
                return o.apply(w, arguments);
            });
        }
        return null;
    },
    /**
     * parse string to JSON object
     */
    parseToJSON: function(data) {
        if (data) {
            try {
                return window.JSON.parse(data);
            } catch (e) {
                try {
                    return (new Function('return ' + data))();
                } catch (e) {}
            }
        }
        return data;
    },
    /**
     * detect if error occured after $.load / $(elem).load,
     * used for formDialog / ajaxDialog or manual call.
     */
    detectLoadError: function(elem) {
        var data = elem && elem.jquery && elem.html() || elem;
        if (! data) return false;
        if ($.isFunction(data.charAt) && data.charAt(0) != '{') {
            data = undefined;
            return false;
        }
        data = this.parseToJSON(data);
        if (data && data.state != undefined && ! data.state) {
            ct.error(data.error || '加载时遇到问题，请重新尝试');
            data = undefined;
            return true;
        }
        data = undefined;
        return false;
    },
    
    /**
     * listen
     */
	listenAjax:function() {
		$().ajaxStart(function(){
			listenning && cmstop.startLoading();
			listenning = 1;
		}).ajaxStop(function(){
			cmstop.endLoading();
		}).ajaxError(function(){
			cmstop.endLoading();
		});
	},
    stopListenOnce:function() {
        listenning = 0;
    },
    startLoading:function(pos, msg, width) {
    	if (_loadingBox) return _loadingBox;
    	msg || (msg = '载入中……');
    	_loadingBox = $('<div class="loading" style="position:fixed;visibility:hidden"><sub></sub> '+msg+'</div>')
    		.appendTo(document.body);
    	if (!isNaN(width = parseFloat(width)) && width)
    	{
    		_loadingBox.css('width', width);
    	}
    	var style = cmstop.pos(pos, _loadingBox.outerWidth(true), _loadingBox.outerHeight(true));
    	style.visibility = 'visible';
    	_loadingBox.css(style);
    	return _loadingBox;
    },
    endLoading:function() {
    	_loadingBox && _loadingBox.remove();
    	_loadingBox = null;
    },
    tips:function(msg, type, pos, delay) {
    	(!type || type == 'ok') && (type = 'success');
    	var tips = createTips('ct_tips '+type, '<sub></sub> '+msg), ival,
    	a = $('<a style="margin-left:10px;color:#000080;text-decoration:underline;" href="close">知道了</a>').click(function(e){
    		e.stopPropagation();
    		e.preventDefault();
    		tips.fadeOut('fast');
    		ival && clearTimeout(ival);
    		ival = null;
    	}).appendTo(tips);
    	pos || (pos = 'center');
    	var style = cmstop.pos(pos, tips.outerWidth(true), tips.outerHeight(true));
    	style.visibility = 'visible';
    	tips.css(style);
    	delay === undefined && (delay = 3);
    	delay && (ival = setTimeout(function(){
    		tips.fadeOut('fast');
    	}, delay * 1000), tips.data('timer', ival));
		return tips;
    },
	timer:function(msg, sec, type, callback, pos) {
		type || (type = 'success');
		msg = msg.replace('%s','<b class="timer">'+sec+'</b>');
		var tips = createTips('ct_tips '+type, '<sub></sub> '+msg),
			timer = tips.find('b.timer'),
			clause = tips.find('.clause');
    	pos || (pos = 'center');
    	var style = cmstop.pos(pos, tips.outerWidth(true), tips.outerHeight(true));
    	style.visibility = 'visible';
    	tips.css(style);
		var iv = setInterval(function(){
			timer.text(--sec);
			sec < 1 && last();
		}, 1000);
		tips.data('timer', iv);
		var last = function(){
			iv && clearInterval(iv);
			iv = null;
			tips.hide();
			callback();
			return false;
		};
		clause.click(last);
		return tips;
	},
    alert:function(msg, type) {
    	return this.tips(msg, type, 'center', 0);
    },
	ok:function(msg, pos, delay) {
        return this.tips(msg, 'success', pos, delay);
    },
    error:function(msg, pos, delay) {
        return this.tips(msg, 'error', pos, delay);
    },
    warn:function(msg, pos, delay) {
    	return this.tips(msg, 'warning', pos, delay);
    },
	confirm:function(msg, ok, cancel, pos) {
		var tips = createTips('ct_tips confirm', '<sub></sub> '+msg+'<br/>');
		$('<button type="button" class="button_style_1">确定</button>').click(function(){
    		ok && ok(tips);
    		tips.hide();
    	}).appendTo(tips);
    	cancelBtn = $('<button type="button" class="button_style_1">取消</button>').click(function(){
    		cancel && cancel();
    		tips.hide();
    	}).appendTo(tips);
    	pos || (pos = 'center');
    	var style = cmstop.pos(pos, tips.outerWidth(true), tips.outerHeight(true));
    	style.visibility = 'visible';
    	tips.css(style);
    	return tips;
	},
	iframe:function(opt, callbacks, onload){
		typeof opt == 'object' || (opt = {url:opt ? opt.toString() : ''});
		opt = $.extend({
			width : 450,
			height: 'auto',
			maxHeight: 500,
			resizable: false,
			modal : true
		}, opt, {
			close:function(){
				bindclosed
				  ? iframe.trigger('close')
				  : dialog.dialog('destroy').remove();
			}
		});
		var dialog = $(document.createElement('div')),
			iframe = $('<iframe frameborder="0" scrolling="auto" src="'+(opt.url||opt.title)+'" width="100%" height="100%" ></iframe>').appendTo(dialog),
			masker = $('<div class="masker"></div>').insertBefore(iframe),
			bindclosed = 0;
		function showMasker(){
		    masker.show();
		    doc.mouseup(hideMasker);
		}
		function hideMasker(){
		    doc.unbind('mouseup', hideMasker);
		    masker.hide();
		}
		dialog.dialog(opt);
		var span = dialog.prev().mousedown(showMasker).children('span:first'), ival;
		dialog.nextAll('.ui-resizable-handle').mousedown(showMasker);
		dialog.css('overflow', 'hidden');
		iframe.bind('load', function(){
			ival && clearTimeout(ival);
			try {
				var d = this.contentDocument || this.contentWindow.document,
					de = d.documentElement,
					w = (this.contentWindow || this);
				if (! bindclosed) {
					iframe.bind('close',function(){
						iframe.unbind().bind('load', function(){
							dialog.dialog('destroy').remove();
						});
						w.location = "about:blank";
					});
					ival && clearTimeout(ival);
					bindclosed = 1;
				}
				if (opt.width == 'auto' || opt.height == 'auto') {
					opt.width == 'auto' && iframe.width(de.scrollWidth);
					opt.height == 'auto' && iframe.height(de.scrollHeight);
					ival = setInterval(function(){
						opt.width == 'auto' && iframe.width(de.scrollWidth);
						opt.height == 'auto' && iframe.height(de.scrollHeight);
					}, 600);
				}
				dialog.dialog('option', 'position', 'center');
				w.getDialog = function(){
				    return dialog;
				};
				callbacks && (w.dialogCallback = callbacks);
				if (d.title && d.title.length) {
					span.text(d.title);
				} else {
					throw "no title";
				}
			} catch (e) {
			    span.text(this.src);
			}
			typeof onload == 'function' && onload(iframe);
		});
		return dialog;
	},
	ajaxDialog:function(opt, url, load, ok, cancel) {
        var buttons = {}, self = this;
        if (typeof ok == 'function') {
        	buttons['确定'] = function(){
        		ok(dialog) && dialog.dialog('close');
        	}
        }
        if (typeof cancel == 'function') {
        	buttons['取消'] = function(){
        		cancel(dialog) && dialog.dialog('close');
        	}
        }
    	typeof opt == 'object' || (opt = {title:opt ? opt.toString() : ''});
		opt = $.extend({
			width : 450,
			height: 'auto',
			maxHeight: 500,
			resizable: false,
			modal : true
		}, opt, {
            autoOpen:false,
            buttons:buttons,
			close:function(){
				dialog.dialog('destroy').remove();
			}
		});
        var dialog = $(document.createElement('div'));
        dialog.dialog(opt).load(url, function(){
            if (self.detectLoadError(dialog)) return;
		    dialog.dialog('open');
        	typeof load == 'function' && load(dialog);
        }).bind('ajaxload',function(){
            if (self.detectLoadError(dialog)) return;
            typeof load == 'function' && load(dialog);
        }).css('position', 'relative');
        return dialog;
    },
    formDialog:function(opt, url, submitBack, formReady, beforeSubmit, beforeSerialize)
    {
        var form = null;
        function load(dialog) {
            form = $('form:first', dialog);
            var wrap = dialog.parent(), masker, 
            	buttonArea = dialog.nextAll('div.btn_area'),
            	msg, mival = null;
            if (form.length) {
	            masker = $('<div class="masker"></div>').insertBefore(dialog);
                typeof formReady == 'function' && formReady(form, dialog);
                var showmsg = function(html, type){
                	if (!html) return;
		        	msg || (msg = $('<div style="position:absolute;z-index:999;text-align:center;"></div>').prependTo(dialog));
                    msg[0].className = type;
					msg.html(html).css({
                        visibility:'visible',
                        maxWidth:dialog.innerWidth() - 50,
                        top:msg.height() * -3
                    }).show();
                    msg.css({left:Math.floor((dialog.innerWidth() - msg.outerWidth(true)) / 2)});
		        	clearTimeout(mival);
					mival = null;
					msg.animate({top:0}, 150);
					mival = setTimeout(function(){
						msg.slideUp(50);
					}, 3000);
		        },
                success = function(json){
                	if (json && ('state' in json)) {
                		var type = json.state ? 'success' : 'error',
                			msg = json.msg || (json.state ? json.info : json.error);
                		showmsg(msg, type);
                	}
					if (typeof submitBack == 'function') {
						submitBack(json) && dialog.dialog('destroy').remove();
					} else {
						json && ('state' in json) && json.state && dialog.dialog('destroy').remove()
					}
        			return false;
                },
                complete = function(){
                	masker.hide();
					buttonArea.children('button').attr('disabled', false).removeAttr('disabled');
                },
                beforeSub = function(f, d, options) {
        			buttonArea.children('button').attr('disabled', 'disabled');
					masker.css({height:wrap.height(),width:wrap.width()}).show();
					if (typeof beforeSubmit == 'function' && beforeSubmit(form, dialog, options) === false)
					{
						complete();
						return false;
					}
        		    return true;
        		},
                submit = function(){
                	form.ajaxSubmit({
                        dataType:'json',
                		type:'post',
                		success:success,
						error:function(){showmsg('请求异常', 'error');},
                		complete:complete,
                		beforeSubmit:beforeSub,
                		beforeSerialize:beforeSerialize
                    });
                };
                if (form[0].getAttribute('name') && form.validate) {
                    form.validate({
                        submitHandler:submit
                    });
                } else {
                	form.find('input,textarea,select')
                	  .not(':button,:submit,:image,:reset,:hidden,[disabled],[readonly]')
                	  .eq(0).focus();
                	form.submit(function(e){
                        submit();
                        return false;
                    });
                }
            }
        }
        function ok(){
			form && form.submit();
            return false;
        }
        function cancel(){return true;}
        
        return ct.ajaxDialog(opt, url, load, ok, cancel);
    },
	template:function(input){
		input.jquery || (input = $(input));
		var path = input.val();
		var d = ct.iframe({
			title:'?app=system&controller=template&action=selector&path=' + path,
			width:600,
			height:'auto'
		},{
			ok:function(val){
				input.val(val);
				d.dialog('close');
			}
		});
	},
    getCookie:function(name) {
    	var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    },
    setCookie:function(name, value, options) {
    	 options = options || {};

        if (value === null) {
            value = '';
            options = $.extend({}, options);
            options.expires = -1;
        }
        if (!options.expires) {
        	options.expires = 1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString();
        }
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    }
};
window.ct = window.cmstop = cmstop;

cmstop.assoc = {
    refresh:function() {
        if (top != self) {
        	top.superAssoc.refresh()
        }
    },
    open:function(url, target, path) {
    	if (top != self) {
        	window.__ASSOC_TABID__ = top.superAssoc.open(url,target,path);
    	}
    },
    get:function(target) {
    	if (top != self) {
    		return top.superAssoc.get(target);
    	} else {
    		return null;
    	}
    },
    close:function(target) {
    	if (top != self) {
    		top.superAssoc.close(target);
    	}
	},
	opener:function() {
		if (top != self) {
    		return window.__ASSOC_TABID__ && top.superAssoc.get(window.__ASSOC_TABID__);
    	} else {
    		return null;
    	}
	},
    call:function(method) {
    	if (top != self) {
    		var args = Array.prototype.slice.call(arguments,1);
    		return top.superAssoc[method].apply(null,args);
    	}
    }
};
    
$.fn.extend({
	ajaxSubmit:function(options) {
	    if (!this.length) {
	        return this;
	    }
	    if (typeof options == 'function')
	        options = { success: options };
	
	    var url = $.trim(this.attr('action'));
	    if (url) {
		    url = (/^([^#]+)/.exec(url)||{})[1];
	   	}
	   	url = url || window.location.href || '';
	
	    options = $.extend({
	        url:  url,
	        type: this.attr('method') || 'GET'
	    }, options || {});
	
	    // provide opportunity to alter form data before it is serialized
	    if (options.beforeSerialize && options.beforeSerialize(this, options) === false) {
	        return this;
	    }
	
	    var a = this.serializeArray();
	    if (options.data) {
	        options.extraData = options.data;
	        for (var n in options.data) {
	          if(options.data[n] instanceof Array) {
	            for (var k in options.data[n])
	              a.push( { name: n, value: options.data[n][k] } );
	          }
	          else
	             a.push( { name: n, value: options.data[n] } );
	        }
	    }
	    
	    // give pre-submit callback an opportunity to abort the submit
	    if (options.beforeSubmit && options.beforeSubmit(a, this, options) === false) {
	        return this;
	    }
	
	    options.data = a;
		
		$.ajax(options);
	
		this.trigger('form-submit-notify', [this, options]);
	
		return this;
	},
    ajaxForm : function(jsonok, infoHandler, beforeSubmit) {
    	var form = this,
		url = this.attr('action'),
		type = this.attr('method') || 'POST',
		jsonok = cmstop.func(jsonok) || function(json){
			json.state
		    	? cmstop.ok('保存成功')
		    	: cmstop.error(json.error)
		},
		beforeSubmit = cmstop.func(beforeSubmit) || function(){},
    	submitHandler = function(){
    		if (form.data('lock')) return;
    		if (beforeSubmit(form) === false) {
    			return;
    		}
    		var buttons = form.find('*')
    			.filter(':button,:submit,:reset')
    			.attr('disabled','disabled'),
    		complete = function(){
    			form.data('lock', false);
    			buttons.attr('disabled','').removeAttr('disabled');
    		};
    	    form.data('lock', true);
    		$.ajax({
	    		dataType:'json',
	    		url:url,
	    		type:type,
	    		data:form.serialize(),
	    		success:jsonok,
	    		complete:complete,
	    		error:function(){
	    			cmstop.error('请求异常');
	    		}
	    	});
    	};
    	// CTRL + ENTER|S quick submit
		$().unbind('keydown.ajaxForm');
    	$().bind('keydown.ajaxForm',function(e){
    		if (e.ctrlKey && (e.keyCode == 13 || e.keyCode == 83))
    		{
    			e.stopPropagation();
    			e.preventDefault();
    			form.submit();
    		}
    	});
        if (this.attr('name')) {
            this.validate({
                submitHandler:submitHandler,
                infoHandler:infoHandler
            });
        } else {
			this.submit(function(e){
				e.stopPropagation();
				e.preventDefault();
				submitHandler();
			});
        }
        return this;
    },
    floatImg : function(options) {
		var opts = $.extend({
			url:'',
			width : null,
			height : null
		}, options||{});
		var hiddenObject = $(document.createElement('div'));
		$.extend(hiddenObject[0].style, {
			position   : 'absolute',
			overflow   : 'hidden',
			display    : 'none',
			padding    : '4px',
			background : '#ccc',
			border     : '1px solid #fff',
			width      : opts.width,
			height     : opts.height,
			zIndex     : 8888
		});
		$(document.body).append(hiddenObject);

		this.bind('mouseover',function(e){
			var data = this.value || this.getAttribute('thumb');
			if (!data) return;
			var left = e.pageX + 10;
			var top = e.pageY + 10;
			var imgsrc = httpRegex.test(data) ? data : (opts.url+data);
			imgsrc = imgsrc.replace(/\?[0-9\.]*$/,'') + '?' + Math.random(9);
			var html = ['<img src="'+imgsrc+'"'];
			opts.width && html.push(' width="'+opts.width+'"');
			opts.height && html.push(' height="'+opts.height+'"');
			html.push(' />');
	        hiddenObject.html(html.join('')).css({
				'top':top,
				'left':left,
				'display':'block'
			});
		}).bind('mousemove',function(e){
			var left = e.pageX+10;
			var top  = e.pageY+10;
			hiddenObject.css({
				top:top,
				left:left
			});
		}).bind('mouseout',function(){
			hiddenObject.hide();
		});
		return this;
	},
	attrTips : function(attr, theme) {
		var b, $b,
		ihide = function(){
			var delay = $b.data('delay');
			delay && clearTimeout(delay);
			$b.data('delay', null);
			$b.stop(1).css({opacity:'',display:'none'});
		};
		if (bubble.inst) {
			b = bubble.inst;
			$b = b.get();
		} else {
			b = new bubble();
			bubble.inst = b;
			$b = b.get();
		}
		var pos = null;
		this.bind('mouseover', function(e){
			pos = e;
			var t = this,
				c = this.getAttribute(attr),
				delay = $b.data('delay');
			delay && clearTimeout(delay);
			if (!c) return;
			delay = setTimeout(function(){
				$b.data('point', t);
				b.setYellow(theme != 'tips_green');
				b.html(c);
				b.pointTo(pos);
				$b.fadeIn('normal');
			}, 200);
			$b.data('delay', delay);
		}).bind('mouseout', ihide).bind('mousemove',function(e){
			pos = e;
			if ($b.data('point') != this) return;
			b.pointTo(e);
		});
		doc.bind('mousedown.bubble', ihide);
		return this;
 	},
	maxLength : function() {
		this.each(function(){
			var maxLength = this.maxLength;
			var s = $('<strong class="c_green" style="margin-left:5px">0</strong>')
				.insertAfter(this);
			$.event.add(this, 'keyup', function(ev){
				$.textLength(this, s, maxLength, ev);
			});
            if (maxLength > 0 && ! 'maxLength' in this) {
                $.event.add(this, 'keydown', function(ev) {
                    if (this.value.length >= maxLength) {
                        switch (ev.keyCode) {
                            case 8: case 9: case 17: case 36: case 35: case 37:
                            case 38: case 39: case 40: case 46: case 65:
                                return;
                            default:
                                return ev.keyCode != 32 && ev.keyCode != 13 && this.value.length == limit;
                        }
                    }
                });
            }
		}).keyup();
		return this;
	}
});
$.textLength = function(el, strong, maxLength, ev) {
	if (maxLength && maxLength > 0) {
		var l = el.value.length;
		strong.html(l);
		if (l > maxLength) {
            strong.addClass('c_red');
            ev.preventDefault();
            el.value = el.value.substr(0, maxLength);
        }
	} else {
		strong.html(el.value.length);
	}
	if (el.tagName == 'TEXTAREA' && el.scrollHeight > el.clientHeight) {
		el.style.height = el.scrollHeight + 'px';
	}
};

$.ajaxSetup({
	beforeSend:function(xhr){
		xhr.setRequestHeader("If-Modified-Since","0");
		xhr.setRequestHeader("Cache-Control","no-cache");
	}
});

})(jQuery, window);

(function($){

var types = ['DOMMouseScroll', 'mousewheel'];
$.fn.mousewheel = function(fn) {
	return fn ? this.bind("mousewheel", fn) : this.trigger("mousewheel");
};
$.event.special.mousewheel = {
	setup: function() {
		if ( this.addEventListener )
			for ( var i=types.length; i; )
				this.addEventListener( types[--i], handler, false );
		else
			this.onmousewheel = handler;
	},

	teardown: function() {
		if ( this.removeEventListener )
			for ( var i=types.length; i; )
				this.removeEventListener( types[--i], handler, false );
		else
			this.onmousewheel = null;
	}
};
function handler(event) {
	var args = [].slice.call( arguments, 1 ), delta = 0, returnValue = true;

	event = $.event.fix(event || window.event);
	event.type = "mousewheel";

	if ( event.wheelDelta ) delta = event.wheelDelta/120;
	if ( event.detail ) delta = -event.detail/3;

	// Add events and delta to the front of the arguments
	args.unshift(event, delta);

	return $.event.handle.apply(this, args);
}
})(jQuery);

var url = {
	member: function (userid) {
		ct.assoc.open('?app=member&controller=index&action=profile&userid='+userid, 'newtab');
	},
	ip: function (ip) {
		ct.assoc.open('?app=system&controller=ip&action=show&ip='+ip, 'newtab');
	}
};
