var wechat = {
	add : function() {
		ct.formDialog('添加项目', '?app=wechat&controller=setting&action=add', function(json) {
			if (json.state) {
				tableApp.reload();
				return true;
			} else {
				ct.error(json.error);
			}
		});
	},

	edit : function(wechatid) {
		ct.formDialog('修改项目', '?app=wechat&controller=setting&action=edit&wechatid=' + wechatid, function(json) {
			if (json.state) {
				tableApp.reload();
				return true;
			} else {
				ct.error(json.error);
			}
		});
	},

	del : function(wechatid) {
		$.getJSON('?app=wechat&controller=setting&action=delete&wechatid=' + wechatid, function(json) {
			if (json.state) {
				ct.ok('删除成功');
				tableApp.reload();
			} else {
				ct.error(json.error);
			}
		});
	}

}