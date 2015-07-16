var flist = {
	add: function (id, tr) {
		var url = '?app=freelist&controller=freelist&action=add';
		var title = '添加列表';
		if(typeof id == 'object' || !id) {
			id = null;
		}
		if(typeof id == 'string') {
			url += '&flid='+id;
			title = '修改列表';
		}

		ct.form(title, url, 505, 'auto', function (json){
			if (json.state) {
				if(id) {
					tableApp.updateRow(id, json.data);
				} else {
					// 弹出筛选框 并传入数据
					// flag 如果为 true 则表示为新增数据
					var flag = true;
					flist.fadd(json.data.flid, flag)
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
			$.getJSON('?app=freelist&controller=freelist&action=delete&id='+id, function(json){
				if (json.state) {
					if(mul) id = null;
					tableApp.deleteRow(id);
				} else {
					ct.error('删除失败！');
				}
			});
		});
	},

	// 筛选器弹窗
	fadd:function(id, flag) {
		var url = '?app=freelist&controller=freelist&action=fadd&flid=';
		var title = '配置筛选器';
		if(typeof id == 'string') {
			url +=id;
		}
		ct.form(title, url, 500,'auto',function(json) {
			if (json.state) {
				// flag 对应上面的flag
				if(id && !flag) {
					tableApp.updateRow(id, json.data);
				} else {
					tableApp.addRow(json.data);
				}
				return true;
			} else {
				ct.error(json.error);
				return false;
			}
			return true;
		},function(form, dialog) {
			form.find(".modelset").modelset();
			form.find(".selectree").selectree();
			form.find(".suggest").suggest();
		});
	},

	// 开始更新
	update: function (id) {
		if(typeof id == 'object' || !id) {
			id = tableApp.checkedIds().join(',');
			var mul = 1;	//多行更新模式
		}
		if(!id) return ct.warn('请选择要更新的记录');
		$.getJSON('?app=freelist&controller=freelist&action=update&id='+id, function(json){
			if (json.state) {
				if(mul) {
					ct.ok('更新成功！');
					tableApp.load();
				} else {
					ct.ok('更新成功！');
					tableApp.updateRow(id, json.data);
				}
			} else {
				ct.error('更新失败！');
			}
		});
	},

	// 停止自动更新
	stop:function(id) {
		if(typeof id == 'object' || !id) {
			id = tableApp.checkedIds().join(',');
			var mul = 1;	//多行更新模式
		}
		if(!id) return ct.warn('请选择要停止更新的记录');
		$.getJSON('?app=freelist&controller=freelist&action=stop&id='+id, function(json){
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