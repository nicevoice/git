var relateddata;

function related_select(contentid, keywords)
{
	userAgent = navigator.userAgent.toLowerCase();
	IE = (/opera/.test(userAgent) ? 0 : parseInt((/.+msie[\/: ]([\d.]+)/.exec(userAgent) || {1:0})[1])) > 0;
	IE ? maxHeight = 560 : maxHeight = 'auto';
	ct.ajaxDialog({title:'选择相关内容', maxHeight:maxHeight, width:800,  height:'auto'}, '?app=system&controller=related&action=search&contentid='+contentid+'&keywords='+encodeURIComponent(keywords), null,function (dialog) {
		var li	= dialog.find('ul#list').children('li');
		var	i	= 0;
		var	html= '';
		while (li.html() != null) {
			obj	= li.find('span.f_l').find('a');
			html += '<li><input type="hidden" name="related[]" id="related_'+i+'" value="'+li.find('span.f_l input').val()+'"/><a href="'+obj.attr('href')+'" target="_blank">'+obj.html()+'</a></li>'+"\n";
			li	= li.next();
			i++;
		}
		$('#related_data').html(html);
		return true;
	});
}

function related_data()
{
	var data = [];
	var ids = [];
	$('#related_data input').each(function(i){
		data[i] = $(this).val();
		ids[i] = parseInt($(this).val().split('|').pop());
	});
	return [data,ids];
}

function related_append(data)
{
	var html = '';
	$.each(data, function(i, r){
		if(r.title) var title = r.title.substr(0, 20);
		rid++;
		html += '<li id="'+r.contentid+'">';
		html += ' <input type="checkbox" name="related" id="related_'+r.contentid+'" value="'+r.title+'|'+r.thumb+'|'+r.url+'|'+r.time+'|'+r.contentid+'" class="checkbox_style" onclick="related_checked('+r.contentid+')"/>';
		html += '   <a href="'+r.caturl+'" target="_blank">'+r.catname+'</a>\uff1a<a href="'+r.url+'" target="_blank">'+title+'</a>';
		html += '   <span class="date">'+r.time+'</span>';
		html += '</li>';
	});
	$('#data').append(html);
	init_panel();
}


function related_checked(rid)
{
	if ($('#related_'+rid).attr('checked') == true)
	{
		related_list_add(rid, $('#related_'+rid).val());
	}
	else
	{
		$('#checked_'+rid).parent().remove();
		checked_count--;
		$('#checked_count').html(checked_count);
	}
	init_panel();
}


function init_panel()
{
	$('#list').find('img').show().css('visibility','visible')
	if($('#list').children().length ==1)
	{
		$('#list').find('img:lt(2)').hide();
	}
	else
	{
		$('#list li:first').find('img:eq(0)').hide();
		$('#list li:last').find('img:eq(1)').css('visibility','hidden');
	}
}

function related_checked_delete(rid)
{
	$('#related_'+rid).attr('checked', false);
	$('#checked_'+rid).parent().remove();
	checked_count--;
	init_panel();
	$('#checked_count').html(checked_count);
}

function related_checked_up(i)
{
	var obj = $('#checked_'+i).parent();
	if (obj.prev().is('li'))
	{
		var prev_html = obj.prev().html();
		var this_html = obj.html();
		obj.prev().html(this_html);
		obj.html(prev_html);
	}
	init_panel();
}

function related_checked_down(i)
{
	var obj = $('#checked_'+i).parent();
	if (obj.next().is('li'))
	{
		var next_html = obj.next().html();
		var this_html = obj.html();
		obj.next().html(this_html);
		obj.html(next_html);
	}
	init_panel();
}

function related_search_ok(response)
{
		if (response.state)
		{
			$('#data').empty();
			sort_mode = '';
			related_append(response.data);
			count = response.data.length;
			total = response.total;
			$('#count').html(response.data.length);
			$('#total').html(response.total);
			page	= 1;
		}
		else
		{
			alert(response.error);
		}
}

function init()
{
	checked_count++;
}


function related_list_add(rid, data)
{
	checked_count++;
	var r = data.split('|');
	rid =  rid? rid :r[4];
	var html = '';
	html += '<li class="layout">';
	html += '   <span id="checked_'+rid+'" class="f_r"><img src="images/up.gif" alt="向上" title="向上" height="16" width="16" class="hand" onclick="related_checked_up(\''+rid+'\')"/> <img src="images/down.gif" alt="向下" title="向下" height="16" width="16" class="hand" onclick="related_checked_down(\''+rid+'\')"/> <img src="images/edit.gif" alt="编辑" title="编辑" height="16" width="16" class="hand" onclick="related_edit(\''+rid+'\')"/> <img src="images/del.gif" alt="取消" title="取消" height="16" width="16" class="hand" onclick="related_checked_delete(\''+rid+'\')"/></span>';
	html += '   <span class="f_l"><input type="hidden" name="data_'+rid+'" id="data_'+rid+'" value="'+data+'" /><a id="a_'+rid+'" href="'+r[2]+'" target="_blank">'+(r[0].substr(0, 20))+'</a></span>';
	html += '</li>';
	$('#list').append(html);
	$('#checked_count').html(checked_count);
}

function related_list_edit(rid, data)
{
	var r = data.split('|');
	$('#data_'+rid).val(data);
	$('#a_'+rid).html(r[0]);
	$('#a_'+rid).attr('href', r[2]);
}

function related_add()
{

	ct.form('添加相关', '?app=system&controller=related&action=add', 400, 200, null,null,function(f, d){
		rid++;
		var data = f.find('#title').val()+'|'+f.find('input[name="thumb"]').val()+'|'+f.find('#url').val()+'|'+f.find('#time').val()+'|'+(contentid+'_'+rid);
		related_list_add((contentid+'_'+rid), data);
		init_panel();
		d.dialog('close');
		return false;
	});
}

function related_edit(rid)
{
	var relateinfo = $('#data_'+rid).val().split('|');
	ct.form('修改相关', '?app=system&controller=related&action=edit&rid='+rid, 400, 200, null,function(f, d){
		f.find('#edit_title').val(relateinfo[0]);
		f.find('input[name="thumb"]').val(relateinfo[1]);
		f.find('#edit_url').val(relateinfo[2]);
		f.find('#edit_time').val(relateinfo[3]);
	},function(f,d){
		var data = f.find('#edit_title').val()+'|'+f.find('input[name="thumb"]').val()+'|'+f.find('#edit_url').val()+'|'+f.find('#edit_time').val()+'|'+rid;
		related_list_edit($('#edit_rid').val(), data);
		d.dialog('close');
		return false;
	});
}

var sort_mode = '';
function related_sort(mode){
	sort_mode = mode;
	var data = new Array();
	$('#data > li').each(function(i){
		data[i] = [$(this).attr('id'), $(this).html(), $(this).children('.date').html(), $('#related_'+$(this).attr('id')).attr('checked')];
	});
	data.sort(function(a, b) { 
		return sort_mode == 'asc' ? a[2].localeCompare(b[2]) : b[2].localeCompare(a[2]);
	});
	var html = '';
	$.each(data, function(i, r){
		html += '<li id="'+r[0]+'">'+r[1]+'</li>'+"\n";
	});
	$('#data').html(html);
	$.each(data, function(i, r){
		if (r[3] == true) $('#related_'+r[0]).attr('checked', true);
	});
}

function related_ok(){
	var related = [];
	$("input[name*='data']").each(function(i){
		related[i] = $(this).val().split('|');
	});
	window.returnValue = related;
	window.close();
}

function related_cancel(){
	window.returnValue = null;
	window.close();
}

// deprecated
//ajax form submit function, add by yangwenfeng @ 2009.05.25
function async_form(form_id, c_b){
	$('#'+form_id).bind('submit.form-plugin',function() {
        $(this).ajaxSubmit({ 
    		dataType:  'json',
    		success: function(json){
    			if(json.state){
    				if(json.redirect){
    					//redirect
    					var r_url = json.redirect == 'refresh' ? window.location.href : json.redirect;
    					window.location.href=r_url;
    				}
    			}
    			
    			if(c_b){
    				var sub_callback = eval('('+c_b+')');
    				sub_callback(json);
    			}
    		}
    	});
        return false;
    }); 
}

function related_picture(data)
{
	var html = '';
	$.each(data, function(i, r){
		rid++;
		html += '<li id="'+rid+'">';
		html += ' <input type="radio" name="related_picture" id="related_'+rid+'" value="'+r.contentid+'" class="checkbox_style" />';
		html += '   <a href="'+r.caturl+'">'+r.catname+'</a>\uff1a<a href="'+r.url+'" target="_blank" title='+r.title+'>'+r.title.substr(0,20)+'</a>';
		html += '   <span class="date">'+r.time+'</span>';
		html += '</li>';
	});
	$('#data').append(html);
}


function related_picture_ok(response)
{
	if (response.state)
	{
		$('#data').empty();
		sort_mode = '';
		related_picture(response.data);
		count = response.data.length;
		total = response.total;
		$('#count').html(response.data.length);
		$('#total').html(response.total);
	}
	else
	{
		alert(response.error);
	}
}

function picture_ok()
{
	var pic_id='';
	$("input[name*='related_picture']").each(function(i){
		if ($(this).attr('checked') == true)
		{
			pic_id=$(this).val();
		}
	});
	
	window.returnValue = pic_id;

	window.close();
}

function init_list() {
	$('#related_data').find('li').each(function(i){
		related_list_add(null, $(this).find('input').val());
	});
}