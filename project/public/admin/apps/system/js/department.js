(function(){
var rowTemplate = '\
<tr id="row_{departmentid}">\
	<td><a class="clickable" href="javascript:;">{name}</a></td>\
	<td>{rolename}</td>\
	<td class="t_r">{numpeople}</td>\
	<td class="t_r">\
		<img class="hand" height="16" width="16" src="images/add_1.gif" alt="添加" />\
		<img class="hand edit" height="16" width="16" src="images/edit.gif" alt="编辑" />\
		<img class="hand del" height="16" width="16" src="images/delete.gif" alt="删除" />\
	</td>\
</tr>';
var acaTreeTemplate = '\
<tr id="acarow_{acaid}">\
	<td class="t_l">\
		<label><input type="checkbox" class="checkbox_style" name="acaid[]" value="{acaid}" /> {name}</label>\
	</td>\
</tr>';
var acaTreeOptions = {
	idField:'acaid',
	treeCellIndex:0,
	template:acaTreeTemplate,
	rowIdPrefix:'acarow_',
	collapsed:1,
	baseUrl:'?app=system&controller=role&action=acatree',
	rowReady:function(id,tr,json) {
		var checkbox = tr.find('input:checkbox');
		if (json.disabled) {
			checkbox.remove();
			return;
		}
		checkbox.click(function(){
			this.checked
			  ? tr.getDescendants().find('input:checkbox').attr('disabled','disabled').attr('checked', true)
			  : tr.getDescendants().find('input:checkbox').removeAttr('disabled').attr('checked', false);
		});
		json.checked && checkbox.attr('checked',true);
		var p = tr.data('parentTr');
		p && p.find('input:checkbox').is(':checked,:disabled')
		  && checkbox.attr('checked', true).attr('disabled', true);
	}
};
var returnFalse = function(){return false};
var tree = null;
var func = {
	edit:function(id,tr,json) {
		ct.form('编辑部门','?app=system&controller=department&action=edit&departmentid='+id,
		350,200,function(json){
			if (json.state) {
				tree.updateRow(id,json.data);
				return true;
			}
		});
	},
	add:function(id) {
		ct.form('添加部门','?app=system&controller=department&action=add&parentid='+id,
		350,200,function(json){
			if (json.state) {
				tree.addRow(json.data);
				return true;
			}
		});
	},
	del:function(id,tr,json) {
		var url = '?app=system&controller=department&action=delete&departmentid='+id;
		ct.form('删除部门', url, 400, 220, function(json){
			if (json.state) {
				if (json.data && json.data.length) {
					for(var i=0,l;l=json.data[i++];tree.updateRow(l.departmentid,l)){}
				}
				tree.deleteRow(id);
			}
			return true;
		});
	},
	editRole:function(id, tr, json){
		var url = '?app=system&controller=role&action=edit&roleid='+id;
        ct.form('编辑角色', url, 420, 'auto', function(json) {
            if (json.state) {
        		tree.updateRow(id, json.data);
	            return true;
        	}
        }, function(form, dialog){
        	var table = dialog.find('table.treeTable');
        	new ct.treeTable(table, $.extend({rowsPrepared:function(){
        		dialog.dialog('option', 'position', 'center');
        	}}, acaTreeOptions)).load('roleid='+id.split('-')[1]);
        });
	},
	delRole:function(id, tr, json) {
		var msg = '确定删除角色<b style="color:red">'+json.name+'</b>吗？';
		var url = '?app=system&controller=role&action=mydelete&id='+id;
        ct.confirm(msg,function(){
            $.getJSON(url, function(json){
            	if (json.state) {
            		ct.tips('删除完毕','success');
            		tree.deleteRow(id);
            	} else {
            		ct.warn(json.error);
            	}
            });
        });
	},
	setRole:function(id, tr, json){
		var url = '?app=system&controller=department&action=setRole&departmentid='+id;
		ct.form('设置角色', url, 340, 420, function(json){
        	tr.getChildren().filter('.role').remove();
			if (json && json.length) {
    			for (var i=0,l;l=json[i++];) {
    				tree.addRow(l);
    			}
    		}
	        return true;
        });
	},
	init:function(){
		tree = new ct.treeTable('#treeTable',{
			idField:'departmentid',
			collapsed:1,
			treeCellIndex:0,
			template:rowTemplate,
			baseUrl:'?app=system&controller=department&action=tree',
			rowReady:function(id,tr,json) {
				if (json.isrole) {
					var editRow = function(){func.editRole(id,tr,json)};
					tr.addClass('role');
					tr.find('img.edit, a.clickable').click(function(){
				        func.editRole(id, tr);
				    }).dblclick(returnFalse);
				    tr.find('img.hand:first').remove();
				    tr.find('img.del').click(function(){
				        func.delRole(id,tr,json);
				    }).dblclick(returnFalse);
				    tr.dblclick(editRow);
				    tr.contextMenu('#role_menu', function(action) {
						func[action](id, tr, json);
					});
				} else {
					var editRow = function(){func.edit(id,tr,json)};
					tr.find('a.clickable').click(editRow);
					var edit = tr.find('img.edit').click(editRow);
					var add = edit.prev();
					add.click(function(){func.add(id,tr,json)}).dblclick(returnFalse);
					edit.next().click(function(){func.del(id,tr,json)}).dblclick(returnFalse);
					tr.dblclick(editRow);
					tr.contextMenu('#department_menu', function(action) {
						func[action](id, tr, json);
					});
				}
			}
		});
		tree.load();
	}
};
window.app = func;
})();