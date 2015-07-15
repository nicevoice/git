var dms	= {
	add_app : function() {
		ct.formDialog('添加应用', '?app=dms&controller=app&action=add', function(json) {
			if (json.state) {
				tableApp.reload();
				return true;
			} else {
				ct.error(json.error);
			}
		});
	},
	edit_app : function(id) {
		ct.formDialog('编辑应用', '?app=dms&controller=app&action=edit&id='+id, function(json) {
			if (json.state) {
				tableApp.reload();
				return true;
			} else {
				ct.error(json.error);
			}
		});
	},
	del_app : function(id) {
		$.getJSON('?app=dms&controller=app&action=delete&id='+id, function(json) {
			if (json.state) {
				ct.ok('删除成功');
				tableApp.reload();
			} else {
				ct.error(json.error);
			}
		});
	}
}