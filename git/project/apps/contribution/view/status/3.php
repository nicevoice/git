<div id="created_x" class="th_pop" style="display:none;width:150px">
	<div>
		<a href="javascript: tableApp.load('created_min=<?=date('Y-m-d', TIME)?>');">今日</a> |
		<a href="javascript: tableApp.load('created_min=<?=date('Y-m-d', strtotime('yesterday'))?>&created_max=<?=date('Y-m-d', strtotime('yesterday'))?>');">昨日</a> | 
		<a href="javascript: tableApp.load('created_min=<?=date('Y-m-d', strtotime('last monday'))?>');">本周</a> | 
		<a href="javascript: tableApp.load('created_min=<?=date('Y-m-01', strtotime('this month'))?>');">本月</a>
	</div>
	<ul>
		<?php  for ($i=2; $i<=7; $i++) { 
			$createdate = date('Y-m-d', strtotime("-$i day"));?>
		<li><a href="javascript: tableApp.load('created_min=<?=$createdate?>&created_max=<?=$createdate?>');"><?=$createdate?></a></li>
		<?php } ?>
	</ul>
</div>
<div id="createdby_x" class="th_pop" style="display:none;width:160px">
	<input type="text" id="createdbyname" size="12" /> <input type="button" value="查询" class="button_style_2" onclick="tableApp.load('createdbyname='+$('#createdbyname').val());"/>
</div>
<div id="created_x" class="th_pop" style="display:none;width:150px">
	<div>
		<a href="javascript: tableApp.load('created_min=<?=date('Y-m-d', TIME)?>');">今日</a> |
		<a href="javascript: tableApp.load('created_min=<?=date('Y-m-d', strtotime('yesterday'))?>&created_max=<?=date('Y-m-d', strtotime('yesterday'))?>');">昨日</a> | 
		<a href="javascript: tableApp.load('created_min=<?=date('Y-m-d', strtotime('last monday'))?>');">本周</a> | 
		<a href="javascript: tableApp.load('created_min=<?=date('Y-m-01', strtotime('this month'))?>');">本月</a>
	</div>
	<ul>
		<?php  for ($i=2; $i<=7; $i++) { 
			$createdate = date('Y-m-d', strtotime("-$i day"));?>
		<li><a href="javascript: tableApp.load('created_min=<?=$createdate?>&created_max=<?=$createdate?>');"><?=$createdate?></a></li>
		<?php } ?>
	</ul>
</div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8">
	<thead>
		<tr>
		<th width="30" class="t_l bdr_3"><input type="checkbox" id="check_all" class="checkbox_style"/></th>
		<th>标题</th>
		<th width="80">管理操作</th>
		<th width="80" class="ajaxer"><em class="more_pop" name="createdby_x"></em><div name="createdby">投稿人</div></th>
		<th width="120" class="ajaxer"><em class="more_pop" name="created_x"></em><div name="created">投稿时间</div></th>
		</tr>
	</thead>
	<tbody id="list_body"></tbody>
</table>
<div class="table_foot">
	<div id="pagination" class="pagination f_r"></div>
	<div class="f_r"> 共有<span id="pagetotal">0</span>条记录&nbsp;&nbsp;&nbsp;每页
	<input type="text" name="pagesize" size="3" id="pagesize" value=""/>条&nbsp;&nbsp;</div>
	<p class="f_l">
		<input type="button" name="reject" onclick="contribution.reject();" value="退稿" class="button_style_1"/>
		<input type="button" name="remove" onclick="contribution.remove();" value="删除" class="button_style_1"/>
		<input type="button" name="refresh" onclick="tableApp.load();" value="刷新" class="button_style_1"/>
	</p>
</div>

<script type="text/javascript">
var manage_td = '<img src="images/sh.gif" alt="发稿" title="发稿" width="16" height="16" class="manage" onclick="contribution.publish(\'{contributionid}\');"/> &nbsp; <img src="images/reject.gif" alt="退稿" title="退稿" width="16" height="16" class="manage" onclick="contribution.reject(\'{contributionid}\');"/>';
var row_template  = '<tr id="row_{contributionid}" right_menu_id="right_menu_{status}">';
	row_template += '	<td><input type="checkbox" class="checkbox_style" name="chk_row" id="chk_row_{contributionid}" value="{contributionid}" /></td>';
	row_template += '	<td><a href="javascript:contribution.view({contributionid});" title="查看内容"><div class="icon article"></div></a> <a href="javascript:tableApp.load(\'catid={catid}\');">[{catname}]</a> <a href="javascript:contribution.view({contributionid});" >{title}</a> </td>';
	row_template += '	<td class="t_c" id="manage_{contributionid}" name="manage" value="manage">'+manage_td+'</td>';
	row_template += '	<td class="t_c">{creator}</td>';
	row_template += '	<td class="t_c">{created}</td>';
	row_template += '</tr>';
</script>