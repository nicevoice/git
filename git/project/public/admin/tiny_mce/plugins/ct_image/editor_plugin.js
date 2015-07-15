/**
 * editor_plugin.js
 *
 * Copyright 2012, CmsTop Co.ltd.
 * Released under LGPL License.
 *
 * License: http://tinymce.moxiecode.com/license
 * Contributing: http://tinymce.moxiecode.com/contributing
 */

(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('ct_image');

	tinymce.create('tinymce.plugins.CmsTopImagePlugin', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('ct_image');
			ed.addCommand('ct_image', function() {
                var n = ed.selection.getNode();

                if (n && ed.dom.getAttrib(n, 'class').indexOf('mceItem') != -1)
					return;

                // 选中图片，弹出图片编辑对话框
                if (n.nodeName == 'IMG') {
					var className = n.getAttribute('class') || n.getAttribute('classname') || '';
					if (!/cmstop(flash|video)/i.test(className)) {
						ed.execCommand('mceAdvImage', true);
						return;
					}
                }

				ed.windowManager.open({
					file : '?app=editor&controller=image',
					width : 746 + parseInt(ed.getLang('ct_image.delta_width', 0)),
					height : 385 + parseInt(ed.getLang('ct_image.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url, // Plugin absolute URL
					some_custom_arg : 'custom arg' // Custom argument
				});
			});

			// Register ctImage button
			ed.addButton('ctImage', {
				title : 'ct_image.desc',
				cmd : 'ct_image',
				image : url + '/img/ct_image.gif'
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				if (typeof (n.getAttribute) == 'function') {
					var className = n.getAttribute('class') || n.getAttribute('classname') || '';
					cm.setActive('ctImage', (n.nodeName == 'IMG' && !/cmstop(flash|video)/i.test(className)));
				}
			});
		},

		/**
		 * Creates control instances based in the incomming name. This method is normally not
		 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		 * method can be used to create those.
		 *
		 * @param {String} n Name of the control to create.
		 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no control was created.
		 */
		createControl : function(n, cm) {
			return null;
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'CmsTop Image Plugin',
				author : 'CmsTop',
				authorurl : 'http://www.cmstop.com/',
				infourl : 'http://dev.cmstop.com/tinymce/plugins/CmsTopImagePlugin',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('ct_image', tinymce.plugins.CmsTopImagePlugin);
})();
