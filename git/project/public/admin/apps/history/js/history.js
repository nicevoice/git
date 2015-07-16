var histy = 
{
	//添加或修改,根据id,id为空或object时编辑模式
	save: function (id, tr)
	{
		var url = '?app=history&controller=history&action=save';
		var title = '新建任务';
		if(typeof id == 'object' || !id)
		{
			id = null;
		}
		if(typeof id == 'string')		//编辑
		{
			url += '&hid='+id;
			title = '修改任务';
		}
		ct.form(title, url, 480, 295, function (json){
			if (json.state)
			{
				if(id) {
					tableApp.updateRow(id, json.data);
				}else{
					tableApp.addRow(json.data);
				}
				return true;
			}
			else
			{
				ct.error(json.error);
				return false;
			}
		},function (form,dialog){
			$('input.input_calendar').DatePicker({'format':'yyyy-MM-dd'});
			$('a.checkAll, a.cancelAll').click(function (){
				$(this).parent().parent().find('input[type=checkbox]').attr('checked', this.className == 'checkAll');
			});
		});
	},
	
	//单行或多行删除
	del: function (id)
	{
		if(typeof id == 'object' || !id) 
		{
			id = tableApp.checkedIds().join(',');
			var mul = 1;	//多行删除模式
		}
		if(!id) return ct.warn('请选择要删除的任务');
		ct.confirm('确定删除选中记录？', function(){
			$.getJSON('?app=history&controller=history&action=delete&id='+id, function(json){
				if (json.state)
				{
					if(mul) id = null;
					tableApp.deleteRow(id);
				}
				else
				{
					ct.error('删除失败！');
				}
			});
		});
	},
	
	//获取代码
	code: function ()
	{
		var alias = $(this).attr('alias');
		var code = '复制(以下两种方式之一)红色代码到相应位置生成历史页面日历控件.<br/>\
		1. shtml调用方式: <img align="absmiddle" tips="include shtml方式载入日历, 有利于SEO, 推荐" class="tips hand" src="images/question.gif"/><br/>\
		<span>&lt;!--#include virtual="/section/history/' + alias + '/calendar.html"--&gt;</span><br/>\
		2. js调用方式: <img align="absmiddle" tips="js方式载入日历" class="tips hand" src="images/question.gif"/><br/>\
		<span>&lt;script type="text/javascript" src="'+ IMG_URL +'/apps/history/js/history.js#'+alias+'"&gt;&lt;/script&gt;</span>';
		ct.ok(code,'center',10);
		$('#ui-dialog-title-ct_dialog_alertct_ok').text('调用代码');
		$('#ct_dialog_alertct_ok sup').remove();
		$('.pop_box p').css('padding', '10px').find('span').css('color', '#d00').end().find('img.tips').attrTips('tips', 'tips_green', 200, 'top');
		$('.btn_area button').focus().removeClass('ui-state-focus');
		$('div.pop_box, #ct_dialog_alertct_ok').width(400);
	}
}