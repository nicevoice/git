var design = {
	init: function ()
	{
		$('#design dl').find("img").show();
		$('#design dl:first-child').find("img:nth-child(3)").hide();
		$('#design dl:last-child').find("img:nth-child(4)").hide();
	},
	add : function (pid, type)
	{
		var url = '?app=field&controller=project&action=design_add&pid='+pid+'&type='+type;
		ct.form('添加字段', url, 400, 'auto', function (response){
			if (response.state)
			{
				var fid = response.fid;
				$.get('?app=field&controller=project&action=design_view&fid='+fid, function (response){
					$('#design').append(response);
					design.init();
				});
				return true;
			}
			else
			{
				ct.error(response.error);
			}
		});
	},
	edit: function (fid)
	{
		var url = '?app=field&controller=project&action=design_edit&fid='+fid;
		ct.form('修改字段', url, 400, 'auto', function (response){
			if (response.state)
			{
				$.get('?app=field&controller=project&action=design_view&fid='+fid, function (html){
					var s = $(html);
					var prevdl = $('#'+fid).prev();
					$('#'+fid).replaceWith(s);
					design.init();
				});
				return true;
			}
			else
			{
				ct.error(response.error);
			}
		});
	},
	remove: function (i)
	{
		ct.confirm('您真的要删除此字段?',function(){
			$.getJSON('?app=field&controller=field&action=delete&fid='+i, function (response){
				if (response.state)
				{
					$('#'+i).remove();
					design.init();
				}
				else
				{
					ct.error(response.error);
				}
			});
			return true;
		},function(){return true});
		
	},
	up: function (i)
	{
		var obj = $('#'+i);
		if (obj.prev().is('dl'))
		{
			var next_id		= obj.prev().attr('id');
			var content		= obj.html();
			var next_content= obj.prev().html();

			obj.attr('id', next_id).html(next_content);
			obj.prev().attr('id', i).html(content);

			design.init();

			$.getJSON('?app=field&controller=project&action=sort&sort='+i+'&nextsort='+next_id);
		}
	},
	down: function (i)
	{
		var obj = $('#'+i);
		if (obj.next().is('dl'))
		{
			var next_id		= obj.next().attr('id');
			var content		= obj.html();
			var next_content= obj.next().html();

			obj.attr('id', next_id).html(next_content);
			obj.next().attr('id', i).html(content);

			design.init();

			$.getJSON('?app=field&controller=project&action=sort&sort='+i+'&nextsort='+next_id);
		}
	}
}