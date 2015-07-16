(function($){

var treeOptions = {
	url:"?app=system&controller=property&action=cate&proid=%s",
	paramId : 'proid',
	paramHaschild:"hasChildren",
	renderTxt:function(div, id, item){
		return $('<span id="'+id+'">'+item.name+'</span>');
	},
	active : function(div, id, item){
		$('#topproperty').hide();
	    $('#subproperty').show();
		current_proid = id;
		property.edit(current_proid);
	}
};
window.property =  {
	add: function (parentid) {
		if (parentid)
		{
			$('#edit').attr('class', '');
			$('#add').attr('class', 's_3');
			$("#property_edit_box").load('?app=system&controller=property&action=add&parentid='+parentid);
		}
		else
		{
			$('#topproperty').show();
			$('#subproperty').hide();
			$("#property_edit_box").load('?app=system&controller=property&action=add');
		}
	},
	
	add_submit: function (response)
	{
		if (response.state)
		{
            this.reload(response.proid);
			document.getElementById('property_add').reset();
			ct.ok('保存成功');
		}
		else
		{
			ct.error(response.error);
		}
	},
	
	edit: function (proid)
	{
		$('#add').attr('class', '');
		$('#edit').attr('class', 's_3');
		$("#property_edit_box").load('?app=system&controller=property&action=edit&proid='+proid);
	},
	
	edit_submit: function (response)
	{
		if (response.state)
		{
            this.reload(response.proid);
			ct.ok('保存成功');
		}
		else
		{
			ct.error(response.error);
		}
	},
	
	del: function (proid)
	{
		var span = $('#'+proid);
		ct.confirm('确认删除 <span class="c_red">'+span.html()+'</span> 属性吗？', function (){
			$.getJSON('?app=system&controller=property&action=delete&proid='+proid, function(response) {
				if (response.state)
				{
					var li = span.closest('li');
					var s = li.siblings('li:eq(0)');
					if (s.length) {
						s.triggerHandler('clk.tree');
					} else {
						s = li.parent().parent();
						if (s[0].nodeName == 'LI') {
							property.reload(s.attr('idv'));
						} else {
							property.add();
						}
					}
					li.remove();
					ct.ok('属性删除成功');
				}
				else
				{
					ct.error(response.error);
				}
			});
		}, function (){
			
		});
	},
	
	move: function (proid)
	{
		ct.form('移动属性', '?app=system&controller=property&action=move&proid='+proid, 350, 300, function(response){
			if (response.state) {
				property.reload(proid);
				return true;
			} else {
				ct.error(response.error);
			}
		}, function (dialog){
			dialog.find("#property_move").tree({async:false,expanded:true});
		});
	},
	
	reload: function (proid, path)
	{
		$('#property_tree').empty();
		$('#property_tree').tree($.extend({
			prepared:function(){
				var t = this;
				if (path) {
					t.open(path, true);
				} else {
					$.getJSON('?app=system&controller=property&action=path&proid='+proid, function(path){
						t.open(path, true);
					});
				}
			}
		}, treeOptions));
	},
	
	repair: function ()
	{
		$.getJSON('?app=system&controller=property&action=repair', function(response){
			if (response.state)
			{
				ct.alert("操作成功", 'ok');
			}
			else
			{
				ct.error(response.error);
			}
		});
	},
	
	content_move: function (proid)
	{
		ct.form('移动'+$('#'+proid).html()+'属性内容至：', '?app=system&controller=content&action=move&sourceid='+proid, 350, 300, function(response){
			if (response.state)
			{
				ct.tips('操作成功');
				return true;
			} 
			else 
			{
				ct.error(response.error);
			}
		}, function (dialog){
			dialog.find("#property_tree").tree({
				async:false,
				expanded:true
			});
		});
	},
	
	content_clear: function (proid)
	{
		ct.confirm('确认清空 <span class="c_red">'+$('#'+proid).html()+'</span> 属性内容吗？', function (){
			$.getJSON('?app=system&controller=content&action=clear&proid='+proid, function(response) {
				if (response.state)
				{
					ct.ok('操作成功');
				}
				else
				{
					ct.error(response.error);
				}
			});
		}, function (){
			
		});
	}
};
})(jQuery);