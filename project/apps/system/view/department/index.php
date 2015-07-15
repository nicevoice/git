<?php $this->display('header', 'system');?>

<!--tree table-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/treetable/style.css"/>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.treetable.js"></script>
<!--contextmenu-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<script type="text/javascript" src="apps/system/js/department.js"></script>
<div class="bk_10"></div>
<div class="table_head">
  <button onclick="app.add(0)" class="button_style_2 f_l" type="button">添加</button>
</div>
<div class="bk_8"></div>
<table width="98%" id="treeTable" class="table_list mar_l_8" cellpadding="0" cellspacing="0" style="empty-cells:show;">
  <thead>
    <tr>
      <th class="bdr_3">名称</th>
      <th width="100">主管角色</th>
      <th width="80">人数</th>
      <th width="100">操作</th>
    </tr>
  </thead>
  <tbody></tbody>
</table>
<ul id="department_menu" class="contextMenu">
   <li class="edit"><a href="#edit">编辑</a></li>
   <li class="new"><a href="#add">添加子部门</a></li>
   <li class="edit"><a href="#setRole">设置角色</a></li>
   <li class="delete"><a href="#del">删除</a></li>
</ul>
<ul id="role_menu" class="contextMenu">
   <li class="edit"><a href="#editRole">编辑</a></li>
   <li class="delete"><a href="#delRole">删除</a></li>
</ul>
<script type="text/javascript">
app.init();
</script>
</body>
</html>