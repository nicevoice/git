var platform = {
	add: function (id, tr) {
		var url = '?app=pay&controller=platform&action=save';
		var title = '添加接口';
		if(typeof id == 'object' || !id) {
			id = null;
		}
		if(typeof id == 'string') {
			url += '&apiid='+id;
			title = '修改接口';
		}

		ct.form(title, url, 505, 'auto', function (json){
			if (json.state) {
				if(id) {
					tableApp.updateRow(id, json.data);
				} else {
					tableApp.addRow(json.data)
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
			var mul = 1;	//多行删除模式
		}
		if(!id) return ct.warn('请选择要删除的记录');
		ct.confirm('确定删除选中记录？', function(){
			$.getJSON('?app=pay&controller=platform&action=delete&apiid='+id, function(json){
				if (json.state) {
					if(mul) id = null;
					tableApp.deleteRow(id);
				} else {
					ct.error('删除失败！');
				}
			});
		});
	},

	// 启用接口
	enable: function (id) {
		if(typeof id == 'object' || !id) {
			id = tableApp.checkedIds().join(',');
			var mul = 1;	//多行更新模式
		}
		if(!id) return ct.warn('请选择要启用的记录');
		$.getJSON('?app=pay&controller=platform&action=enable&apiid='+id, function(json){
			if (json.state) {
				if(mul) {
					tableApp.load()
				} else {
					tableApp.updateRow(id, json.data);
				}
			} else {
				ct.error('启用失败！');
			}
		});
	},

	// 禁用接口
	disable:function(id) {
		if(typeof id == 'object' || !id) {
			id = tableApp.checkedIds().join(',');
			var mul = 1;	//多行更新模式
		}
		if(!id) return ct.warn('请选择要禁用更新的记录');
		$.getJSON('?app=pay&controller=platform&action=disable&apiid='+id, function(json){
			if (json.state) {
				if(mul) {
					tableApp.load()
				} else {
					tableApp.updateRow(id, json.data);
				}
			} else {
				ct.error('停止更新失败！');
			}
		});
	}
}