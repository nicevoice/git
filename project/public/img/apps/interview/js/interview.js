var question = 
{
	add: function (contentid)
	{
		var nickname = $("#nickname").val();
		var content = $("#content").val();
		if (nickname == '')
		{
			alert('昵称不能为空');
			return false;
		}
		if (content == '')
		{
			alert('内容不能为空');
			return false;
		}
		$.getJSON(APP_URL+"?app=interview&controller=question&action=add&contentid="+contentid+"&nickname="+encodeURIComponent(nickname)+"&content="+encodeURIComponent(content)+"&jsoncallback=?", function (response){
			if (response.state)
			{
				if (response.ischeck) {
					ct.tips('发送成功,等待审核', null, null, 1);
				}
		        question.load();
		        $('#content').val('');
		        $('#content').focus();
			}
			else
			{
				ct.error(response.error);
			}
		});
	},
	
	load: function ()
	{
		$.getJSON(APP_URL+'?app=interview&controller=question&action=load&contentid='+contentid+'&jsoncallback=?', function(data) {
			if(data)
			{
				var html = '';
				$.each(data, function(key, r) {
					html += '<li><p><strong>'+r.nickname+'：</strong>'+r.content+'<em> '+r.created+'</em></p></li>';
				});
				$('#question').html(html);
				document.getElementById('question').scrollTop = document.getElementById('question').scrollHeight;
			}
			else
			{
				$('#question').html('<li><span class="c_red b">暂无网友提问</span></li>');
			}
		});
	},
	
	scroll: function (ms)
	{
		if (ms === undefined) ms = 10000;
		window.question_timer = setInterval(function () {question.load();}, ms);
	},
	
	stop: function ()
	{
		clearInterval(window.question_timer);
	}
}


var chat =
{	
	load: function ()
	{
		$.getJSON(APP_URL+'?app=interview&controller=chat&action=load&contentid='+contentid+'&jsoncallback=?', function(data) {
			if(data)
			{
				var html = '';
				$.each(data, function(key, r) {
					if (r.guestid == null)
					{
						html += '<dt><p><strong>主持人：</strong>';
					}
					else
					{
						html += '<dd><p><strong><font color="'+$('#guest_'+r.guestid).attr('color')+'">'+$('#guest_'+r.guestid).attr('name')+'</font>：</strong>';
					}
					html += r.content+'<cite> '+r.created+'</cite>'+(r.guestid?'</p></dd>':'</p></dt>');
				});
				$('#chat_scroll').html(html);
				document.getElementById('chat_scroll').scrollTop = document.getElementById('chat_scroll').scrollHeight;
			}
			else
			{
				$('#chat').html('<li><span class="c_red b">暂无文字实录</span></li>');
			}
		});
	},
	
	scroll: function (ms)
	{
		if (ms === undefined) ms = 10000;
		window.chat_timer = setInterval(function () {chat.load();}, ms);
	},
	
	stop: function ()
	{
		clearInterval(window.chat_timer);
	}
}


var member =
{
	init: function ()
	{
		if ($.cookie('nickname'))
		{
			var nickname = $.cookie('nickname');
			$('#inputWordBar>li').hide();
			$('#logined').show();
			$('#nickname').val(nickname);
			$('#nickname_show').html(nickname);
		}
		else if ($.cookie(COOKIE_PRE+'auth'))
		{
			var nickname = $.cookie(COOKIE_PRE+'username');
			$('#inputWordBar>li').hide();
			$('#logined').show();
			$('#nickname').val(nickname);
			$('#nickname_show').html(nickname);
		}
		else
		{
			$('#inputWordBar>li').hide();
			$('#login_form').show();
		}
	},
	
	login: function ()
	{
        var self = this;
		var username = $("#question_username").val();
		var password = $("#question_password").val();
		if (username == '')
		{
			alert('用户名不能为空');
			return false;
		}
		if (password == '')
		{
			alert('密码不能为空');
			return false;
		}
		$.getJSON(APP_URL+'?app=member&controller=index&action=ajaxlogin&username='+username+'&password='+password+'&jsoncallback=?', function(data) {
			if(data.state)
			{
				$('#inputWordBar>li').hide();
				$('#logined').show();
				$('#nickname').val($("#question_username").val());
				$('#nickname_show').html($("#question_username").val());
                self.rsync(data.rsync);
			}
			else
			{
				$('#inputWordBar>li').hide();
				$("#login_error").show();
				$('#errorbox').html(data.error);
			}
		});
	},
	
	logout: function ()
	{
        var self = this;
		$.getJSON(APP_URL+"?app=member&controller=index&action=ajaxlogout&jsoncallback=?", function(data) {
			$.cookie('nickname', null);
			$('#inputWordBar>li').hide();
			$("#login_form").show();
            self.rsync(data.rsync);
		});
	},
	
	nickname: function(nickname)
	{
		if (nickname === undefined)
		{
			$('#inputWordBar>li').hide();
			$("#login_nickname").show();
		}
		else
		{
			if (nickname == '')
			{
				alert('昵称不能为空');
				return false;
			}
			$.cookie('nickname', nickname);
			$('#inputWordBar>li').hide();
			$('#logined').show();
			$('#nickname').val(nickname);
			$('#nickname_show').html(nickname);
		}
	},

    rsync: function(scripts) {
        if (! scripts) return;
        scripts = scripts.match(/src=\s*['"]?([^'"\s]+)/gim);
        var script;
        while (script = scripts.shift()) {
            (function(src) {
                src = src.replace(/^src=\s*['"]?/, '');
                if (/^https?:\/\//i.test(src) && src.indexOf(APP_URL) !== 0) $.getScript(src);
            })(script);
        }
    }
};