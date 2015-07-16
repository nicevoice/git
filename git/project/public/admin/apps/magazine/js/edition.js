var edition = 
{
	view: function (id)
	{
		ct.assoc.open('?app=magazine&controller=page&action=index&id=' + id);
	},
	//添加或修改,根据id,id为空或object时编辑模式
	save: function (id, tr)
	{
		var url = '?app=magazine&controller=edition&action=save&mid=' + mid;
		var title = '新建期号';
		if(typeof id == 'object' || !id)
		{
			id = null;
		}
		if(typeof id == 'string')		//编辑
		{
			url += '&eid='+id;
			title = '修改期号';
		}
		ct.form(title, url, 480, 260, function (json){
			if (json.state)
			{
				if(id) {
					tableApp.load();
				}else{
					tableApp.addRow(json.data);
					ct.assoc.open('?app=magazine&controller=page&action=index&id=' + json.data.eid);
				}
				return true;
			}
			else
			{
				ct.error(json.error);
				return false;
			}
		},function (form,dialog){
			$('input.mode').click(function (){
				$('tr.mode1,tr.mode2,tr.mode3').hide();
				$('tr.mode'+this.value).show();
			});
			$('input.input_calendar').DatePicker({'format':'yyyy-MM-dd'});
			$('a.checkAll, a.cancelAll').click(function (){
				$(this).parent().parent().find('input[type=checkbox]').attr('checked', this.className == 'checkAll');
			});
		});
	},
	search : function ()
	{
		$('#disabled_x a').click(function (){
			tableApp.load('disabled='+this.rel);
		});
		$('#year a, #publish a, #created a').click(function (){
			var field = $(this).parents('.th_pop').attr('id');
			var min = $(this).attr('min');
			var max = $(this).attr('max');
			var where = 'field='+field;
			if(min) where += '&min='+min;
			if(max) where += '&max='+max;
			tableApp.load(where);
		});
	},
	//进入版面管理
	manage: function (id)
	{
		this.view(id);
	},
	
	//单行或多行删除
	del: function (id)
	{
		if(typeof id == 'object' || !id) 
		{
			id = tableApp.checkedIds().join(',');
			var mul = 1;	//多行删除模式
		}
		if(!id) return ct.warn('请选择要删除的记录');
		ct.confirm('确定删除选中记录？', function(){
			$.getJSON('?app=magazine&controller=edition&action=delete&id='+id, function(json){
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
	
	//批量改变状态
	disabled: function (v)
	{
		id = tableApp.checkedIds().join(',');
		if(!id) return ct.warn('请选择要修改状态的记录');
		$.getJSON('?app=magazine&controller=edition&action=disabled&value='+v+'&id='+id, function(json){
			if (json.state)
			{
				tableApp.load();
			}
			else
			{
				ct.error('修改状态失败！');
			}
		});
	},
	
	pagesize: function ()
	{
		$('#pagesize').change(function (){
			tableApp.setPageSize(this.value);
			$.cookie('cmstop_editionsPageSize', this.value);
			tableApp.load();
		});
	},
	//查看前台 
	access: function (id, tr)
	{
		if(typeof id == 'object') {
			window.open($(this).attr('href'));
		}else{
			window.open(tr.find('img.access').attr('href'));
		}
	},
	//发布内容
	publish: function (eid, isForce){
		if(!eid) {
			ids = tableApp.checkedIds();
			if(ids.length < 1) return ct.warn('请选择要发布的期');
			var countMark = 0;
			$('tr.row_chked>td.count>span').each(function (i, e){
				var num = parseInt(e.innerHTML);
				if(num < 1) countMark = 1;
			});
			if(countMark) {
				ct.confirm('某些期没有关联任何文章无法发布，是否继续？', function (){
					edition._publish(ids);
				});
			}else{
				this._publish(ids);
			}
		}
		else
		{
			var num = parseInt($('#tr_'+eid+'>td.count>span').text());
			if(num < 1) return ct.warn('本期还没有关联任何文章，不能发布');
			ids = new Array(eid);
			this._publish(ids);
		}
	}, 
	
	_publish: function (ids)
	{
		if(ids.length < 1)
		{
			tableApp.load();
			return false;
		}
		eid = ids.shift();	//递归运行ajax,一期一次
		var num = parseInt($('#tr_'+eid+'>td.count>span').text());
		if(num < 1) return edition._publish(ids);
		$.getJSON('?app=magazine&controller=page&action=publish&eid=' + eid, function(data) {
			if(data.state) 
			{
					var number = $('#tr_'+eid).find('td a.manage:last').text();
					$.growlUI('发布' + number + '成功');
					setTimeout(function (){
						edition._publish(ids);
					}, 1000);
			}
			else
			{
				ct.ok('发布失败', 'error');
			}
		});
	}
}