var survey = 
{

	code : function(contentid) 
	{
		ct.ajax('获取调用代码', '?app=survey&controller=survey&action=code&contentid='+contentid, 500, 360);
	},

	question: function (contentid)
	{
		ct.assoc.open('?app=survey&controller=question&action=index&contentid='+contentid, 'newtab');
	},

	report: function (contentid)
	{
		ct.assoc.open('?app=survey&controller=report&action=index&contentid='+contentid, 'newtab');
	},
	

	data_clear: function (contentid)
	{
		ct.confirm("此操作不可恢复，您确认清空答卷数据吗？", function(){
			$.getJSON('?app=survey&controller=survey&action=data_clear&contentid='+contentid, function(response){
				if (response.state)
				{
					ct.ok('操作成功');
				}
				else
				{
					ct.error(response.error);
				}
			});
		});
	},
	
	exportdata:function(contentid,questionid,optionid)
	{
		var url = '?app=survey&controller=export&action=index&contentid='+contentid;
		if(typeof questionid !== 'undefined') url+='&questionid='+questionid;
		if(typeof optionid !== 'undefined')  url+='&optionid='+optionid;
		window.open(url,'','width=0,height=0');
	}
}