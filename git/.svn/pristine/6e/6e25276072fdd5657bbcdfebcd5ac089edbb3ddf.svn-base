<div class="bk_8"></div>
<form name="dms_app_add" id="dms_app_add" method="POST" action="?app=dms&controller=app&action=add">
	<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form" id="add_table">
		<tr>
			<th>应用名称：</th>
			<td><input type="text" name="name" id="name" value="" /></td>
		</tr>
		<tr>
			<th>所属域：</th>
			<td><input type="text" name="domain" id="domain" value="" /></td>
		</tr>
		<tr>
			<th>APPKEY：</th>
			<td><input type="text" name="key" id="key" value="<?=md5(time())?>" /></td>
		</tr>
		<tr>
			<th>IP：</th>
			<td><textarea name="ip" id="ip"></textarea></td>
		</tr>
		<tr>
			<th>权限：</th>
			<td>
				<table id="treetable" class="table_list app_priv" cellpadding="0" cellspacing="0">
					<tbody></tbody>
				</table>
			</td>
		</tr>
	</table>
</form>
<script type="text/javascript">
var tree = new ct.treeTable('#treetable', treeTableOptions);
tree.load();
</script>