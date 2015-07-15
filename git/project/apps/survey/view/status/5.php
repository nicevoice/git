	<div id="publishedby_x" class="th_pop" style="display:none;width:150px">
  <table>
    <tr>
       <td><input type="text" id="publishedbyname" size="12" /></td><td><input type="button" value="查询" class="button_style_1" onclick="tableApp.load('publishedbyname='+$('#publishedbyname').val());"/></td>
    </tr>
  </table>
	</div>
	<div id="starttime_x" class="th_pop" style="display:none;width:150px">
		<div>
			<a href="javascript: tableApp.load('starttime_min=<?=date('Y-m-d', TIME)?>');">今日</a> |
			<a href="javascript: tableApp.load('starttime_min=<?=date('Y-m-d', strtotime('yesterday'))?>&starttime_max=<?=date('Y-m-d', strtotime('yesterday'))?>');">昨日</a> | 
			<a href="javascript: tableApp.load('starttime_min=<?=date('Y-m-d', strtotime('last monday'))?>');">本周</a> | 
			<a href="javascript: tableApp.load('starttime_min=<?=date('Y-m-01', strtotime('this month'))?>');">本月</a>
		</div>
		<ul>
			<?php for ($i=2; $i<=7; $i++) {
				$publishdate = date('Y-m-d', strtotime("-$i day")); ?>
				<li><a href="javascript: tableApp.load('starttime_min=<?=$publishdate?>&starttime_max=<?=$publishdate?>');"><?=$publishdate?></a></li>
			<?php } ?>
		</ul>
	</div>
	<div id="endtime_x" class="th_pop" style="display:none;width:150px">
		<div>
			<a href="javascript: tableApp.load('endtime_min=<?=date('Y-m-d', TIME)?>');">今日</a> |
			<a href="javascript: tableApp.load('endtime_min=<?=date('Y-m-d', strtotime('yesterday'))?>&endtime_max=<?=date('Y-m-d', strtotime('yesterday'))?>');">昨日</a> | 
			<a href="javascript: tableApp.load('endtime_min=<?=date('Y-m-d', strtotime('last monday'))?>');">本周</a> | 
			<a href="javascript: tableApp.load('endtime_min=<?=date('Y-m-01', strtotime('this month'))?>');">本月</a>
		</div>
		<ul>
			<?php for ($i=2; $i<=7; $i++) {
				$unpublishdate = date('Y-m-d', strtotime("-$i day")); ?>
				<li><a href="javascript: tableApp.load('endtime_min=<?=$unpublishdate?>&endtime_max=<?=$unpublishdate?>');"><?=$unpublishdate?></a></li>
			<?php } ?>
		</ul>
	</div>
	<div id="weight_x" class="th_pop" style="display:none;width:60px">
		<ul>
			<li><a href="javascript: tableApp.load('weight_min=0&weight_max=29');">0-29</a></li>
			<li><a href="javascript: tableApp.load('weight_min=30&weight_max=49');">30-49</a></li>
			<li><a href="javascript: tableApp.load('weight_min=50&weight_max=59');">50-59</a></li>
			<li><a href="javascript: tableApp.load('weight_min=60&weight_max=69');">60-69</a></li>
			<li><a href="javascript: tableApp.load('weight_min=70&weight_max=79');">70-79</a></li>
			<li><a href="javascript: tableApp.load('weight_min=80&weight_max=89');">80-89</a></li>
			<li><a href="javascript: tableApp.load('weight_min=90&weight_max=100');">90-100</a></li>
		</ul>
	</div>
	<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8">
		<thead>
		<tr>
			<th width="30" class="t_l bdr_3"><input type="checkbox" id="check_all" class="checkbox_style"/></th>
			<th>标题</th>
			<th width="80">管理操作</th>
			<th width="60" class="ajaxer"><em class="more_pop" name="weight_x"></em><div name="weight">权重</div></th>
			<th width="80" class="ajaxer"><em class="more_pop" name="publishedby_x"></em><div name="publishedby">发布人</div></th>
			<th width="120" class="ajaxer"><em class="more_pop" name="starttime_x"></em><div name="starttime">开始时间</div></th>
			<th width="120" class="ajaxer"><em class="more_pop" name="endtime_x"></em><div name="endtime">截止时间</div></th>
		</tr>
		</thead>
		<tbody id="list_body"></tbody>
	</table>
  <div class="table_foot">
    <div id="pagination" class="pagination f_r"></div>
    <p class="f_l">
<?php 
if (priv::aca('survey', 'survey', 'remove')){
?>
      <input type="button" name="remove" onclick="survey.remove();" value="删除" class="button_style_1"/>
<?php 
}
if (priv::aca('survey', 'survey', 'publish')){
?>
      <input type="button" name="publish" onclick="survey.publish();" value="发布" class="button_style_1"/>
<?php 
}
if (priv::aca('survey', 'survey', 'unpublish')){
?>
      <input type="button" name="unpublish" onclick="survey.unpublish();" value="撤稿" class="button_style_1"/>
<?php 
}
if (priv::aca('survey', 'survey', 'move')){
?>
      <input type="button" name="move" onclick="survey.move();" value="移动" class="button_style_1"/>
<?php 
}

if (priv::aca('survey', 'survey', 'reference')){
?>
      <input type="button" name="reference" onclick="survey.reference();" value="引用" class="button_style_1"/>
<?php 
}
?>
    </p>
  </div>
  <!--右键菜单-->
  <ul id="right_menu" class="contextMenu">
<?php 
if (priv::aca('survey', 'survey', 'view')){
?>
    <li class="view"><a href="#survey.view">查看</a></li>
<?php 
}
if (priv::aca('survey', 'survey', 'edit')){
?>
    <li class="edit"><a href="#survey.edit">编辑</a></li>
<?php 
}
if (priv::aca('survey', 'survey', 'remove')){
?>
    <li class="remove"><a href="#survey.remove">删除</a></li>
<?php 
}
if (priv::aca('survey', 'survey', 'publish')){
?>
    <li class="publish"><a href="#survey.publish">发布</a></li>
<?php 
}
if (priv::aca('survey', 'survey', 'unpublish')){
?>
    <li class="unpublish"><a href="#survey.unpublish">撤稿</a></li>
<?php 
}
if (priv::aca('survey', 'survey', 'move')){
?>
    <li class="move separator"><a href="#survey.move">移动</a></li>
<?php 
}

if (priv::aca('survey', 'survey', 'reference')){
?>
    <li class="reference"><a href="#survey.reference">引用</a></li>
<?php 
}
?>
    <li class="note separator"><a href="#survey.note">批注</a></li>
    <li class="version"><a href="#survey.version">版本</a></li>
    <li class="log"><a href="#survey.log">日志</a></li>
  </ul>
<script type="text/javascript">
var action = '';
<?php 
if (priv::aca('survey', 'survey', 'edit')){
?>
action += '<img src="images/edit.gif" alt="编辑" width="16" height="16" class="hand edit"/> &nbsp;';
<?php 
}
if (priv::aca('survey', 'survey', 'remove')){
?>
action += '<img src="images/delete.gif" alt="删除" width="16" height="16" class="hand delete" />';
<?php 
}
?>
var row_template = '<tr id="row_{contentid}">';
	row_template +='	<td><input type="checkbox" class="checkbox_style" name="chk_row" id="chk_row_{contentid}" value="{contentid}" /></td>';
	row_template +='	<td><a href="javascript:survey.view({contentid})" style="color:{color}" tips="ID：{contentid}<br />Tags：{tags}<br />创建：{createdbyname}（{created}）<br />修改：{modifiedbyname}（{modified}）<br />审核：{checkedbyname}（{checked}）<br/>题目数：{questions} <br/>人数上限：{maxanswers}" class="title_list">{title}</a> {note} {lock}</td>';
	row_template +='	<td class="t_c">'+action+'</td>';
	row_template +='	<td class="t_r">{weight}</td>';
	row_template +='	<td class="t_c"><a href="javascript: url.member({publishedby});">{publishedbyname}</a></td>';
	row_template +='	<td class="t_c">{starttime}</td>';
	row_template +='	<td class="t_c">{endtime}</td>';
	row_template +='</tr>';
</script>