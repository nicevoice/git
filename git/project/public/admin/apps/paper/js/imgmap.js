function fetchTpl(tpl, json)
{
	var html='', reg, row;
	if(!json[0]) {
		json = new Array(json);	//一维转二维统一处理
	}
	$.each(json, function(i, r){
		row = tpl;
		r.j = r.k ? parseInt(r.k) + 1 : i + 1;
		if(r.title) r.cutTitle = r.title.substr(0, 14);
		if(!r.k) r.k = i;
		$.each(r, function (k, v){
			row = row.replace(RegExp('\{'+k+'\}', 'g'), v);
		});
		html += row;
	});
	html = html.replace(RegExp('\{cutTitle\}', 'g'), '');
	return html;
}
//http://odyniec.net/projects/imgareaselect/usage.html#callback-functions
var area = {
	ias: {},	//插件实例
	posi: '',
	dblclick: 0,	//双击选区状态中不激活savemap
	init: function (){
		//写入原有热点,绑定事件
		if(coords.length > 0) {
			$('#store').html(fetchTpl(area.tpl, coords));
			area.event();
			area.mark();
		}
		var selection = $('div.imgareaselect-selection').parent();
		//右键菜单
		$('#rmenu li').mouseover(function (){
			$('#rmenu li').removeClass('hover');
			$(this).addClass('hover');
		});
		$('#rmenu li.remove').click(function (){
			$('#store>li.selected').find('a.remove').click();
		});
		$('#rmenu li.mark').click(function (){
			$('#store>li.selected').dblclick();
		});
		$(document).click(function (){
			$('#rmenu').hide();
		});
		selection.bind('contextmenu', function (e){
			$('#rmenu').css({left: e.pageX, top: e.pageY}).show();
			return false;
		});
		$('.imgareaselect-outer').mousedown(function (){
			$('#store li.selected').removeClass('selected');
			$('div.mark').show().css('z-index', 1);
			$('div.mark').removeClass('over');
		});
		$('div.imgareaselect-selection').parent().hover(
			function (){
				$('div.mark').each(function (i, e){
					if($(e).css('z-index') < 0) return $('#tips').html($(e).text()).show();
				});
			}, function (){
				$('#tips').hide();
			}
		).mousemove(function (e){
			$('#tips').css({left: e.pageX + 10, top: e.pageY + 10});
		}).dblclick(function (){
			$('#store li.selected a.mark').click();
			area.dblclick = 1;
			setTimeout("area.dblclick = 0", 300);
			return false;
		});
	},
	tpl: '<li rel="{k}" title="{title}">\
			<input name="x1[]" type="text" value="{x1}"/>\
			<input name="y1[]" type="text" value="{y1}"/>\
			<input name="x2[]" type="text" value="{x2}"/>\
			<input name="y2[]" type="text" value="{y2}"/>\
			<input name="mapid[]" type="text" value="{mapid}"/>\
			<input name="contentid[]" type="text" value="{contentid}"/>\
			<a class="mark" href="javascript:;"><img src="images/edit.gif" alt="标注"/></a>\
			<a class="remove" href="javascript:;"><img src="images/delete.gif" alt="移除"/></a>\
			<span>{j}: {cutTitle}</span>\
		</li>',
	add: function (op)
	{
		if($('#store>li>input[name=contentid[]][value=0]').length) {
			return ct.warn('请先标注完毕再添加');
		}
		if(!op.x1) {
			area.emptyArea();
			var op = {x1:10, y1:10, x2:10, y2:10};
		}
		op.k = $('#store li').length;
		op.mapid = 0;
		op.contentid = 0;
		op.title = '';
		var li = $(fetchTpl(area.tpl, op));
		$('#store').append(li);
		area.createMark(op.k, op);
		//绑定事件
		area.event(op.k);
		area.updateData(op);
		$('#store>li').removeClass('selected');
		li.addClass('selected');
		//$('div.mark').hide();
	},
	
	//右侧li事件
	event: function (k)
	{ 
		if(k) 
		{
			var li = $('#store li[rel=' + k + ']');
			var mark = $('div.mark[rel=' + k + ']');
		}
		else
		{
			var li = $('#store li');
			var mark = $('div.mark');
		}
		li.click(area.clickLi).dblclick(area.setMark).mouseover(function (){
			$(this).addClass('over');
			var k = $(this).attr('rel');
			$('div.mark[rel='+k+']').addClass('over');
		}).mouseout(function (){
			$('#store li').removeClass('over');
			$('div.mark').removeClass('over');
		});
		li.find('a.mark').click(area.setMark);
		li.find('a.remove').click(function (){
			area.remove($(this).parent().attr('rel'));
			//重排顺序
			$('#store>li').each(function (i, e){	
				var srel = parseInt($(e).attr('rel'));
				if(srel == i) return true;
				$(e).attr('rel', i);
				$('div.mark[rel='+srel+']').attr('rel', i);
				var title = $(e).find('span').text();
				title = title.replace((srel+1) + ':', (i + 1) + ':');
				$(e).find('span').text(title);
				var title = $('div.mark[rel='+i+']').text();
				title = title.replace((srel+1) + ':', (i + 1) + ':');
				$('div.mark[rel='+i+']').text(title);
			});
		});
	},
	
	ps : function (){
		if(typeof posi != 'object') area.posi = $('#pic').offset();
		return area.posi;	//图片的定位
	},	
	mark: function (k)
	{
		$.each(coords, function(i, e){
			area.createMark(i, e);
		});
	},
	createMark: function (i, e){
		var ps = area.ps();
		var mark = $('<div rel="' + i + '" class="mark">'+(i+1)+': '+e.title+'</div>');
		var left = parseInt(e.x1) + parseInt(ps.left)  + 'px';
		var top = parseInt(e.y1) + parseInt(ps.top) + 'px';
		mark.width(e.x2 - e.x1 - 6);
		mark.height(e.y2 - e.y1 - 6);
		mark.css({left: left, top: top, opacity: .6});
		$('#pic').parent().append(mark);
		
		mark.mouseover(function (){
			area.clickLi($(this).attr('rel'));
			
		});
	},
	//拖动的同时移动mark,如果没有mark则生成新的
	moveMark: function (k, op)
	{
		var mark = $('div.mark[rel='+k+']');
		var ps = area.ps();
		var left = parseInt(op.x1) + parseInt(ps.left)  + 'px';
		var top = parseInt(op.y1) + parseInt(ps.top) + 'px';
		mark.width(op.x2 - op.x1 - 6);
		mark.height(op.y2 - op.y1 - 6);
		mark.css({left: left, top: top});
	},
	
	//在右侧增加表单保存本缩略图数据
	updateInput: function (k, op)
	{
		var li = $('#store li[rel='+k+']');
		li.find('input[name=x1[]]').val(op.x1);	
		li.find('input[name=y1[]]').val(op.y1);	
		li.find('input[name=x2[]]').val(op.x2);	
		li.find('input[name=y2[]]').val(op.y2);	
	},
	
	//显示当前选区数据
	updateData: function (op)
	{
		$('#data h2 span:eq(0)').text(op.x1);
		$('#data h2 span:eq(1)').text(op.y1);
		$('#data h2 span:eq(2)').text(op.x2);
		$('#data h2 span:eq(3)').text(op.y2);
	},
	
	//点击li时切换当前选区
	clickLi: function (k)
	{
		if(typeof k == 'object') {
			var my = $(this);
			var k = my.attr('rel');
		}else{
			if($('#store li.selected').attr('rel') == k) return false;
			return $('#store li[rel=' + k + ']').click();
		}
		
		$('#store li').removeClass('selected');
		my.addClass('selected');
		var x1 = my.find('input[name=x1[]]').val();
		var y1 = my.find('input[name=y1[]]').val();
		var x2 = my.find('input[name=x2[]]').val();
		var y2 = my.find('input[name=y2[]]').val();
		area.ias.setSelection(x1, y1, x2, y2);
		area.ias.setOptions({show: true}); 
		area.ias.update();
		area.updateData({x1:x1, y1:y1, x2:x2, y2:y2});
		$('div.mark').css('z-index', 1).removeClass('over');
		$('div.mark[rel=' + k + ']').css('z-index',-1).addClass('over');
	},
	
	
	//标注文章
	setMark: function ()
	{
		ct.ajax('标记文章','?app=paper&controller=content&action=relate', 520, 490);
	},
	
	//删除标注
	remove: function (k)
	{
		var li = $('#store li[rel=' + k + ']');
		var mapid = li.find('input[name=mapid[]]').val();
		if(mapid > 0) {
			$.get('?app=paper&controller=content&action=delMap&id='+mapid, function (data) {
				if(data != '1') {
					ct.error('移除失败');
				}
			});
		}
		li.remove();
		$('div.mark[rel=' + k + ']').remove();
		area.emptyArea();
	},
	//清空选区
	emptyArea: function ()
	{
		op = {x1:10, y1:10, x2:10, y2:10};
		//初始化选区
		area.ias.setSelection(op.x1, op.y1, op.x2, op.y2);
		var set = area.ias.getOptions();
		set.show = true;
		area.ias.setOptions({show: true}); 
		area.ias.update();
		$('div.mark').show().css('z-index', 1);
	},
	//保存所有
	/*saveCoords: function ()
	{
		var length = $('#store li').length;
		if(!length) return ct.warn('还没有添加任何热点');
		var pass = true;
		$('#store li').each(function (i, e){
			if($(e).find('input[name=contentid[]]').val() < 1) {
				pass = false;
			}
		});
		if(!pass) {
			ct.warn('热点没有标记完毕');
			return false;
		}
		
		$.ajax({
			url: '?app=paper&controller=page&action=saveCoords&pageid=' + pageid,
			type: 'post',
			dataType: 'json',
			data: $('#mapform').serialize(),
			success: function (json) {
				if(json.state) {
					ct.ok('保存成功');
					url = json.url;
					$.each(json.data, function(i,e) {
						$('#store li:eq('+i+')').find('input[name=mapid[]]').val(e);	//写入新添加的mapid
					});
				}
				else
				{
					ct.error('保存失败');
				}
			}
		});
		return true;
	},*/
	
	//保存单点
	saveMap: function (li){
		var x1 = li.find('input[name=x1[]]').val();
		var x2 = li.find('input[name=x2[]]').val();
		var y1 = li.find('input[name=y1[]]').val();
		var y2 = li.find('input[name=y2[]]').val();
		var post = {
			mapid: li.find('input[name=mapid[]]').val(),
			coords: x1 + ',' + y1 + ',' + x2 + ',' + y2,
			contentid: li.find('input[name=contentid[]]').val(),
			sort: li.attr('rel'),
			pageid: pageid
		};
		$.post('?app=paper&controller=content&action=saveMap', post, function (mapid) {
			li.find('input[name=mapid[]]').val(mapid);
		});
	},
	
	//查看前台
	prevView: function ()
	{
		var cid = $('#store li:first').find('input[name=contentid[]]').val();
		if(!cid) return ct.warn('没有关联的文章');
		window.open('?app=paper&controller=content&action=prevView&cid=' + cid + '&pageid=' + pageid);
	},
	
	//以下API的三个回调函数
	start: function (img, option)
	{
		$('#store li.selected').removeClass('selected');
	},
	
	end: function (img, option)
	{
		if(option.x2 <= option.x1 + 19 || option.y2 <= option.y1 + 19) {	//如果选区太小
			return false;
		}
		var curr = $('#store li.selected');
		if(!curr.length) {
			area.add(option);
			area.updateData(option);
			curr = $('#store li.selected');
		}
		//curr.click();
		if(curr.find('input[name=contentid[]]').val() == 0) {
			curr.find('a.mark').click();
		}else{
			$('div.mark').show();
			setTimeout(function (){
				if(!area.dblclick) area.saveMap(curr);
			}, 350);
		}
	}, 
	
	change: function (img, option) 
	{
		var k = $('#store li.selected').attr('rel');
		if(!k) return false;
		area.updateInput(k, option);
		area.updateData(option);
		area.moveMark(k, option);
	}
};