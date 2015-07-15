  <div id="created_x" class="th_pop" style="display:none;width:150px">
     <div>
        <a href="javascript: tableApp.load('created_min=<?=date('Y-m-d', TIME)?>');">今日</a> |
        <a href="javascript: tableApp.load('created_min=<?=date('Y-m-d', strtotime('yesterday'))?>&created_max=<?=date('Y-m-d', strtotime('yesterday'))?>');">昨日</a> | 
        <a href="javascript: tableApp.load('created_min=<?=date('Y-m-d', strtotime('last monday'))?>');">本周</a> | 
        <a href="javascript: tableApp.load('created_min=<?=date('Y-m-01', strtotime('this month'))?>');">本月</a>
     </div>
     <ul>
       <?php 
       for ($i=2; $i<=7; $i++) { 
       	  $createdate = date('Y-m-d', strtotime("-$i day"));
       ?>
        <li><a href="javascript: tableApp.load('created_min=<?=$createdate?>&created_max=<?=$createdate?>');"><?=$createdate?></a></li>
       <?php } ?>
     </ul>
  </div>
  <div id="createdby_x" class="th_pop" style="display:none;width:150px">
  <table>
    <tr>
       <td><input type="text" id="createdbyname" size="12" /></td><td><input type="button" value="查询" class="button_style_1" onclick="tableApp.load('createdbyname='+$('#createdbyname').val());"/></td>
    </tr>
  </table>
  </div>
  <?php $this->display('workflow/list_inc', 'system');?>
  <table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8">
    <thead>
      <tr>
        <th width="30" class="t_l bdr_3"><input type="checkbox" id="check_all" /></th>
        <th>标题</th>
        <th width="60">管理操作</th>
<?php if ($workflowid && $_roleid < 3){?>
        <th width="100" class="ajaxer"><em class="more_pop" name="workflow_x"></em><div name="workflow_roleid">工作流</div></th>
<?php }?>
        <th width="80" class="ajaxer"><em class="more_pop" name="createdby_x"></em><div name="createdby">送审人</div></th>
        <th width="120" class="ajaxer"><em class="more_pop" name="created_x"></em><div name="created">送审时间</div></th>
      </tr>
    </thead>
    <tbody id="list_body">
    </tbody>
  </table>
  <div class="table_foot">
    <div id="pagination" class="pagination f_r"></div>
    <p class="f_l">
<?php 
if (priv::aca('interview', 'interview', 'remove')){
?>
      <input type="button" name="remove" onclick="interview.remove();" value="删除" class="button_style_1"/>
<?php 
}
if ($workflowid){
	if (priv::aca('interview', 'interview', 'pass')){
	?>
	      <input type="button" name="pass" onclick="interview.pass();" value="送审" class="button_style_1"/>
	<?php 
	}
	if (priv::aca('interview', 'interview', 'reject')){
	?>
	      <input type="button" name="reject" onclick="interview.reject();" value="退稿" class="button_style_1"/>
	<?php 
	}
}
elseif (priv::aca('interview', 'interview', 'approve')) 
{
?>
    <input type="button" name="approve" onclick="interview.approve();" value="送审" class="button_style_1"/>
<?php 
}
if (priv::aca('interview', 'interview', 'move')){
?>
      <input type="button" name="move" onclick="interview.move();" value="移动" class="button_style_1"/>
<?php 
}

if (priv::aca('interview', 'interview', 'reference')){
?>
      <input type="button" name="reference" onclick="interview.reference();" value="引用" class="button_style_1"/>
<?php 
}
?>
    </p>
  </div>
  <!--右键菜单-->
  <ul id="right_menu" class="contextMenu">
<?php 
if (priv::aca('interview', 'interview', 'view')){
?>
    <li class="view"><a href="#interview.view">查看</a></li>
<?php 
}
if (priv::aca('interview', 'interview', 'edit')){
?>
    <li class="edit"><a href="#interview.edit">编辑</a></li>
<?php 
}
if (priv::aca('interview', 'interview', 'remove')){
?>
    <li class="remove"><a href="#interview.remove">删除</a></li>
<?php 
}
if ($workflowid){
	if (priv::aca('interview', 'interview', 'pass')){
	?>
	    <li class="pass"><a href="#interview.pass">送审</a></li>
	<?php 
	}
	if (priv::aca('interview', 'interview', 'reject')){
	?>
	    <li class="reject"><a href="#interview.reject">退稿</a></li>
	<?php 
	}
}
elseif (priv::aca('interview', 'interview', 'approve')) 
{
?>
    <li class="approve"><a href="#interview.approve">送审</a></li>
<?php 
}
if (priv::aca('interview', 'interview', 'move')){
?>
    <li class="move separator"><a href="#interview.move">移动</a></li>
<?php 
}

if (priv::aca('interview', 'interview', 'reference')){
?>
    <li class="reference"><a href="#interview.reference">引用</a></li>
<?php 
}
?>
    <li class="note separator"><a href="#interview.note">批注</a></li>
    <li class="version"><a href="#interview.version">版本</a></li>
    <li class="log"><a href="#interview.log">日志</a></li>
  </ul>

<script type="text/javascript">
var action = '';
<?php 
if (priv::aca('interview', 'interview', 'edit')){
?>
action += '<img src="images/edit.gif" alt="编辑" width="16" height="16" class="hand edit"/> &nbsp;';
<?php 
}
if (priv::aca('interview', 'interview', 'remove')){
?>
action += '<img src="images/delete.gif" alt="删除" width="16" height="16" class="hand delete" />';
<?php 
}
?>
var row_template = '<tr id="row_{contentid}">\
	                 	<td><input type="checkbox" class="checkbox_style" name="chk_row" id="chk_row_{contentid}" value="{contentid}" /></td>\
	                    <td>{thumb_icon}<a href="javascript: interview.view({contentid});" style="color:{color}" tips="ID：{contentid}<br />来源：{source_name}<br />Tags：{tags}<br />创建：{createdbyname}（{created}）<br />修改：{modifiedbyname}（{modified}）<br />审核：{checkedbyname}（{checked}）" class="title_list">{title}</a> {note} {lock}</td>\
	                	<td class="t_c">'+action+'</td>\
<?php if ($workflowid && $_roleid < 3){?>
	                 	<td class="t_c"><a href="javascript: url.role({workflow_roleid});">{workflow_rolename}</a></td>\
<?php }?>
	                	<td class="t_c"><a href="javascript: url.member({createdby});">{createdbyname}</a></td>\
	                	<td class="t_c">{created}</td>\
	               </tr>';
</script>