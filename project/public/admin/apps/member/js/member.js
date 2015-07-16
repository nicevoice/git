//member
var member = {
	add : function(){
		ct.form('添加用户','?app=member&controller=index&action=add',360,200,
			function(json){
				if(json.state) {
					ct.ok('添加成功');
					tableApp.load();
					return true;
				} else {
					ct.error(json.error);
				}
			},
			function(){return true});
	},
	edit : function(userid) {
		ct.form('修改用户资料','?app=member&controller=index&action=edit&userid='+userid,410,400,function(json) {
			if(typeof(tableApp) != 'undefined') tableApp.updateRow(userid, json.data);
			return true;
		},function(){return true});
	},
	password : function(userid) {
		ct.form('修改用户密码','?app=member&controller=index&action=password&userid='+userid,350,160,function(json) {
			if(json.state) {
				if(typeof(tableApp) != 'undefined') tableApp.updateRow(userid, json.data);
				return true;
			} else {
				ct.tips(json.error);
			}
		},function(){return true});
	},
	avatar : function(userid) {
		ct.form('修改用户头像','?app=member&controller=index&action=avatar&userid='+userid,300,250,function(json) {
			if(json.state) {
				ct.ok('修改成功');
				return true;
			} else {
				ct.tips(json.error);
			}
		},function(){return true});
	},
	del : function(userid) {
		var msg = '确定删除该用户吗？';
		ct.confirm(msg,function(){
			$.post('?app=member&controller=index&action=delete',{userid:userid},function(json){
				json.state
				 ? (ct.ok('删除完毕'), tableApp.deleteRow(userid))
				 : ct.error(json.error);
			},'json');
		}).dialog('option','width',360);
	},
	view : function(userid) {
		window.location = "?app=member&controller=index&action=profile&userid="+userid;
	},
	showGroup : function(groupid) {
		$('#dropdown_'+groupid).click();
	},
	sendmail: function(userid) {
		var url = '?app=member&controller=index&action=sendmail&userid='+userid;
		ct.form('发送邮件',url,380,360,function(json){
				if(json.state){
					ct.ok(json.info);
					return true;
				}else{
					return false;
				}
		});
	},
	remarks : function() {
		userid = tableApp.checkedIds();
		if(userid.length<1) {
			ct.warn('请选择需要添加备注的用户！');
			return;
		} else {
			ct.form('添加备注','?app=member&controller=index&action=remarks&userid='+userid,400,200,function(json) {
				tableApp.load();
				return true;
			},function(){return true});
		}
	},
	mulDel : function() {
		var userid;
		userid = tableApp.checkedIds();
		if(userid.length<1) {
			ct.warn('请选择需要删除的用户！');
			return;
		} else {
			var msg = '确定删除选中的<b style="color:red">'+userid.length+'</b>条记录吗？';
			ct.confirm(msg,function(){
				$.post('?app=member&controller=index&action=delete',{userid:userid.join(',')},function(json){
					json.state
					 ? (ct.ok('删除完毕'), tableApp.deleteRow(userid))
					 : ct.error(json.error);
				},'json');
			}).dialog('option','width',360);
		}
	},
	changeGroup : function() {
		var userid;
		userid = tableApp.checkedIds();
		if(userid.length<1) {
			ct.warn('请选择需要用户组修改的用户！');
			return;
		} else {
			ct.form('修改用户组','?app=member&controller=group&action=changegroup&userid='+userid,360,200,function(json) {
				if(json.state) {
					ct.tips('修改完毕','success');
					for(var i =0,l;l=json.data[i++];tableApp.updateRow(l['userid'],l)){}
					
				} else {
					ct.error(json.error);
				}
				return true;
			},function(){return true});
		}
	},
	search: function() {
		ct.ajax('用户搜索', '?app=member&controller=index&action=search', 350, 200, null, function(dialog){
			tableApp.load(dialog.find('form'));
			return true;
		});
	},
	audit : function(userid) {
		if (userid === undefined) {
			userid = tableApp.checkedIds();
			var msg = '确定审核通过选中的<b style="color:red">'+userid.length+'</b>条记录吗？';
			userid = userid.join(',');
		} else {
			var msg = '确定审核通过该用户吗？';
		}
		if(userid.length < 1) {
			ct.warn('请选择需要审核的用户！');
			return;
		} else {
			ct.confirm(msg,function(){
				$.post('?app=member&controller=audit&action=audit',{userid:userid},function(json){
					json.state
					 ? (ct.tips('审核完毕','success'), tableApp.deleteRow(userid))
					 : ct.error(json.error);
				},'json');
			}).dialog('option','width',360);
		}
	},
	editColumn : function(userid) {
		ct.form('修改专栏资料','?app=space&controller=index&action=edit&userid='+userid,500,300,function(json) {
			if(json.state) {
				ct.ok('操作成功');
				return true;
			}
		},function(){return true});
	},
	unlock : function(userid) {
		var msg = '确定解锁该用户吗？';
		ct.confirm(msg,function(){
			$.post('?app=member&controller=index&action=unlock',{userid:userid},function(json){
				json.state
				 ? (ct.ok('解锁完毕'), tableApp.deleteRow(userid))
				 : ct.error(json.error);
			},'json');
		}).dialog('option','width',360);
	}
}