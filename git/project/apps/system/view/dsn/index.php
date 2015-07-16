<?php $this->display('header', 'system');?>
<!--tablesorter-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>

<script type="text/javascript" src="apps/system/js/dsn.js"></script>
<div class="bk_10"></div>
<div class="table_head">
  <input type="button" name="add" value="添加" class="button_style_2 f_l" onclick="App.add();  return false;"/>
</div>
<div class="bk_8"></div>
<table width="98%" id="item_list" class="tablesorter table_list mar_l_8" cellspacing="0" cellpadding="0" style="empty-cells:show;">
  <thead>
    <tr>
      <th class="t_c bdr_3 sorter"><div>名称</div></th>
      <th width="110" class="t_c sorter"><div>驱动</div></th>
      <th width="220" class="t_c"><div>主机</div></th>
      <th width="110" class="t_c"><div>数据库</div></th>
      <th width="110" class="t_c"><div>字符集</div></th>
      <th width="100" class="t_c"><div>添加时间</div></th>
      <th width="80">管理操作</th>
    </tr>
  </thead>
  <tbody></tbody>
</table>
<script type="text/javascript">
$.validate.setConfigs({
    xmlPath:'<?=ADMIN_URL?>apps/<?=$app?>/validators/dsn/'
});
App.init('?app=<?=$app?>&controller=<?=$controller?>','page');
</script>
<?php $this->display('footer', 'system');?>