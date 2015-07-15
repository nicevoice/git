$(function(){
	function open(url, input){
		var d = ct.iframe({title:url, width:620}, {
			ok:function(checked){
				input.value = checked;
				d.dialog('close');
			}
		});
	}
	$('.push-to-place').each(function(){
		var input = this;
		$('<button type="button" class="button_style_1">选择</button>').click(function(){
			open('?app=special&controller=online&action=recommend&placeid='+input.value, input);
		}).insertAfter(input);
	});
});