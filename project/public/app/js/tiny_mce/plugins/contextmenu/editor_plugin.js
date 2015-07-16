/**
 * $Id: editor_plugin_src.js 848 2008-05-15 11:54:40Z spocke $
 *
 * @author Moxiecode
 * @modified by shanhuhai
 * @copyright Copyright � 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

(function() {
	var Event = tinymce.dom.Event, each = tinymce.each, DOM = tinymce.DOM;

	tinymce.create('tinymce.plugins.ContextMenu', {
		init : function(ed) {
			var t = this;

			t.editor = ed;
			t.onContextMenu = new tinymce.util.Dispatcher(this);

			ed.onContextMenu.add(function(ed, e) {
				if (!e.ctrlKey) {
					t._getMenu(ed).showMenu(e.clientX, e.clientY);
					Event.add(ed.getDoc(), 'click', hide);
					Event.cancel(e);
				}
			});

			function hide() {
				if (t._menu) {
					t._menu.removeAll();
					t._menu.destroy();
					Event.remove(ed.getDoc(), 'click', hide);
				}
			};

			ed.onMouseDown.add(hide);
			ed.onKeyDown.add(hide);
		},

		getInfo : function() {
			return {
				longname : 'Contextmenu',
				author : 'Moxiecode Systems AB',
				authorurl : 'http://tinymce.moxiecode.com',
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/contextmenu',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		},

		_getMenu : function(ed) {
			var t = this, m = t._menu, se = ed.selection, col = se.isCollapsed(), el = se.getNode() || ed.getBody(), am, p1, p2;

			if (m) {
				m.removeAll();
				m.destroy();
			}

			p1 = DOM.getPos(ed.getContentAreaContainer());
			p2 = DOM.getPos(ed.getContainer());

			m = ed.controlManager.createDropMenu('contextmenu', {
				offset_x : p1.x + ed.getParam('contextmenu_offset_x', 0),
				offset_y : p1.y + ed.getParam('contextmenu_offset_y', 0),
				constrain : 1
			});

			t._menu = m;

			if ((el.nodeName == 'A' && !ed.dom.getAttrib(el, 'name')) || !col) {
				m.addSeparator();
				m.add({title : 'advanced.link_desc', icon : 'link', cmd : ed.plugins.advlink ? 'mceAdvLink' : 'mceLink', ui : true});
				m.add({title : 'advanced.unlink_desc', icon : 'unlink', cmd : 'UnLink'});
			}

			m.addSeparator();
			am = m.addMenu({title : 'contextmenu.align'});
			am.add({title : 'contextmenu.left', icon : 'justifyleft', cmd : 'JustifyLeft'});
			am.add({title : 'contextmenu.center', icon : 'justifycenter', cmd : 'JustifyCenter'});
			am.add({title : 'contextmenu.right', icon : 'justifyright', cmd : 'JustifyRight'});
			am.add({title : 'contextmenu.full', icon : 'justifyfull', cmd : 'JustifyFull'});

			m.addSeparator();
			m.add({title : 'advanced.image_desc', icon : 'image', cmd : ed.plugins.advimage ? 'mceAdvImage' : 'mceImage', ui : true});
			if (el.nodeName == 'IMG' && ed.dom.getAttrib(el, 'src') && ed.dom.getAttrib(el, 'class') != 'mceItemFlash' ) {
				m.add({title : '\u88c1\u526a\u56fe\u7247', onclick : function(){
					ct.editImage(ed.dom.getAttrib(el, 'src'),function(json){
						ed.dom.setAttribs(el,{'src':UPLOAD_URL+json.file+'?'+Math.random()*5,'width':null,'height':null});
					});
				}});
			}
			
			t.onContextMenu.dispatch(t, m, el, col);

			return m;
		}
	});

	// Register plugin
	tinymce.PluginManager.add('contextmenu', tinymce.plugins.ContextMenu);
})();