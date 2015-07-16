var cron = 
{
	//添加或修改,根据id,id为空或object时编辑模式
	save: function (id)
	{
		var url = '?app=system&controller=cron&action=save';
		var title = '添加计划任务';
		if(typeof id == 'object' || !id)
		{
			id = null;
		}
		if(typeof id == 'string')		//编辑
		{
			url += '&id='+id;
			title = '编辑计划任务';
		}
		if($('#row_'+id+'>td:contains("系统任务")').length) {
			var dialog = ct.warn('这是系统任务,请谨慎修改','center',2);
			var timehandler = setTimeout(form, 2000);
			dialog.find('a').click(function(){
				clearTimeout(timehandler);
				form();
			});
		}else{
			form();
		}
		function form()
		{
			ct.form(title, url, 500, 380, function (response){
				if (response.state)
				{
					if(id) {
						tableApp.updateRow(id, response.data);
					}else{
						tableApp.addRow(response.data);
					}
					return true;
				}
				else
				{
					ct.error(response.error);
					return false;
				}
			}, function (form, dialog) {
                var rule = form.find('[name=rule]'),
                    tip = '#rule_test_tip';
                
				$('input.mode').click(function (){
					$('tr[class^=mode]').hide();
					$('tr.mode'+this.value).show();
                    if (this.value == 4) {
                        var position = rule.position();
                        $(tip).show().css({
                            left: position.left,
                            top: position.top + rule.height() + 8
                        }).show();
                    } else {
                        $(tip).hide();
                    }
				});
				$('input.input_calendar').click(function (){
					var op = {
						format: 'yyyy-MM-dd HH:mm', 
						// minDate与maxDate目前的datepicker列于todo中
						minDate: '%y-%M-%d %H:%m',
						maxDate: '%y-%M-{%d+365}'
					};
					//结束时间不能早于开始时间
					if(this.id == 'starttime')
					{
						op.maxDate = "#F{$dp.$D('endtime') || '%y-%M-{%d+365}'}";
					}
					if(this.id == 'endtime')
					{
						op.minDate = "#F{$dp.$D('starttime') || '%y-%M-%d %H:%m'}";
					}
					DatePicker(this, op);
				});
				$('a.checkAll, a.cancelAll').click(function (){
					$(this).parent().parent().find('input[type=checkbox]').attr('checked', this.className == 'checkAll');
				});
                // 为 rule 添加一个自定义验证规则
                function testRule(elem, args) {
                    var v = $.trim(elem.val());
                    if (! v) return true;

                    var lastvalid = elem.data('lastvalid');
                    if (lastvalid && lastvalid.val == v) {
                        setTimeout(function(){
                            elem.data('handler')(elem, lastvalid.info, lastvalid.state);
                        },1);
                        return;
                    }
                    $.ajax({
                        dataType : 'json',
                        url : args,
                        data: form.find(':input').not('[name=app]').not('[name=controller]').not('[name=action]').serialize(),
                        success:function(json){
                            var lastvalid = {
                                state:json.state ? 'pass' : 'error',
                                info:json.state ? json.info : json.error,
                                val: v
                            };
                            elem.data('handler')(elem, lastvalid.info, lastvalid.state);
                            elem.data('lastvalid',lastvalid);
                            if (json.state) {
                                var ruleTip = $(tip),
                                    position = rule.position();
                                if (! ruleTip.length) {
                                    ruleTip = $('<div id="rule_test_tip"></div>').appendTo(form).css({
                                        position: 'absolute',
                                        width: '250px',
                                        height: '20px',
                                        'line-height': '20px'
                                    });
                                }
                                ruleTip.css({left: position.left,  top: position.top + rule.height() + 8}).show();
                                ruleTip.html('下次运行时间：' + new Date(json.nextrun * 1000).toLocaleString());
                            } else {
                                $(tip).empty().hide();
                            }
                        }
                    });
                }
                $.validate.setRules({
                    callback: testRule
                });
                form.find('[name=rule]').change(function() {
                    if (! this.value) {
                        $(tip).empty().hide();
                    }
                });
                $.trim(rule.val()) && setTimeout(function() {
                    rule.blur();
                }, 50);
			});
		}
		
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
		var tip = '确定删除选中的任务吗？';
		if($('#list_body>tr.row_chked, #row_'+id).find('td:contains("系统任务")').length > 0) {
			tip = '选中的任务包含系统任务, 强烈建议不要删除, 除非你确定自己在干什么.';
		}
		ct.confirm(tip, function(){
			$.getJSON('?app=system&controller=cron&action=delete&id='+id, function(response){
				if (response.state)
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
	//复制为新记录
	copy: function (id)
	{
		if(typeof id == 'object' || !id)
		{
			id = tableApp.checkedIds();
			if(id.length < 1)
			{
				return ct.warn('请选择要复制的记录');
			}
			else if(id.length == 1)
			{
				id = id[0];
			}
			else
			{
				return ct.warn('只能复制一条记录');
			}
		}
		$.getJSON('?app=system&controller=cron&action=copy&id='+id, function(response){
			if (response.state)
			{
				tableApp.addRow(response.data);
			}
			else
			{
				ct.error('复制失败！');
			}
		});
	},
	//查看运行日志
	log: function (id)
	{
		var url = '?app=system&controller=cron&action=viewlog';
		var title = '全部任务运行日志';
		if(typeof id != 'string')		//单任务的日志
		{
			id = tableApp.checkedIds();
			if(id.length == 1)		//如果只选中一行,也视为单任务日志
			{				
				id = id[0];
			}
			else
			{
				id = null;
			}
		}
		if(id)
		{
			url += '&id='+id;
			title = $('#row_'+id+' a:first').text() + '-运行日志';
		}
		ct.ajax(title, url, 600, 380,
			function (){
				
			}
		);
	},
	//action是all,select,id中的一种
	delLog: function (action)
	{
		var url = '?app=system&controller=cron&action=delLog';
		if(action == 'all')
		{
			url += '&type=all';
			var info = '确定要删除所有任务的运行日志吗?';
		}
		else if(action == 'select')
		{
			var id = logTable.checkedIds().join(',');
			if(!id) return ct.warn('请选择要删除的记录');
			url += '&type=select&id='+id;	//logid串
			var info = '确定删除选中的日志吗?';
		}
		else
		{
			url += '&type=cron&id='+action;	//这个id是cronid
			var info = '确定删除选中任务的日志吗？'
		}
		ct.confirm(info, function(){
			$.getJSON(url, function(response){
				if (response.state)
				{
                    ct.ok('操作成功');
					logTable.load();
				}
				else
				{
					ct.error(response.error);
				}
			});
		});
	},
	//无条件执行某个任务
	run: function (id)
	{
		$.getJSON('?app=system&controller=cron&action=interval&run='+id, function (response){
			if (response.state)
			{
				ct.ok(response.info);
				tableApp.reload();
			}
			else
			{
				ct.error('检测出错');
			}
		});
	},
	interval: function ()
	{
		$.getJSON('?app=system&controller=cron&action=interval', function (response){
			if (response.state)
			{
				ct.ok(response.info)
				tableApp.load();
			}
			else
			{
				ct.error('检测出错');
			}
		});
	},
    change: function(id, disabled) {
        $.getJSON('?app=system&controller=cron&action=change&id=' + id + '&disabled=' + (disabled ? 1 : 0), function(json) {
            if (json && json.state) {
                ct.ok('操作成功');
                tableApp.updateRow(id, json.data);
            } else {
                ct.error(json && json.error || '操作失败');
            }
        });
    }
};

