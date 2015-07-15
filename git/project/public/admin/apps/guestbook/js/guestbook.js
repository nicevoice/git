//JavaScript Document
//the js file for guestbook page

var guestbook = {
	view : function(gid) {
		ct.form(
			'查看留言',
			'?app=guestbook&controller=guestbook&action=reply&gid='+gid,
			560,
			380,
			function(json) {
				if(json) {
					ct.ok('操作成功');
				} else {
					ct.error('发生错误');
				}
				return true;
			}
		);
	},
	del : function(gid) {
		if (gid === undefined) {
			gid = tableApp.checkedIds();
			var msg = '确定删除选中的<b style="color:red">'+gid.length+'</b>条记录吗？';
		} else {
			var msg = '确定删除编号为<b style="color:red">'+gid+'</b>的记录吗？';
		}
		if (gid.length === 0) {
			ct.warn('请选择要操作的记录');
			return false;
		}
		ct.confirm(msg, function(){
			$.getJSON('?app=guestbook&controller=guestbook&action=delete&gid='+gid, function(response){
				if (response.state) {
					tableApp.deleteRow(gid);
					ct.ok('操作成功');
				} else {
					ct.error(response.error);
				}
			});
		});
	}
}

