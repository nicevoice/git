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

var magazine = {
	container: 'magazineDiv',		//容器id
	
	//取得项模板
	tpl : function ()
	{
		return tpl;
	},
	
	//取得容器对象
	ctn : function ()
	{
		if(typeof magazine.container == 'string')
		{
			return $('#'+magazine.container);
		}
		return magazine.container;
	},
	view: function (id)
	{
		ct.assoc.open('?app=magazine&controller=edition&action=index&id=' + id);
	},
	//添加或修改
	save : function ()
	{
		var url = '?app=magazine&controller=magazine&action=save';
		var title = '添加杂志';
		if(this.id != 'add')
		{
			var id = $(this).parents('div').attr('rel');
			url += '&id='+id;
			title = '修改杂志';
		}
		ct.form(title, url, 500, 395, function (json){
			if (json.state)
			{
				if(title == '修改杂志')
				{
					magazine.update(json.data, json.data.mid);
				}
				else
				{
					magazine.insert(json.data);
				}
				return true;
			}
			else
			{
				ct.error(json.error);
				return false;
			}
		},function (){
			setTimeout(function (){
				$('#suggestInput').focus(function (){
					$('#suggest').show();
				})
				.blur(function (){
					$('#suggest').hide();
				})
				.keydown(function (e){
					var li = $('#suggest>li.ac_over');
					switch(e.keyCode) 
					{
						case 38: // up
							if(li.prev('li').length < 1) break;
							li.removeClass('ac_over').prev('li').addClass('ac_over');
							break;
						case 40: //down
							if(li.next('li').length < 1) break;
							li.removeClass('ac_over').next('li').addClass('ac_over');
							break;
						case 13: //enter
							this.value = li.text();
							$(this).blur();
							$('input[name=publish]').focus();
							break;
					}
				});
				
				$('#suggest>li').css('cursor', 'pointer').mouseover(function (){
					$('#suggest>li.ac_over').removeClass('ac_over');
					$(this).addClass('ac_over');
				}).mousedown(function (){
					$('#suggestInput').val($(this).text()).blur();
					$('input[name=publish]').focus();
				});
			}, 500);
		});
	},
	
	//载入/刷新列表
	load : function ()
	{
		$.getJSON('?app=magazine&controller=magazine&action=page', function (json){
			var length = json.length;
			if(length < 1 || !json[0]) {
				var html = '<div class="empty">还没有任何记录</div>';
			}else{
				var html = fetchTpl(magazine.tpl(), json);
			}
			magazine.ctn().html(html);
			//绑定事件
			magazine.init_event();
		});
	},
	
	//更新其中一行
	update : function (json, id)
	{
		var html = fetchTpl(magazine.tpl(), json);
		var row = magazine.ctn().find('div[rel=' + id + ']');
		row.replaceWith(html);
		magazine.init_event(id);
	},
	
	//添加一行
	insert : function (json)
	{
		var html = fetchTpl(magazine.tpl(), json);
		magazine.ctn().append(html);
		magazine.init_event(json.mid);
	},
	
	//移除一行
	remove : function (id)
	{
		magazine.ctn().find('div[rel=' + id + ']').remove();
	},
	
	//绑定事件,如果不存在id则为列表容器
	init_event: function (id)
	{
		
		var scape = magazine.ctn();
		if(id)
		{
			var scape = scape.find('div[rel=' + id + ']');
		}
		scape.find('input.edit').click(magazine.save);
		scape.find('input.delete').click(magazine.del);
		scape.find('input.manage').click(function (){
			var id = $(this).parents('div').attr('rel');
			magazine.view(id);
		});
		scape.find('input.newEdition').click(magazine.newEdition);
		
		//magazine.autoMiddle(scape)
	},
	
	autoMiddle : function(scape)
	{
		setTimeout(function (){
			scape.find('li.magazine_logo div').each(function (i, e){
				var top = ($(e).height() - $(e).find('img').height())/2;
				$(e).find('img').css({marginTop: top});
			});
		}, 100);
	},
	
	del : function ()
	{
		var id = $(this).parents('div').attr('rel');
		if(!id) return false;
		ct.confirm('如果确认删除，将清除所有已建内容，请慎重操作？', function(){
			$.getJSON('?app=magazine&controller=magazine&action=delete&id='+id, function (json){
				if (json.state)
				{
					magazine.remove(id);
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
		var pid = $(this).parents('div.magazineItem').attr('rel');
		if(!pid) return ;
		var url = '?app=magazine&controller=edition&action=save&mid=' + pid;
		ct.form('新建期号', url, 480, 260, function (json) {
			if (json.state)
			{
				ct.assoc.open('?app=magazine&controller=page&action=index&id=' + json.data.eid);
			}
			else
			{
				ct.error(json.error);
				return false;
			}
		}, function (form,dialog){
			dialog.find('#magazine').dropdown();
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