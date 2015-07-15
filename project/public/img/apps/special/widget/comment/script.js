DIY.registerEngine('comment', {
	dialogWidth : 320,
	addFormReady:function(form, dialog) {
		
	},
	editFormReady:function(form, dialog) {
		
	},
	afterRender: function(widget) { 
		widget.find('.mod-comment').comment();
	},
	beforeSubmit:function(form, dialog){},
	afterSubmit:function(form, dialog){}
});