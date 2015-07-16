var option =
{
	i : 0,
	init: function ()
	{
		$('#options tr').find("img").show().css('visibility','visible');
		$('#options tr:first-child').find("img:nth-child(2)").css('visibility','hidden');
		$('#options tr:last-child').find("img:nth-child(1)").css('visibility','hidden');
	},
	
	add: function (name, votes, sort, optionid)
	{
		option.i++;
		var i = option.i;
		if (typeof name === 'undefined') name = '';
		if (typeof votes === 'undefined') votes = '';
		if (typeof sort === 'undefined') sort = i;
		if (typeof optionid === 'undefined') optionid = '';
		
		html = '<tr id="options_'+i+'">';
		html += '<td class="t_c"><input type="hidden" name="option['+i+'][optionid]" id="optionid_'+i+'" value="'+optionid+'"/><input type="text" name="option['+i+'][sort]" id="sort_'+i+'" value="'+sort+'" size="1" onchange="option.order_sort('+i+', this.value)"/></td>';
		html += '<td><input type="text" name="option['+i+'][name]" id="name_'+i+'" value="'+name+'" size="50" maxlength="100" uncount="1" style="width:98%" /></td>';
		html += '<td class="t_c"><input type="text" name="option['+i+'][votes]" id="votes_'+i+'" value="'+votes+'" size="4" /></td>';
		html += '<td style="padding:5px 0px">';
		html += '<img src="images/down.gif" alt="下移" title="下移" width="16" height="16" onclick="option.down('+i+')" class="hand" style="margin-left:8px"/>　';
		html += '<img src="images/up.gif" alt="上移" title="上移" width="16" height="16" onclick="option.up('+i+')" class="hand" />　';
		html += '<img src="images/del.gif" height="16" width="16" alt="删除" title="删除" onclick="option.remove('+i+')" class="hand" />';
		html += '</td></tr>';
		$('#options').append(html);
		option.init();
	},

	up: function (i)
	{
		var obj = $('#options_'+i);
		if (obj.prev().is('tr'))
		{
			var prev_id = obj.prev().attr('id');
			var sort = $('#sort_'+i).val();
			var prev_sort = $('#sort_'+prev_id).val();
			
			$('#sort_'+i).val(prev_sort);
			$('#sort_'+prev_id).val(sort);
			
			obj.insertBefore(obj.prev());
			option.init();
		}
	},
	
	down: function (i)
	{
		var obj = $('#options_'+i);
		if (obj.next().is('tr'))
		{
			var next_id = obj.next().attr('id');
			var sort = $('#sort_'+i).val();
			var next_sort = $('#sort_'+next_id).val();
			
			$('#sort_'+i).val(next_sort);
			$('#sort_'+next_id).val(sort);
			
			obj.insertAfter(obj.next());
			option.init();
		}
	},
	
	remove: function (i)
	{
		if($('#options>tr').length < 3)
		{
			ct.error('至少得保留两个投票选项');
			return false;
		}
		$('#options_'+i).remove();
		option.init();
	},
	
	order_sort: function (i, val)
	{
		if(isNaN(val))
		{
			ct.warn('请输入阿拉伯数字！');
			$('#sort_'+i).val('0');
			return ;
		}
		
		var data = new Array();
		$('#options>tr').each(function(i){
			var id = $(this).attr('id');
			data[i] = [$('#name_'+id).val(), $('#votes_'+id).val(), $('#sort_'+id).val(), $('#optionid_'+id).val()];
		});
		data.sort(function(a, b) {
			return a[2]-b[2];
		});
		
		$('#options').empty();
		$.each(data, function(i, r){
			option.add(r[0], r[1], r[2], r[3]);
		});
	}
}