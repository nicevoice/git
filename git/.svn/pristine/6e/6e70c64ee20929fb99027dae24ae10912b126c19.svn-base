var where = null;
var limit = 0;
var total = 0;
var models = Array();
var appname = null;
var number = 0;

var html = 
{
	index: function ()
	{
		$.getJSON('?app=system&controller=html&action=createIndex', function (response){
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
		});
	},
	
	roll: function ()
	{
		ct.form('生成滚动页', '?app=system&controller=html&action=roll', 300, 250, function (response){
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
		},function(){
		    $('input.input_calendar').DatePicker({'format':'yyyy-MM-dd'});
	},
	
	rank: function ()
	{
		$.getJSON('?app=system&controller=html&action=rank', function (response){
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
		});
	},

	tags: function ()
	{
		$.getJSON('?app=system&controller=html&action=tags', function (response){
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
		});
	},
	
	map: function ()
	{
		$.getJSON('?app=system&controller=html&action=map', function (response){
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
		});
	},
	
	category: function ()
	{
		$.getJSON('?app=system&controller=html&action=category&catid='+current_catid, function(response){
			if (response.state)
			{
				ct.ok('操作成功');
			}
			else
			{
				ct.error(response.error);
			}
		});
	},
	
	ls: function ()
	{
		$('#ls').attr('class', 's_3');
		$('#show').attr('class', '');
		$('#show_form').hide();
		$('#ls_form').show();
	},
	
	show: function ()
	{
		$('#ls').attr('class', '');
		$('#show').attr('class', 's_3');
		$('#show_form').show();
		$('#ls_form').hide();
	},
	
	init: function ()
	{
		$('#html_ls').ajaxForm('html.ls_submit');
		$('#html_show').ajaxForm('html.show_submit');
	},
	
	ls_submit: function (response)
	{
		if (response.state)
		{
			if (response.catids)
			{
				if (response.percent == 0)
				{
					$("input[type='submit']").attr('disabled', true);
					$("#ls_progressbar").progressBar({ barImage: IMG_URL+'images/progressbg_yellow.gif'} );
	                setTimeout('html.ls_create()', 1000);
				}
				else if (response.percent < 1)
				{
					percent = ((response.percent*100)+'').substr(0, 2);
					$('#ls_progressbar').progressBar(percent);
					setTimeout('html.ls_create()', 1000);
				}
				else if (response.percent == 1)
				{
					$("input[type='submit']").attr('disabled', false);
					$('#ls_progressbar').progressBar(100);
					setTimeout(function(){
						$('#ls_progressbar').hide();
					}, 2000);
					ct.ok('全部生成完毕');
				}
			}
			else
			{
				ct.ok('操作成功');
			}
		}
		else
		{
			ct.error(response.error);
		}
	},
	
	ls_create: function ()
	{
		$.getJSON('?app=system&controller=html&action=ls&maxlimit='+$('#maxlimit').val()+'&catids=true', function(response){
			html.ls_submit(response);
		});
	},
	
	show_submit: function (response)
	{
		if (response.state)
		{
			where = response.where;
			total = response.total;
			limit = $('#limit').val();
			models = response.models;
			
	        $("input[type='submit']").attr('disabled', true);
	        
	        $("#show_progressbar").progressBar({ barImage: IMG_URL+'images/progressbg_yellow.gif'} );
	        
			html.show_create(response);
		}
		else
		{
			ct.error(response.error);
		}
	},
	
	show_create: function (response)
	{
		if (response.finished && models.length === 0)
		{
			where = null;
			limit = 0;
			total = 0;
			models = Array();
			appname = null;
			number = 0;
	
			$("input[type='submit']").attr('disabled', false);
			
			$('#show_progressbar').progressBar(100);
			setTimeout(function(){
				$('#show_progressbar').hide();
			}, 2000);
			
			ct.ok('全部生成完毕');
		}
		else
		{		
			if (response.finished)
			{
				number += response.offset;
				percent = ((number/total*100)+'').substr(0, 2)+'%';
				appname = models.shift();
				offset = 0;
				count = '';
			}
			else
			{
				percent = (((number + response.offset)/total*100)+'').substr(0, 2);
				offset = response.offset;
				count = response.count;
			}
			if (percent == '0') percent = '1';
			
			$('#show_progressbar').progressBar(percent);
						
			$.getJSON('?app='+appname+'&controller=html&action=show_batch&where='+where+'&limit='+limit+'&count='+count+'&offset='+offset, function(response) {
				if (response.state)
				{
					html.show_create(response);
				}
				else
				{
					ct.error(response.error);
				}
			});
		}
	}
}