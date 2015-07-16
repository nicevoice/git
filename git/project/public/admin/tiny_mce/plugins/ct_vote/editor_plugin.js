(function() {

	tinymce.create('tinymce.plugins.ct_votePlugin', {

		init : function(ed, url) {

			ed.addCommand('mceVote', function() {
				var params = null,catobj = $('#catid');
				params =catobj[0].tagName == 'INPUT'?[catobj.val(),catobj.next().html()]:catobj.val();
				ed.windowManager.open({
					file : '?app=editor&controller=vote&action=index',
					width : ed.getParam('template_popup_width', 560),
					height : ed.getParam('template_popup_height', 390),
					inline : 1
				}, {
					plugin_url : url,
					catid : params
				});
			});

			ed.addButton('ctVote', {
				title : '\u6295\u7968',
				cmd : 'mceVote'
			});
			ed.onNodeChange.add(function(ed, cm, n) {
				var vote = $(n).closest('#vote');
				cm.setActive('ctVote', vote[0]?1:0);
				var doc = ed.getDoc();
				if(doc.getElementById('vote'))
				doc.getElementById('vote').style.background = '';
			});
			
			ed.onDblClick.add(function(ed, e) {
				var vote = $(e.target).closest('#vote');
				if(vote[0]){
					ed.selection.select(vote[0]);
					vote.css('background','#FFFF99');
				}
			});

		},

		createControl : function(n, cm) {
			return null;
		},
		
		getInfo : function() {
			return {
				longname : 'Vote plugin',
				author : 'shanhuhai',
				authorurl : 'http://tinymce.moxiecode.com',
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/example',
				version : "1.0"
			};
		}
	});

	tinymce.PluginManager.add('ct_vote', tinymce.plugins.ct_votePlugin);
})();