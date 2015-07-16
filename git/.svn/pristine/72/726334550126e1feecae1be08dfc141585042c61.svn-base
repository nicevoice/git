var workflow = 
{
	add: function ()
	{
		ct.form('添加工作流', '?app=system&controller=workflow&action=add', 400, 350, function (response){
			if (response.state)
			{
				tableApp.addRow(response.data);
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
	
	edit: function (workflowid)
	{
		ct.form('修改工作流', '?app=system&controller=workflow&action=edit&workflowid='+workflowid, 400, 350, function (response){
			if (response.state)
			{
				tableApp.updateRow(workflowid, response.data);
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
	
	del: function (workflowid)
	{
		if (workflowid === undefined)
		{
			workflowid = tableApp.checkedIds();
			var msg = '确定删除选中的<b style="color:red">'+workflowid.length+'</b>条记录吗？';
		}
		else
		{
			var msg = '确定删除编号为<b style="color:red">'+workflowid+'</b>的记录吗？';
		}
		if (workflowid.length === 0)
		{
			ct.warn('请选择要操作的记录');
			return false;
		}
		ct.confirm(msg, function(){
			$.getJSON('?app=system&controller=workflow&action=delete&workflowid='+workflowid, function(response){
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