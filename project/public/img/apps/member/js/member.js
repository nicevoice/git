var member =
{
	init: function ()
	{
		$('#login_box>div').hide();
		if ($.cookie(COOKIE_PRE+'auth'))
		{
			$('#logined').show();
			var username = $.cookie(COOKIE_PRE+'username');
			if(!username) username = $.cookie(COOKIE_PRE+'rememberusername');
			$('#username_show, span.username_show').html(username);
			return true;
		}
		else
		{
			$('#login_form').show();
		}
	},
	
	login: function ()
	{
		var username = $("#username").val();
		var password = $("#password").val();
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
        $('#member_login').submit();
	},
	
	logout: function ()
	{
        var self = this;
		$.getJSON(APP_URL+"?app=member&controller=index&action=ajaxlogout&jsoncallback=?", function(data) {
			if(data.state)
			{
				$('#login_box>div').hide();
				$("#login_form").show();
                self.rsync(data.rsync);
			}
			else
			{
				alert(data.error);
			}
		});
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