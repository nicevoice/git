var sectionpriv = 
{
	set: function (sectionid)
	{
		ct.ajax('设置区块权限', '?app=page&controller=section_priv&action=index&sectionid='+sectionid, 350, 300, function (dialog){
			dialog.find('form').ajaxForm('sectionpriv.add_submit');
		});
	},
	
	add_submit: function (response)
	{
		if (response.state)
		{
			$('input[name="username"]').val('');
			sectionpriv.add(response.sectionid, response.userid, response.username, response.rolename);
		}
		else
		{
			ct.error(response.error);
		}
	},
	
	add: function (sectionid, userid, username, rolename)
	{
		 var row = '<tr id="row_'+userid+'">\
					    <td><a href="javascript: url.member('+userid+');">'+username+'</a></td>\
					    <td>'+rolename+'</td>\
					    <td class="t_c"><img src="images/delete.gif" alt="删除" width="16" height="16" class="hand" onclick="sectionpriv.del('+sectionid+', '+userid+')"/></td>\
					</tr>';
		 $('#list_body').append(row);
	},
	
	del: function (sectionid, userid)
	{
		$.getJSON('?app=page&controller=section_priv&action=delete&sectionid='+sectionid+'&userid='+userid, function (response) {
			if (response.state)
			{
				$('#row_'+userid).remove();
			}
			else
			{
				ct.error(response.error);
			}
		});
	}
}