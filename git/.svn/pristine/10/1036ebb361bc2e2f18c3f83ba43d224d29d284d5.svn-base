var content_log = 
{
	search: function (catid)
	{
		ct.ajax('操作日志搜索', '?app=system&controller=content_log&action=search&catid='+catid, 360, 300, function(){
		    $('input.input_calendar').DatePicker({'format':'yyyy-MM-dd HH:mm'});
		}, function(){
			tableApp.load($('#log_search'));
			return true;
		});
	},
	
	del: function (days)
	{
		$.getJSON('?app=system&controller=content_log&action=delete&days='+days, function(response){
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