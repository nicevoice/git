var sign = 
{
	view:  function (signid)
	{
		ct.ajax('查看报名者信息', '?app=activity&controller=sign&action=view&signid='+signid,400,500);
	
		
	},
	
	edit:  function (signid)
	{

		ct.form('编辑报名者信息', '?app=activity&controller=sign&action=edit&signid='+signid,400,500, function (response){
			if (response.state)
			{

				tableApp.updateRow(signid, response.data);
				return true;
			}
			else
			{
				ct.error(response.error);
				return false;
			}
		});
	},
	
	pass: function(signid)
	{
		if (signid === undefined)
		{
			signid = tableApp.checkedIds();
		}
		if (signid.length === 0)
		{
			ct.warn('请选择要操作的记录');
			return false;
		}
        sign._common(signid, 'pass');
    },
    
    unpass: function(signid)
	{
		if (signid === undefined)
		{
			signid = tableApp.checkedIds();
		}
		if (signid.length === 0)
		{
			ct.warn('请选择要操作的记录');
			return false;
		}
        sign._common(signid, 'unpass');
    },
    
    exports: function(contentid)
    {
    	var dialog = ct.confirm('<table width="200" style="text-align:left"\
    	<tr><td rowspan="2">导出</td>\
	    <td><input id="cccc" type="radio" name="exportype" value="checked" checked="checked"/>已审核报名者</td></tr>\
  		<tr><td><input type="radio" name="exportype" value="all"/>全部报名者</td></tr>\
		</table>',function(c){
    	  	window.open("?app=activity&controller=sign&action=export&type="+c.find('input:checked').val()+"&contentid="+contentid,'','width=0,height=0');
    	});
    	dialog.find('button').addClass('button_style_1');
    },
    
	del :function(signid)
	{
		if (signid === undefined)
		{
			signid = tableApp.checkedIds();
			var msg = '确定删除选中的<b style="color:red">'+signid.length+'</b>条记录吗？';
		}
		else
		{
			var msg = '确定删除编号为<b style="color:red">'+signid+'</b>的记录吗？';
		}
		if (signid.length === 0)
		{
			ct.warn('请选择要操作的记录');
			return false;
		}
		ct.confirm(msg, function(){
			sign._common(signid, 'delete');
		});
	},
	_common: function (signid, action)
	{
        if (signid == undefined) signid = tableApp.checkedIds();
		if (signid.length === 0)
		{
			ct.warn('请选择要操作的记录');
			return false;
		}
		$.getJSON('?app=activity&controller=sign&action='+action+'&signid='+signid, function(response){
			if (response.state)
			{
				if (window.tableApp)
				{
					tableApp.load();
				}
				else
				{
					window.location.reload();
				}
				ct.ok('操作成功');
			}
			else
			{
				ct.error(response.error);
			}
		});
	}
}