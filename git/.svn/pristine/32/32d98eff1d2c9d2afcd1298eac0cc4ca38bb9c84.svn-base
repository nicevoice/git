var comment = {
	page: function(topicid, page, pagesize)
	{
		$.getJSON('?app=comment&controller=comment&action=page&topicid='+topicid+'&page='+page+'&pagesize='+pagesize, function(response){
			var html = comment.html(response.data);
			$('#commentList').html(html);
			if(page == 1 && response.hotdata){
				html = comment.htmlHot(response.hotdata);
				$("#hotcomment").html(html);
			}
		});
	},

	html: function(data)
	{
		var html = '';
		var leng = data.length;
		if(leng > 0)
		{
			for(var key = 0; key < leng; key++)
			{
				if (!data[key].commentid) break;
				html += '<dl class="mode-comment" id="commentbox_'+data[key].commentid+'">';
				html += '<dt><span class="user-info">'+data[key].location+' '+defaultname+' '+data[key].nickname+' </span><span class="post-time">'+data[key].date+'</span></dt>';
				html += '<dd class="citation-wrap fs-14">'+data[key].content+'</dd>';
				html += '<dd><div class="relay-t f-r"><span class="f-l" onclick="comment.support('+data[key].commentid+', this)">支持<em>[<b id="supportNum_'+data[key].commentid+'">'+data[key].supports+'</b>]</em></span>';
				html +=(data[key].content.indexOf('hide') == -1 || data[key].content.indexOf('hide') == 12) ? '<span class="re-btn f-l" onclick="comment.reply('+data[key].commentid+', this);">回复</span>' : '';
				html += comment.showReport(data[key].commentid);
				html += '<div class="clear"></div><div id="replyBox_'+data[key].commentid+'" class="replyBox"></div><div class="hr-dotted hr-h10"></div></dd></dl>';
			}
		}
		return html;
	},

	htmlHot: function(data)
	{
		var html = '';
		var leng = data.length;
		if(leng > 0)
		{
			for(var key = 0; key < leng; key++)
			{
				if (!data[key].commentid) break;
				html += '<dl class="mode-comment" id="commentbox_hot_'+data[key].commentid+'">';
				html += '<dt><span class="user-info">'+data[key].location+' '+defaultname+' '+data[key].nickname+' </span><span class="post-time">'+data[key].date+'</span></dt>';
				html += '<dd class="citation-wrap fs-14">'+data[key].content+'</dd>';
				html += '<dd><div class="relay-t f-r"><span class="f-l" onclick="comment.support('+data[key].commentid+', this)">支持<em>[<b id="supportNum_hot_'+data[key].commentid+'">'+data[key].supports+'</b>]</em></span>';
				html += (data[key].content.indexOf('hide') == -1) ? '<span class="re-btn f-l" onclick="comment.reply(\'hot_'+data[key].commentid+'\', this);">回复</span>' : '';
				html += comment.showReport(data[key].commentid);
				html += '<div class="clear"></div><div id="replyBox_hot_'+data[key].commentid+'" class="replyBox"></div><div class="hr-dotted hr-h10"></div></dd></dl>';
			}
		}
		return html;
	},
	
	post: function(form)
	{
		$(form).ajaxSubmit({
			dataType: 'json',
			beforeSubmit: function(data, jqForm) {
				if(islogin ==1 && userid <1) {
					ct.error('请登录后再发表评论！');
					return false;
				} else if(data['content'] == '') {
					ct.error('评论内容不能为空');
					return false;
				}
			},
		    success: function(json) {
				if(json.state) {
					if(parseInt(json.data.commentid) != 0){
						comment.page(topicid, 1, 10);
					}
					$(form).find('textarea').val('');
					var msg = ischeck ? '评论发表成功，请等待审核！' : '评论发表成功!';
					ct.ok(msg);
				} else {
					ct.error(json.error);
				}
		    }
		});
		return false;
	},
	
	reply: function(commentid, obj)
	{
		if(islogin ==1 && userid <1) {
			ct.error('请登录后再回复！');
			$('.quickLogin').click();
			return false;
		}
		var _this = $(obj);
		var _wrapper = $('#replyBox_'+commentid);
		if(_this.hasClass('re-click')) {
			_wrapper.slideUp(500,function(){ _wrapper.empty();_this.removeClass('re-click');});
			return;
		} else {
			_wrapper.empty();
		}
		$('.re-click').removeClass('re-click').parents('dd').find('.replyBox').hide();
		var nickname = (username =='') ? defaultname : username;
		var string = '';
		if(userid>0) {
			string ='欢迎你，<strong class="cor-06c">'+nickname+'</strong> <lable><input type="checkbox"  name="anonymous" value="1" title="选中后，你将以匿名方式发布留言，不会显示你的用户名" /> 匿名发布</lable>';
		} else {
			string = '游客发布';
		}
		_this.addClass('re-click');
		var html = "<div class='post-comment-area italk-area re-italk-area' id='replyBox'><form action='?app=comment&controller=comment&action=reply' method='POST' onsubmit='return comment.post(this);'><input type='hidden' name='topicid' value='"+topicid+"'/><input type='hidden' name='followid' value='"+parseInt(commentid.toString().replace('hot_', ''))+"'/><textarea name='content' id='replyContent' rows='10' cols='58' class='textarea textarea-w630'></textarea><div class='user-writeinfo'><span class='f-r'><input type='submit' value='发表评论' name='' class='post-btn p-pl'></span><span class='f-l'>"+string+"</span></div></form></div>";
		_wrapper.hide();
		_wrapper.html(html).slideDown(500);
		seccodeInit(_wrapper.find('textarea')[0]);
	},

	support: function(commentid, obj)
	{
		$.getJSON('?app=comment&controller=comment&action=support&commentid='+commentid, function(response){
			var msg = response.state ? response.supports : response.error;
			$(obj).before('<span class="f-l">已支持<em>[<b id="supportNum_11">'+msg+'</b>]</em></span>').remove();
		});
	},

	report: function(commentid, obj)
	{
		$.getJSON('?app=comment&controller=comment&action=report&commentid='+commentid, function(response){
			var reportString,report,msg = response.state ? '<a>已举报</a>' : response.error;
			$(obj).before(msg).remove();
			reportString = $.cookie('comment_report');
			report = reportString ? reportString.split(',') : [];
			report.push(commentid);
			$.cookie('comment_report', report.join(','), {'expires':1, 'domain':COOKIE_DOMAIN});
		});
	},

	display: function(obj)
	{
		$(obj).prev().slideDown("slow").end().remove();
	},

	showReport: function(commentid) {
		var reportString,report,html;
		reportString = $.cookie('comment_report');
		report = reportString ? reportString.split(',') : [];
		if (report.indexOf(commentid) == -1) {
			html = '<a href="javascript:void(0)" onclick="comment.report('+commentid+', this)" class="f-l">举报</a></div>';
		} else {
			html = '<a href="javascript:void(0)">已举报</a></div>';
		}
		return html;
	}
}