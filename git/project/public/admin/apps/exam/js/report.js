var report = {
	question: function(contentid, questionid)
	{
		var url = "?app=exam&controller=report&action=question&contentid="+contentid+"&questionid="+questionid;
		window.location.href = url;
	},
	
	option: function(optionid)
	{
		ct.iframe({
			title:'?app=exam&controller=report&action=option&optionid='+optionid,
			width:550,
			height:400
		});
	},
	
	view: function(answerid)
	{
		ct.iframe({
			title:'?app=exam&controller=report&action=view&answerid='+answerid,
			width:550,
			height:400
		});
	},
	
	search: function(contentid)
	{
		ct.ajax('高级检索','?app=exam&controller=report&action=search&contentid='+contentid, 550, 500, function(d){
			d.find('input:button').click(function(){
				d.dialog('close');
			});
			d.find('select').each(function(){
				$(this).prepend('<option value="-1">请选择..</option>').val('-1');
			})
			
		});
	}
}