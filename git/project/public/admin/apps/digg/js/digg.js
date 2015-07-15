var digg = 
{	
	view: function (modelid, contentid) 
	{
		ct.assoc.open('?app=digg&controller=digg&action=view&modelid='+modelid+'&contentid='+contentid, 'newtab');
	},
	tab :function (type) {
		if(type == 'supports') {
			$('#supports').removeClass('s_3').addClass('s_3');
			$('#againsts').removeClass('s_3');
		} else {
			$('#againsts').removeClass('s_3').addClass('s_3');
			$('#supports').removeClass('s_3');
		}
	}
}