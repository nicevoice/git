// Some global instances
var tinymce = null, tinyMCEPopup, tinyMCE;

tinyMCEPopup = {
	init : function() {
		var t = this, w = t.getWin(), ti;

		// Find API
		tinymce = w.tinymce;
		tinyMCE = w.tinyMCE;
		t.editor = tinymce.EditorManager.activeEditor;
		t.params = t.editor.windowManager.params;

		// Setup local DOM
		t.dom = t.editor.windowManager.createInstance('tinymce.dom.DOMUtils', document);
		//t.dom.loadCSS(t.editor.settings.popup_css);

		// Setup on init listeners
		t.listeners = [];
		t.onInit = {
			add : function(f, s) {
				t.listeners.push({func : f, scope : s});
			}
		};

		t.isWindow = !t.getWindowArg('mce_inline');
		t.id = t.getWindowArg('mce_window_id');
		t.editor.windowManager.onOpen.dispatch(t.editor.windowManager, window);
	},

	getWin : function() {
		return window.dialogArguments || opener || parent || top;
	},

	getWindowArg : function(n, dv) {
		var v = this.params[n];

		return tinymce.is(v) ? v : dv;
	},

	getParam : function(n, dv) {
		return this.editor.getParam(n, dv);
	},

	getLang : function(n, dv) {
		return this.editor.getLang(n, dv);
	},

	execCommand : function(cmd, ui, val) {
		this.restoreSelection();
		return this.editor.execCommand(cmd, ui, val);
	},

	resizeToInnerSize : function() {
		var t = this, n, b = document.body, vp = t.dom.getViewPort(window), dw, dh;

		dw = t.getWindowArg('mce_width') - vp.w;
		dh = t.getWindowArg('mce_height') - vp.h;

		if (t.isWindow)
			window.resizeBy(dw, dh);
		else
			t.editor.windowManager.resizeBy(dw, dh, t.id);
	},

	executeOnLoad : function(s) {
		this.onInit.add(function() {
			eval(s);
		});
	},

	storeSelection : function() {
		this.editor.windowManager.bookmark = tinyMCEPopup.editor.selection.getBookmark('simple');
	},

	restoreSelection : function() {
		var t = tinyMCEPopup;

		if (!t.isWindow && tinymce.isIE)
			t.editor.selection.moveToBookmark(t.editor.windowManager.bookmark);
	},

	requireLangPack : function() {
		var u = this.getWindowArg('plugin_url') || this.getWindowArg('theme_url');

		if (u)
			document.write('<script type="text/javascript" src="' + u + '/langs/' + this.editor.settings.language + '_dlg.js' + '"></script>');
	},

	pickColor : function(e, element_id) {
		this.execCommand('mceColorPicker', true, {
			color : document.getElementById(element_id).value,
			func : function(c) {
				document.getElementById(element_id).value = c;

				if (tinymce.is(document.getElementById(element_id).onchange, 'function'))
					document.getElementById(element_id).onchange();
			}
		});
	},

	openBrowser : function(element_id, type, option) {
		tinyMCEPopup.restoreSelection();
		this.editor.execCallback('file_browser_callback', element_id, document.getElementById(element_id).value, type, window);
	},

	close : function() {
		var t = this;

		t.dom = t.dom.doc = null; // Cleanup
		t.editor.windowManager.close(window, t.id);
	},

	// Internal functions	

	_restoreSelection : function() {
		var e = window.event.srcElement;

		if (e.nodeName == 'INPUT' && (e.type == 'submit' || e.type == 'button'))
			tinyMCEPopup.restoreSelection();
	},


	_onDOMLoaded : function() {
		var t = this, ti = document.title, bm, h;

		// Translate page
		h = document.body.innerHTML;

		// Replace a=x with a="x" in IE
		if (tinymce.isIE)
			h = h.replace(/ (value|title|alt)=([^\s>]+)/gi, ' $1="$2"');

		document.body.innerHTML = t.editor.translate(h);
		document.title = ti = t.editor.translate(ti);
		document.body.style.display = '';

		// Restore selection in IE when focus is placed on a non textarea or input element of the type text
		if (tinymce.isIE)
			document.attachEvent('onmouseup', tinyMCEPopup._restoreSelection);

		t.restoreSelection();

		// Call onInit
		tinymce.each(t.listeners, function(o) {
			o.func.call(o.scope, t.editor);
		});

		t.resizeToInnerSize();

		if (t.isWindow)
			window.focus();
		else
			t.editor.windowManager.setTitle(ti, t.id);

		if (!tinymce.isIE && !t.isWindow) {
			tinymce.dom.Event._add(document, 'focus', function() {
				t.editor.windowManager.focus(t.id)
			});
		}

		// Patch for accessibility
		tinymce.each(t.dom.select('select'), function(e) {
			e.onkeydown = tinyMCEPopup._accessHandler;
		});

		// Move focus to window
		window.focus();
	},

	_accessHandler : function(e) {
		var e = e || window.event;

		if (e.keyCode == 13 || e.keyCode == 32) {
			e = e.target || e.srcElement;

			if (e.onchange)
				e.onchange();

			return tinymce.dom.Event.cancel(e);
		}
	},

	_wait : function() {
		var t = this, ti;

		if (tinymce.isIE && document.location.protocol != 'https:') {
			// Fake DOMContentLoaded on IE
			document.write('<script id=__ie_onload defer src=\'javascript:""\';><\/script>');
			document.getElementById("__ie_onload").onreadystatechange = function() {
				if (this.readyState == "complete") {
					t._onDOMLoaded();
					document.getElementById("__ie_onload").onreadystatechange = null; // Prevent leak
				}
			};
		} else {
			if (tinymce.isIE || tinymce.isWebKit) {
				ti = setInterval(function() {
					if (/loaded|complete/.test(document.readyState)) {
						clearInterval(ti);
						t._onDOMLoaded();
					}
				}, 10);
			} else {
				window.addEventListener('DOMContentLoaded', function() {
					t._onDOMLoaded();
				}, false);
			}
		}
	}
};

tinyMCEPopup.init();
//tinyMCEPopup._wait(); // Wait for DOM Content Loaded

//tinyMCEPopup.requireLangPack();



function selectFile(url)
{
	if(url)
	{
		var win = tinyMCEPopup.getWindowArg("window");
	/*	var after = substr(url.lastIndexOf('.')),sresult = '';
		switch(after)
		{
			case 'swf': sresult = 'flash';break;
			case '3gp': sresult = 'qt';break;
			case 'wmp' : sresult = ''
		}*/

        var html = '', src, name;
        if (Object.prototype.toString.call(url) == '[object Object]') {
            src = url.src;
            name = url.name;
        } else {
            src = url;
            name = '';
        }
        
        //for image browsers
        try {
            win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = src;
            name && (win.document.getElementById('alt').value = name);
            win.ImageDialog.showPreviewImage(src);
        } catch (e) { void(e); }
	}
    
}

function selectFiles(urls)
{
	if(urls)
	{
        var html = '', i, length, url, src, name;
        for (i = 0, length = urls.length; i < length; i++) {
            url = urls[i];
            if (Object.prototype.toString.call(url) == '[object Object]') {
                src = url.src;
                name = url.name && (url.name.indexOf('.') > -1 ? url.name.substr(0, url.name.lastIndexOf('.')) : url.name) || '';
            } else {
                src = url;
                name = '';
            }
            html += '<p data-mce-style="text-align:center;" style="text-align:center;"><img src="' + src + (name ? ('" alt="' + name) : '') + '" /></p>';
            name && (html += '<p data-mce-style="text-align:center;font-size:12px;text-indent:0;" style="text-align:center;font-size:12px;text-indent:0;">'+name+'</p>');
        }
		tinyMCEPopup.execCommand('mceInsertContent', false, html);
        tinyMCEPopup.close();
	}
}


function cancelSelectFile()
{
    // close popup window
    tinyMCEPopup.close();
}