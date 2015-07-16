(function() {
	tinymce.create('tinymce.plugins.ct_save', {
		init : function(ed, url) {
			var t = this;
			t.editor = ed;
			ed.addShortcut('ctrl+s', ed.getLang('save.save_desc'), function() {
				t._save();
			});
		},
		_save : function() {
			try {
				$(document.getElementsByTagName('form')).submit();
			} catch (e) {
				return false;
			}
		}
	});

	// Register plugin
	tinymce.PluginManager.add('ct_save', tinymce.plugins.ct_save);
})();