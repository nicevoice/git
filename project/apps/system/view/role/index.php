<?php $this->display('header', 'system');?>

<!--tree table-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/treetable/style.css"/>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.treetable.js"></script>

<!--contextmenu-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.contextMenu.js"></script>

<!--tablesorter-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.table.js"></script>

<!--pagination-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.pagination.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/dropdown/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.dropdown.js"></script>

<script type="text/javascript" src="js/cmstop.tabnav.js"></script>
<script type="text/javascript">

var App = function(){
var row_template = 
'<tr id="row_{roleid}">\
	<td class="t_r">{roleid}</td>\
	<td><a class="role" href="javascript:;">{name}</a></td>\
	<td class="t_r">{admincount}</td>\
	<td class="t_c">\
	   <img class="manage edit" height="16" width="16" alt="编辑" src="images/edit.gif"/>\
       <img class="manage delete" height="16" width="16" alt="删除" src="images/delete.gif"/>\
	</td>\
</tr>',
init_row_event = function(id, tr){
    tr.find('>td>img.edit, a.role').click(function(){
        a.edit(id, tr);
    });
    tr.find('>td>img.delete').click(function(){
        a.del(id);
    });
},
rowTemplate = '\
<tr id="acarow_{acaid}">\
	<td class="t_l">\
		<label><input type="checkbox" class="checkbox_style" name="acaid[]" value="{acaid}" /> {name}</label>\
	</td>\
</tr>',
treeOptions = {
    idField:'acaid',
	treeCellIndex:0,
	template:rowTemplate,
	rowIdPrefix:'acarow_',
	collapsed:1,
	baseUrl:'?app=system&controller=role&action=acatree',
	rowReady:function(id,tr,json)
	{
		var checkbox = tr.find('input:checkbox');
		if (json.disabled)
		{
			checkbox.remove();
			return;
		}
		checkbox.click(function(){
			this.checked
			  ? tr.getDescendants().find('input:checkbox').attr('disabled', true).attr('checked', true)
			  : tr.getDescendants().find('input:checkbox').removeAttr('disabled').attr('checked', false);
		});
		json.checked && checkbox.attr('checked',true);
		var p = tr.data('parentTr');
		p && p.find('input:checkbox').is(':checked,:disabled')
		  && checkbox.attr('checked', true).attr('disabled', true);
	}
},
editUrl,delUrl,addUrl,
a = {
    table:null,
    init:function(baseUrl){
        editUrl = baseUrl+'&action=edit';
        addUrl = baseUrl+'&action=add';
        delUrl = baseUrl+'&action=delete';
        a.table = new ct.table('#item_list',{
            dblclickHandler : a.edit,
            rowCallback : init_row_event,
            template : row_template,
            baseUrl : baseUrl+'&action=page'
        });
        a.table.load();
        
        $('select[name=departmentid]').dropdown({
			departmentid:{
				onchange:function(val){
					a.table.load('departmentid='+val);
				}
			}
		});
    },
    edit:function(id, tr){
        var url = editUrl+'&roleid='+id;
        tr.trigger('check');
        ct.form('编辑角色', url, 420, 'auto', function(json){
            if (json.state)
        	{
        		a.table.updateRow(id, json.data);
	            return true;
        	}
        },function(form,dialog){
        	var table = dialog.find('table.treeTable');
        	var tree = new ct.treeTable(table, $.extend({rowsPrepared:function(){
        		dialog.dialog('option', 'position', 'center');
        	}},treeOptions));
			tree.load('roleid='+id);
        });
    },
    add:function(){
        ct.form('添加角色',addUrl, 420, 'auto',function(json){
        	if (json.state)
        	{
	            a.table.addRow(json.data);
	            return true;
        	}
        },function(form,dialog){
        	var table = dialog.find('table.treeTable');
        	var tree = new ct.treeTable(table, $.extend({rowsPrepared:function(){
        		dialog.dialog('option', 'position', 'center');
        	}},treeOptions));
			tree.load();
        });
    },
	clone:function(id) {
		var url = addUrl+'&roleid='+id;
		ct.form('添加角色',url, 420, 'auto',function(json){
        	if (json.state)
        	{
				a.table.addRow(json.data);
	            return true;
        	}
        },function(form,dialog){
        	var table = dialog.find('table.treeTable');
			var tree = new ct.treeTable(table, $.extend({rowsPrepared:function(){
        		dialog.dialog('option', 'position', 'center');
        	}},treeOptions));
			tree.load('roleid='+id);
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
</script>
</head>
<body>
<div class="bk_10"></div>
<div class="table_head">
  <button onclick="App.add()" class="button_style_2 f_l" type="button">添加</button>
  <div class="f_l"><?=element::department_dropdown('departmentid', null, ' style="width:220px"', $tips = '所有部门')?></div>
</div>
<div class="bk_5"></div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8">
  <thead>
    <tr>
      <th width="50" class="bdr_3 sorter"><div>ID</div></th>
      <th class="sorter"><div>角色名称</div></th>
      <th width="100">管理员数</th>
      <th width="100">管理操作</th>
    </tr>
  </thead>
  <tbody></tbody>
</table>
<!--右键菜单-->
<ul id="right_menu" class="contextMenu">
   <li class="edit"><a href="#App.edit">编辑</a></li>
   <li class="delete"><a href="#App.del">删除</a></li>
   <li class="clone"><a href="#App.clone">复制</a></li>
</ul>
<script type="text/javascript">
App.init('?app=<?=$app?>&controller=<?=$controller?>');
</script>
</body>
</html>