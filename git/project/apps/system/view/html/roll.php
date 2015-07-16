<div class="bk_8"></div>
<form name="html_roll" id="html_roll" method="POST" action="?app=system&controller=html&action=roll">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
  <tr>
    <th>频道范围：</th>
    <td><select name="channelid[]" multiple="multiple" style="width:120px;height:100px">
   <option value="0">全部</option>
   <?php foreach ($channels as $channelid=>$r){ ?>
   <option value="<?=$channelid?>"><?=$r['name']?></option>
   <?php } ?>
</select></td>
  </tr>
   <tr>
    <th>起始日期：</th>
    <td><input type="text" name="date_min" value="<?=$date_min?>" size="18" class="input_calendar"/></td>
  </tr>
   <tr>
    <th>截止日期：</th>
    <td><input type="text" name="date_max" value="<?=$date_max?>" size="18" class="input_calendar"/></td>
  </tr>
</table>
</form>