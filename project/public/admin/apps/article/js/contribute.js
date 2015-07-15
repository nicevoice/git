//退回稿件
var baseUrl = '?app=article&controller=contribute';
var contribute = {
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
								if (window.tableApp) tableApp.deleteRow(contentid);
								ct.ok('操作成功');
							} else {
								ct.error(data.error);
							}
						}, 
						'json')
				});
	},
	audit : function(contentid){
		contribute.__common(contentid, 'pass');
	},
	reject : function(contentid) {
		contribute.__common(contentid, 'reject');
	},
	view :  function(contentid) {
		ct.assoc.open(baseUrl+'&action=view&contentid='+contentid, 'newtab');
	},
	edit : function(contentid) {
		ct.assoc.open('?app=article&controller=article&action=edit&contentid='+contentid, 'newtab');
	}
}