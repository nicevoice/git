(function($){

var treeOptions = {
	url:"?app=system&controller=category&action=cate&catid=%s",
	paramId : 'catid',
	paramHaschild:"hasChildren",
	renderTxt:function(div, id, item){
		return $('<span id="'+id+'">'+item.name+'</span>');
	},
	active : function(div, id, item){
		$('#topcategory').hide();
	    $('#subcategory').show();
		current_catid = id;
		category.edit(current_catid);
	}
};
window.category =  {
	add: function (parentid) {
        function handler(url, func) {
            $.getJSON(url, function(json) {
                if (json.state && json.html) {
                    func && func(json.html);
                } else {
                    ct.error(json.error || '加载失败，请稍后重试');
                }
            });
        }

		if (parentid) {
            handler('?app=system&controller=category&action=add&parentid='+parentid, function(html) {
                $('#edit').attr('class', '');
                $('#add').attr('class', 's_3');
                $("#category_edit_box").html(html);
            });
		} else {
            handler('?app=system&controller=category&action=add', function(html) {
                $('#topcategory').show();
			    $('#subcategory').hide();
                $("#category_edit_box").html(html);
            });
		}
	},
	
	add_submit: function (response)
	{
		if (response.state)
		{
            this.reload(response.catid);
			document.getElementById('category_add').reset();
			ct.ok('保存成功');
		}
		else
		{
			ct.error(response.error);
		}
	},
	
	edit: function (catid)
	{
		$('#add').attr('class', '');
		$('#edit').attr('class', 's_3');
		$("#category_edit_box").load('?app=system&controller=category&action=edit&catid='+catid);
	},
	
	edit_submit: function (response)
	{
		if (response.state)
		{
            this.reload(response.catid);
			ct.ok('保存成功');
		}
		else
		{
			ct.error(response.error);
		}
	},
	
	del: function (catid)
	{
		var span = $('#'+catid);
		ct.confirm('确认删除 <span class="c_red">'+span.html()+'</span> 栏目吗？', function (){
			$.getJSON('?app=system&controller=category&action=delete&catid='+catid, function(response) {
				if (response.state)
				{
					var li = span.closest('li');
					var s = li.siblings('li:eq(0)');
					if (s.length) {
						s.triggerHandler('clk.tree');
					} else {
						s = li.parent().parent();
						if (s[0].nodeName == 'LI') {
							category.reload(s.attr('idv'));
						} else {
							category.add();
						}
					}
					li.remove();
					ct.ok('栏目删除成功');
				}
				else
				{
					ct.error(response.error);
				}
			});
		}, function (){
			
		});
	},
	
	move: function (catid)
	{
		ct.form('移动栏目', '?app=system&controller=category&action=move&catid='+catid, 350, 300, function(response){
			if (response.state) {
				category.reload(catid);
				return true;
			} else {
				ct.error(response.error);
			}
		}, function (dialog){
			dialog.find("#category_move").tree({async:false,expanded:true});
		});
	},
	
	reload: function (catid, path)
	{
		$('#category_tree').empty();
		$('#category_tree').tree($.extend({
			prepared:function(){
				var t = this;
				if (path) {
					t.open(path, true);
				} else {
					$.getJSON('?app=system&controller=category&action=path&catid='+catid, function(path){
						t.open(path, true);
					});
				}
			}
		}, treeOptions));
	},
	
	repair: function () {
        ct.startLoading('center', '修复中，请稍后...');
		$.getJSON('?app=system&controller=category&action=repair', function(response) {
            ct.endLoading();
			if (response.state) {
				ct.alert("操作成功", 'ok');
			} else {
				ct.error(response.error);
			}
		});
	},

	searchCategory : function() {
		var floatBox	= document.createElement('div');
		floatBox	= $(floatBox);
		$('body').append(floatBox);
		floatBox.addClass('floatbox').css('position','fixed').css('visibility','visible').css('left','133px').css('top','67px');
		floatBox.append('<img class="close" src="images/close.gif" alt="" style="position: absolute; top: 1px; right: 2px; cursor: pointer;" />');
		floatBox.append('<input class="bdr_6" id="searchCategory" type="text" size="30" url="?app=system&controller=category&action=searchall&keyword=%s" />');
		var searchCategory	= $('#searchCategory');
		searchCategory.focus();
		floatBox.find('.close').bind('click',function() {
			floatBox.remove();
		});
		searchCategory.autocomplete({
			'itemSelected':function(a, item){
				var id	= item.catid;
				$("#category_edit_box").load('?app=system&controller=category&action=edit&catid=' + id);
				$('li').find('div.active').removeClass('active');
				$('li[idv="'+id+'"]').children('div').addClass('active');
				floatBox.remove();
			}, itemPrepared:function(){}
		});
	},
	
	content_move: function (catid)
	{
		ct.form('移动'+$('#'+catid).html()+'栏目内容至：', '?app=system&controller=content&action=move&sourceid='+catid, 350, 300, function(response){
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
			dialog.find("#category_tree").tree({
				async:false,
				expanded:true
			});
		});
	},
	
	content_clear: function (catid)
	{
		ct.confirm('确认清空 <span class="c_red">'+$('#'+catid).html()+'</span> 栏目内容吗？', function (){
			$.getJSON('?app=system&controller=content&action=clear&catid='+catid, function(response) {
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