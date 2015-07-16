<?php $this->display('header', 'system');?>
<!--tree table-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/treetable/style.css"/>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.treetable.js"></script>
<!--contextmenu-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<div class="bk_10"></div>
<div class="table_head">
  <button onclick="app.addApp()" class="button_style_4 f_l" type="button">添加应用</button>
</div>
<div class="bk_8"></div>
<table width="600" id="treeTable" class="table_list treeTable mar_l_8" cellpadding="0" cellspacing="0">
  <thead>
    <tr>
      <th class="bdr_3">权限名称</th>
      <th width="290">权限标识</th>
      <th width="60">管理操作</th>
    </tr>
  </thead>
  <tbody>
  </tbody>
</table>
<ul id="aca_app_menu" class="contextMenu">
   <li class="edit"><a href="#edit">编辑</a></li>
   <li class="new"><a href="#addController">添加控制器</a></li>
   <li class="delete"><a href="#del">删除</a></li>
</ul>
<ul id="aca_controller_menu" class="contextMenu">
   <li class="edit"><a href="#edit">编辑</a></li>
   <li class="new"><a href="#addAction">添加动作</a></li>
   <li class="delete"><a href="#del">删除</a></li>
</ul>
<ul id="aca_action_menu" class="contextMenu">
   <li class="edit"><a href="#edit">编辑</a></li>
   <li class="delete"><a href="#del">删除</a></li>
</ul>
<script type="text/javascript">
(function(){
var rowTemplate = '\
<tr id="row_{acaid}">\
	<td>{name}</td>\
	<td style="overflow:hidden">{aca}</td>\
	<td class="t_r">\
		<img class="hand" height="16" width="16" src="images/add_1.gif" alt="添加" />\
		<img class="hand edit" height="16" width="16" src="images/edit.gif" alt="编辑" />\
		<img class="hand del" height="16" width="16" src="images/delete.gif" alt="删除" />\
	</td>\
</tr>';
var menu_table = {0:'#aca_app_menu',1:'#aca_controller_menu',2:'#aca_action_menu'};
var title_table = {0:'应用程序',1:'控制器',2:'动作'};
var addrow = function(json){
	if (json.state)
	{
		tree.addRow(json.data);
		return true;
	}
};
var func = {
	edit:function(id,tr,json){
		ct.form('编辑'+title_table[json.type],'?app=system&controller=aca&action=edit&acaid='+id,
		380,180,
		function(json){
			if (json.state)
			{
				tr = tree.updateRow(id,json.data);
				if (json.child && json.child.length)
				{
					tr.getDescendants().remove();
					for(var i=0,l;l=json.child[i++];tree.addRow(l)){}
				}
				return true;
			}
		});
	},
	addApp:function(){
		ct.form('添加应用','?app=system&controller=aca&action=add',
		300,180,addrow);
	},
	addController:function(id,tr,json){
		ct.form('添加控制器','?app=system&controller=aca&action=add&parentid='+id,
		300,180,addrow);
	},
	addAction:function(id,tr,json){
		ct.form('添加动作','?app=system&controller=aca&action=add&parentid='+id,
		380,180,addrow);
	},
	del:function(id,tr){
		ct.confirm('此操作不可恢复，且级联删除，确定要删除吗？',function(){
			$.getJSON('?app=system&controller=aca&action=delete&acaid='+id, function(json){
				if (json.state) {
					tree.deleteRow(id);
					ct.tips('删除完成','success');
				} else {
					ct.error(json.error);
				}
			});
		});
	}
};
var returnFalse = function(){return false};
var tree = new ct.treeTable('#treeTable',{
	idField:'acaid',
	treeCellIndex:0,
	template:rowTemplate,
	collapsed:true,
	baseUrl:'?app=system&controller=aca&action=tree',
	rowReady:function(id,tr,json)
	{
		var editRow = function(){func.edit(id,tr,json)};
		var edit = tr.find('img.edit').click(editRow);
		var add = edit.prev();
		if (json.type==2) {
			add.remove();
		} else if(json.type==1) {
			add.click(function(){func.addAction(id,tr,json)}).dblclick(returnFalse);
		} else {
			add.click(function(){func.addController(id,tr,json)}).dblclick(returnFalse);
		}
		edit.next().click(function(){func.del(id,tr,json)}).dblclick(returnFalse);
		tr.dblclick(editRow);
		tr.contextMenu(menu_table[json.type], function(action) {
			func[action](id, tr, json);
		});
	}
});
window.app = func;
tree.load();
})();
</script>
</body>
</html>