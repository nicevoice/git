<?php $this->display('header', 'system');?>
<!--tree table-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/treetable/style.css"/>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.treetable.js"></script>

<!--tablesorter-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<!--pagination-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.pagination.js"></script>
<!--contextmenu-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.contextMenu.js"></script>

<script type="text/javascript" src="js/cmstop.tabnav.js"></script>

<script type="text/javascript" src="apps/system/js/my_department.js"></script>

<div class="bk_10"></div>
<div class="tag_1">
  <ul class="tag_list" id="tabnav">
    <li index="0"><a href="javascript:;">部门信息</a></li>
    <li index="1"><a href="javascript:;">部门人员</a></li>
  </ul>
</div>
<div class="bk_8"></div>
<div class="part">
	<table width="98%" id="treeTable" class="table_list mar_l_8" cellpadding="0" cellspacing="0" style="empty-cells:show;">
	  <thead>
	    <tr>
	      <th class="bdr_3 t_c">名称</th>
	      <th width="100">主管角色</th>
	      <th class="t_c" width="80">人数</th>
	      <th width="100" class="t_c">操作</th>
	    </tr>
	  </thead>
	  <tbody></tbody>
	</table>
</div>
<div class="part">
	<table width="98%" id="user_list" cellpadding="0" cellspacing="0"  class="tablesorter table_list mar_l_8" style="empty-cells:show;">
	  <thead>
	    <tr>
	      <th width="30" class="bdr_3 t_c"><input type="checkbox" /></th>
	      <th width="60" class="ajaxer"><div name="userid">ID</div></th>
	      <th>用户名</th>
	      <th>姓名</th>
	      <th width="120">角色</th>
	      <th width="150">部门</th>
	      <th width="70">状态</th>
	      <th width="80">管理操作</th>
	    </tr>
	  </thead>
	  <tbody>
	  </tbody>
	</table>
	<div class="table_foot">
	  <div id="user_pager" class="pagination f_r"></div>
	  <p class="f_l">
	    <input type="button" onclick="User.del();" value="删 除" class="button_style_1"/>
	  </p>
	</div>
</div>
<ul id="department_menu" class="contextMenu">
   <li class="edit"><a href="#edit">编辑</a></li>
   <li class="new"><a href="#add">添加子部门</a></li>
   <li class="edit"><a href="#setRole">设置角色</a></li>
   <li class="new"><a href="#adduser">添加人员</a></li>
   <li class="delete"><a href="#del">删除</a></li>
</ul>
<ul id="role_menu" class="contextMenu">
   <li class="delete"><a href="#delRole">删除</a></li>
</ul>
<ul id="user_menu" class="contextMenu">
   <li class="edit"><a href="#User.edit">编辑</a></li>
   <li class="delete"><a href="#User.del">删除</a></li>
</ul>
<script type="text/javascript">
$(function(){
	var divs = $('div.part');
    $('#tabnav').tabnav({
		dataType:null,
		forceFocus:true,
		focused:function(li){
			divs.hide();
			divs.eq(li.attr('index')).show();
		}
	});
	app.init();
	User.init('?app=system&controller=administrator');
});
</script>
</body>
</html>