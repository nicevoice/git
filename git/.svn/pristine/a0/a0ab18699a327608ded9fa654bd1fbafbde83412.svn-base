var field = {
	add: function (id, tr) {
		var url = '?app=field&controller=project&action=add';
		var title = '新增方案';
		if(typeof id == 'object' || !id) {
			id = null;
		}
		if(typeof id == 'string') {
			url += '&pid='+id;
			title = '修改方案';
		}

		ct.form(title, url, 400, 250, function (json){
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
			var mul = 1;	//多行删除模式
		}
		if(!id) return ct.warn('请选择要删除的记录');
		ct.confirm('确定删除选中记录？删除后与之相关联的字段将失效', function(){
			$.getJSON('?app=field&controller=project&action=delete&pid='+id, function(json){
				if (json.state) {
					if(mul) id = null;
					tableApp.deleteRow(id);
				} else {
					ct.error('删除失败！');
				}
			});
		});
	}
}