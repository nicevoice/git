var model = 
{
	add: function ()
	{
		ct.form('添加内容模型', '?app=system&controller=model&action=add', 430, 255, function (response){
			if (response.state)
			{
				tableApp.load();
				ct.ok('操作成功');
				return true;
			}
			else
			{
				ct.error(response.error);
				return false;
			}
		});
	},
	
	edit: function (modelid)
	{
		ct.form('修改内容模型', '?app=system&controller=model&action=edit&modelid='+modelid, 430, 255, function (response){
			if (response.state)
			{
				tableApp.load();
				ct.ok('操作成功');
				return true;
			}
			else
			{
				ct.error(response.error);
				return false;
			}
		});
	},
	
	del: function (modelid)
	{
		ct.confirm('确定删除编号为<b style="color:red">'+modelid+'</b>的内容模型吗？', function(){
			$.getJSON('?app=system&controller=model&action=delete&modelid='+modelid, function(response){
				if (response.state)
				{
					tableApp.load();
					ct.ok('删除成功！');
				}
				else
				{
					ct.error('删除失败！');
				}
			});
		});
	}
}