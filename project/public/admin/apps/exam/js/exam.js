var exam =
{

	code : function(contentid) 
	{
		ct.ajax('获取调用代码', '?app=exam&controller=exam&action=code&contentid='+contentid, 500, 360);
	},

	question: function (contentid)
	{
		ct.assoc.open('?app=exam&controller=question&action=index&contentid='+contentid, 'newtab');
	},

	report: function (contentid)
	{
		ct.assoc.open('?app=exam&controller=report&action=index&contentid='+contentid, 'newtab');
	},
	

	data_clear: function (contentid)
	{
		ct.confirm("此操作不可恢复，您确认清空答卷数据吗？", function(){
			$.getJSON('?app=exam&controller=exam&action=data_clear&contentid='+contentid, function(response){
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
		var url = '?app=exam&controller=export&action=index&contentid='+contentid;
		if(typeof questionid !== 'undefined') url+='&questionid='+questionid;
		if(typeof optionid !== 'undefined')  url+='&optionid='+optionid;
		window.open(url,'','width=0,height=0');
	}
}