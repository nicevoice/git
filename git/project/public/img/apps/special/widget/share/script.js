(function(){
function _init(form,dialog) {
	var style = $("input[name='style']").val();
	var number = $("input[name='number']").val();
	var tbody_button = $("tbody[rel='button']");
	var tbody_float = $("tbody[rel='float']");
	if(!number) number = 'b2';
	$("input[value='"+number+"']").attr('checked','checked');
	$('#share_button').click(function(){
		tbody_button.show();
		tbody_float.hide();
	});
	$('#share_float').click(function(){
		tbody_float.show();
		tbody_button.hide();
	});
}
DIY.registerEngine('share', {
	//dialogWidth : 600,
	addFormReady:function(form, dialog) {
		_init(form,dialog);
	},
	editFormReady:function(form, dialog) {
		_init(form,dialog);
	},
	afterRender: function(widget) { 
		
	},
	beforeSubmit:function(form, dialog){
		
	},
	afterSubmit:function(form, dialog){}
});

})()