<form id="html_ls" method="POST" action="?app=system&controller=html&action=ls">
<input name="catid" type="hidden" value="<?=$catid?>"/>
<div class="bk_20"></div>
<table border="0" cellspacing="0" cellpadding="0" class="table_form" width="100%">
  <tr>
    <th width="120">生成前：</th>
    <td><input type="text" name="maxlimit" id="maxlimit" size="5"/> 页（留空则表示生成全部列表页）</td>
  </tr>
  <?php if($hasChild):?>
  <tr>
    <th>生成子栏目页：</th>
    <td>
        <label><input type="radio" name="child" value="1" class="radio_style" checked /> 是</label>
        <label><input type="radio" name="child" value="0" class="radio_style"> 否</label>
    </td>
  </tr>
  <?php endif?>
</table>
</form>