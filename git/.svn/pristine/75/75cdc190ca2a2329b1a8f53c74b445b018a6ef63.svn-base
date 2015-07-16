<?php $this->display('header', 'system');?>
<div class="table_head">
  	<form method="POST" id="content_version_add" name="content_version_add" action="?app=system&controller=content_version&action=add">
  	  <input type="hidden" name="contentid" id="contentid" value="<?=$contentid?>" size="30" />
<table>
    <tr>
	    <td>名称：<input type="text" name="name" id="name" value="" size="30" maxlength="50"/></td>
	    <td><input type="submit" name="submit" id="submit" value="保存新版本" class="button_style_1"/></td>
    </tr>
</table>
    </form>
</div>
  <table width="100%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list">
    <thead>
      <tr>
        <th width="40" class="bdr_3">ID</th>
        <th>名称</th>
        <th width="120">创建时间</th>
        <th width="80">创建人</th>
        <th width="100">操作</th>
      </tr>
    </thead>
    <tbody id="list_body">
<?php foreach ($data as $n=>$r) { ?>
<tr id="tr_<?=$r['versionid'];?>">
	<td class="t_r"><?=$r['versionid']?></td>
	<td><?=$r['name']?></td>
	<td class="t_c"><?=$r['created']?></td>
	<td><a href="javascript: url.member({<?=$r['createdby']?>});"><?=$r['createdbyname']?></a></td>
	<td class="t_c"><a href="javascript:ct.assoc.open('?app=system&controller=content_version&action=view&versionid=<?=$r['versionid']?>','newtab')" >查看</a> | <a href="javascript: version_restore(<?=$r['versionid']?>);">恢复</a> | <a href="javascript: version_delete(<?=$r['versionid']?>);">删除</a></td>
</tr>
<?php } ?>
    </tbody>
  </table>
<script type="text/javascript" src="apps/system/js/content_version.js"></script>
<script type="text/javascript">
$(function(){
	$.validate.setConfigs({
		xmlPath:'<?=ADMIN_URL?>apps/<?=$app?>/validators/content/'
	});
	version_add();
});
</script>
<?php $this->display('footer', 'system');
