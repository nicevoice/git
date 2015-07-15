/**
 * $Id: editor_plugin_src.js 201 2007-02-12 15:56:56Z spocke $
 *
 * shanhuhai 
 * @copyright Copyright � 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

(function() {
	tinymce.PluginManager.requireLangPack('example');

	tinymce.create('tinymce.plugins.ct_wdcountPlugin', {
		concache : '',
		msgcache : '',
		init : function(ed, url) {
		    var t = this;
			ed.addCommand('mceWdcount', function() {
				var post = false;
				var content = ed.getContent();
		        post = t.concache?(t.concache != content):true;
				if(post)
				{
					$.post('?app=editor&controller=wdcount&action=index',{content:ed.getContent({format : 'raw'})},function(data){
						ct.ok(data);
						t.msgcache = data;
					});
					t.concache = content;
				}
				else
				{
					ct.ok(t.msgcache);
				}
			});
			ed.addButton('ctWdcount', {
				title : '\u8f93\u5165\u7edf\u8ba1',
				cmd : 'mceWdcount'
			});
		},

		createControl : function(n, cm) {
			return null;
		},

		getInfo : function() {
			return {
				longname : 'Example plugin',
				author : 'shanhuhai',
				authorurl : 'http://tinymce.moxiecode.com',
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/example',
				version : "1.0"
			};
		}
	});

	tinymce.PluginManager.add('ct_wdcount', tinymce.plugins.ct_wdcountPlugin);
})();