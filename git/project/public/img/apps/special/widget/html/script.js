(function(){
var htmlCode = function(form, dialog) {
	setTimeout(function(){
		form.find('textarea').editor('mini',{
			forced_root_block : '',
			height:'230px',
			width:'440px'
		});
	}, 0);
	dialog.bind('dialogbeforeclose',function(){
		try {
			tinyMCE.editors.html_code.remove();
		} catch (e) {}
	});
}
DIY.registerEngine('html', {
	dialogWidth : 460,
	addFormReady:htmlCode,
	editFormReady:htmlCode,
	afterRender: function(widget) { },
	beforeSubmit:function(form, dialog){
		if (window.tinyMCE) {
			var content = tinyMCE.activeEditor.getContent();
			form.find('textarea').val(content);
		}
	},
	afterSubmit:function(form, dialog){}
});


})()