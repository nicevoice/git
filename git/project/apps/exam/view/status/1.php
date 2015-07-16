	<div id="created_x" class="th_pop" style="display:none;width:150px">
		<div>
			<a href="javascript: tableApp.load('created_min=<?=date('Y-m-d', TIME)?>');">今日</a> |
			<a href="javascript: tableApp.load('created_min=<?=date('Y-m-d', strtotime('yesterday'))?>&created_max=<?=date('Y-m-d', strtotime('yesterday'))?>');">昨日</a> | 
			<a href="javascript: tableApp.load('created_min=<?=date('Y-m-d', strtotime('last monday'))?>');">本周</a> | 
			<a href="javascript: tableApp.load('created_min=<?=date('Y-m-01', strtotime('this month'))?>');">本月</a>
		</div>
		<ul>
			<?php for ($i=2; $i<=7; $i++) { 
			$createdate = date('Y-m-d', strtotime("-$i day"));?>
			<li><a href="javascript: tableApp.load('created_min=<?=$createdate?>&created_max=<?=$createdate?>');"><?=$createdate?></a></li>
			<?php } ?>
		</ul>
	</div>
	<div id="published_x" class="th_pop" style="display:none;width:150px">
		<div>
			<a href="javascript: tableApp.load('published_min=<?=date('Y-m-d', TIME)?>');">今日</a> |
			<a href="javascript: tableApp.load('published_min=<?=date('Y-m-d', strtotime('yesterday'))?>&published_max=<?=date('Y-m-d', strtotime('yesterday'))?>');">昨日</a> | 
			<a href="javascript: tableApp.load('published_min=<?=date('Y-m-d', strtotime('last monday'))?>');">本周</a> | 
			<a href="javascript: tableApp.load('published_min=<?=date('Y-m-01', strtotime('this month'))?>');">本月</a>
		</div>
		<ul>
			<?php for ($i=2; $i<=7; $i++) {
				$publishdate = date('Y-m-d', strtotime("-$i day")); ?>
				<li><a href="javascript: tableApp.load('published_min=<?=$publishdate?>&published_max=<?=$publishdate?>');"><?=$publishdate?></a></li>
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
	<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8">
		<thead>
		<tr>
			<th width="30" class="t_l bdr_3"><input type="checkbox" id="check_all" class="checkbox_style"/></th>
			<th>标题</th>
			<th width="60">管理操作</th>
			<th width="80" class="ajaxer"><em class="more_pop" name="createdby_x"></em><div name="createdby">创建人</div></th>
			<th width="120" class="ajaxer"><em class="more_pop" name="created_x"></em><div name="created">创建时间</div></th>
		</tr>
		</thead>
		<tbody id="list_body"></tbody>
	</table>
  <div class="table_foot">
    <div id="pagination" class="pagination f_r"></div>
    <p class="f_l">
<?php 
if (priv::aca('exam', 'exam', 'remove')){
?>
      <input type="button" name="remove" onclick="exam.remove();" value="删除" class="button_style_1"/>
<?php 
}
if (priv::aca('exam', 'exam', 'approve')){
?>
      <input type="button" name="approve" onclick="exam.approve();" value="送审" class="button_style_1"/>
<?php 
}
if (priv::aca('exam', 'exam', 'move')){
?>
      <input type="button" name="move" onclick="exam.move();" value="移动" class="button_style_1"/>
<?php 
}

if (priv::aca('exam', 'exam', 'reference')){
?>
      <input type="button" name="reference" onclick="exam.reference();" value="引用" class="button_style_1"/>
<?php 
}
?>
    </p>
  </div>
  <!--右键菜单-->
  <ul id="right_menu" class="contextMenu">
<?php 
if (priv::aca('exam', 'exam', 'view')){
?>
    <li class="view"><a href="#exam.view">查看</a></li>
<?php 
}
if (priv::aca('exam', 'exam', 'edit')){
?>
    <li class="edit"><a href="#exam.edit">编辑</a></li>
<?php 
}
if (priv::aca('exam', 'exam', 'remove')){
?>
    <li class="remove"><a href="#exam.remove">删除</a></li>
<?php 
}
if ($_roleid < 3 || !table('category', $catid, 'workflowid')){
?>
    <li class="publish"><a href="#exam.publish">发布</a></li>
<?php
}
if (priv::aca('exam', 'exam', 'approve')){
?>
    <li class="approve"><a href="#exam.approve">送审</a></li>
<?php 
}
if (priv::aca('exam', 'exam', 'move')){
?>
    <li class="move separator"><a href="#exam.move">移动</a></li>
<?php 
}

if (priv::aca('exam', 'exam', 'reference')){
?>
    <li class="reference"><a href="#exam.reference">引用</a></li>
<?php 
}
?>
    <li class="note separator"><a href="#exam.note">批注</a></li>
    <li class="version"><a href="#exam.version">版本</a></li>
    <li class="log"><a href="#exam.log">日志</a></li>
  </ul>
<script type="text/javascript">
var action = '';
<?php 
if (priv::aca('exam', 'exam', 'edit')){
?>
action += '<img src="images/edit.gif" alt="编辑" width="16" height="16" class="hand edit"/> &nbsp;';
<?php 
}
if (priv::aca('exam', 'exam', 'remove')){
?>
action += '<img src="images/delete.gif" alt="删除" width="16" height="16" class="hand delete" />';
<?php 
}
?>
var row_template = '<tr id="row_{contentid}">';
	row_template +='	<td><input type="checkbox" class="checkbox_style" name="chk_row" id="chk_row_{contentid}" value="{contentid}" /></td>';
	row_template +='	<td><a href="javascript:exam.view({contentid})" style="color:{color}" tips="ID：{contentid}<br />Tags：{tags}<br />创建：{createdbyname}（{created}）<br />修改：{modifiedbyname}（{modified}）<br/>题目数：{questions} <br/>人数上限：{maxanswers}" class="title_list">{title}</a> {note} {lock}</td>';
	row_template +='	<td class="t_c">'+action+'</td>';
	row_template +='	<td class="t_c"><a href="javascript: url.member({createdby});">{createdbyname}</a></td>';
	row_template +='	<td class="t_c">{created}</td>';
	row_template +='</tr>';
</script>