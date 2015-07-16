if(!typeof console) var debug = console.info;
var page = 
{
	view: function (id)
	{
		ct.assoc.open('?app=magazine&controller=page&action=index&id=' + id);
	},
	editAll: function()
	{
		$('tr img.edit').click();
	},
	saveAll: function()
	{
		var divs = $('div.editHand');
		if(divs.length < 1) return;
		var str = '';
		divs.each(function(i, e){
			var div = $(e);
			var input = div.find('input:text');
			var p = {};
			str += div.attr('rel') + '|';
			str += input.eq(0).val() + '|';
			str += input.eq(1).val() + '|';
			str += input.eq(2).val() + '||';
		});
		$.post('?app=magazine&controller=page&action=save&eid='+eid, {data: str}, function(data){
			if(data == 1) {
				$.growlUI('保存成功');
				divs.remove();
				tableApp.load();
			}else{
				ct.error('保存出错');
			}
		});
	},
	//行编辑模式
	edit: function(id, tr)
	{
		var ps = tr.offset();
		var div = $('<div/>').attr('className', 'editHand').attr('rel', id).css({
			position: 'absolute',
			left: ps.left,
			top: ps.top,
			width: tr.width() - 64,
			height: tr.height() - 2
		});
		var tdName = tr.find('td[name=name]');
		var ps2 = tdName.offset();
		var css = {position: 'absolute'};
		
		div.append($('<input type="text"/>'));
		css.left = ps2.left + (tdName.width()-220)/2;
		css.width = 200;
		div.find('input').css(css).val($.trim(tdName.text()));
		
		div.append($('<input type="text"/>'));
		css.left = ps2.left + tdName.width() + 102;
		css.width = 97;
		div.find('input:eq(1)').css(css).val($.trim(tr.find('td[name=editor]').text()));
		
		div.append($('<input type="text"/>'));
		css.left = css.left + 106;
		div.find('input:eq(2)').css(css).val($.trim(tr.find('td[name=arteditor]').text()));
		
		div.append($('<input type="button" value="保存"/>'));
		css.left = css.left + 106;
		div.find('input:eq(3)').css(css).addClass('button_style_1').width(60);
		tr.parents('table').before(div);
		
		div.find('input:button').click(page.save);
	},
	save: function()
	{
		var div = $(this).parent();
		var input = div.find('input:text');
		var post = {
			pid: div.attr('rel'),
			name: input.eq(0).val(),
			editor: input.eq(1).val(),
			arteditor: input.eq(2).val()
		};
		$.post('?app=magazine&controller=page&action=save&eid='+eid, post, function(data){
			if(data == 1) {
				var tr = $('#tr_' + post.pid);
				tr.find('td[name=name]>a').html(post.name);
				tr.find('td[name=editor]').html(post.editor);
				tr.find('td[name=arteditor]').html(post.arteditor);
				div.remove();
				$.growlUI('保存成功');
			}else{
				ct.error('保存出错');
			}
		});
	},
	add: function(id)
	{
		ct.form('添加栏目', '?app=magazine&controller=page&action=add&eid='+eid, 400, 200, function (json){
			if (json.state)
			{
				tableApp.addRow(json.data);
				return true;
			}
			else
			{
				ct.error(json.error);
				return false;
			}
		});
	},
	//单行或多行删除
	del: function (id)
	{
		if(typeof id == 'object' || !id) 
		{
			id = tableApp.checkedIds().join(',');
			var mul = 1;	//多行删除模式
		}
		if(!id) return ct.warn('请选择要删除的记录');
		ct.confirm('确定删除选中记录？', function(){
			$.getJSON('?app=magazine&controller=page&action=delete&id='+id, function(json){
				if (json.state)
				{
					tableApp.load();
				}
				else
				{
					ct.error('删除失败！');
				}
			});
		});
	},

	manage: function(pid)
	{
		ct.assoc.open('?app=magazine&controller=content&action=index&pid=' + pid);
	},
	
	//查看前台 
	access: function (eid, tr)
	{
		var url = tr.find('td>img.view').attr('href');
		if(url == 'javascript:;' || url == '') {
			ct.warn('该期尚未发布，或无头版头条新闻');
			return false;
		}
		url = WWW_URL + url;
		window.open(url);
	},
	//发布内容
	publish: function (eid)
	{
		var count = 0;
		var pageMark = 0;
		$('td.count>span').each(function (i, e){
			var num = parseInt(e.innerHTML);
			if(num < 1) pageMark = 1;
			count += num;
		});
		if(count < 1) return ct.warn('没有关联任何文章，不能发布');
		if(pageMark) {
			ct.confirm('有些版面没有文章，是否发布?', publish);
		}else{
			publish();
		}
		function publish()
		{
			$.getJSON('?app=magazine&controller=page&action=publish&eid=' + eid, function(data) {
				if(data.state) 
				{
					$.growlUI('发布完成');
				}
				else
				{
					ct.ok('发布失败', 'error');
				}
			});
		}
	},
	//关联文章
	relate: function(pid)
	{
		ct.ajax('关联文章','?app=magazine&controller=content&action=relate&pid='+pid, 805, 500);
	}
}