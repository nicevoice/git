/**
 * $Id: editor_plugin_src.js 201 2007-02-12 15:56:56Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright �? 2004-2008, Moxiecode Systems AB, All rights reserved.
 */
window.flag = false;
(function() {

	tinymce.create('tinymce.plugins.ct_modeswitchPlugin', {
		
	    init : function(ed, url) {
			var id = ed.id,t = this;
			ed.addCommand('mceModeswitch',function(){
				ed.remove();
				editor(id,(window.flag=window.flag === true?false:true)?'complete':'');
				ed.nodeChanged;
			});
			ed.addButton('ctModeswitch',{
				title:'\u7f16\u8f91\u5668\u6a21\u5f0f\u5207\u6362',
				cmd:'mceModeswitch'
			})
			ed.onNodeChange.add(function(ed, cm, e, c) {
				cm.setDisabled('ctModeswitch', ed.getParam('fullscreen_is_enabled'));
			});

		},
		
		getInfo : function() {
			return {
				longname : 'modeswitch',
				author : 'shanhuhai',
				authorurl : 'http://www.cmstop.com',
				version : "1.0"
			};
		}
	});

	tinymce.PluginManager.add('ct_modeswitch', tinymce.plugins.ct_modeswitchPlugin);
})();