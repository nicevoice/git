var cdn = {
	add : function() {
		ct.formDialog('添加项目', '?app=cdn&controller=cdn&action=add', function(json) {
			if (json.state) {
				tableApp.reload();
				return true;
			} else {
				ct.error(json.error);
			}
		});
	},

	edit : function(cdnid) {
		ct.formDialog('修改项目', '?app=cdn&controller=cdn&action=edit&cdnid=' + cdnid, function(json) {
			if (json.state) {
				tableApp.reload();
				return true;
			} else {
				ct.error(json.error);
			}
		});
	},

	del : function(cdnid) {
		$.getJSON('?app=cdn&controller=cdn&action=del&cdnid=' + cdnid, function(json) {
			if (json.state) {
				ct.ok('删除成功');
				tableApp.reload();
			} else {
				ct.error(json.error);
			}
		});
	},

	add_rules : function() {
		ct.formDialog('添加规则', '?app=cdn&controller=rules&action=add', function(json) {
			if (json.state) {
				rules_tableApp.reload();
				return true;
			} else {
				ct.error(json.error);
			}
		});
	},

	edit_rules : function(id) {
		ct.formDialog('修改规则', '?app=cdn&controller=rules&action=edit&id='+id, function(json) {
			if (json.state) {
				rules_tableApp.reload();
				return true;
			} else {
				ct.error(json.error);
			}
		});
	},

	delete_rules : function(id) {
		$.getJSON('?app=cdn&controller=rules&action=delete&id=' + id, function(json) {
			if (json.state) {
				ct.ok('删除成功');
				rules_tableApp.reload();
			} else {
				ct.error(json.error);
			}
		});
	},

	add_type : function() {
		ct.formDialog('添加类型', '?app=cdn&controller=setting&action=add', function(json) {
			if (json.state) {
				tableApp.reload();
				return true;
			} else {
				ct.error(json.error);
			}
		});
	},

	edit_type : function(tid) {
		ct.formDialog('修改类型', '?app=cdn&controller=setting&action=edit&tid='+tid, function(json) {
			if (json.state) {
				tableApp.reload();
				return true;
			} else {
				ct.error(json.error);
			}
		});
	},

	delete_type : function(tid) {
		$.getJSON('?app=cdn&controller=setting&action=delete&tid=' + tid, function(json) {
			if (json.state) {
				ct.ok('删除成功');
				tableApp.reload();
			} else {
				ct.error(json.error);
			}
		});
	}
}