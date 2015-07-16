var module = 
{
	set: function (options)
	{
		module.options = options;
	},
	
	init: function ()
	{
		var areas= new Array('module_left', 'module_right');
		$.each(areas, function(i, val)
		{
			$('#'+val).find('h3 >span>span').show();
			$('#'+val+' div:first-child').find('h3 >span>span:nth-child(2)').hide();
			$('#'+val+' div:last-child').find('h3>span>span:nth-child(1)').hide();
		});
		
		var i = 100;
		$('#data_btn>li').each(function(){
			var li = $(this).css('z-index',i--), a = li.find('>a:first'), div = a.next();
			var iHide = function(){ div.hide(); },
				iShow = function(){ div.show(); };
			li.mouseout(function(e){
				var t = e.relatedTarget;
				t == li[0] || li.find('*').index(t) != -1 || iHide();
			});
			a.mouseover(function(){ iShow(); });
		});
	},
	
	save: function ()
	{
		$.getJSON(module.options.save_url, {'module_left':$('#module_left').sortable('toArray').join("#"),'module_right':$('#module_right').sortable('toArray').join("#")}, function(data){
			if (data.state)
			{
				module.init();
			}
			else
			{
				ct.error(data.error);
			}
		});
	},
	
	restore: function ()
	{
	  	$.getJSON(module.options.restore_url, function(data){
			if (data.state)
			{
				ct.tips('操作成功！');
				setTimeout(function(){
						window.location.reload();
				}, 1000);
			}
			else
			{
				ct.error(data.error);
			}
	  	});
	},
	
	up: function(obj)
	{
		var obj = $(obj).closest('div');
		if (obj.prev().is('div'))
		{
			var this_id = obj.id;
			var prev_id = obj.prev().id;
			
			obj.insertBefore(obj.prev());
			
			module.save();
			
			var options = {};
			$("#"+this_id).effect('clip',options,500,function(){
					setTimeout(function(){
						$("#"+this_id+":hidden").removeAttr('style').hide().fadeIn();
					}, 100);
			});
			
			$("#"+prev_id).effect('clip',options,500,function(){
					setTimeout(function(){
						$("#"+prev_id+":hidden").removeAttr('style').hide().fadeIn();
					}, 100);
			});
		}
	},
	
	down: function(obj)
	{
		var obj = $(obj).closest('div');
		if (obj.next().is('div'))
		{
			var this_id = obj.id;
			var next_id = obj.next().id;
			
			obj.insertAfter(obj.next());
			
			module.save();
			
			var options = {};
			$("#"+this_id).effect('clip',options,500,function(){
					setTimeout(function(){
						$("#"+this_id+":hidden").removeAttr('style').hide().fadeIn();
					}, 100);
			});
			
			$("#"+next_id).effect('clip',options,500,function(){
					setTimeout(function(){
						$("#"+next_id+":hidden").removeAttr('style').hide().fadeIn();
					}, 100);
			});
		}
	},
	
	del: function(obj)
	{
		$(obj).closest('div').fadeOut('slow');
		setTimeout(function(){
			$(obj).closest("div").remove();
			module.save();
		}, 1500);
	}
}
$(function (){
	$('div.tag_span>span').mouseover(function (){
		$(this).siblings().removeClass('s_4').parent().parent().find('ul.txt_list').hide();
		var index = $(this).parent().find('span').index($(this));
		$(this).addClass('s_4').parent().parent().find('ul.txt_list').eq(index).show();
	});
})
