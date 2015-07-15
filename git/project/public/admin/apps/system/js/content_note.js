var content_note = 
{
	add_submit: function (response)
	{
		if (response.state)
		{
			$('#note').val('');
			tableApp.load();
		}
		else
		{
			ct.error(response.error);
		}
	},
	
	search: function (catid)
	{
		ct.ajax('批注搜索', '?app=system&controller=content_note&action=search&catid='+catid, 360, 280, function(){
		    $('input.input_calendar').DatePicker({'format':'yyyy-MM-dd HH:mm'});
		}, function(){
			tableApp.load($('#note_search'));
			return true;
		});
	},
	
	del: function (days)
	{
		$.getJSON('?app=system&controller=content_note&action=delete&days='+days, function(response){
			if (response.state)
			{
				tableApp.load();
				ct.ok('操作成功');
			}
			else
			{
				ct.error(response.error);
			}
		});
	}
}