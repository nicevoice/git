(function($){
var TEMPLATE = '<dt><span class="user-info">{location}网友 {nickname}</span><span class="post-time">{date}</span></dt><dd><p>{content}</p></dd>';
//var REPLY_FORM = '<div class="comment-item-reply"><form class="reply-form" method="post" action="http://app.cmstop.loc/?app=comment&controller=comment&action=reply"><input type="hidden" value="{topicid}" name="topicid" /><input type="hidden" value="{commentid}" name="followid" /><textarea value="" class="comment-reply-input" name="content" /></textarea><p><input type="submit" value="回复" /></p></form></div>';
var REPLY_FORM = '<div class="comment-item-reply"><textarea class="reply-content"></textarea><a href="javascript:void(0)" class="reply-submit">回复</a></div>';
var DEFAULT_NAME = window.defaultname || '思拓网友';
var comment = function(container){
	if (container.data('comment-inited')) return;
	container.data('comment-inited', true);
	var options = container.find('[name="options"]').val();
	try {
		options = (new Function('return ('+options+')'))();
	} catch (e) {
		return;
	}
	this.topicid = options.topicid;
	this.container = container;
	this.pagesize = options.pagesize;
	this.loginUrl = options.loginUrl;
	this.logoutUrl = options.logoutUrl;
	this.checkloginUrl = options.checkloginUrl;
	this.needLogin = options.needLogin;
	this.bulidform();
	this.bulidlist();

};
comment.prototype = {
	bulidform :function() {
		var box = this.container;
		if ($.cookie(COOKIE_PRE+'auth')) {
			// 已登录
			var username = $.cookie(COOKIE_PRE+'username');
			if(username == null) username = DEFAULT_NAME;
			box.find('.nickname').html(username);
			box.find('.quickLogout').show();
			box.find('.quickLogin').hide();
		} else {
			if(this.needLogin == 1) {
				//需要登录才能评论
				box.find('.nickname').html('请登录后再发表评论');
				box.find('.anonymous').hide();
			} else {
				// 不需要登录
				box.find('.anonymous').hide();
				box.find('.nickname').html(DEFAULT_NAME);
			}
		}

		var fastLogin = box.find('.fast-login');
		var t = this;

		box.find('.viewall').attr('href', APP_URL+'?app=comment&controller=comment&action=index&topicid='+this.topicid);

		box.find('.showlogin').click(function(){
			fastLogin.show();
			return false;
		});

		box.find('.login-form').submit(function(){
			t.login();
			return false;
		});
		
		box.find('.logout').click(function(){
			t.logout();
			return false;
		});

		box.find('form.comment-form').submit(function(){
			// 检查评论有效性
			if(box.find('.content').val() == '') {
				alert('请填写评论内容！');
				return false;
			}
			// return false;
		});
	},

	bulidlist : function() {
		var box = this.container,
			commentList = box.find('.comment-list');
			
		function g(item){
			var html = TEMPLATE;
			var replybox = REPLY_FORM;
			for (var key in item) {
				html = html.replace(new RegExp('{'+key+'}',"gm"), item[key]);
				replybox = replybox.replace(new RegExp('{'+key+'}',"gm"), item[key]);
			}
			html = $(html);
			html.find('.reply').toggle(
				function() {
					html.find('.reply').after(replybox);
				},
				function() {
					html.find('.comment-item-reply').remove();
				}
			);
			replybox = $(replybox);
			replybox.find('.reply-submit').click(function(){
				reply_content = replybox.find('.reply-content').val();
				$.getJSON(
					APP_URL+'?app=comment&controller=comment&action=specialReply&jsoncallback=?',
					{'topicid' : item.topicid, 'followid' : item.commentid, 'content' : reply_content},
					function(json) {
						if(json.state == true) {
							location.reload();
						} else {
							alert('评论失败，请重试');
						}
					}
				);
				return false;
			});
			return html;
		};
		
		$.getJSON(
			APP_URL+'?app=comment&controller=comment&action=get&jsoncallback=?',
			{'topicid' : this.topicid, 'pagesize' :this.pagesize},
			function(json){
				if(json.total > 0) {
					for (var i=0,item;item=json.data[i++];) {
						commentList.append(g(item));
					}
				} else {
					box.find('.viewall').hide();
					commentList.append('<dt></dt><dd><p style="text-align:center;">暂无评论<p></dd>');
				}
			}
		);
	},

	closelogin : function() {
		// $(this.container).find('.fast-login').hide();
	},
	
	// 登录
	login : function() {
        var t = this;
		var box = $(".login-form");
		var xbox = this.container;
		var username = box.find("input[name='username']").val();
		var password = box.find("input[name='password']").val();
		if ( username == '') {
			alert('用户名不能为空');
			return false;
		}
		if ( password == '') {
			alert('密码不能为空');
			return false;
		}

		$.getJSON(
			APP_URL+'?app=member&controller=index&action=ajaxlogin&username='+username+'&password='+password+'&jsoncallback=?', function(json){
			if(json.state) {
				comment.username = json.username;
				xbox.find('.nickname').html(comment.username);
				xbox.find('.fast-login').hide();
				xbox.find('.quickLogin').hide();
				xbox.find('.quickLogout').show();
				xbox.find('.anonymous').show();
                t.rsync(json.rsync);
			} else {
				alert(json.error);
			}
		});
	},
	
	//退出
	logout : function() {
		var t = this;
		var xbox = this.container;
		$.getJSON(APP_URL+'?app=member&controller=index&action=ajaxlogout&jsoncallback=?', function(json){
			if(json.state) {
				if(t.islogin() == 'TRUE') {
					//需要登录
					xbox.find('.nickname').html('请登录后再发表评论');
				} else {
					xbox.find('.nickname').html(DEFAULT_NAME);
				}
				xbox.find('.anonymous').hide();
				xbox.find('.quickLogout').hide();
				xbox.find('.quickLogin').show();
				username = '';
                t.rsync(json.rsync);
			} else {
				alert('退出失败');
			}
		});
	},
	
	islogin : function() {
		$.getJSON(APP_URL+'?app=member&controller=index&action=ajaxIsLogin&jsoncallback=?', function(json){
			if(json.state == 'TRUE') {
				return true;
			} else {
				return false;
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
$.fn.comment = function(){
	return this.each(function(){
		var t = $(this);
		t.data('comment-inited') || (new comment(t));
	});
};

$(function(){
	$('.mod-comment').comment();
});
})(jQuery);

var comment = {
	display: function(obj)
	{
		$(obj).prev().slideDown("slow").end().remove();
	}
}