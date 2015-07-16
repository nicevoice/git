<?php $this->display('header', 'system');?>
<!--tablesorter-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>

<script type="text/javascript" src="apps/system/js/tweets.js"></script>
<div class="bk_10"></div>
<div class="table_head">
  <button type="button" name="add" class="button_style_2 f_l" onclick="App.add();  return false;">添加</button>
</div>
<div class="bk_8"></div>
<table width="98%" id="item_list" class="tablesorter table_list mar_l_8" cellspacing="0" cellpadding="0" style="empty-cells:show;">
  <thead>
    <tr>
      <th class="t_c bdr_3" width="60"><div> ID </div></th>
      <th class="t_c"><div> 标识名称 </div></th>
      <th width="80" class="t_c"><div> 应用类型 </div></th>
      <th width="250" class="t_c"><div> 用户名 </div></th>
      <th width="80" class="t_c"><div> 添加时间 </div></th>
      <th width="80" class="t_c"><div> 激活 </div></th>
      <th width="80"> 管理操作 </th>
    </tr>
  </thead>
  <tbody></tbody>
</table>
<script type="text/javascript">
$.validate.setConfigs({
    xmlPath:'<?=ADMIN_URL?>apps/<?=$app?>/validators/tweets/'
});
App.init('?app=<?=$app?>&controller=<?=$controller?>', 'page');
</script>
<?php $this->display('footer', 'system');?>