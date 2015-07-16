var pagepriv = 
{
	set: function (pageid)
	{
		ct.ajax('设置页面权限', '?app=page&controller=page_priv&action=index&pageid='+pageid, 350, 300, function (dialog){
			dialog.find('form').ajaxForm('pagepriv.add_submit');
		});
	},
	
	add_submit: function (response)
	{
		if (response.state)
		{
			$('input[name="username"]').val('');
			pagepriv.add(response.pageid, response.userid, response.username, response.rolename);
		}
		else
		{
			ct.error('设置失败');
		}
	},
	
	add: function (pageid, userid, username, rolename)
	{
		 var row = '<tr id="row_'+userid+'">\
					    <td><a href="javascript: url.member('+userid+');">'+username+'</a></td>\
					    <td>'+rolename+'</td>\
					    <td class="t_c"><img src="images/delete.gif" alt="删除" width="16" height="16" class="hand" onclick="pagepriv.del('+pageid+', '+userid+')"/></td>\
					</tr>';
		 $('#list_body').append(row);
	},
	
	del: function (pageid, userid)
	{
		$.getJSON('?app=page&controller=page_priv&action=delete&pageid='+pageid+'&userid='+userid, function (response) {
			if (response.state)
			{
				$('#row_'+userid).remove();
			}
			else
			{
				ct.error('设置失败');
			}
		});
	}
}