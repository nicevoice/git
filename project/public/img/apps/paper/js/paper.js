//报纸模板前台使用的js文件
$(function (){
	//使当前期高亮
	$('#eBack a').each(function (i, e){
		if(e.innerHTML == total_number) {
			$(e).addClass('ecurrent').css({'color':'red','font-weight':'bold'});
		}
	});
	//map的显示效果
	$('#paper-screenshot>.map').hover(
		function (){
			$(this).addClass("hilight");
		},
		function (){
			$(this).removeClass("hilight");
		});

	$('#conList>li').hover(
		function (){
			var id = $(this).attr('rel');
			$('#paper-screenshot>.map[rel=' + id + ']').addClass('hilight');
		},
		function (){
			var id = $(this).attr('rel');
			$('#paper-screenshot>.map[rel=' + id + ']').removeClass('hilight');
		});
	
	//报纸列表
	$('#paper_select').change(function (){
		var url = $(this).find('option:selected').attr('url');
		if(url == 'javascript:;' || url == '') return false;
		if(url.indexOf('ttp:') == -1) {
			url = WWW_URL + url;
		}
		location.href = url;
	}).val(pid);
	
	
	//往期回顾
	$('a.eList').click(function (){
		$('#eBack').slideToggle('fast');
	});
	$('a.prevE, a.nextE').click(prevNext);
	
	//pv统计
	$.getJSON(APP_URL+'?app=system&controller=content&action=stat&jsoncallback=?&contentid='+contentid, function(data){
		$('#pv').text(data.pv);
		$('#comments').text(data.comments);
	});
});
//递归选择上/下期(有些期是空链接)
function prevNext()
{
	var current = $('.ecurrent');
	if($(this).hasClass('nextE')) {
		var a = current.next('a');
	}else{
		var a = current.prev('a');
	}
	if(!a.html()) return ;
	
	location.href = a.attr('href');
}
//上一版，下一版
function prevNextPage(t)
{
	var current = $('#page-printed li.c-red');
	if($(t).hasClass('next')) {
		var o = current.next('li');
	}else{
		var o = current.prev('li');
	}
	if(!o.html()) return;
	var url = o.find('a').attr('href');
	if(url && url != 'javascript:;') location.href = url;
}