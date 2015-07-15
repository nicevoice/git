var topic = {
	add: function (id, tr) {
		var url = '?app=comment&controller=comment&action=topic_add';
		var title = '添加话题';
		if(typeof id == 'object' || !id) {
			id = null;
		}
		if(typeof id == 'string') {
			url += '&tid='+id;
			title = '修改话题';
		}

		ct.form(title, url, 505, 'auto', function (json){
			if (json.state) {
				if(id) {
					tableApp.updateRow(id, json.data);
				} else {
					tableApp.addRow(json.data);
				}
				return true;
			} else {
				ct.error(json.error);
				return false;
			}
		});
	},

	//单行或多行删除
	del: function (id) {
		if(typeof id == 'object' || !id) {
			id = tableApp.checkedIds().join(',');
		}
		if(!id) return ct.warn('请选择要删除的记录');
		ct.confirm('确定删除选中记录？', function(){
			$.getJSON('?app=comment&controller=comment&action=topic_del&id='+id, function(json){
				if (json.state) {
					tableApp.deleteRow(id);
				} else {
					ct.error('删除失败！');
				}
			});
		});
	},

	// 话题开启
	enable: function (id) {
		if(typeof id == 'object' || !id) {
			id = tableApp.checkedIds().join(',');
		}
		if(!id) return ct.warn('请选择要开启的记录');
		$.getJSON('?app=comment&controller=comment&action=topic_enable&id='+id, function(json){
			if (json.state) {
				tableApp.load();
			} else {
				ct.error('开启失败！');
			}
		});
	},

	// 话题关闭
	disable:function(id) {
		if(typeof id == 'object' || !id) {
			id = tableApp.checkedIds().join(',');
		}
		if(!id) return ct.warn('请选择要禁用的记录');
		$.getJSON('?app=comment&controller=comment&action=topic_disable&id='+id, function(json){
			if (json.state) {
				tableApp.load();
			} else {
				ct.error('禁用失败！');
			}
		});
	}
}