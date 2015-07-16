//comment JS
//xuxu@ 16:55 2010-2-1
var setStateId = 0;
var ip;
var comment = {
	setTextArea : function(obj,commentid) {
			if($("#content"+setStateId+">textarea").val() != undefined) {
				$.post(
					'?app=comment&controller=comment&action=edit&ajax=1',
					{commentid:commentid,content:$("#content"+setStateId+">textarea").val()},
					function(data){
						$("#content"+setStateId).html(data.conentet);
					},
					'json'
				);
			} else {
				obj.innerHTML = '<textarea onblur="comment.setTextArea($(\'#content'+commentid+'\'),'+commentid+')" rows="5" style="width:100%;padding:3px">'+obj.innerHTML.replace(/<[\/]?span[^>]*>/ig,'')+'</textarea>';
				$("#content"+commentid+">textarea").focus();
				setStateId = commentid;
			}
	},
	get_by_contentid : function(id) {
		tableApp.load('rwkeyword='+id);
		return true;
	},
	multiCheck :function() {
		var ids = tableApp.checkedIds();
		if(ids.length < 1) {
			ct.warn('请选择需要操作的记录！');
		} else {
			ct.confirm(
				'确认审核通过评论？',
				function() {
					$.get(
						'?app=comment&controller=comment&action=check&commentid='+ids,
						{},
						function(json) {
							if(json.state) {
								ct.ok('操作成功');
								tableApp.load();
							} else { 
								ct.error(json.error);
							}
						},
						'json'
					);
				}
			);
		}
	},
	multiDel :function() {
		var ids = tableApp.checkedIds();
		if(ids.length < 1) {
			ct.warn('请选择需要操作的记录！');
		} else {
			ct.confirm(
				'确认删除这'+ids.length+'条评论？',
				function() {
					$.get(
						'?app=comment&controller=comment&action=delete&commentid='+ids,
						{},
						function(json) {
							if(json.state) {
								ct.ok('操作成功');
								tableApp.load();
							} else { 
								ct.error(json.error);
							}
						},
						'json'
					);
				}
			);
		}
	},
	searchDel :function() {
		$.get(
			'?app=comment&controller=comment&action=search_delete',
			$('#search_f').serialize() ,
			function(json) {
				if(json.state) {
					ct.ok('删除成功');
					tableApp.load();
				} else {
					ct.error(json.error);
				}
			},
			'json'
		); 
	},
	check : function(id) {
		$.get(
			'?app=comment&controller=comment&action=check&commentid='+id,
			'',
			function(data) {
				if(data.state) {
					$('#div_'+id).slideToggle("slow",function(){$('#div_'+id).remove();});
				} else {
					ct.error(data.error);
				}
			},
			'json'
		);
	},
	del: function(id) {
		$.get(
			'?app=comment&controller=comment&action=delete&commentid='+id,
			'',
			function(data) {
				if(data.state){
					$('#div_'+id).slideToggle("slow",function() { $('#div_'+id).remove();}); 
				} else {
					ct.error(data.error);
				}
			},
			'json')
	},
	edit :function(id) {
		$('#content'+id).dblclick()
	},
	resetReport : function(id){
		$.get(
			'?app=comment&controller=comment&action=report_reset&commentid='+id,
			'',
			function(data) {
				if(data.state) {
					$('#div_'+id).slideToggle("slow",function(){
							$('#div_'+id).remove();
					});
				} else {
					ct.error(data.error);
				}
			},
			'json'
		)
	},
	sensitive_reset : function(id){
		$.get(
			'?app=comment&controller=comment&action=sensitive_reset&commentid='+id,
			'',
			function(data) {
				if(data.state) {
					$('#div_'+id).slideToggle("slow",function(){
							$('#div_'+id).remove();
					});
				} else {
					ct.error(data.error);
				}
			},
			'json'
		)
	},
	ipEdit : function(id,tr) {
		ct.form(
			'修改IP',
			'?app=comment&controller=comment&action=ip_edit&commentid='+id,360,180,
			function(json) {
				if(json.state) {
					ct.ok('修改成功');
					$('#ip_'+id).html(json.data['ip']).next("span").html('('+json.data['location']+')');
				} else {
					ct.error(json.error);
				}
				return true;
			},
			function(){return true}
		);
	},
	ipDisallow : function(id,tr) {
		ip = tr.find('.ip').html();
		ct.confirm(
			'此操作锁定用户'+ipTime+'小时，确认锁定'+ip+'？',
			function() {
				$.get(
					'?app=comment&controller=comment&action=ip_disallow',
					{"ip":ip,"commentid":id},
					function(json) {
						if(json.state) {
							$("#ip_"+id).attr('title','已锁定');
							$("#ip_"+id).css('color','red');
							$("#ip_"+id).next().css;
						} else { 
							ct.error(json.error);
						}
					},
					'json'
				);
			}
		)
	},
	ipDeleteAll:function(id,tr) {
		ip = tr.find('.ip').html();
		ct.confirm(
			'此操作不可恢复，确认删除<span>'+ip+'</span>所有评论？',
			function() {
				$.get(
					'?app=comment&controller=comment&action=ip_delete',
					{"ip":ip,"commentid":id},
					function(json){
						if(json.state){
							tableApp.load();
						} else {
							ct.error(json.error);
						}
					},
					'json'
				);
			}
		)
	},
	url :function(userid) {
		if(userid > 0) {
			ct.assoc.open('?app=member&controller=index&action=profile&userid='+userid, 'newtab');
		} else {
			return ;
		}
	},
	top : function(id) {
		$.get(
			'?app=comment&controller=comment&action=top&commentid='+id,
			'',
			function(json) {
				if(json.state) {
					ct.ok(json.data);
				} else {
					ct.error(json.error);
				}
			},
			'json'
		);
	},
	canceltop : function(id) {
		$.get(
			'?app=comment&controller=comment&action=canceltop&commentid='+id,
			'',
			function(json) {
				if(json.state) {
					ct.ok(json.data);
				} else {
					ct.error(json.error);
				}
			},
			'json'
		);
	}
}