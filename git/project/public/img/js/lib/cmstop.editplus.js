(function(){
function innerWidth(){
	return document.documentElement.clientWidth;
}
function innerHeight(){
	return document.documentElement.clientHeight;
}
function getSel(o) {
	o.focus();
	return o.selectionStart !== undefined
		? o.value.substring(o.selectionStart, o.selectionEnd)
		: document.selection && document.selection.createRange
		   ? document.selection.createRange().text
		   : window.getSelection
		     ? (window.getSelection() + '')
		     : '';
}
    var PLUGINS = {};
    var OPTIONS = {
        buttons:'fullscreen,wrap',
        linenum:true,
        textarea:null,
        width:null,
        height:null
    };
    var editplus = function(options){
        options = $.extend({}, OPTIONS, options||{});
        var _this = this;
    	var $textarea = options.textarea;
    	$textarea.jquery || ($textarea = $($textarea));
  		var textarea = $textarea[0];
		options.width || (options.width = textarea.offsetWidth);
		options.height || (options.height = textarea.offsetHeight);
  		var editbox = $('<div class="editbox" style="display:none;"><div class="editarea"></div></div>');
  		var editarea = editbox.find('div');
  		this.editarea = editarea;
		$textarea.after(editbox).appendTo(editarea);
		editbox[0].style.cssText = "visibility:hidden";
		$textarea.css({
	  		'outline'	: 'none',
  			'resize'	: 'none',
  			'border'	: 'none',
  			'padding'	: 0,
  			'margin'	: 0,
  			'overflow-x': 'auto'
  		}).attr('wrap', 'off');
        this.ctrlarea = null;
        this.buttons = {};
        if (options.buttons) {
        	var buttons = options.buttons.split(/\s*,\s*/);
        	var $ctrlarea = $('<p class="ctrlarea"></p>').prependTo(editbox);
        	function addBtn(name,opt) {
        		var a = $('<a href="#'+name+'" title="'+opt.desc+'">'+opt.text+'</a>');
				if (opt.icon) {
					a.prepend('<img src="'+opt.icon+'" />');
				}
				a.click(function(){
					_this[name](a, _this.getSelection());
	    			return false;
				});
				_this.buttons[name] = a;
				$ctrlarea.append(a);
        	}
        	for (var i=0,btn;i<buttons.length;i++) {
        		if ((btn = buttons[i])) {
        			if (btn == '|') {
        				$ctrlarea.append('<span>|</span>');
        			} else if (btn == '/') {
        				$ctrlarea.append('<br />');
        			} else if (btn in PLUGINS){
        				addBtn(btn, PLUGINS[btn]);
        			}
        		}
        	}
        	this.ctrlarea = $ctrlarea;
        }
        if (options.linenum) {
			var $linenum = $('<textarea class="linenum" disabled="disabled"></textarea>');
			$linenum.css({
				'outline'	: 'none',
	  			'resize'	: 'none',
	  			'border'	: 'none',
	  			'padding'	: 0,
	  			'margin'	: 0,
		  		'scrolling'	: 'no'
			});
			var linenum = $linenum[0];
			$textarea.before($linenum);
			var ln = 0;
	        var addNum = function(l){
	            var h = '';
	            while (l--) {
	                h += (++ln)+'\n';
	            }
	            linenum.value += h;
	        };
	        addNum(200);
		    $textarea.scroll(function(){
	            linenum.scrollTop = textarea.scrollTop;
	            while (linenum.scrollTop != textarea.scrollTop) {
	                addNum(10);
	                linenum.scrollTop = textarea.scrollTop;
	            }
		    });
		    $.browser.msie && editbox.bind('selectstart',function(e){
		    	if (e.target == linenum) return false;
		    });
		    this.linenum = $linenum;
        }
        $textarea.tabindent();
		this.textarea = $textarea;
  		this.editbox = editbox;
  		this.editarea = editarea;

        this.setDim(options.width, options.height);
		editbox.css('visibility','visible');
		return this;
    };
    editplus.setPlugin = function(name,func,opt) {
        PLUGINS[name] = $.extend({
            text:name,
            desc:name,
            icon:null
        }, opt||{});
        fn[name] = func;
        return this;
    };
	var fn = editplus.prototype = {
        setDim:function(w, h) {
		    if (!h) {
		    	h = this.editbox.outerHeight();
		    }
		    var editbox_w = document.compatMode == 'CSS1Compat'
		    	? (w - parseInt(this.editbox.css('border-left-width'))*2) : w;
		    var editarea_w = editbox_w;
		    if (this.linenum) {
		    	editarea_w = editarea_w - this.linenum.outerWidth();
		    }
		    this.editbox.width(editbox_w);
		    this.textarea.width(editarea_w);
		    var editbox_h = document.compatMode == 'CSS1Compat'
		    	? (h - parseInt(this.editbox.css('border-top-width'))*2) : h;
		    var editarea_h = editbox_h;
        	if (this.ctrlarea) {
		    	editarea_h = editarea_h - this.ctrlarea.outerHeight();
		    }
        	this.editbox.height(editbox_h);
		    this.editarea.height(editarea_h);
		    this.textarea.height(editarea_h);
		    this.linenum && this.linenum.height(editarea_h);
        }, getSelection:function(){
        	if (!this.textarea.is(':visible')) return null;
        	this.textarea[0].focus();
        	var selection = document.selection && document.selection.createRange();
			var text = selection ? selection.text : getSel(this.textarea[0]);
			return {
				selection:selection,
				text:text
			};
        }
    };
	fn.hide = function(){
		 this.editbox.hide();
	};
	fn.show = function(){
		 this.editbox.show();
	};
    $.editplus = editplus;
	$.fn.editplus = function(options){
		options || (options = {});
		options.textarea = this;
		return new editplus(options);
	};
	
	function _toInt(o) {
		o = parseInt(o);
		return isNaN(o) ? 0 : o;
	}
	editplus.setPlugin('fullscreen',function(a,sel){
		var t = this, ed = this.editbox, tx = this.textarea;
		if (t._placeholder) {
			// return bak
			var h = t._storedStyle.height,
				w = t._storedStyle.width;
			delete t._storedStyle.height;
			delete t._storedStyle.width;
			ed.css(t._storedStyle);
			tx.unbind('.editplus');
			t._placeholder.replaceWith(ed);
			t.setDim(w, h);
			$(document.body).css(t._storedBodyStyle);
			t._storedStyle = null;
			t._storedBodyStyle = null;
			t._storedBodyScroll = null;
			t._placeholder = null;
			a.removeClass('active').attr('title', '全屏编辑');
		} else {
			// maxi
			var h = ed.outerHeight(),
				w = ed.outerWidth();
			t._storedStyle = {
				width:w,
				height:h,
				margin:[
					ed[0].style.marginTop||0,
					ed[0].style.marginRight||0,
					ed[0].style.marginBottom||0,
					ed[0].style.marginLeft||0
				].join(' '),
				position:'static',
				zIndex:0
			};
			t._placeholder = $('<textarea name="'+tx[0].name+'" style="width:'+w+'px;height:'+h+'px;"></textarea>');
			ed.after(t._placeholder);
			t._placeholder[0].value = tx[0].value;
			tx.bind('propertychange.editplus input.editplus', function(e){
				t._placeholder.val(this.value);
			});
			var zIndex = _toInt(ed.closest('.dialog-box').css('z-index'));
			zIndex = zIndex ? (zIndex + 1) : 1000;
			var $body = $(document.body);
			ed.appendTo($body).css({
				position:'fixed',margin:0,
				top:0,left:0,zIndex:zIndex
			});
			// hide scroll bar
			t._storedBodyStyle = {
				'overflow-x':$body.css('overflow-x'),
				'overflow-y':$body.css('overflow-y'),
				'scroll':$body.css('scroll')
			};
			$body.css({
				'overflow-x':'hidden',
				'overflow-y':'hidden',
				'scroll':'no'
			});
			t.setDim(innerWidth(), innerHeight());
			a.addClass('active').attr('title', '还原尺寸');
		}
	},{
		text:'全屏',
		desc:'全屏编辑'
	}).setPlugin('wrap',function(a,sel) {
		if (this.textarea.attr('wrap') == 'off') {
	        this.textarea.attr('wrap', 'soft');
	        this.textarea.css('overflow-x','hidden');
	        a.addClass('active');
	    } else {
	        this.textarea.attr('wrap', 'off');
	        this.textarea.css('overflow-x','auto');
	        a.removeClass('active');
	    }
	},{
		text:'自动换行',
		desc:'自动换行'
	}).setPlugin('save',function(a,sel){
		// 还原
		this._placeholder && this.buttons['fullscreen'].click();
		$(this.textarea[0].form).submit();
	},{
		text:'保存'
	}).setPlugin('visual',function(a,sel){
		if (!('tinyMCE' in window) || !('editor' in window)) return;
		if (a.data('visual')) {
			tinyMCE.get('data').remove();
			this.linenum.show();
			a.data('visual', 0);
			a.removeClass('active').attr('title','可视编辑');
		} else {
			this.linenum.hide();
			editor('data','mini',{
				width:this.editarea.innerWidth()+1,
				height:this.editarea.innerHeight()-5
			});
			a.data('visual', 1);
			a.addClass('active').attr('title', '还原');
		}
	},{
		text:'可视编辑',
		desc:'可视编辑'
	});
	
	/**
	 * tabindent plugin
	 * @author muqiao
	 */
	var _deindent = /(([^|\n] {1,3})\t)|((^|\n) {1,4}|\t)/g;
	var _deindent_r = function(){return arguments[1] ? arguments[2] : arguments[4]};
	var _tabkeydown = function(e) {
		var tab = "    ";
		var ie = 0, o = this;
		if (window.event) {
			e = window.event;
			ie = 1;
		}
		o.focus();
		var sel = (document.selection && document.selection.createRange());
		var seltext = sel ? sel.text : (o.selectionStart !== undefined
    		? o.value.substring(o.selectionStart, o.selectionEnd) : '');
		var keycode = e.keyCode ? e.keyCode : e.charCode;
		if (keycode == 9) {
	        if (ie) {
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
    		if (e.shiftKey) {
    			// de indent paragraph
    			if (sel) {
					var s = 0, ol = sel.text.length, otext;
    				while (1) {
    					sel.moveStart('character', -1);
    					if (sel.parentElement() != o) {
    						o.focus();
    						sel = document.selection.createRange();
    						sel.moveStart('character', -s);
    						break;
    					}
    					var t = sel.text, c = t.charAt(0), l = t.length;
    					if (ol + 1 > l || c == '\r') {
    						sel.moveStart('character', 1);
    						break;
    					}
    					ol = l;
    					s += 1;
    				}
    				sel.moveEnd('character', 1);
        			if (sel.parentElement() != o) {
						o.focus();
						sel = document.selection.createRange();
						sel.moveStart('character', -s);
						otext = sel.text;
    				} else {
    					otext = sel.text;
        				sel.moveEnd('character', -1);
        				if (sel.parentElement() != o) {
        					o.focus();
    						sel = document.selection.createRange();
    						sel.moveStart('character', -s);
        				} else {
        					otext = otext.substr(0, otext.length-1);
        				}
    				}
					var m = /(^ {0,3}\t)|(^ {1,4})/.exec(otext);
					var offset = m ? (m[2] ? m[2].length : 1) : 0;
    				var text = otext.replace(_deindent, _deindent_r);
    				var l = -text.replace(/\r\n/g, '\n').length;
    				sel.text = text;
    				sel.moveStart('character', s+l-offset);
    				sel.select();
    			} else {
					var s = o.selectionStart + 0, e = o.selectionEnd + 0, os = s;
					while (s != 0 && o.value.charAt(s-1) != '\n') {
						s = s - 1;
					}
					var otext = o.value.substring(s, e);
					var m = /(^ {0,3}\t)|(^ {1,4})/.exec(otext);
					var offset = m ? (m[2] ? m[2].length : 1) : 0;
					var text = otext.replace(_deindent, _deindent_r);
					o.value = o.value.substring(0, s)+text+o.value.substr(o.selectionEnd);
					o.selectionStart = os - offset;
					o.selectionEnd = e + text.length - otext.length;
    			}
    		} else if (seltext) {
    			// indent paragraph
    			if (sel) {
    				var s = 0, ol = sel.text.length, otext;
    				while (1) {
    					sel.moveStart('character', -1);
    					if (sel.parentElement() != o) {
    						o.focus();
    						sel = document.selection.createRange();
    						sel.moveStart('character', -s);
    						break;
    					}
    					var t = sel.text, c = t.charAt(0), l = t.length;
    					if (ol + 1 > l || c == '\r') {
    						sel.moveStart('character', 1);
    						break;
    					}
    					ol = l;
    					s += 1;
    				}
    				sel.moveEnd('character', 1);
        			if (sel.parentElement() != o) {
						o.focus();
						sel = document.selection.createRange();
						sel.moveStart('character', -s);
						otext = sel.text;
    				} else {
    					otext = sel.text;
    					sel.moveEnd('character', -1);
        				if (sel.parentElement() != o) {
        					o.focus();
    						sel = document.selection.createRange();
    						sel.moveStart('character', -s);
        				} else {
        					otext = otext.substr(0, otext.length-1);
        				}
    				}
    				var text = tab + otext.replace(/\n/g, '\n' + tab);
					var l = -text.replace(/\r\n/g, '\n').length;
					sel.text = text;
					sel.moveStart('character', l+s+tab.length);
					sel.select();
    			} else {
					var s = o.selectionStart + 0, e = o.selectionEnd + 0, os = s;
					while (s != 0 && o.value.charAt(s-1) != '\n') {
						s = s - 1;
					}
					var text = o.value.substring(s, e).replace(/\n/g, '\n'+tab);
					o.value = o.value.substring(0, s)+tab+text+o.value.substr(o.selectionEnd);
					o.selectionStart = os + tab.length;
					o.selectionEnd = o.selectionStart + text.length - os + s;
    			}
    		} else {
    			// insert tab
    			if (sel) {
    				sel.text = tab+sel.text;
    			} else {
					var s = o.selectionStart + 0;
					o.value = o.value.substring(0, s) + tab + o.value.substr(s);
					o.selectionStart = s + tab.length;
					o.selectionEnd = o.selectionStart;
    			}
			}
	    }
	};
	$.fn.tabindent = function(){
		this.keydown(_tabkeydown);
		return this;
	}
})(jQuery);