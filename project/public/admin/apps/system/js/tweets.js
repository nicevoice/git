(function(){
var row_template = 
'<tr id="row_{tweetid}">\
	<td class="t_c">{tweetid}</td>\
	<td class="t_l">{name}</td>\
	<td class="t_c">{drivername}</td>\
	<td class="t_l">{username}</td>\
	<td class="t_c">{created}</td>\
	<td class="t_c">{connect}</td>\
	<td class="t_c">\
	   <img class="manage edit" height="16" width="16" alt="编辑" src="images/edit.gif"/>\
       <img class="manage delete" height="16" width="16" alt="删除" src="images/delete.gif"/>\
	</td>\
</tr>',
init_row_event = function(id, tr)
{
    tr.find('>td>img.edit').click(function(){
        App.edit(id, tr);
    });
    tr.find('>td>img.delete').click(function(){
        App.del(id, tr);
    });
    tr.find('>td>.sina').click(function(){
    	App.sina(id, tr);
    });
},
editUrl, delUrl, addUrl, testUrl, sinaUrl, renrenUrl,
App = {
	table: null,
	init : function(baseUrl, dataAction) {
		editUrl = baseUrl + '&action=edit';
		addUrl = baseUrl + '&action=add';
		delUrl = baseUrl + '&action=delete';
		sinaUrl = baseUrl + '&action=sina';
		App.table = new ct.table('#item_list', {
			dblclickHander : App.edit,
			rowCallback : init_row_event,
			template : row_template,
			baseUrl : baseUrl + '&action=' + (dataAction||'page')
		});
		App.table.load();
	},
	add : function() {
		ct.form('添加转发账户', addUrl, 400, 280, function(json) {
			if (json.state) {
				App.table.addRow(json.data);
				return true;
			}
		});
	},
	edit : function(id, tr) {
		ct.form('修改转发账户', editUrl+'&id='+id, 400, 280, function(json) {
			if (json.state) {
				App.table.updateRow(id, json.data);
				return true;
			}
		});
	},
	del : function(id, tr) {
		var msg;
		if (id == undefined) {
			ct.warn('请选中要删除项');
			return;
		} else {
			msg = '确定删除编号为<b style="color:red">'+id+'</b>的记录吗？';
		}
		ct.confirm(msg, function(){
			var data = 'id=' + id;
			$.post(delUrl, data, function(json){
				json.state
				? (ct.ok('删除完毕'), App.table.deleteRow(id))
				: ct.error(json.error);
			}, 'json');
		})
	},
	sina : function(id, tr) {
		$.post(sinaUrl+'&id='+id, '', function(json) {
			if (json.state) {
				document.location.href = json.url;
			} else {
				 ct.error(json.error);
				return false;
			}
		}, 'json');
	}
}
window.App = App;
})();