//contribute JS
var baseUrl = '?app=space&controller=panel';
var contribute = {
	view: function (contentid)  {
		window.open(baseUrl+'&action=view&contentid='+contentid);
	},
	del :function(contentid) {
		if (contentid.length === 0) {
			ct.warn('请选择要操作的记录');
			return false;
		}
		var msg = '确定删除编号为<b style="color:red">'+contentid+'</b>的记录吗？';
		ct.confirm(msg, function(){
			$.getJSON(baseUrl+'&action=delete', {contentid: contentid}, function(response){
				if(response.state) {
					tableApp.deleteRow(contentid);
					contribute.num(response.num);
					ct.ok('操作成功');
				} else {
					ct.error(response.error);
				}
			});
		});
	},
	edit :function(contentid) {
		if(contribute.islock(contentid)) {
			ct.error('当前文档已被锁定，无法修改！');
			return false;
		}
		window.open(baseUrl+'&action=edit&contentid='+contentid);
	},
	islock: function (contentid) {
		var r = false;
		$.getJSON('?app=space&controller=panel&action=islock', {'contentid': contentid}, function(response){
			r = response.state;
		});
		return r;
	},
	_common: function (contentid, action) {
		if (contentid.length === 0) {
			ct.warn('请选择要操作的记录');
			return false;
		} else {
			ct.confirm('是否执行操作？',function(){
					$.getJSON(baseUrl+'&action='+action, {contentid: contentid}, function(response){
						if (response.state) {
							tableApp.deleteRow(contentid);
							contribute.num(response.num);
							ct.ok('操作成功');
						} else {
							ct.error(response.error);
						}
					})
				}
			);
		}
	},
	num :function(json) {
		for(var n in json) {
			$('#num_'+n).html(json[n]);
			try {
				if(n == nowPage) $('#pagetotal').html(json[n]);
			} catch(e) { alert(e);}
		}
	}
}