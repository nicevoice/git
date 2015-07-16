var vote = 
{
	code : function(contentid) 
	{
		ct.ajax('获取调用代码', '?app=vote&controller=vote&action=code&contentid='+contentid, 500, 360);
	},
	
	vote_log : function(contentid) 
	{
		ct.iframe({
			title:'?app=vote&controller=log&action=index&contentid='+contentid, 
			width:500,
			height:360
		});
	},
	
	log_data : function (optionid) 
	{
		ct.iframe({
			title:'?app=vote&controller=log_data&action=index&optionid='+optionid, 
			width:500,
			height:360
		});
	}
};