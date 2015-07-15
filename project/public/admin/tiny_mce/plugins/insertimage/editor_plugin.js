
(function() {
	
	tinymce.create('tinymce.plugins.insertimagePlugin', {

		init : function(ed, url) {
			
			ed.addCommand('mceInsertImage', function() {
				if (ed.dom.getAttrib(ed.selection.getNode(), 'class').indexOf('mceItem') != -1)
					return;
				ed.windowManager.open({
					file : '?app=system&controller=attachment&action=index&tinymce=true&ext_limit=jpg,jpeg,png,gif',
					width : 780 + parseInt(ed.getLang('insertimage.delta_width', 0)),
					height : 450 + parseInt(ed.getLang('insertimage.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url, 
					some_custom_arg : null
				});
			});
			
			ed.addButton('insertImage', {
				title : '\u6279\u91cf\u4e0a\u4f20\u56fe\u7247',
				cmd : 'mceInsertImage'
			});

			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('insertImage', n.nodeName == 'IMG' && ed.dom.getAttrib(ed.selection.getNode(), 'class').indexOf('mceItem') != -1);
			});
		},

		createControl : function(n, cm) {
			return null;
		},

		getInfo : function() {
			return {
				longname : 'Example plugin',
				author : 'Some author',
				authorurl : 'http://tinymce.moxiecode.com',
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/example',
				version : "1.0"
			};
		}
	});

	tinymce.PluginManager.add('insertimage', tinymce.plugins.insertimagePlugin);
})();