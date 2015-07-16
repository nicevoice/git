<?php $this->display('header', 'system');?>
<!--tablesorter-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<!--contextmenu-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<!--pagination-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.pagination.js"></script>

<!--tree table-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/treetable/style.css"/>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.treetable.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/dropdown/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.dropdown.js"></script>

<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/suggest/style.css" />
<script src="<?=IMG_URL?>js/lib/cmstop.suggest.js" type="text/javascript"></script>

<script type="text/javascript" src="js/cmstop.tabnav.js"></script>
<script type="text/javascript" src="apps/system/js/administrator.js"></script>
<div class="bk_10"></div>
<div class="table_head">
	<div class="search_icon search f_r mar_r_8">
		<form id="search_f" onsubmit="return false;">
			<input type="text" size="30" value="" name="keywords">
			<a title="搜索" onclick="App.table.load($('#search_f'));return false;" onfocus="this.blur()" style="outline:none;" href="javascript:;">搜索</a>
		</form>
	</div>
	<button type="button" class="button_style_2 f_l" onclick="App.add();  return false;">添加</button>
	<div class="f_l"><?=element::department_dropdown('search_departmentid', null, ' style="width:220px"', $tips = '所有部门')?></div>
	
	<div class="f_l" style="margin-left:10px"><?=element::role_dropdown('search_roleid',null,null,' style="width:150px"','所有角色')?></div>
  
</div>
<div class="bk_8"></div>
<table width="98%" id="item_list" cellpadding="0" cellspacing="0"  class="tablesorter table_list mar_l_8" style="empty-cells:show;">
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
  <div id="pagination" class="pagination f_r"></div>
  <p class="f_l">
    <input type="button" onclick="App.del();" value="删 除" class="button_style_1"/>
  </p>
</div>
<!--右键菜单-->
<ul id="right_menu" class="contextMenu">
   <li class="view"><a href="#url.member">查看资料</a></li>
   <li class="stat"><a href="#App.stat">工作报表</a></li>
   <li class="priv"><a href="#App.priv">查看权限</a></li>
   <li class="priv"><a href="#App.clonepriv">克隆权限</a></li>
   <li class="priv"><a href="#App.score">评分</a></li>
   <li class="edit"><a href="#App.edit">编辑</a></li>
   <li class="delete"><a href="#App.del">删除</a></li>
</ul>
<script type="text/javascript">
$.validate.setConfigs({
    xmlPath:'<?=ADMIN_URL?>apps/<?=$app?>/validators/<?=$controller?>/'
});
App.init('?app=<?=$app?>&controller=<?=$controller?>','<?=$pagesize?>');
</script>
<?php $this->display('footer', 'system');