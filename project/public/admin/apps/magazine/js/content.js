if(!typeof console) var debug = console.info;
String.prototype.trim = function()
{
	return this.replace(/(^\s*)|(\s*$)/g, "");
}
var content = 
{
	view: function (id)
	{
		ct.assoc.open('?app=magazine&controller=content&action=index&id=' + id);
	},
	
	//关联文章
	relate: function()
	{
		ct.ajax('关联文章','?app=magazine&controller=content&action=relate&pid='+pid, 805, 500);
	},
	
	add: function(id)
	{
		ct.form('添加栏目', '?app=magazine&controller=content&action=add&eid='+eid, 400, 200, function (json){
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
		if(!id) return ct.warn('请选择要删除关联的记录');
		ct.confirm('确定删除关联？本操作不删除原文章', function(){
			$.getJSON('?app=magazine&controller=content&action=delete&id='+id+'&pid='+pid, function(json){
				if (json.state)
				{
					tableApp.load();
				}
				else
				{
					ct.error('删除失败');
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
}