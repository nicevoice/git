DIY.registerEngine('picture', {
	addFormReady:function(form, dialog) {},
	editFormReady:function(form, dialog) { },
	afterRender: function(widget) { 
		widget.find('img').each(function(){
			this.src += '?'+Math.random()
		});
	},
	beforeSubmit:function(form, dialog){},
	afterSubmit:function(form, dialog){}
});