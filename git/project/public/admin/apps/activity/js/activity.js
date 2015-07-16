var activity = 
{
	viewsign: function (contentid) 
	{
		ct.assoc.open('?app=activity&controller=activity&action=viewsigns&contentid='+contentid, 'newtab');
	},
	
	stop: function(contentid)
	{
		var msg = '确定暂停此活动吗？';
		if (contentid.length === 0)
		{
			ct.error('请选择要操作的记录');
			return false;
		}
		ct.confirm(msg, function(){
			content._common(contentid, 'stop');
		});
	},
	
	unstop: function(contentid)
	{
		if (contentid.length === 0)
		{
			ct.warn('请选择要操作的记录');
			return false;
		}
        content._common(contentid, 'unstop');
	}
}