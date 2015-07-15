//稿件
var baseUrl = '?app=article&controller=article';
var article = {
	__common : function(contentid,action) {
		if (contentid === undefined) {
			contentid = tableApp.checkedIds();
			var msg = '确定操作选中的<b style="color:red">'+contentid.length+'</b>条记录吗？';
			var ids = contentid.join(',');
		} else {
			var msg = '确定操作编号为<b style="color:red">'+contentid+'</b>的记录吗？';
			var ids = contentid;
		}
		if (contentid.length === 0) {
			ct.warn('请选择要操作的记录');
			return false;
		}
		ct.confirm(msg, function(){
					$.post(
						baseUrl+'&action='+action,
						{contentid:ids},
						function(data){
							if (data.state) {
								if (window.tableApp) {
									tableApp.deleteRow(contentid);
								}
								ct.ok('操作成功');
							} else {
								ct.error(data.error);
							}
						}, 
						'json')
				});
	},
	reject : function(contentid) {
		article.__common(contentid, 'reject');
	},
	remove : function(contentid) {
		//传递catid
		article.__common(contentid, 'remove');
	},
	del : function(contentid) {
		article.__common(contentid, 'delete');
	},
	view :  function(contentid) {
		ct.assoc.open(baseUrl+'&action=view&contentid='+contentid, 'newtab');
	},
	clear :function() {
		//传递 catid
		ct.confirm('确认清空回收站', function(){
					$.getJSON(
						baseUrl+'&action=clear_contribution',
						function(json){
							if (json.state) {
								if (window.tableApp) {
									tableApp.load();
								}
								ct.ok('操作成功');
							} else {
								ct.error(json.error);
							}
						}, 
						'json')
				});
	},
	publish : function(contentid) {
		ct.assoc.open(baseUrl+'&action=edit&contentid='+contentid, 'newtab');
	}
}