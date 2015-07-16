// 文件管理器 功能函数库
// by:longbill 2009-08-14
// 显示文件列表
function ls(data,method)
{
	if (!method) method = 'overwrite';
	var file_list = $("#file_list");
	if (method != 'add') $("#file_list").empty();
	
	for(i=0;i<data.length;i++)
	{
		var temp = row_template;
		var s = data[i].alias;
		if (data[i].resolution != '*')
		{
			s+='&lt;br&gt;规格:'+data[i].resolution;
			if (parseInt(data[i].resolution) > 1 && data[i].isimage)
				data[i].preview = 'preview';
			else
				data[i].preview = 'no';
		}
		else
			data[i].preview = 'no';
		s+= '&lt;br&gt;大小:'+data[i].filesize;
		s+= '&lt;br&gt;上传人:'+data[i].createdby;
		s+= '&lt;br&gt;上传时间:'+data[i].created;
		data[i].description = s;
		for (var key in data[i])
		{
			temp = temp.replace(new RegExp('\\{'+key+'\\}',"gm"), data[i][key]);
		}
		var li = $(temp);
		//当图片文件不存在时显示默认图片 modified by shanhuhai 2010-11-16
		li.find('img').error(function(){this.src=IMG_URL+'images/nopic.jpg'})
		li.appendTo(file_list);
	}
	//切换显示方式按钮样式
	$('#vt_'+file_config.list_type).addClass('vt_list_on');
}


// 点击缩略图
function img_dblclick(o)
{
	preview($(o).parents('li'));
}

function file_click(o,event) 
{
	var li = $(o);
	if (select_single)
	{
		if (window.prevSelected)
		{
			window.prevSelected.removeClass('selected').find('input:radio')[0].checked = false;
		}
		window.prevSelected = li;
		li.addClass('selected').find('input:radio')[0].checked = true;
	}
	else
	{
		li.toggleClass('selected')
		li.find('input:checkbox')[0].checked = li.hasClass('selected');
	}
}

function file_mouseover(o)
{
	$(o).addClass('mouseover');
}


function file_mouseout(o)
{
	$(o).removeClass('mouseover');
}


// 文件名双击编辑
function filename_dblclick(o,event) 
{
	if (window.rename_lock) return true;
	rename_file($(o).parents('li').attr("aid"));
}


//平滑滚动检查是否需要载入更多数据
function check_more()
{
	var o = $('#scroll_div');
	if (o.scrollTop()+o.height() > o.get(0).scrollHeight - 200)
	{
		show_more();
	}
}

	
// 显示目录列表，绑定右键菜单
function dir(data)
{
	var dir_html = format_data(data, dir_row_template);
	$("#current_dir").html(dir_html);
	$("#current_dir > li").click(function()
	{
		var fid = $(this).attr('fid');
		cd(fid);
	})
	.contextMenu("#folder_right_menu",
	function(action, el, pos) {
		var call_back = eval('('+action+')');
		call_back($(el).attr('fid'));
		return;	
	});
}

function delete_folder(fid)
{
	var name = $('[fid='+fid+']').find('a').html();
	ct.confirm('确认要删除'+name+'吗？', function(){
		if (!fid || !name) return;
		$.get(
			'?app=system&controller=attachment&action=ls','fid='+fid+'&size=1',
			function(data)
			{
				if(data.state)
				{
					var next = function(){
						$.post('?app=system&controller=attachment&action=delete_folder','fid='+fid,
						function(data)
						{
							if (data.state)
							{
								file_data = [];
								refresh_data();
							}
							else
							{
								ct.error(data.error);
							}
						},'json');
					};
					if (data.total > 0 || data.dirs.length > 0)
					{
						var s = [
							name,' 里面还有',
							data.total,'个文件',
							"和",data.dirs.length,'个文件夹',
							" 确认要全部删除吗？"
						];
						ct.confirm(s.join(''),next);
					}
					else
					{
						next();
					}
				}
				else
				{
					ct.error(data.error);
				}
			},'json'
		);
	});
}

function rename_folder(fid)
{
	ct.form('重命名','?app=system&controller=attachment&action=rename_folder&fid='+fid,260,148,
	function(json){
		if (json.state)
		{
			delete(file_data[file_config.cur_fid]);
			delete(file_data[json.fid]);
			$('[fid='+json.fid+']').children('a').html(json.name);
			return true;
		}
	});
}

function rename_file(file_id)
{
	if (!file_id)
	{
		var li = get_selected();
		var file_id = li.attr('aid');
	}
	window.rename_lock = true;
	//var file_obj = find_file_object(file_id);
	var span = $('#file_'+file_id).find('span');
	var oldname = span.html();
	var input = $('<input class="inline_input" aid="'+file_id+'" type="text" name="newname" oldvalue="'+oldname+'" value="'+oldname+'" />');
	// 回车或者失去焦点时保存
	input.bind('keydown',function(event)
	{
		if (event.keyCode == 13) rename_submit(input);
	}).bind('blur',function()
	{
		rename_submit(input);
	});
	span.html(input);
	// 默认选中
	$(input).focus().select();
}

function rename_submit(input)
{
	var newname = input.val();
	var oldname = input.attr('oldvalue');
	if (newname == oldname)
	{
		input.parent().html(oldname);
		window.rename_lock = false;
		return;
	}
	$.post('?app=system&controller=attachment&action=rename_file',{newname:newname,aid:input.attr('aid').toString().replace('file_','')},function(data)
	{
		if (data.state)
		{
			input.parent().html(newname);
		}
		else
		{
			ct.tips(data.error,'error');
			input.parent().html(oldname);
		}
		window.rename_lock = false;
	},'json');
}

function new_folder()
{
	var fid = current_data.fid;
	ct.form('创建目录','?app=system&controller=attachment&action=mkdir&parent='+fid,260,148,
	function(json){
		if (json.state) {
			delete(file_data[fid]);
			dir(json.dirs);
			return true;
		}
	});
}

// 文件右键 “查看”
function open_file()
{
	var aid = get_selected();
	if(aid.attr('rel') == 'preview')
	{
		preview(aid);
		return;
	}
	aid = aid.attr('aid');
	var file = find_file_object(aid);
	window.open(file.orig_src,'_blank');
}

// 从服务器获取文件夹数据 ，使用缓存
function load_data(fid,callback,forceload)
{
	if (!forceload && file_data[fid])
	{
		callback(file_data[fid]);
	}
	else
	{
		current_vars.fid = fid;
		window.loading = true;
		$.get(
			'?app=system&controller=attachment&action=ls',
			current_vars,
			function(data)
			{
				if(data.state)
				{
					file_data[data.fid] = data;
					file_data[data.fid]['files_page_1'] = data.files;
					if (callback) callback(file_data[data.fid]);
				}
				else
				{
					ct.error(data.error);
				}
				window.loading = false;
				load_recent();
			},'json'
		);
	}
}

// 显示fid为fid的文件夹内容，
function cd(fid)
{
	window.searching = false;
	$('.attachment_menu_now').removeClass('attachment_menu_now');

	//进入某确定文件夹，清除搜索输入
	$('#my_documents').attr('checked','');
	$('#search_keyword').val('');
	$('#only_children').attr('checked','');
	$('#calendar_from').val('');
	$('#calendar_to').val('');
	//初始化显示条件
	current_vars = {'size':file_config.page_size,from:0 ,ext_limit:ext_limit};
	
	// 上级目录
	if (fid == '..')
	{
		if (current_data)
			fid = current_data.parent;
		else 
			fid = 0;
	}
	
	if (!fid) fid = '0';
	
	load_data(fid,function(data)
	{
		$('#current_folder_name').html(data.name);
		file_config.cur_fid = data.fid;
		current_data = data;
		dir(data.dirs);
		ls(data.files);
		show_pagination();
		//更新目录导航 selecter
		path_navigation();
	});
	$('[fid='+fid+']').addClass('attachment_menu_now');

}

//更新文件夹树状目录上面的快速切换选项
function path_navigation()
{
	//找到当前目录到根目录的路径
	var last_fid = 0;
	var folders = [];
	var fid = file_config.cur_fid;
	while(fid != 0 && file_data[fid] && fid != last_fid)
	{
		folders.push({ name:file_data[fid].name,fid:file_data[fid].fid });
		last_fid = fid;
		fid = file_data[fid].parent;
	}

	//转换路径为可读字符串
	var temp_dir = '';
	var opts = [];
	for(i=folders.length-1;i>=0;i--)
	{
		temp_dir += folders[i].name+'/';
		opts.push({name:temp_dir,fid:folders[i].fid});
	}
	
	//增加option
	var sel = document.getElementById('folder_select');
	sel.options.length = 0;
	for(i=opts.length-1;i>=0;i--)
	{
		sel.options.add(new Option(opts[i].name,opts[i].fid));
	}
	//最下面的是根目录
	sel.options.add(new Option('/',0));
}

//剪贴板缓存变量
var clipboard=[];
var copy_action = 'copy';
var cut_from_fid = 0;
//剪切
function cut_file()
{
	copy_file('cut');
	window.cut_from_fid = file_config.cur_fid;
}
//复制
function copy_file(action)
{
	var aid = $('li.selected');
	if (aid.length<=0)
	{
		var s = action == 'cut' ? '剪切':'复制';
		ct.warn("请选择要"+s+"的文件");
		return;
	}
	var selected_aid = [];
	$('.selected').each(function()
	{
		selected_aid.push($(this).attr('id').toString().replace('file_',''));
	});
	window.clipboard = selected_aid;
	window.copy_action = action?action:'copy';
}
//粘贴
function paste_file()
{
	if (window.searching)
	{
		ct.warn("不能在搜索结果中粘贴，请点击左边的文件夹图标，进入一个文件夹后再执行粘贴操作!");
		return;
	}
	$.post('?app=system&controller=attachment&action=paste_file',
	{
		aids:window.clipboard.join(','),
		fid:file_config.cur_fid,
		copy_action:window.copy_action
	},function(data)
	{
        if (ct.detectLoadError(data)) return false;
		if (data == 'ok')
		{
			refresh_data();
			if (window.copy_action == 'cut')
			{
				delete(file_data[window.cut_from_fid]);
			}
		}
		else
		{
			ct.error(data);
		}
	});
}

function select_folder(o)
{
	cd($(o).val());
}

function upload()
{
	
}


function _dirname(dir_id)
{
	var dir_name = dir_id.split('_');
	dir_name = dir_name.pop();
	return dir_name;
}

function list_type(l_type)
{
	$("#vt_thumb").removeClass('vt_list_on');
	$("#vt_list").removeClass('vt_list_on');
	row_template = (l_type == 'thumb') ? row_thumb_template : row_list_template;
	file_config.list_type = l_type;
	ls(current_data.files);
}

//搜索
function search_file()
{
	window.searching = true;
	current_vars.keyword = $('#search_keyword').val();
	current_vars.only_children = $('#only_children').attr('checked')?'1':'0';
	current_vars.calendar_from = $('#calendar_from').val();
	current_vars.calendar_to = $('#calendar_to').val();
	current_vars.page = 1;
	current_vars.search = '1';
	current_vars.my_documents = $('#my_documents').attr('checked')?'1':'0';
	
	window.loading = true;
	$.get(
		'?app=system&controller=attachment&action=ls',
		current_vars,
		function(data)
		{
			if(data.state)
			{
				current_data = data;
				ls(data.files);
				show_pagination();
			}
			else
			{
				ct.error(data.error);
			}
			window.loading = false;
		},'json'
	);
}

//平滑滚动载入更多
function show_more()
{
	if (window.loading ) return;
	if (window.show_more_lock) return;
	if (!current_data)
	{
		refresh_data();
		return;
	}
	if (current_data.files.length >= current_data.total) return;
	current_vars.from = current_data.files.length;

	window.laoding = true;
	window.show_more_lock = true;
	$.get(
		'?app=system&controller=attachment&action=ls',
		current_vars,
		function(data)
		{
			if(data.state && current_vars.from == data.from)
			{
				for(var i=0;i<data.files.length;i++)
				{
					current_data.files.push(data.files[i]);
				}
				ls(data.files,'add');
				show_pagination();
				file_data[current_data.fid] = current_data;
				
			}
			else
			{
			}
			setTimeout(function(){ window.show_more_lock = false;},10);
			window.loading = false;
		},'json'
	);
}

/* 平滑滚动提示信息 */
function show_pagination()
{
	$('#pagination').html('已显示'+current_data.total+'项中的'+current_data.files.length+'项');
}

function refresh_data()
{
	var fid = current_data ? current_data.fid : file_config.cur_fid;
	file_data[fid] = null;
	cd(fid);
}

function edit_img()
{
	
}

function delete_file()
{
	var aid = $('li.selected');
	if (aid.length<=0)
	{
		ct.error("请选择要删除的文件",'error');
		return;
	}
	var aid2del = [];
	aid.each(function()
	{
		aid2del.push($(this).attr('id').toString().replace('file_',''));
	});
	if (aid2del.length == 1)
	{
		var f = ' "'+aid.find('span').html()+'" ';
	}
	else
	{
		var f = "这"+aid2del.length+"个文件";
	}
	ct.confirm('确认要删除'+f+'吗？',function(){
		var aids = aid2del.join(',');
		for(var i =0 ;i<aid2del.length;i++)
		{
			$("#file_"+aid2del[i]).fadeTo(100,0.7);
		}
		$.post(
			'?app=system&controller=attachment&action=delete_file',
			{'aids': aids},
			function(data)
			{
                if (ct.detectLoadError(data)) return false;
				if (data.aids)
				{
					for(i=0;i<data.aids.length;i++)
					{
						var aid = data.aids[i];
						$("#file_"+aid).animate({ width:0 },300,function(){ $(this).remove(); });
					}
				}
				delete(file_data[file_config.cur_fid]);
				if (data.error)
				{
					var s = '';
					for(i=0;i<data.error.length;i++)
					{
						s += data.error[i]+'<br>';
					}
					if (s) ct.error(s);
				}
			},
			'json'
		);
	});
}

function add_file(data)
{
	var file_html = format_data(data);
	if(file_config.add_mode == 'append'){
		$("#file_list").append(file_html);
	}else{
		$("#file_list").preprend(file_html);
	}
}

// 回调插入文件函数
window_insert = function()
{
	var li = $("li.selected");
	if (select_single)
	{
		if (li.length < 1)
		{
			ct.warn("请选择要插入的文件");
			return;
		}
		else if(li.length > 1)
		{
			ct.warn("只能插入一个文件");
			return;
		}
		else
		{
			selectFile({
                aid:li.attr('aid'),
                src:li.attr('src'),
                name:li.text()
            });
		    for_tinymce && tinyMCEPopup.close();
		}
	}
	else
	{
		var urls = [];
		li.each(function()
		{
            var url = $(this);
			urls.push({
                aid:url.attr('aid'),
                src:url.attr('src'),
                name:url.text()
            });
		});
		selectFiles(urls);
	}
}


// 解析模板
function format_data(data, template)
{
	if(typeof data === 'object' && data.length == 0) return '';
	if(!data) return '';
	var template_row = template || row_template;
	var html = template_row;
	$.each(data, function(key ,value){
		if(typeof value === 'object' && value !== null){
			if(html == template_row){
				html = format_data(value, template_row);
			} else {
				html += format_data(value, template_row);
			}
		}else{
			if(value == '' || value == null) value = '&nbsp;';
			html = html.replace(new RegExp('\\{'+key+'\\}',"gm"), value);
		}
	});
 	return html;
}

// 通过aid查找文件信息
function find_file_object(aid)
{
	if (!aid) return;
	aid = aid.toString().replace('file_','');
	for(i=0;i<=current_data.files.length;i++)
	{
		if (aid == current_data.files[i].aid) return current_data.files[i];
	}
}

// 获得当前选中的项
function get_selected(single)
{
	if (single && $('input[name=file_chk]:checked').length != 1)
	{
		ct.warn("请选择一个文件");
		return false;
	}
	return $('input[name=file_chk]:checked').parents('li');
}

//当点击我的文档时候，清空搜索条件，选中我的文档条件
function show_my_documents()
{
	$('#my_docs_button').addClass('attachment_menu_now');
		$('#my_documents').attr('checked','checked');
		$('#search_keyword').val('');
		$('#only_children').attr('checked','');
		$('#calendar_from').val('');
		$('#calendar_to').val('');
		search_file();
}
// 全选
function select_all()
{
	if (select_single) return;
	$('#file_list > li:not(.selected)').trigger('click');
	notice_selected();
}
// 反选
function select_inverse()
{
	if (select_single) return;
	$('#file_list > li').trigger('click');
	notice_selected();
}

// 提示选中多少项
function notice_selected()
{
	try{ clearTimeout(window.select_notice_timer);} catch(e){ }
	$('#select_notice').css('display','none').html('已选中'+$('#file_list > li.selected').length+'项').fadeIn(300);
	window.select_notice_timer = setTimeout("$('#select_notice').fadeOut(500);",5000);
}
// 编辑图片
function edit_file()
{
	var li = get_selected(), src, win = window;
    if (! li || ! li.attr('src')) {
        return false;
    }
    src = li.attr('src');

    if ($(window).width() < 750 || $(window).height() < 500) {
        if (window.parent) win = window.parent;
    }

    if (! win['ImageEditor']) {
        ct.warn('<h3><strong>加载图片编辑器时遇到问题</strong></h3>如果当前页面的大小小于图片编辑器所需大小，<br />建议您按下 F11 键进入全屏模式后重试。<br />', 'center', 1000);
        return false;
    }

    function afterEdit(json) {
        $.getJSON('?app=system&controller=image&action=thumb&width=100&height=100&file=' + json.file, function(json) {
            var li = $('li.selected:eq(0)');
            li.find('div.icon img').attr('src', UPLOAD_URL + json.thumb + '?' + Math.random() * 9);
            li.attr('src', UPLOAD_URL + json.file + '?' + Math.random() * 9);
        });
    }

    if (win['cmstop'] && win.cmstop.editImage) {
        win.cmstop.editImage(src, afterEdit);
    } else {
        var inst = win['ImageEditor'].open(src);
		inst.bind("saved", afterEdit);
		return inst;
    }
}

//载入常用文件夹
function load_recent()
{
	$.get('?app=system&controller=attachment&action=recent',{},function(data)
	{
		if (data.state && data.dirs)
		{
			$('#recent_dir').empty();
			for(i=0;i<data.dirs.length;i++)
			{
				if (!data.dirs[i].name) continue;
				var li = document.createElement('li');
				var a = document.createElement('a');
				a.href = "javascript:cd("+data.dirs[i].fid+")";
				a.innerHTML = data.dirs[i].name;
				li.appendChild(a);
				$(li).attr('fid',data.dirs[i].fid);
				$(li).contextMenu("#folder_right_menu",
				function(action, el) {
					var call_back = eval('('+action+')');
					call_back($(el).attr('fid'));
					return;	
				});
				$('#recent_dir').append(li);
			}
		}
	},'json');
}

//显示提示信息
function show_notice(o)
{
	window.allow_notice = true;
	$('#notice').html($(o).find('[description]').attr('description'));
	
	$(o).mouseout(function()
	{
		window.allow_notice = false;
		try{ clearTimeout(window.notice_timer); } catch(e) {}
		$('#notice').hide();
	});
}

function download_file()
{
	var lis = $('li.selected');
	if (lis.length <= 0) return;
	var aids = [];
	lis.each(function(){ aids.push($(this).attr('aid')); });
	aids = aids.join(',');
	var src = '?app=system&controller=attachment&action=download_file&aids='+aids;
	try{$('#download_iframe').remove();} catch(e){}
	$('<iframe style="display:none;"></iframe>')
		.attr('id','download_iframe')
		.appendTo(document.body)
		.attr('src',src);
	
}

function copy_link()
{
    var li = get_selected(), src = li.attr('src');
    if (document.body.createTextRange) {
        var input = document.createElement('input'),
            range;
        input.type = 'text';
        input.value = src;
        input.style.display = 'none';
        document.body.appendChild(input);
        input.select();
        range = input.createTextRange();
        range.execCommand('copy');
        document.body.removeChild(input);
        input = null;
        ct.ok('复制成功');
    } else {
        ct.tips('<input type="text" size="20" style="width:200px;" value="' + li.attr('src') + '" onmouseenter="this.select();" onfocus="this.select();" />', 'success', 'center', 999);
    }
}

function download_folder(fid)
{
	fid = parseInt(fid);
	if (fid > 0)
	var src = '?app=system&controller=attachment&action=download_folder&fid='+fid;
	//window.location.href = src;
	try{$('#download_iframe').remove();} catch(e){}
	$('<iframe style="display:none;"></iframe>')
		.attr('id','download_iframe')
		.appendTo(document.body)
		.attr('src',src);
}

/*--------------------append by yanbingbing@cmstop.com----------------*/

function select_ok()
{
	var selected = $("#file_list>li.selected"), res;
	if (select_single)
	{
		if (selected.length < 1)
		{
			ct.warn("请选择要插入的文件");
			return;
		}
		else if(selected.length > 1)
		{
			ct.warn("只能插入一个文件");
			return;
		}
		else
		{
			var aid = selected.find('input:radio').attr('aid');
			res = {aid:aid,src:selected.attr('src')};
		}
	}
	else
	{
		res = [];
		selected.each(function(){
			var s = $(this);
			var aid = s.find('input:checkbox').attr('aid');
			res.push({aid:aid,src:s.attr('src')});
		});
	}
	if (window.dialogCallback && dialogCallback.ok)
	{
		dialogCallback.ok(res);
	}
	else
	{
		window.getDialog && getDialog().dialog('close');
	}
}
function select_cancel()
{
	if (window.dialogCallback && dialogCallback.cancel)
	{
		dialogCallback.cancel();
	}
	else
	{
		window.getDialog && getDialog().dialog('close');
	}
}