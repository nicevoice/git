var chat = 
{
	guestid: function (guestid)
	{
		if (typeof(guestid) == 'undefined') guestid = '';
		$('#guestid').val(guestid);
		$('#guests > a').attr('class', '');
		$('#guest_'+guestid).attr('class', 's_5');
		$('#content').focus();
	},

	reset: function ()
	{
        document.getElementById('chat_scroll').scrollTop = document.getElementById('chat_scroll').scrollHeight;
        $('#content').val('');
        $('#content').focus();
	},
	
	add_submit: function (response)
	{
		if (response.state)
		{
	        chat.append(response.data);
	        chat.reset();
		}
		else
		{
			ct.error('发送失败');
		}
	},
	
	append: function (r)
	{
		if (!r) return ;
		
		var opt = '<span class="f_r" style="width:72px;"><img src="images/section.gif" alt="推荐" width="16" height="16" class="hand recommend" style="display:none"/> &nbsp;<img src="images/edit.gif" alt="编辑" width="16" height="16" class="hand edit" style="display:none"/> &nbsp;<img src="images/delete.gif" alt="删除" width="16" height="16" class="hand delete" style="display:none"/></span>';
		if (r.guestid == null)
		{
			$('#chat').append('<li id="'+r.chatid+'">'+opt+'<span class="b c_red">主持人：</span><span id="content_'+r.chatid+'">'+r.content+'</span> <span class="date">'+r.created+'</span></li>');
		}
		else
		{
			var style = $('#guest_'+r.guestid).attr('style');
			var name = $('#guest_'+r.guestid).html();
			$('#chat').append('<li id="'+r.chatid+'">'+opt+'<span class="b c_blue"><font style="'+style+'">'+name+'</font>：</span><span id="content_'+r.chatid+'">'+r.content+'</span> <span class="date">'+r.created+'</span></li>');
		}
		$('#'+r.chatid+' img.recommend').click(function(){
			$.getJSON("?app=interview&controller=chat&action=recommend&chatid="+r.chatid, function(response) {
				if (response.state)
				{
					ct.ok('推荐成功');
					return true;
				}
				else
				{
					ct.tips(response.error, 'error');
					return false;
				}
			});
		});
		$('#'+r.chatid+' img.edit').click(function(){
			ct.form('文字实录编辑', '?app=interview&controller=chat&action=edit&chatid='+r.chatid, 400, 300, function(response){
				if (response.state)
				{
					$('#content_'+r.chatid).html(response.data.content);
					return true;
				}
				else
				{
					ct.tips(response.error, 'error');
					return false;
				}
			});
		});
		$('#'+r.chatid+' img.delete').click(function(){
			$.getJSON("?app=interview&controller=chat&action=delete&chatid="+r.chatid, function(response) {
				if (response.state)
				{
					$('#'+r.chatid).remove();
					return true;
				}
				else
				{
					ct.tips(response.error, 'error');
					return false;
				}
			});
		});
		$('#'+r.chatid).mouseover(function(){
			$('#'+r.chatid+' img').show();
		}).mouseout(function(){
			$('#'+r.chatid+' img').hide();
		});

	},
	
	load: function (data)
	{
		if (!data) return ;
		$('#chat').html('');
		$.each(data, function(key, r) {
			chat.append(r);
		});
	},
	
	scroll: function(contentid)
	{
		$.getJSON("?app=interview&controller=chat&action=chat&contentid="+contentid, function(response) {
			chat.load(response);
		});
		chat.reset();
	}
}