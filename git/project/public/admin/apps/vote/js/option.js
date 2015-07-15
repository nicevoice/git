var trNum = 0;
var hitObj	= undefined;
$('#options').sortable({
	'handle':'td:first-child',
	'axis':'y',
	'start':function(e,u) {
		hitObj = u.item.children().eq(0);
		hitObj.width(31);
		$(e.target).find('tr').find('td').css('border-top','1px solid #D0E6EC').css('padding-top', '4px');
	},
	'stop':function(e,u) {
		var rid	= u.item.children().eq(0).find('input.sort').val();
		$.each($(this).find("tr"), function(i, tr) {
			if ($(tr).children().eq(0).find('input.sort').val() == rid) {
				var c	= i + 1 - rid;
				option.move(hitObj.parent('tr'), c);
				return false;
			}
		});
		hitObj.width(30);
		hitObj.parent('tr').find('td').css('padding-bottom', '5px');
		$(e.target).find('tr').find('td').css('border-top','none').css('padding-top', '5px');
		hitObj = undefined;
	}
});
$( "#options" ).disableSelection();

var option =
{
	add: function (name, votes, sort, optionid)
	{
		var isTinyMCE = (typeof (tinyMCE) != 'undefined');
		trNum++;
		if (typeof name === 'undefined') name = '';
		if (typeof votes === 'undefined') votes = '';
		if (typeof sort === 'undefined') sort = trNum;
		if (typeof optionid === 'undefined') optionid = '';
		
		html  = '<tr id="'+trNum+'">';
		html += '<td class="t_c" width="30" style="cursor:move">';
		html += '<span>'+sort+'</span>';
		html += '<input type="hidden" name="option['+trNum+'][optionid]" id="optionid_'+trNum+'" value="'+optionid+'"/>';
		html += '<input type="hidden" name="option['+trNum+'][sort]" class="sort" id="sort_'+trNum+'" value="'+sort+'" size="1" onchange="option.order_sort('+trNum+', this.value)"/>';
		html += '</td>';
		html += '<td width="'+(isTinyMCE?300:360)+'"><input type="text" name="option['+trNum+'][name]" id="name_'+trNum+'" value="'+name+'" size="50" maxlength="100" uncount="1" style="width:98%" /></td>';
		html += '<td width="60" class="t_c"><input type="text" name="option['+trNum+'][votes]" id="votes_'+trNum+'" value="'+votes+'" size="4" /></td>';
		html += '<td width="29" style="padding:5px 0px;text-align:center">';
		html += '<img src="images/del.gif" height="16" width="16" alt="删除" title="删除" onclick="option.remove('+trNum+')" class="hand" />';
		html += '</td></tr>';
		$('#options').append(html);
	},
	
	remove: function (line)
	{
		if($('#options>tr').length < 3)
		{
			ct.error('至少得保留两个投票选项');
			return false;
		}
		$('#'+line).remove();
		
		$.each($('#options>tr'), function(i, tr) {
			tr = $(tr);
			tr.find('span').html(i+1);
			tr.find('.sort').val(i+1);
		});
		trNum--;
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
	},
	move : function(obj, c) {
		var parentObj = obj.parent();
		var index = parentObj.find('tr').index(obj[0]);
		if (c < 0) { // 上移
			c = Math.abs(c);
			for (var i=0; i<c; i++) {
				option.change(parentObj.find('tr').eq(index+i), parentObj.find('tr').eq(index+i+1));
			}
		} else if(c > 0) { // 下移
			for (var i=0; i<c; i++) {
				option.change(parentObj.find('tr').eq(index-i), parentObj.find('tr').eq(index-i-1));
			}
		}
	},
	change : function(o1, o2) {
		var s1 = o1.find('.sort').val();
		var s2 = o2.find('.sort').val();
		o1.find('.sort').val(s2);
		o2.find('.sort').val(s1);
		o1.find('span').html(s2);
		o2.find('span').html(s1);
	}
}