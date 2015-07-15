//space
var space = {
	add : function(){
		ct.form('添加专栏','?app=space&controller=index&action=add',500,430,
			function(json){
				if(json.state) {
					ct.ok('添加成功');
					tableApp.load();
					return true;
				} else {
					ct.error(json.error);
				}
			},
			function(){return true});
	},
	edit : function(spaceid) {
		ct.form('修改专栏资料','?app=space&controller=index&action=edit&spaceid='+spaceid,500,430,function(json) {
			if(json.state) {
				if(typeof(tableApp) != 'undefined' && location.search && location.search.indexOf('?app=space&controller=index&action=index') === 0) tableApp.updateRow(spaceid, json.data);
				return true;
			} else {
				ct.error(json.error);
			}
		},function(){return true});
	},
	
	panel :function(spaceid) {
		ct.assoc.open('?app=space&controller=index&action=panel&spaceid='+spaceid, 'newtab');
	},
	del : function(spaceid) {
		var msg = '确定删除该专栏吗？';
		ct.confirm(msg,function(){
			$.post('?app=space&controller=index&action=delete',{spaceid:spaceid},function(json){
				json.state
				 ? (ct.ok('删除完毕'), tableApp.deleteRow(spaceid))
				 : ct.error(json.error);
			},'json');
		}).dialog('option','width',360);
	},
	search : function() {
		return;
	},
	open :function (spaceid) {
		space.action(spaceid,3);
	},
	ban :function (spaceid) {
		space.action(spaceid,0);
	},
	recommend :function (spaceid) {
		space.action(spaceid,4);
	},
	cancel :function (spaceid) {
		space.action(spaceid,3);
	},
	published :function(author) {
		ct.assoc.open('?app=article&controller=article&action=add&modelid=1&author='+author, 'newtab');

	},
	action :function (spaceid,status,reload) {
		$.post('?app=space&controller=index&action=status',{spaceid:spaceid,status:status},function(json){
				if(json.state) {
					ct.ok('操作成功');
					if(reload) {
						setTimeout('location.href = location.href',2000);
					} else {
						tableApp.load();
					}
				}else {
					ct.error(json.error);
				}
			},'json');
	},
	mulDel : function() {
		var spaceid;
		spaceid = tableApp.checkedIds();
		if(spaceid.length<1) {
			ct.warn('请选择需要删除的专栏！');
			return;
		} else {
			var msg = '确定删除选中的<b style="color:red">'+spaceid.length+'</b>条记录吗？';
			ct.confirm(msg,function(){
				$.post('?app=space&controller=index&action=delete',{spaceid:spaceid.join(',')},function(json){
					json.state
					 ? (ct.ok('删除完毕'), tableApp.deleteRow(spaceid))
					 : ct.error(json.error);
				},'json');
			}).dialog('option','width',360);
		}
	},
	mulOpen : function() {
		var spaceid;
		spaceid = tableApp.checkedIds();
		if(spaceid.length<1) {
			ct.warn('请选择需要操作的专栏！');
			return;
		} else {
			$.post('?app=space&controller=index&action=status',{spaceid:spaceid.join(','),status:3},function(json){
				json.state
				 ? (ct.ok('操作成功'), tableApp.load())
				 : ct.error(json.error);
			},'json');
		}
	},
	mulRecommend : function() {
		var spaceid;
		spaceid = tableApp.checkedIds();
		if(spaceid.length<1) {
			ct.warn('请选择需要操作的专栏！');
			return;
		} else {
			$.post('?app=space&controller=index&action=status',{spaceid:spaceid.join(','),status:4},function(json){
				json.state
				 ? (ct.ok('操作成功'), tableApp.load())
				 : ct.error(json.error);
			},'json');
		}
	},
	mulBan : function() {
		var spaceid;
		spaceid = tableApp.checkedIds();
		if(spaceid.length<1) {
			ct.warn('请选择需要操作的专栏！');
			return;
		} else {
			$.post('?app=space&controller=index&action=status',{spaceid:spaceid.join(','),status:0},function(json){
				json.state
				 ? (ct.ok('操作成功'), tableApp.load())
				 : ct.error(json.error);
			},'json');
		}
	},
	mulCancel : function() {
		var spaceid;
		spaceid = tableApp.checkedIds();
		if(spaceid.length<1) {
			ct.warn('请选择需要操作的专栏！');
			return;
		} else {
			$.post('?app=space&controller=index&action=status',{spaceid:spaceid.join(','),status:3},function(json){
				json.state
				 ? (ct.ok('操作成功'), tableApp.load())
				 : ct.error(json.error);
			},'json');
		}
	}
}