/**
 * $Id: editor_plugin_src.js 201 2007-02-12 15:56:56Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright � 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

(function() {

	tinymce.create('tinymce.plugins.ct_morereadPlugin', {
	
		init : function(ed, url) {
			
			ed.addCommand('mceMoreread', function() {
					var params = null,catobj = $('#catid');
					params =catobj[0].tagName == 'INPUT'?[catobj.val(),catobj.next().html()]:catobj.val();
					ed.windowManager.open({
						file : '?app=editor&controller=moreread&action=index',
						width : ed.getParam('template_popup_width',579),
						height : ed.getParam('template_popup_height',360),
						inline : 1
					}, {
						plugin_url : url,
						catid : params,
						mrs : ed.selection.getContent() && $(ed.selection.getContent()).find('a')
					});
			});

			ed.addButton('ctMoreread', {
				title : '\u5ef6\u4f38\u9605\u8bfb',
				cmd : 'mceMoreread'
			});
			
			ed.onNodeChange.add(function(ed, cm, n) {
				var moreread = $(n).closest('#moreread');
				cm.setActive('ctMoreread', moreread[0]?1:0);
				var doc = ed.getDoc();
				if(doc.getElementById('moreread'))
				doc.getElementById('moreread').style.background = '';
			});
			
			ed.onDblClick.add(function(ed, e) {
				var moreread = $(e.target).closest('#moreread');
				if(moreread[0]){
					ed.selection.select(moreread[0]);
					moreread.css('background','#FFFF99');
				}
			});
		}
		
	});

	tinymce.PluginManager.add('ct_moreread', tinymce.plugins.ct_morereadPlugin);
})();