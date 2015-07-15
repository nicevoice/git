<form action="?app=system&controller=aca&action=edit" method="post">
<input type="hidden" name="acaid" value="<?=$aca['acaid']?>" />
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
  <tr>
    <th width="70"><span class="c_red">*</span> 名称：</th>
    <td><input type="text" name="name" value="<?=$aca['name'];?>" size="30"/></td>
  </tr>
  <?php if ($aca['controller']):?>
  <?php if ($aca['action']):?>
  <tr>
  	<th>控制器：</th>
    <td><span><?=$aca['app'].'/'.$aca['controller']?></span></td>
  </tr>
  <tr>
  	<th><span class="c_red">*</span> 动作：</th>
    <td><input type="text" name="key_action" value="<?=$aca['action']?>" size="30" /> 多个请用“<span class="c_red">,</span>”隔开</td>
  </tr>
  <?php else:?>
  <tr>
  	<th>应用程序：</th>
    <td><span><?=$aca['app']?></span></td>
  </tr>
  <tr>
  	<th><span class="c_red">*</span> 控制器：</th>
    <td><input type="text" name="key_controller" value="<?=$aca['controller']?>" size="30" /></td>
  </tr>
  <?php endif;else:?>
  <tr>
  	<th><span class="c_red">*</span> 应用程序：</th>
    <td><input type="text" name="key_app" value="<?=$aca['app']?>" size="30" /></td>
  </tr>
  <?php endif;?>
</table>
</form>