var question = 
{
    edit: function(questionid) {
        ct.formDialog({title: '编辑提问'}, '?app=interview&controller=question&action=edit&questionid=' + questionid, function(json) {
            if (json && json.state) {
                ct.ok('编辑成功');
                return true;
            }
            ct.error(json && json.error || '编辑失败，请重试');
            return false;
        }, function(form, dialog) {
            // TODO random ip plugin
        });
    },

	commend: function (questionid)
	{
		question._action(questionid, 'commend');
	},
	
	pass: function (questionid)
	{
		question._action(questionid, 'pass');
	},

	del: function (questionid)
	{
		if (questionid === undefined)
		{
			questionid = tableApp.checkedIds();
			var msg = '确定删除选中的<b style="color:red">'+questionid.length+'</b>条记录吗？';
		}
		else
		{
			var msg = '确定删除ID为<b style="color:red">'+questionid+'</b>的记录吗？';
		}
		if (questionid.length === 0)
		{
			ct.warn('请选择要操作的记录');
			return false;
		}
		ct.confirm(msg, function(){
			question._action(questionid, 'delete');
		});
	},
	
	remove: function (questionid)
	{
		question._action(questionid, 'remove');
	},
	
	clear: function ()
	{
		ct.confirm('您确定清空所有网友提问记录？', function(){
			$.getJSON('?app=interview&controller=question&action=clear&contentid='+contentid, function(response) {
				if (response.state)
				{
					tableApp.load();
				}
				else
				{
					ct.error(response.error);
				}
			});
		});
	},
	
	ipLock: function (questionid)
	{
		question._action(questionid, 'iplock');
	},
	
	ipUnlock: function (questionid)
	{
		question._action(questionid, 'ipunlock');
	},
	
	_action: function (questionid, action)
	{
        if (questionid == undefined) questionid = tableApp.checkedIds();
		if (questionid.length === 0)
		{
			ct.warn('请选择要操作的记录');
			return false;
		}
		$.getJSON('?app=interview&controller=question&action='+action+'&questionid='+questionid, function(response) {
			if (response.state)
			{
				tableApp.load();
			}
			else
			{
				ct.error(response.error);
			}
		});
	}
}