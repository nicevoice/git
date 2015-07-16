/**
 * 解析字符串模板
 * @param string tpl 	格式如<tr><td>{key}</td></tr>
 * @param array  json		json格式一或二维数组
 * @return string			返回替换后的多行html代码
 */

function fetchTpl(tpl, json)
{
	var html='', reg, row;
	if(!json[0]) {
		json = new Array(json);	//一维转二维统一处理
	}
	$.each(json, function(i, r){
		row = tpl;
		$.each(r, function (k, v){
			row = row.replace(new RegExp('\{'+k+'\}', 'g'), v);
		});
		html += row;
	});
	return html;
}

var paper = {
	container: 'paperDiv',		//容器id
	
	//取得项模板
	tpl : function ()
	{
		return tpl;
	},
	
	//取得容器对象
	ctn : function ()
	{
		if(typeof paper.container == 'string')
		{
			return $('#'+paper.container);
		}
		return paper.container;
	},
	view: function (id)
	{
		ct.assoc.open('?app=paper&controller=edition&action=index&id=' + id);
	},
	//添加或修改
	save : function ()
	{
		var url = '?app=paper&controller=paper&action=save';
		var title = '添加报纸';
		if(this.id != 'add')
		{
			var id = $(this).parents('div').attr('rel');
			url += '&id='+id;
			title = '修改报纸';
		}
		if (ct.IE7) {
			var w = 485;
		}else if(ct.IE8){
			var w = 445;
		}else{
			var w = 450;
		}
		ct.form(title, url, w, 225, function (json){
			if (json.state)
			{
				if(title == '修改报纸')
				{
					paper.update(json.data, json.data.paperid);
				}
				else
				{
					paper.insert(json.data);
				}
				return true;
			}
			else
			{
				ct.error(json.error);
				return false;
			}
		},function (){
			
		});
	},
	
	//载入/刷新列表
	load : function ()
	{
		$.getJSON('?app=paper&controller=paper&action=page', function (json){
			var length = json.length;
			if(length < 1 || !json[0]) {
				var html = '<div class="empty">还没有任何记录</div>';
			}else{
				var html = fetchTpl(paper.tpl(), json);
			}
			paper.ctn().html(html);
			//绑定事件
			paper.init_event();
		});
	},
	
	//更新其中一行
	update : function (json, id)
	{
		var html = fetchTpl(paper.tpl(), json);
		var row = paper.ctn().find('div[rel=' + id + ']');
		row.replaceWith(html);
		paper.init_event(id);
	},
	
	//添加一行
	insert : function (json)
	{
		var html = fetchTpl(paper.tpl(), json);
		paper.ctn().append(html);
		paper.init_event(json.paperid);
	},
	
	//移除一行
	remove : function (id)
	{
		paper.ctn().find('div[rel=' + id + ']').remove();
	},
	
	//绑定事件,如果不存在id则为列表容器
	init_event: function (id)
	{
		var scape = paper.ctn();
		if(id)
		{
			var scape = scape.find('div[rel=' + id + ']');
		}
		scape.find('input.edit').click(paper.save);
		scape.find('input.delete').click(paper.del);
		scape.find('input.manage').click(function (){
			var id = $(this).parents('div').attr('rel');
			paper.view(id);
		});
		scape.find('input.newEdition').click(paper.newEdition);
		
		scape.find('li.paper_logo a').click(function (){
			if(this.href == 'javascript:;') {
				$.growlUI('前台页面未生成');
				return false;
			}
		});
	},
	
	del : function ()
	{
		var id = $(this).parents('div').attr('rel');
		if(!id) return false;
		ct.confirm('如果确认删除，将清除所有已建内容，请慎重操作？', function(){
			$.getJSON('?app=paper&controller=paper&action=delete&id='+id, function (json){
				if (json.state)
				{
					paper.remove(id);
					return true;
				}
				else
				{
					ct.error(json.error);
					return false;
				}
			});
		});
	}, 
	
	//新建期号
	newEdition: function ()
	{
		var pid = $(this).parents('div.paperItem').attr('rel');
		if(!pid) return ;
		var url = '?app=paper&controller=edition&action=save&paperid=' + pid;
		ct.form('新建期号', url, 400, 165, function (json){
			if (json.state)
			{
				ct.assoc.open('?app=paper&controller=page&action=index&id=' + json.data.editionid);
				return true;
			}
			else
			{
				ct.error(json.error);
				return false;
			}
		},function (form,dialog){
			dialog.find('#paper').dropdown();
			$('input.mode').click(function (){
				$('tr.mode1,tr.mode2,tr.mode3').hide();
				$('tr.mode'+this.value).show();
			});
			$('input.input_calendar').DatePicker({'format':'yyyy-MM-dd'});
			$('a.checkAll, a.cancelAll').click(function (){
				$(this).parent().parent().find('input[type=checkbox]').attr('checked', this.className == 'checkAll');
			});
		});
	}
};