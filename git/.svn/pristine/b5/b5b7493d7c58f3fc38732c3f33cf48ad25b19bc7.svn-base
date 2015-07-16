//杂志模板前台使用的js文件
$(function (){
	var taba = $('div.tab_3>a');
	taba.click(function (){
		taba.removeClass('s_3');
		var index = taba.index($(this).addClass('s_3'));
		$(this).parent().parent().next().find('ul').hide().eq(index).show();
	});
	
	$('div.column').each(function (){
		var h = $(this).height();
		var lh = $('#leftColumn').height();
		var rh = $('#rightColumn').height();
		if(lh <= rh) {
			$('#leftColumn').append($(this));
		}else{
			$('#rightColumn').append($(this));
		}
	});
	
	$('div.box_9>div.txt_list>ul a').mouseover(function (){
		if(this.title) $('div.mar_t_10>img').attr('src', this.title);
	});
});