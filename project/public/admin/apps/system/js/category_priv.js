var categorypriv = 
{
	set: function (catid)
	{
		ct.ajax('设置栏目权限', '?app=system&controller=category_priv&action=index&catid='+catid, 350, 300, function (dialog){
			dialog.find('form').ajaxForm('categorypriv.add_submit');
		});
	},
	
	del: function (catid, userid)
	{
		$.getJSON('?app=system&controller=category_priv&action=delete&catid='+catid+'&userid='+userid, function (response) {
			if (response.state)
			{
				$('#row_'+userid).remove();
			}
			else
			{
				ct.error(response.error);
			}
		});
	},
	
	add_submit: function (response)
	{
		if (response.state)
		{
			$('input[name="username"]').val('');
			categorypriv.add(response.catid, response.userid, response.username, response.rolename);
		}
		else
		{
			ct.error(response.error);
		}
	},
	
	add: function (catid, userid, username, rolename)
	{
		 var row = '<tr id="row_'+userid+'">\
					    <td><a href="javascript: url.member('+userid+');">'+username+'</a></td>\
					    <td>'+rolename+'</td>\
					    <td class="t_c"><img src="images/delete.gif" alt="删除" width="16" height="16" class="hand" onclick="categorypriv.del('+catid+', '+userid+')"/></td>\
					</tr>';
		 $('#list_body').append(row);
	}
}