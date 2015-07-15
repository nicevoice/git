(function(){

function getPara(para, url){
	url || (url = location.search);
	return ((new RegExp('[&?]'+para+'=(\\w+)').exec(url)) || {1 : null})[1];
}


var model = getPara('app');
var action = getPara('action');
var contentid = getPara('contentid') ? getPara('contentid') : 0;
var form = '#' + model + '_' + action;
var submit_ok, submit_result;
/**
 * 一。内容列表代码
 */
var content = 
{
	add: function (catid, modelid, model)
	{
		ct.ajax('发布内容', '?app=system&controller=content&action=add&catid='+catid+'&modelid='+modelid, 350, 300, function (dialog){
			dialog.find("#category_tree").treeview();
			dialog.find("#category_tree span").unbind('click mouseover');
		}, function () {
			var model = $('#model').val();
			var catid = $('input:checked[name=catid]').val();
			if (!model)
			{
				alert('请选择模型');
				return false;
			}
			if (!catid)
			{
				alert('请选择栏目');
				return false;
			}
			ct.assoc.open('?app='+model+'&controller='+model+'&action=add&catid='+catid, 'newtab');
			return true;
		}, function () {
            return true;
		});
	},
	
	edit: function (contentid)
	{
		var app = $('#row_'+contentid).attr('model');
		if(!app) {
			app = getPara('app');
		}
		if(content.islock(contentid))
		{
			ct.error('当前文档已被锁定，无法修改！');
			return false;
		}
		ct.assoc.open('?app='+app+'&controller='+app+'&action=edit&contentid='+contentid, 'newtab');
	},
	
	view: function (contentid) 
	{
		var app = $('#row_'+contentid).attr('model');
		if(!app) {
			app = getPara('app');
		}
		ct.assoc.open('?app='+app+'&controller='+app+'&action=view&contentid='+contentid, 'newtab');
	},

	category: function (catid) 
	{
		ct.assoc.open('?app=system&controller=content&action=index&catid='+catid, 'newtab');
	},

	search: function (catid, modelid, status)
	{
		ct.ajax('内容搜索', '?app=system&controller=content&action=search&catid='+catid+'&modelid='+modelid+'&status='+status, 360, 350, function(dialog){
            dialog.find('[name=keywords]').val($('#keywords').val());
		    $('input.input_calendar').DatePicker({'format':'yyyy-MM-dd HH:mm'});
		}, function(){
			tableApp.load($('#content_search'));
			return true;
		});
	},
	
	createhtml: function (contentid)
	{
        content._common(contentid, 'show', false);
	},
	
	del: function (contentid)
	{
		if (contentid === undefined)
		{
			contentid = tableApp.checkedIds();
			var msg = '确定删除选中的<b style="color:red">'+contentid.length+'</b>条记录吗？';
		}
		else
		{
			var msg = '确定删除编号为<b style="color:red">'+contentid+'</b>的记录吗？';
		}
		if (contentid.length === 0)
		{
			ct.warn('请选择要操作的记录');
			return false;
		}
		ct.confirm(msg, function(){
			content._common(contentid, 'delete');
		});
	},

	clear: function (catid, title, modelid)
	{
		modelid || (modelid='');
		ct.confirm('<font colr="red">确定要清空'+title+'栏目回收站中的所有内容吗？<br />此操作不可恢复！</font>',function(){
			$.getJSON('?app=system&controller=content&action=clear&catid='+catid+'&modelid='+modelid, function(response){
				if (response.state){
					tableApp.load();
				}else{
					ct.error(response.error);
				}
			});
			return true;
		},function(){
			return true;
		});
	},

	remove: function (contentid) 
	{
		if (contentid === undefined)
		{
			contentid = tableApp.checkedIds();
			var msg = '确定删除选中的<b style="color:red">'+contentid.length+'</b>条记录吗？';
		}
		else
		{
			var msg = '确定删除编号为<b style="color:red">'+contentid+'</b>的记录吗？';
		}
		if (contentid.length === 0)
		{
			ct.warn('请选择要操作的记录');
			return false;
		}
		ct.confirm(msg, function(){
			content._common(undefined, 'remove');
		});
	},
	
	restore: function (contentid)
	{
		content._common(contentid, 'restore');
	},

	restores: function (catid)
	{
		$.getJSON('?app=system&controller=content&action=restores&catid='+catid, function(response){
			if (response.state)
			{
				$('#list_body').empty();
				ct.ok('操作成功');
			}
			else
			{
				ct.error(response.error);
			}
		});
	},

	approve: function (contentid)
	{
		content._common(contentid, 'approve');
	},
	
	pass: function (contentid)
	{
		content._common(contentid, 'pass');
	},
	
	reject: function (contentid)
	{
		content._common(contentid, 'reject');
	},

	publish: function (contentid)
	{
		content._common(contentid, 'publish');
	},
	
	unpublish: function (contentid)
	{
		content._common(contentid, 'unpublish');
	},
	
	islock: function (contentid)
	{
		// error 
		var response = $.ajax({url: '?app=system&controller=content&action=islock&contentid='+contentid, async: false, dataType: "json"}).responseText;
		var a = new Function("return "+response);
		response = a();
		if (!response.state)
		{
			$("#row_"+contentid+" img[src='images/lock.gif']").remove();
		}
		return response.state;
	},
	
	log: function (contentid) 
	{
		ct.iframe({
			title:'?app=system&controller=content_log&action=index&contentid='+contentid,
			width:570,
			height:400
		});
	},
	
	keyword: function(contentid){
		if (contentid == undefined){
			contentid = tableApp.checkedIds();
			if(!contentid.length){
				ct.warn('请选择要操作的记录');
				return false;
			}
			contentid = contentid.join(',');
		}
		ct.form('添加关键词链接','?app=system&controller=keylink&action=content_index&contentid='+contentid, 400, 'auto', function(json){
			if(json.state){
				ct.tips('操作成功！', 'ok');
			}else{
				ct.tips('部分'+json.error, 'error');	
			}
			return true;
		},function(){ return true;});
	},
	
	note: function (contentid)
	{
		ct.iframe({
			title:'?app=system&controller=content_note&action=index&contentid='+contentid,
			width:570,
			height:400
		}).bind('dialogclose',function(){
			if(action=='index') tableApp.reload();
		});
	},
	
	score : function(contentid)
	{
		ct.iframe({title:'?app=system&controller=score&action=index&contentid='+contentid,width:450,height:'auto'}).bind('dialogclose',function(){
			tableApp.reload();
		});
	},
	
	version: function (contentid)
	{
		ct.iframe({
			title:'?app=system&controller=content_version&action=index&contentid='+contentid,
			width:570,
			height:350
		});
	},
	
	tags: function (formid)
	{
	    $('#title').bind('change', function(){
			if ($('#title').val())
	    	{
	    		$.post('?app=system&controller=tag&action=get_tags', $('#'+formid).serialize(), function(response){
	    			if (response.state)
	    			{
	    				$('#tags').val(response.data).keyup();
	    			}
	    		}, 'json');
	    	}
		});
	},

	move: function (contentid) 
	{
		if (contentid == undefined){
			contentid = tableApp.checkedIds();
			if(!contentid.length){
				ct.warn('请选择要操作的记录');
				return false;
			}
		}
		var model = $('#row_'+contentid).attr('model');
		ct.form('移动内容', '?app='+model+'&controller='+model+'&action=move&contentid='+contentid, 350, 300, function (response){
			if (response.state){
				ct.tips('操作成功','success');
				if (action == 'index'){
					tableApp.reload();
					return true;
				}
				else{
					window.location.reload();
				}
			}
			else{
				ct.error(response.error);
				return false;
			}
		}, function (dialog){
			dialog.find("#category_tree").treeview();
			dialog.find("#category_tree span").unbind('click mouseover');
		});
	},
	forward : function(contentid)
	{
		if (contentid == undefined){
			contentid = tableApp.checkedIds();
			if(!contentid.length){
				ct.warn('请选择要操作的记录');
				return false;
			}
		}
		$.get('?app=system&controller=tweets&action=forward_check&contentid='+contentid,'',
			function(json){
				if (json.state) {
					ct.form('转发内容', '?app=system&controller=tweets&action=forward&contentid='+contentid, 520, 480, function (response){
						var string = '警告<br/>';
						if (response.state) {
							if (response.data == null) {
								ct.tips('转发成功','success');
							} else {
								for (var i = 0; i < response.data.length; i++) {
									string += (i+1) + "、" 
									string += response.data[i].text + " 转发至 " + response.data[i].user + " 失败<br/>"
								}
								ct.warn(string);
							}
						} else {
							ct.tips('转发失败','error');
						}
						return false;
					});
				} else {
					ct.error(json.error);
					return false;
				}
			}, 'json');
	},
	copy: function (contentid) 
	{
		if (contentid == undefined)
		{
			ct.warn('请选择要操作的记录');
			return false;
		}
		var app = $('#row_'+contentid).attr('model');
		if(!app) {
			app = getPara('app');
		}
		ct.form('复制内容', '?app='+app+'&controller='+app+'&action=copy&contentid='+contentid, 350, 300, function (response){
			if (response.state)
			{
				ct.ok('操作成功');
				return true;
			}
			else
			{
				ct.error(response.error);
				return false;
			}
		}, function (dialog){
			dialog.find("#category_tree").treeview();
			dialog.find("#category_tree span").unbind('click mouseover');
		});
	},
	
	reference: function (contentid)
	{
		if (contentid == undefined){
			contentid = tableApp.checkedIds();
			if(!contentid.length){
				ct.warn('请选择要操作的记录');
				return false;
			}
		}
		ct.form('引用内容', '?app=system&controller=content&action=reference&contentid='+contentid, 350, 300, function (response){
			if (response.state)
			{
				ct.ok('操作成功');
				return true;
			}
			else
			{
				ct.error(response.error);
				return false;
			}
		}, function (dialog){
			dialog.find("#category_tree").treeview();
			dialog.find("#category_tree span").unbind('click mouseover');
		});
	},

	section : function(contentid) {
		alert(contentid);
		content._common(contentid, 'section');
	},
	
	_common: function (contentid, action, reload, moreParams)
	{
		if (reload == undefined) reload = true;
		if (moreParams == undefined) moreParams ='';
        if (contentid == undefined)
        {
        	contentid = tableApp.checkedIds();
			if (contentid.length === 0)
			{
				ct.warn('请选择要操作的记录');
				return false;
			}
			content._common_execute(contentid, action, reload, 0, moreParams);
			return true;
        }
		var app = controller = $('#row_'+contentid).attr('model');
		if(!app) {
			app = controller = getPara('app');
		}
		if (action == 'show') controller = 'html';
		if (action == 'saveSection') { moreParams+='&model='+app;app='system';controller='content';}
		$.getJSON('?app='+app+'&controller='+controller+'&action='+action+'&contentid='+contentid+moreParams, function(response){
			if (response.state){
				if(action == 'delete' || action == 'remove' || action == 'unpublish'){
					if(getPara('action') == 'index'){
						tableApp.deleteRow(contentid);
					}else{
						ct.assoc.close();
					}
				}else if(action == 'restore'){
					if(getPara('action') == 'index') tableApp.load();
				}
				ct.ok('操作成功');
			}
			else{
				ct.error(response.error);
			}
		});
	},
	
	_common_execute: function (contentid, action, reload, key, moreParams)
	{
		if (moreParams == undefined) moreParams ='';
		var app = controller = $('#row_'+contentid[key]).attr('model');
		if ((app == 'link' || app == 'special') && action == 'show') return ct.warn('链接和专题不能在此生成！');
		if (action == 'show') controller = 'html';
		if(!app) {
			app = controller = getPara('app');
		}
		if (action == 'saveSection') { 
			moreParams = moreParams.replace(/&model=([\d\w]+)/i,'&model='+app);
			app='system';controller='content';
		}
		$.getJSON('?app='+app+'&controller='+controller+'&action='+action+'&contentid='+contentid[key]+moreParams, function(response){
			if (response.state){
				if(action == 'remove' || action == 'restore'){
					tableApp.deleteRow(contentid[key]);
				}else{
					$('#row_'+contentid[key]).removeClass('row_chked');
					$('#chk_row_'+contentid[key]).attr('checked', false);
				}
				key++;
				if (contentid.length > key){
					content._common_execute(contentid, action, reload, key, moreParams);
				}else{
					$('#check_all').attr('checked', false);
					if(action =='remove' || action == 'restore') tableApp.load();
					if(getPara('action') == 'index'){
						if(action == 'delete' || action == 'remove') return tableApp.deleteRow();
						if (reload) tableApp.load();
						ct.tips('操作成功', 'success');
					}
					else {
						ct.ok('操作成功');
					}
				}
			}
			else{
				ct.error(response.error);
			}
		});
	},

	section: function(url)
	{
		$.getJSON('?app=page&controller=section&action=get_section_info&url='+url, function(response){
			url = url.substring(23, url.length-6).replace(/\//g,'');
			var html = '已被推送至：<br />';
			$.each(response.section, function(key, data) {
				html += '<a href="javascript:;" onclick="ct.assoc.open(\''+data.pageurl+'\', \'newtab\')">'+data.pagename+'</a> > <a href="javascript:;" onclick="ct.assoc.open(\''+data.pageurl+'&sectionid='+data.sectionid+'\', \'newtab\')" >'+data.sectionname+'</a><br />';
			});
			ct.tips(html, 'success', $('.section_'+url), 0);
		});
	}
}

/**
 * 二。内容表单代码
 */

content.lock = function (contentid, model)
{
	$.get('?app='+model+'&controller='+model+'&action=lock&contentid='+contentid);
};

content.unlock = function (contentid, model)
{
	$.get('?app='+model+'&controller='+model+'&action=unlock&contentid='+contentid);
};

content.success = function (response)
{
	content.unload_alert = 0;
	if(model == 'survey' && action=='add')
	{
		window.location = '?app=survey&controller=question&action=index&contentid='+response.contentid;
		return;
	}
	$("#submit").attr('disabled', false);
	var type = action == 'add' ? '添加' : '修改';
	
	var tip = '恭喜，内容'+type+'成功。';
	var url = response.url ? response.url : $('#url').val();
	if(url) {
		tip += '查看地址：<br/><a target="_BLANK" href="' + url + '">' + url + '</a>';
	}

	var btnDiv = ct.confirm(tip, function() {
		if(type == '添加'){
			location.reload();
		}
	}, function() {
		if(top != self)
		{
			ct.assoc.close()
		}
		else {
			self.close();
		}
	});
	btnDiv.find('button:first').remove();
	var lBtn = btnDiv.find('button:last').text("关闭");
	var sBtn = $('<button type="button">继续修改</button>').click(function (){
		if(type == '修改') {
			btnDiv.hide();
		}
		if(!contentid) {
			var contentid = response.contentid;
		}
		if(contentid){
			var model = getPara('app');
            model = model == 'contribution' ? 'article' : model;
			location.href = '?app='+model+'&controller='+model+'&action=edit&contentid='+response.contentid;
		}
	});
	lBtn.before(sBtn);
	sBtn.addClass('button_style_1');
	if(action == 'add' && getPara('app') != 'contribution')
	{
		btnDiv.find('button:first').text('修改');
		var button = $('<button type="button">继续添加</button>').click(function (){
			if(!contentid) {
				var contentid = response.contentid;
			}
			if(contentid)
			{
				var catid = $('#catid').val();
				$('form')[0].reset();
				$('#related_data').empty();
				$('#catid').val(catid);
				$.slider.reset();
				model == 'picture' && $('#pictures').html('');
			}
			btnDiv.hide();
		});
		sBtn.before(button);
		button.addClass('button_style_1');
		// 更新列表
		var ifs = $('#frame_container iframe', parent.document).filter(function(){
			if (!/\?app=system&controller=content&action=index/i.test(this.src))
			{
				return false;
			}
			var catid = getPara('catid', this.src);
			if (!catid) {
				return true;
			}
			return catid == $('#catid').val();
		}).each(function(){
			this.contentWindow.tableApp.load();
		});

	}
	
};

content.error = function (response, model){
	$("#submit").attr('disabled', false);
	if(response.filterword){
		var str = "内容中出现以下过滤词语，是否要继续发布：",len = response.filterword.length;
		
		for(var k=0; k<len; k++)
		{
			str += '<span class="filterword">' + response.filterword[k] + '</span> '
		}
		
		ct.confirm(str, function (){
			if($('#article_add').length) var form = $('#article_add');
			if($('#article_edit').length) var form = $('#article_edit');
			form.append('<input type="hidden" value="1" name="ignoreword"/>');
			form.submit();
		});
		$('div.btn_area button:first').text('继续');
		$('p.ct_confirm>span.filterword').css({color: '#d00'});
	}
	else
	{
		ct.error(response.error);
	}
};
window.content = content;
window.show_subtitle = function(){
	if ($('#has_subtitle').attr('checked') == true){
		$('#tr_subtitle').show();
	}else{
		$('#tr_subtitle').hide();
		$('#subtitle').val('');
	}
};

window.expand = function(obj){
	
	if($(obj).children('span').hasClass("span_open"))
	{
		$(obj).children('span').removeClass("span_open");
		$(obj).children('span').addClass("span_close");
		$('#expand').hide();
	}
	else
	{
		$(obj).children('span').removeClass("span_close");
		$(obj).children('span').addClass("span_open");
		$('#expand').show();
	}
};

window.toggle = function(obj){
	if($(obj).children('span').hasClass("span_open"))
	{
		$(obj).children('span').removeClass("span_open");
		$(obj).children('span').addClass("span_close");
                $(obj+"_sub").hide();
	}
	else
	{
		$(obj).children('span').removeClass("span_close");
		$(obj).children('span').addClass("span_open");
                $(obj+"_sub").show();
	}
};

// 内容重复标题检测
window.checkRepeat = {
	'checkRepeatWay' : 0,
	'init': function(state) {
		var $title = $('#title');
		checkRepeat.checkRepeatWay = state;	// 0-无, 1-按键检测, 2-keyup检测
		if (checkRepeat.checkRepeatWay == 1) {
			$title.css('width','456px').css('padding-right','22px').wrap('<div class="check-repeat-box"></div>').attr('autocomplete', 'off').after('<div class="check-repeat-ico" onclick="checkRepeat.repeatCheckExec();"></div>');
		}
		if (checkRepeat.checkRepeatWay == 2) {
			$title.attr('autocomplete', 'off').bind('keyup', checkRepeat.repeatCheckExec);
		}
		$title.parents('td').append('<div class="clear"></div><div class="check-repeat-panel" id="checkRepeat" style="display:none;"></div>');
	},
	// 关闭标题重复检测
	'closeRepeatTitle' : function() {
		$('#checkRepeat').fadeOut(200);
		$('#title').unbind('keyup', checkRepeat.repeatCheckExec);
	},

	// 标题重复检测
	'repeatCheckExec' : function() {
		var title = $('#title').val();
		var panel = $('#checkRepeat');
		if (checkRepeat.checkRepeatWay == 2 && (!title || title.length < 4)) {
			panel.fadeOut('fast');
			return;
		}
		$.getJSON('?app=system&controller=content&action=compare', {'title':title}, function(json) {
			try {
				if (json.state) {
					panel.empty().append('<div class="check-repeat-banner">相似标题：<a class="check-repeat-close" href="javascript:;" onclick="checkRepeat.closeRepeatTitle(); return false;">忽略提醒</a></div>');
					$.each(json.data, function(i,k) {
						panel.append('<div class="check-repeat-row"><div class="icon '+ k.type +'"></div><span class="check-repeat-title"><a href="'+(k.url || 'javascript:;')+'" target="_blank">' + k.title + '</a></span><span class="check-repeat-cate">[ ' + k.catname + ' ]</span></div>');
					});
					panel.fadeIn('200');
				} else {
					if (checkRepeat.checkRepeatWay == 1) {
						ct.ok('没有检测到内容标题相似或重复')
					}
					throw "empty";
				}
			} catch (e) {
				panel.fadeOut('fast');
			}
		});
	}
}

//表单部分公共代码
if(action == 'add' || action == 'edit') {
	$(function (){
		//$("#catid option[childids='1']").attr("disabled", "disabled");
		
		$('.tips').attrTips('tips', 'tips_green');
		content.tags(model+'_'+action);
		var frm = $(form);
		var elements = frm.find('input,textarea,select').not(':button,:submit,:image,:reset,[disabled]');
		elements.filter(function(){var l = parseInt(this.getAttribute('maxLength'));return l > 0 && l < 1000 && !this.getAttribute('uncount')}).maxLength();
		$.fn.autocomplete && elements.filter(function(){
			return (!!this.getAttribute('autocomplete') && this.getAttribute('url'));
		}).autocomplete({
			autoFill:false,
			showEvent:'focus'
		});
		elements.filter('input.input_calendar').DatePicker({'format':'yyyy-MM-dd HH:mm:ss'});
		$.fn.colorInput && elements.filter('input.color-input').colorInput();
		subform(frm);
		elements.filter('[name=title]').focus();
	});
}

function subform(frm){
	frm.ajaxForm(function(json){
        submit_result = json;
        if (json.state){
            submit_ok = true;
            content.success(json);
        }else{
            content.error(json);
        }
    }, null, function(){
        $().focus();
        if (model == 'picture' && $('[name^=pictures]').length == 0) {
            ct.warn('至少需要上传一张图片');
            return false;
        }
        if (model == 'vote' && $('#options>tr').length < 2) {
            ct.error('至少得保留两个投票选项');
            return false;
        }
        if (action == 'add' && model == 'special' && submit_ok) {
            return false;
        }
        if (window.tinyMCE) {
            $('#content').val(tinyMCE.activeEditor.getContent());
        }
        ct.startLoading('center', '正在保存...');
    });
}

if (action == 'add') {
	content.unload_alert = true;
	window.onbeforeunload = function ()
	{
		if (content.unload_alert && $('#title').val() != '')
		{
			return '内容尚未保存，您确认放弃发布吗？';
		}
	};
}

if(action == 'edit') {
	$(function(){
		content.unload_alert = true;
		window.onbeforeunload = function ()
		{
			if (content.unload_alert && window.changed)
			{
				return '内容尚未保存，您确认放弃修改吗？';
			}
		};
		var imgs = $('.content').find('img');
		if(typeof imgs[0] !=='undefined')
		{
			imgs.each(function(){
				if(this.width>=590) this.width = 560;
			})
		}
	});
	var interval = setInterval(function(){content.lock(contentid, model);}, 10000, contentid);
	$(window).unload(function () {
		clearInterval(interval);
		content.unlock(contentid, model);
	});
}

$(function(){
	if(action =='view'){
		$(window).load(function(){
			$('.content').find('img').each(function(){
				if (this.width>590)
				{
					$(this).removeAttr('height');
					this.style.width = '580px';
					$(this).closest('p').css('text-indent','0');
				}
			});
		});
	}
});
})();

var rightMenu = function(obj,e) {
	$(obj).parents('tr').trigger('contextMenu',e);
};
var menuIco = '<a href="javascript:;" onclick="rightMenu(this,event);" class="content-menu"></a>';