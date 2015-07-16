$.fn.floatDiv=function(options) {
	var defaults = {
		hiddenid:'cmstopHiddenImg',//隐藏层id
		thtml:'body',
		InnerHtmls:'CMSTOP漂浮层内容!',
		Xoffset:'6',//距离鼠标X值
		Yoffset:'10',//距离鼠标Y值
		background: '#ccc',//显示层的背景
		padding:'5px',
		border:'1px solid #fff',
		index:'888888888',//z-index
		width: '',//层的宽度.默认自适应
		height:''//层的高度.默认自适应
	};
	var opts = $.extend(defaults, options);
	$(opts.thtml).append('<div id="'+opts.hiddenid+'"></div>'); 
	var hiddenObject = $("#"+opts.hiddenid);
	hiddenObject.css({//给对象定义样式
		'position':'absolute',
		'overflow':'hidden',
		'display':'none',
		'z-index':opts.index,
		'cursor' : 'pointer'
	});
	
	//清除原来的内容
	hiddenObject.html('');
	var offset = $(this).offset();
	var left=offset.left+parseInt($(this).width())+parseInt(opts.Xoffset)+"px";
	var top =offset.top-1+"px";
	
	var imges='<div>';
	imges+='<div ><a style="color:red;" href="javascript:;" id="Close_FloatDiv" title="关闭">关闭</a></div>';
	imges+='<div>';
	imges+=opts.InnerHtmls;
	imges+='</div>';
	imges+='</div>';
	hiddenObject.html(imges);
	
	hiddenObject.css({//给返回的数据定义样式
		'position':'absolute',
		'overflow':'hidden',
		'top':top,
		'left':left,
		'padding':opts.padding,
		'background':opts.background,
		'border':opts.border,
		'z-index':opts.index,
		'cursor' : 'pointer',
		'width':opts.width,
		'height':opts.height
	}).show();
			
	$("#Close_FloatDiv").bind('click',function(){
		hiddenObject.html('');
		hiddenObject.hide();
		return false;
	});
};
(function(){
var form;
var App = {
	init:function(){
		form = document.getElementById('ruleForm');
		$(form).ajaxForm(function(json){
			if (json.state) {
				ct.confirm(json.info+'，继续编辑规则？',null,function(){
					ct.assoc.close();
				});
			} else {
				ct.error(json.error);
			}
		});
		var tinput = $(form.maintable).focus(function(){
			$.getJSON("?app=push&controller=push&action=tables&dsnid="+form.dsnid.value,
			function(json){
				if (json.state)
				{
					tinput.floatDiv({InnerHtmls:json.html});
					$('#cmstopHiddenImg select').change(function(){
						tinput.val(this.value);
					}).val(tinput.val());
				}
				else
				{
					tinput.floatDiv({InnerHtmls:'数据库连接出现错误!'});
				}
			});
		});
		$('#adddsn').click(function(){
			ct.form('添加数据源', '?app=system&controller=dsn&action=add', 400, 370,
			function(json){
	            if (json.state)
	            {
	            	var item = new Option(json.data.name, json.data.dsnid);
					form.dsnid.options.add(item);
					item.selected = true;
					return true;
	            }
	        });
		});
		var addtable = $('#addtable');
		var _tbody = addtable.parents('tbody');
		_tbody.find('input[name^=jointable]:enabled').each(function(){
			var input = $(this).focus(function(){
				$.getJSON("?app=push&controller=push&action=tables&dsnid="+form.dsnid.value,
				function(json){
					if (json.state)
					{
						input.floatDiv({InnerHtmls:json.html});
						$('#cmstopHiddenImg select').change(function(){
							input.val(this.value);
						}).val(input.val());
					}
					else
					{
						input.floatDiv({InnerHtmls:'数据库连接出现错误!'});
					}
				});
			});
		});
		addtable.click(function(){
			var ttr = _tbody.find('>tr:last');
			var tr = ttr.clone();
			var input = tr.find('input').removeAttr('disabled').eq(0);
			input.focus(function(){
				$.getJSON("?app=push&controller=push&action=tables&dsnid="+form.dsnid.value,
				function(json){
					if (json.state)
					{
						input.floatDiv({InnerHtmls:json.html});
						$('#cmstopHiddenImg select').change(function(){
							input.val(this.value);
						}).val(input.val());
					}
					else
					{
						input.floatDiv({InnerHtmls:'数据库连接出现错误!'});
					}
				});
			});
			ttr.before(tr.show());
		});
		$(form.primary).focus(function(){
			var input = $(this);
			var data = [
				'dsnid='+form.dsnid.value,
				'tables[]='+form.maintable.value
			];
			$(form['jointable[]']).filter(':visible').each(function(){
				this.value && data.push('tables[]='+this.value);
			});
			$.getJSON("?app=push&controller=push&action=primary",data.join('&'),
			function(json){
				if (json.state)
				{
					input.floatDiv({InnerHtmls:json.html});
					$('#cmstopHiddenImg select').change(function(){
						input.val(this.value);
					}).val(input.val());
				}
				else
				{
					input.floatDiv({InnerHtmls:'数据库连接出现错误!'});
				}
			});
		});
		$('#fields input.field').focus(function(){
			var input = $(this);
			var data = [
				'dsnid='+form.dsnid.value,
				'tables[]='+form.maintable.value
			];
			$(form['jointable[]']).filter(':visible').each(function(){
				this.value && data.push('tables[]='+this.value);
			});
			$.getJSON("?app=push&controller=push&action=fields",data.join('&'),
			function(json){
				if (json.state)
				{
					input.floatDiv({InnerHtmls:json.html});
					$('#cmstopHiddenImg select').change(function(){
						input.val(this.value);
					}).val(input.val());
				}
				else
				{
					input.floatDiv({InnerHtmls:'数据库连接出现错误!'});
				}
			});
		});
	},
	testGetList:function(){
		var data = $(form).serialize();
		$.post('?app=push&controller=push&action=testGetList',data,
		function(json){
			if (json.state) {
				var d = $('<div></div>').html(json.html||'').dialog({
					autoOpen: true,
					bgiframe: true,
					modal : false,
					height:350,
					width:400,
					title : '获取测试',
					close: function(){
						d.dialog('destroy').remove();
					}
				});
			} else {
				ct.tips('获取失败','error');
			}
		},'json');
	},
    testlink:function(frm)
    {
        frm = $(frm);
        $.post('?app=system&controller=dsn&action=test',frm.serialize(),function(json){
            var info = json.state
                ? $('<div class="success"><sub></sub>资源正常</div>')
                : $('<div class="error"><sub></sub>'+json.error+'</div>');
            frm.before(info);
            setTimeout(function(){info && info.hide()},3000);
        },'json');
    }
};
window.App = App;
})();