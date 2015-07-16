/**
 * 解析字符串模板
 * @param string tpl 	格式如<tr><td>{key}</td></tr>
 * @param array  json		json格式一或二维数组
 * @return string			返回替换后的多行html代码
 */
function fetchTpl(tpl, json)
{
	var html='', reg, row;
	if(!json[0]) {
		json = new Array(json);	//一维转二维统一处理
	}
	$.each(json, function(i, r){
		row = tpl;
		$.each(r, function (k, v){
			row = row.replace(RegExp('\{'+k+'\}', 'g'), v);
		});
		html += row;
	});
	return html;
}

var page = {
	container: 'pageDiv',		//容器id
	
	//取得项模板
	tpl : function ()
	{
		return tpl;
	},
	
	//取得容器对象
	ctn : function ()
	{
		if(typeof page.container == 'string')
		{
			return $('#'+page.container);
		}
		return page.container;
	},
	
	view: function (id)
	{
		ct.assoc.open('?app=paper&controller=content&action=index&id=' + id);
	},
	
	//添加版面
	add : function ()
	{
		$.getJSON('?app=paper&controller=page&action=add&eid=' + eid, function (json){
			if(json.state) {
				$.growlUI('添加成功');
				page.insert(json.data);
			}
		});
	},
	
	
	//更新其中一行
	update : function (json, id)
	{
		var html = fetchTpl(page.tpl(), json);
		var row = page.ctn().find('div[rel=' + id + ']');
		row.replaceWith(html);
		page.init_event(id);
	},
	
	//添加一行
	insert : function (json)
	{
		var html = fetchTpl(page.tpl(), json);
		page.ctn().append(html);
		page.init_event(json.pageid);
	},
	
	//移除一行
	remove : function (id)
	{
		page.ctn().find('div[rel=' + id + ']').remove();
	},
	
	//绑定事件,如果不存在id则为列表容器
	init_event: function (id)
	{
		var scape = page.ctn();
		if(id)
		{
			var scape = scape.find('div[rel=' + id + ']');
		}
		scape.find('li.page_pic>a').click(page.access);
		scape.find('li[field] input').hide();
		scape.find('li[field]').click(function (){
			if($(this).find('input:visible').length) return;
			var t = $(this);
			var ps = t.offset();
			if(ct.IE7) ps.top += 10;
			t.find('span').hide();
			t.find('input').height(14).show().select();
			$('#okBtn').css({left:ps.left + t.width() - 20, top:ps.top - 7, display: 'block'});
			t.removeClass('edit');
		}).css('cursor', 'pointer')
		.hover(function (){
			if($(this).find('input:visible').length) return;
			$(this).addClass('edit');
		}, function (){
			$(this).removeClass('edit');
		});
		
		scape.find('li[field] input').blur(page.blurSave);
		
		page.fillUp(id);	//填充上传控件
		
		scape.find('input.relate').click(page.relate);
		
		this.path();
		
		//删除版
		scape.find('img.delete').click(function (){
			var pageid = $(this).parents('div.item').attr('rel');
			ct.confirm("是否确定删除?<br/>该操作不会影响往期，只影响本期及后续期", function (){
				$.getJSON('?app=paper&controller=page&action=delete&pageid=' + pageid, function (json){
					if(json.state) {
						$.growlUI('删除成功');
						page.load();
					}
				});
			});
		});
		if(id) {
			scape.hover(function (){
				$(this).find('img.delete').show();
			}, function (){
				$(this).find('img.delete').hide();
			});
		}else{
			scape.find('div.item').each(function(i, e) {
				$(e).hover(function (){
					$(this).find('img.delete').show();
				}, function (){
					$(this).find('img.delete').hide();
				});
			});
		}
	},
	
	//访问前台
	access: function (){
		var is_url = 0;
		$('#pageDiv li.page_pic>a[url]').each(function (i, e){
			var url = $(e).attr('url');
			if(url != 'javascript:;' && url != '' && url != false) {
				is_url = 1;
				if(url.substr(0, 4) != 'http') url = WWW_URL + url;
				window.open(url, 'blank');
				return false;
			}
		});
		if(is_url == 0) {
			ct.warn('尚未发布或本版无关联新闻')
			return false;
		}
	},
	
	//预览
	prevView: function ()
	{
		var url = '?app=paper&controller=edition&action=prevView&eid=' + eid;
		$.get(url, function (url){
			url && window.open(url);
		});
	},
	
	//图片和pdf地址判断
	path : function ()
	{
		page.ctn().find('li.imageLi, li.pdfLi').each(function (i, e){
			if($(this).attr('path').length > 0) {
				$(this).addClass('yes');
			}else{
				$(this).removeClass('yes');
			}
		});
	},
	
	//载入/刷新列表
	load : function ()
	{
		$.getJSON('?app=paper&controller=page&action=page&id=' + eid, function (json){
			var length = json.length;
			if(length < 1 || !json[0]) return ;
			page.ctn().html(fetchTpl(page.tpl(), json));
			//绑定事件
			page.init_event();
		});
	},
	
	//即时保存
	blurSave: function()
	{
		var p = $(this).parent();
		var oldVal = p.find('span').text();
		p.find('span').text(this.value);
		p.find('span, img').show();
		$(this).hide();
		$('#okBtn').hide();
		if(oldVal != this.value)
		{
			var field = p.attr('field');
			var pageid = p.parents('div.item').attr('rel');
			
			if(field == 'pageno' && !/^[\d]+$/.test(this.value)) {
				ct.warn('版面号只能是整数');
				p.find('span').text(oldVal);
				return this.value = oldVal;
			}
			var url = '?app=paper&controller=page&action=save';
			$.post(url, {id:pageid, k:field, v: this.value}, function (data){
				if(data != 1) {
					if(field == 'pageno') {
						ct.warn('版面号不允许重复');
					}else{
						ct.error('修改失败');
					}
					p.find('span').text(oldVal);
					p.find('input').val(oldVal);
				}
			});
		}
	},
	
	//填充上传控件
	fillUp: function (id)
	{
		
		if(!id) {
			scape = $('#pageDiv div.item');
		}else{
			scape = $('#pageDiv div.item[rel='+id+']');
		}
		scape.each(function (i, e){
			var id = $(e).attr('rel');
			if(!$('#image_'+id).html()) 
			{
				page.upload('image', id);
				page.upload('pdf', id);
			}
		});
	},
	
	//插入flash upload控件
	upload: function (type, id)
	{
		var div = $('#pageDiv div.item[rel=' + id + ']');
		if(type == 'image')
		{
			var fileDesc = '图像';
			var fileExt = '*.jpg;*.jpeg;*.gif;*.png';
			var image = div.find('li.page_pic img');
			var img = 'images/upst.gif';
		}else{
			var fileDesc = 'PDF文件';
			var fileExt = '*.pdf';
			var img = 'apps/paper/images/upload.gif';
		}
		$("#" + type + '_' + id).uploader({
			script : '?app=system&controller=upload&action=upload',
			fileDesc : fileDesc,
			fileExt : fileExt,
			jsonType : 1,
			buttonImg : img,
			multi : false,
			complete:function(json) {
			 	if (json.state) {
			 		page.saveUp(type, id, json.file);
			 		if(type == 'image') {
			 			image.attr('src', UPLOAD_URL+json.file);
			 			div.find('li.imageLi').attr('path', 1);
			 		}else{
			 			div.find('li.pdfLi').attr('path', 1);
			 			ct.ok('上传成功');
			 		}
					page.path();
			 	} else {
			 		ct.error('上传失败!');
			 	}
			 },
			 error:function(data) {
			 	ct.error(data.error.type+':'+data.error.info);
			 }
		});	
	},
	
	//上传完毕后ajax保存地址
	saveUp: function (type, id, file)
	{
		if(!type || !id || !file) return ;
		var url = '?app=paper&controller=page&action=save';
		$.post(url, {id:id, k:type, v: file}, function (data){
			if(data != 1) {
				ct.error('保存地址失败');
			}
		});
	},
	
	//关联内容
	relate: function (){
		var img = $(this).parents('ul').find('li.page_pic img').attr('src');
		if(img == UPLOAD_URL) return ct.warn('请先上传本版截图再关联文章');
		var id = $(this).parents('div.item').attr('rel');
		page.view(id);
	},
	
	//各按钮的处理
	button: function ()
	{
		$('#stateBtn').mouseover(function (){
			$('#pageDiv div.item:first').css('z-index', -1);	//ie6,7下会遮住模拟下拉框
			$('#stateUl').slideDown('fast');
		});
		$('#stateUl li').hover(function (){
			$(this).addClass('over');
		}, function (){
			$(this).removeClass('over');
		});
		$(document).click(function (){
			$('#stateUl').slideUp('fast');
			$('#pageDiv div.item:first').css('z-index', 0);		//ie6,7
		});
		$('#publish').click(page.publish);
		$('#sleep').click(page.sleep);
		$('#unpublish').click(page.unpublish);
		$('#prevView').click(page.prevView);
		$('#access').click(page.access);
		$('#add').click(page.add);
	},
	
	//发布内容
	publish: function (){
		var countMark = 0;
		$('li.count>span').each(function (i, e){
			var num = parseInt(e.innerHTML);
			if(num < 1) return countMark = 1;
		});
		if(countMark) return ct.warn("有些版面还没有关联文章，不能发布");
		var btn = this;
		btn.disabled = 1;
		$.getJSON('?app=paper&controller=page&action=publish&eid=' + eid, function(data) {
			if(data.state) {
				$.growlUI('操作成功');
				$('#disabled').html('已发布');
			}
			else
			{
				ct.ok('发布失败', 'error');
			}
			btn.disabled = 0;
		});
		
	},
	//休眠
	sleep: function (){
		var btn = this;
		btn.disabled = 1;
		$.getJSON('?app=paper&controller=page&action=sleep&eid=' + eid, function(data) {
			if(data.state) {
				$.growlUI('操作成功');
				$('#disabled').html('休　眠');
			}
			else
			{
				ct.ok('发布失败', 'error');
			}
			btn.disabled = 0;
		});
	},
	//设为未发布
	unpublish: function (){
		var btn = this;
		btn.disabled = 1;
		$.getJSON('?app=paper&controller=page&action=unpublish&eid=' + eid, function(data) {
			if(data.state) {
				$.growlUI('操作成功');
				$('#disabled').html('未发布');
			}
			else
			{
				ct.ok('发布失败', 'error');
			}
			btn.disabled = 0;
		});
	}
};