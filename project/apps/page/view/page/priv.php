<form name="page_priv" id="page_priv" method="POST" action="?app=page&controller=page_priv&action=add">
<input type="hidden" name="pageid" value="<?=$pageid?>" />
<table style="margin:8px">
	<tr>
		<td>用户名：<input type="text" name="username" size="20"/></td>
		<td><input type="submit" name="submit" value="添加" class="button_style_1"/></td>
	</tr>
</table>
</form>
<table id="item_list" width="330" cellpadding="0" cellspacing="0" class="table_list mar_l_8">
<thead>
  <tr>
    <th class="bdr_3">用户名</th>
    <th>角色</th>
    <th width="40">删除</th>
  </tr>
</thead>
<tbody id="list_body">
<?php
foreach ($data as $r)
{
?>
  <tr id="row_<?=$r['userid']?>">
    <td><a href="javascript: url.member(<?=$r['userid']?>);"><?=$r['username']?></a></td>
    <td><?=$r['rolename']?></td>
    <td class="t_c"><img src="images/delete.gif" alt="删除" width="16" height="16" class="hand" onclick="pagepriv.del(<?=$r['pageid']?>, <?=$r['userid']?>)"/></td>
  </tr>
<?php
}
?>
</tbody>
</table>