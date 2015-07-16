(function(){
var rowTemplate = '\
<tr id="departmentrow_{departmentid}">\
	<td>{name}</td>\
	<td>{rolename}</td>\
	<td class="t_r">{numpeople}</td>\
	<td class="t_r">\
		<img class="hand" height="16" width="16" src="images/add_1.gif" alt="编辑" />\
		<img class="hand edit" height="16" width="16" src="images/edit.gif" alt="编辑" />\
		<img class="hand del" height="16" width="16" src="images/delete.gif" alt="删除" />\
	</td>\
</tr>';
var acaTreeTemplate = '\
<tr id="acarow_{acaid}">\
	<td class="t_l">\
		<label><input type="checkbox" class="checkbox_style" name="acaid[]" value="{acaid}" />{name}</label>\
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
			  ? tr.getDescendants().find('input:checkbox').attr('disabled','disabled').attr('checked',true)
			  : tr.getDescendants().find('input:checkbox').removeAttr('disabled');
		});
		json.checked && checkbox.attr('checked',true);
		var p = tr.data('parentTr');
		p && p.find('input:checkbox').is(':checked,:disabled')
		  && checkbox.attr('disabled','disabled');
	}
};
var returnFalse = function(){return false}, tree;
var func = {
	edit:function(id,tr,json) {
		ct.form('编辑部门','?app=system&controller=department&action=myedit&departmentid='+id,
		350,200,function(json){
			if (json.state)
			{
				tree.updateRow(id,json.data);
				return true;
			}
		});
	},
	add:function(id) {
		ct.form('添加部门','?app=system&controller=department&action=myadd&parentid='+id,
		350,200,function(json){
			if (json.state)
			{
				tree.addRow(json.data);
				return true;
			}
		});
	},
	adduser:function(id) {
		User.add(id);
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
		var url = '?app=system&controller=department&action=mySetRole&departmentid='+id;
		ct.form('设置角色', url, 420, 300, function(json){
        	tr.getChildren().filter('.role').remove();
			if (json && json.length) {
    			for (var i=0,l;l=json[i++];) {
    				tree.addRow(l);
    			}
    		}
	        return true;
        });
	},
	del:function(id,tr,json) {
		var url = '?app=system&controller=department&action=mydelete&departmentid='+id;
		ct.form('删除部门', url, 400, 220, function(json){
			if (json.state) {
				if (json.data && json.data.length)
				{
					for(var i=0,l;l=json.data[i++];tree.updateRow(l.departmentid,l)){}
				}
				tree.deleteRow(id);
			}
			return true;
		});
	},
	init:function(){
		tree = new ct.treeTable('#treeTable',{
			idField:'departmentid',
			treeCellIndex:0,
			template:rowTemplate,
			rowIdPrefix:'departmentrow_',
			baseUrl:'?app=system&controller=department&action=mytree',
			rowReady:function(id,tr,json)
			{
				if (json.isrole) {
					var editRow = function(){func.editRole(id,tr,json)};
					tr.addClass('role');
					tr.find('img.hand:lt(2)').remove();
				    tr.find('img.del').click(function(){
				        func.delRole(id,tr,json);
				    }).dblclick(returnFalse);
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
			},
			rowsPrepared:function(tbody) {
				tbody.find('tr:first img.del').remove();
			}
		});
		tree.load();
	}
};
window.app = func;
})();

var User = function(){
var row_template = 
'<tr id="user_{userid}">\
	<td class="t_c">\
	   <input type="checkbox" class="checkbox_style" value="{userid}" />\
	</td>\
	<td class="t_r">{userid}</td>\
	<td><a href="javascript: url.member({userid});">{username}</a></td>\
	<td>{name}</td>\
	<td class="t_c">{role}</td>\
	<td class="t_c">{department}</td>\
	<td class="t_c">{state}</td>\
	<td class="t_c">\
	   <img class="manage edit" height="16" width="16" alt="编辑" src="images/edit.gif"/>\
       <img class="manage delete" height="16" width="16" alt="删除" src="images/delete.gif"/>\
	</td>\
</tr>',
init_row_event = function(id, tr){
    tr.find('>td>img.edit').click(function(){
        a.edit(id, tr);
    });
    tr.find('>td>img.delete').click(function(){
        a.del(id);
    });
},
editUrl,delUrl,addUrl,
a = {
    table:null,
    init:function(baseUrl, pageSize){
        editUrl = baseUrl+'&action=myedit';
        addUrl = baseUrl+'&action=myadd';
        delUrl = baseUrl+'&action=mydelete';
        a.table = new ct.table('#user_list',{
            pageSize: 15,
            dblclickHandler : a.edit,
            rowCallback : init_row_event,
            template : row_template,
			rowIdPrefix:'user_',
			rightMenuId:'user_menu',
			pagerId:'user_pager',
            baseUrl : baseUrl+'&action=mypage'
        });
        a.table.load();
    },
    edit:function(id, tr){
        var url = editUrl+'&userid='+id;
        tr.trigger('check');
        ct.form('编辑管理员', url, 400, 460, function(json){
        	if (json.state) {
	            a.table.updateRow(id, json.data);
	            return true;
        	}
        });
    },
    add:function(id){
        ct.form('添加管理员',addUrl+'&departmentid='+id,400,480,function(json){
        	if (json.state) {
	            a.table.addRow(json.data);
	            return true;
        	}
        },function(form,dialog){
        	dialog.find('select[name=departmentid]').change(function(){
        		dialog.find('select[name=roleid]').parent()
        		.load('?app=system&controller=role&action=dropdown&departmentid='+this.value);
        	});
        });
    },
    del:function(id){
        var msg;
        if (id === undefined)
        {
            id = a.table.checkedIds();
            if (!id.length)
            {
                ct.warn('请选中要删除项');
                return;
            }
            msg = '确定删除选中的<b style="color:red">'+id.length+'</b>条记录吗？';
        }
        else
        {
            msg = '确定删除编号为<b style="color:red">'+id+'</b>的记录吗？';
        }
        ct.confirm(msg,function(){
            $.post(delUrl,'id='+id,
            function(json){
                json.state
                 ? (ct.warn('删除完毕'), a.table.deleteRow(id))
                 : ct.warn(json.error);
            },'json');
        });
    }
};
return a;
}();