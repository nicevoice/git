<div class="bk_8"></div>
<form name="log_search" id="log_search" method="GET" action="">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
  <tr>
    <th width="60">栏目：</th>
    <td><?=element::category('catid', 'catid', $catid, 1, null, '不限')?></td>
  </tr>
  <tr>
    <th>模型：</th>
    <td><?=element::model('modelid', 'modelid', $modelid)?></td>
  </tr>
  <tr>
    <th>操作：</th>
    <td>
	  <select name="opaction" style="width:100px">
	      <option value="">不限</option>
	      <?php foreach (config('content_action') as $act=>$name) {?>
		  <option value="<?=$act?>" <?=$opaction == $act ? 'selected' : ''?>><?=$name?></option>
		  <?php } ?>
	  </select>
    </td>
  </tr>
  <tr>
    <th>操作人：</th>
    <td><input type="text" name="createdbyname" value="<?=$createdbyname?>" size="15"/></td>
  </tr>
   <tr>
    <th>时间：</th>
    <td><input type="text" name="created_min" value="<?=$created_min?>" size="18" class="input_calendar" style="width:100px;"/> ~ <input type="text" name="created_max" value="<?=$created_max?>" size="18" class="input_calendar" style="width:100px;"/></td>
  </tr>
  <tr>
    <th>内容ID：</th>
    <td><input type="text" name="contentid" value="<?=$contentid?>" size="10"/></td>
  </tr>
  <tr>
    <th>标题：</th>
    <td><input type="text" name="title" value="<?=$title?>" size="20"/></td>
  </tr>
</table>
</form>