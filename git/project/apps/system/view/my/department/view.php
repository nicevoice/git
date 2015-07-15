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

<script type="text/javascript" src="js/cmstop.tabnav.js"></script>

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
	    </tr>
	  </thead>
	  <tbody></tbody>
	</table>
</div>

<div class="part">
	<table width="98%" id="user_list" cellpadding="0" cellspacing="0"  class="tablesorter table_list mar_l_8" style="empty-cells:show;">
	  <thead>
	    <tr>
	      <th width="60" class="bdr_3 ajaxer"><div name="userid">ID</div></th>
	      <th>用户名</th>
	      <th>姓名</th>
	      <th width="120">角色</th>
	      <th width="150">部门</th>
	      <th width="70">状态</th>
	    </tr>
	  </thead>
	  <tbody>
	  </tbody>
	</table>
	<div class="table_foot">
		<div id="user_pager" class="pagination f_r"></div>
	</div>
</div>
<script type="text/javascript">
(function(){
var rowTemplate = '\
<tr id="departmentrow_{departmentid}">\
	<td>{name}</td>\
	<td>{rolename}</td>\
	<td class="t_r">{numpeople}</td>\
</tr>';
var returnFalse = function(){return false};
var tree = new ct.treeTable('#treeTable',{
	idField:'departmentid',
	treeCellIndex:0,
	rowIdPrefix:'departmentrow_',
	template:rowTemplate,
	baseUrl:'?app=system&controller=department&action=mytree',
	rowReady:function(id,tr,json) {
		json.isrole && tr.addClass('role');
	}
});
tree.load();
})();

var User = function(){
var row_template = 
'<tr id="user_{userid}">\
	<td class="t_r">{userid}</td>\
	<td><a href="javascript: url.member({userid});">{username}</a></td>\
	<td>{name}</td>\
	<td class="t_c">{role}</td>\
	<td class="t_c">{department}</td>\
	<td class="t_c">{state}</td>\
</tr>',
a = {
    table:null,
    init:function(baseUrl, pageSize){
        a.table = new ct.table('#user_list',{
            pageSize: 15,
            template : row_template,
			pagerId:'user_pager',
			rowIdPrefix:'user_',
            baseUrl : baseUrl+'&action=mypage'
        });
        a.table.load();
    }
};
return a;
}();
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
	User.init('?app=system&controller=administrator');
});
</script>
</body>
</html>