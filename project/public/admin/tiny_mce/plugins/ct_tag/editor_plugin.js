/**
 * $Id: editor_plugin_src.js 201 2007-02-12 15:56:56Z spocke $
 *
 * @author shanhuhai
 * @copyright Copyright � 2004-2008, Moxiecode Systems AB, All rights reserved.
 */
(function() {

	tinymce.create('tinymce.plugins.ct_tagPlugin', {
	    cache : '',
		init : function(ed, url) {
			var t = this;
			ed.addCommand('mceTag', function() {
				var word = ed.selection.getContent();
				ed.execCommand('mceReplaceContent',false,'<a href="'+APP_URL+'?app=search&controller=index&action=search&wd='+encodeURIComponent(word)+'" target="_blank">'+word+'</a>');
			});
			ed.addButton('ctTag', {
				title : 'TAG\u5185\u94fe',
				cmd : 'mceTag'
			});
			ed.onNodeChange.add(function(ed, cm, n, co) {
				cm.setDisabled('ctTag',ed.selection.getContent()==''||co);
			});
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

	tinymce.PluginManager.add('ct_tag', tinymce.plugins.ct_tagPlugin);
})();