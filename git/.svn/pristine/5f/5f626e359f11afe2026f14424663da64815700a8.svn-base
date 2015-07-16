<form name="edit" method="POST" action="?app=system&controller=administrator&action=clonepriv">
<input type="hidden" name="srcuserid" value="<?=$srcuserid?>" />
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>将<b class="c_red"><?=username($srcuserid)?></b>的权限克隆给</caption>
	<tr>
	    <th width="60">用户名：</th>
	    <td>
			<input name="taruserid" class="suggest"
				single="1"
				width="180"
				url="?app=system&controller=administrator&action=suggest&q=%s"
				listUrl="?app=system&controller=administrator&action=page&page=%s"
				paramVal="userid"
				paramTxt="username"
			/>
		</td>
	</tr>
	<tr>
	    <th></th>
	    <td>将修改部门和角色，并继承<b class="c_red"><?=username($srcuserid)?></b>的所有权限</td>
	</tr>
</table>
</form>