var i = 0;
var asort = 0;
var option =
{
	init: function ()
	{
		$('#options tr').find("img").show();
		$('#options tr:first-child').find("img:nth-child(2)").hide();
		$('#options tr:last-child').find("img:nth-child(3)").hide();
	},
	
	add: function (name, image, sort, optionid)
	{
		if(name == '其他') return '';
		i++;	
		if (typeof name === 'undefined') name = '';
		if (typeof image === 'undefined') image = '';
		if (typeof sort === 'undefined')  {asort++} else{asort =sort};
		if (typeof optionid === 'undefined') optionid = '';
		
		html = '<tr id="'+i+'">';
		html += '<td class="t_c"><input type="hidden" name="option['+i+'][optionid]" id="optionid_'+i+'" value="'+optionid+'"/><input type="text" name="option['+i+'][sort]" id="sort_'+i+'" value="'+asort+'" size="2" onchange="option.sort('+i+', this.value)"/></td>';
		html += '<td><input type="text" name="option['+i+'][name]" id="name_'+i+'" value="'+name+'" maxlength="100" style="width:150px" /></td>';
		html += '<td class="t_c"><input type="text" name="option['+i+'][image]" id="image_'+i+'" value="'+image+'" style="width:60px"/> <div id="uploadimage_'+i+'"></div></td>';
		html += '<td class="t_c">';
		html += '<img src="images/del.gif" height="16" width="16" alt="删除" title="删除" onclick="option.remove('+i+')" class="hand"/> ';
		html += '<img src="images/up.gif" alt="上移" title="上移" width="16" height="16" onclick="option.up('+i+')" class="hand"/> ';
		html += '<img src="images/down.gif" alt="下移" title="下移" width="16" height="16" onclick="option.down('+i+')" class="hand"/>';
		html += '</td>';
		html += '</tr>';
		$('#options').append(html);
		
		option.upload(i);
		$('#image_'+i).floatImg({url: UPLOAD_URL});
		option.init();
	},

	up: function (i)
	{
		var obj = $('#'+i);
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
		var obj = $('#'+i);
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
		if($('#options tr').length < 3)
		{
			ct.error('至少得保留两个投票选项');
			return false;
		}
		$('#'+i).remove();
		option.init();
	},
	
	sort: function (i, val)
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
			data[i] = [$('#name_'+id).val(), $('#image_'+id).val(), $('#sort_'+id).val(), $('#optionid_'+id).val()];
		});
		data.sort(function(a, b) {
			return a[2]-b[2];
		});
		
		$('#options').html('');
		$.each(data, function(i, r){
			option.add(r[0], r[1], r[2], r[3]);
		});
	},
	
	upload: function (n)
	{
		$("#uploadimage_"+n).uploader({
			script         : '?app=survey&controller=question&action=upload',
			fileDesc		 : '注意:您只能上传jpeg,png,gif格式的文件!',
			fileExt		 : '*.jpg;*.jpeg;*.gif;*.png;',
			buttonImg	 	 :'images/thumb.gif',
			multi			: false,
			complete:function(response,data)
			 {
			 	if(response != 0)
			 	{
			 		var img = response.split('|');
			 		var aid = img[0];
			 		var img = img[1];
                    $('#image_'+n).val(img);
			 	}
			 	else
			 	{
			 		ct.error('对不起！您上传文件过大而失败!');
			 	}
			 },
			 error:function(data)
			 {
			 	ct.error(data.error.type +' : '+ data.error.info );
			 }
		});	
	}
}