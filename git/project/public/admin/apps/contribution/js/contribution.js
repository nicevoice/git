//稿件
var baseUrl = '?app=contribution&controller=index';
var contribution = {
	__common : function(contributionid,action) {
		if (contributionid === undefined) {
			contributionid = tableApp.checkedIds();
			var msg = '确定操作选中的<b style="color:red">'+contributionid.length+'</b>条记录吗？';
			var ids = contributionid.join(',');
		} else {
			var msg = '确定操作编号为<b style="color:red">'+contributionid+'</b>的记录吗？';
			var ids = contributionid;
		}
		if (contributionid.length === 0) {
			ct.warn('请选择要操作的记录');
			return false;
		}
		ct.confirm(msg, function(){
					$.post(
						baseUrl+'&action='+action,
						{contributionid:ids},
						function(data){
							if (data.state) {
								if (window.tableApp) {
									tableApp.deleteRow(contributionid);
								}
								ct.ok('操作成功');
							} else {
								ct.error(data.error);
							}
						}, 
						'json')
				});
	},
	reject : function(contributionid) {
		contribution.__common(contributionid, 'reject');
	},
	remove : function(contributionid) {
		contribution.__common(contributionid, 'remove');
	},
	del : function(contributionid) {
		contribution.__common(contributionid, 'delete');
	},
	view :  function(contributionid) {
		ct.assoc.open(baseUrl+'&action=view&contributionid='+contributionid, 'newtab');
	},
	clear :function() {
		ct.confirm('确认清空回收站', function(){
					$.getJSON(
						baseUrl+'&action=clear',
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
	publish : function(contributionid) {
		ct.assoc.open(baseUrl+'&action=add&contributionid='+contributionid, 'newtab');
	}
}