//contribution JS
var baseUrl = '?app=contribution&controller=panel';
var contribution = {
	view: function (contributionid)  {
		window.open(baseUrl+'&action=view&contributionid='+contributionid);
	},
	del :function(contributionid) {
		if (contributionid.length === 0) {
			ct.warn('请选择要操作的记录');
			return false;
		}
		var msg = '确定删除吗？';
		ct.confirm(msg, function(){
			$.getJSON(baseUrl+'&action=delete', {contributionid: contributionid}, function(response){
				if(response.state) {
					tableApp.deleteRow(contributionid);
					ct.ok('操作成功');
				} else {
					ct.error(response.error);
				}
			});
		});
	},
	edit :function(contributionid) {
		window.open(baseUrl+'&action=edit&contributionid='+contributionid);
	},
	submit : function(contributionid) {
		contribution._common(contributionid, 'submit','确认投递该文章？');
	},
	cancel : function(contributionid) {
		contribution._common(contributionid, 'cancel','确认取消投递？');
	},
	_common: function (contributionid, action,msg) {
		if (contributionid.length === 0) {
			ct.warn('请选择要操作的记录');
			return false;
		} else {
			ct.confirm(msg,function(){
					$.getJSON(baseUrl+'&action='+action, {contributionid: contributionid}, function(response){
						if (response.state) {
							tableApp.deleteRow(contributionid);
							contribution.num(response.num);
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
			try {
				$('#num_'+n).html(json[n]);
			} catch(e) { }
		}
	}
}