var psn = {
	add:function (){
		ct.form('添加发布点', '?app=system&controller=psn&action=add', 350, 280, function (response){
			if (response.state) {
				tableApp.addRow(response.data);
				ct.ok('操作成功');
				return true;
			} else {
				ct.error(response.error);
				return false;
			}
		});
	},
	edit: function(psnid){
		ct.form('修改发布点', '?app=system&controller=psn&action=edit&psnid='+psnid, 350, 280,
		function (response){
			if (response.state)
			{
				tableApp.updateRow(psnid, response.data);
				ct.ok('操作成功');
				return true;
			}
			else
			{
				ct.error(response.error);
				return false;
			}
		});
	},
	del: function (psnid){
		ct.confirm('确定删除编号为<b style="color:red">'+psnid+'</b>的发布点吗？', function(){
			$.getJSON('?app=system&controller=psn&action=delete&psnid='+psnid, function(response){
				if (response.state)
				{
					tableApp.load();
					ct.ok('删除成功！');
				}
				else
				{
					ct.error(response.error);
				}
			});
		});
	},
	select: function (id, type){
		var $psn = $('#'+id);
		var path = $psn.val();
		var url = '?app=system&controller=psn&action=select&type='+type+'&path='+encodeURIComponent(path);
		var dialog = ct.iframe({
				title:url, width:500, height:348
			}, {
			ok:function(pos){
				pos = /({psn:\d+})(.*)/i.exec(pos);
				var val = [pos[1]];
				
				var path = pos[2].replace(/(%2f)|(\\+)/ig,'/').replace(/^\/+|\/+$/g,'').replace(/\/+/g,'/');
				path && val.push(path);
				
				$psn.val(val.join('/'));
				
				dialog.dialog("close");
			},
			cancel:function(){
				dialog.dialog("close");
			}
		});
	}
}