<form id="html_ls" method="POST" action="?app=system&controller=html&action=createCate">
<input name="catid" type="hidden" value="<?=$catid?>"/>
<div class="bk_20"></div>
<table border="0" cellspacing="0" cellpadding="0" class="table_form" width="100%">
  <?php if($catid==0):?>
  <tr>
  <th width="40%">生成列表页：</th>
  <td><input type="checkbox" name="child" value="1" checked onclick="$('#tbody_maxlimit').toggle();"></td>
  </tr>
  <tbody id="tbody_maxlimit">
  <tr>
  <th>生成前：</th>
  <td><span style="width:350px"><input type="text" id="maxlimit" name="maxlimit" size="5" value="<?php if ($maxlimit) echo $maxlimit;else echo setting('system', 'listpages') ?>"/>&nbsp;页</span> </td>
  </tr>
  </tbody>
  <?php elseif($hasChild):?>
  <tr>
  <th width="40%"><label>生成子栏目页：</label></th>
  <td><input type="checkbox" name="child" value="1" checked class="radio_style" onclick="$('#tbody_maxlimit').toggle();"/>  </td>
  </tr>
  <tbody id="tbody_maxlimit">
  <tr>
  <th>生成前：</th>
  <td><span style="width:350px"><input type="text" id="maxlimit" name="maxlimit" size="5" value="<?php if ($maxlimit) echo $maxlimit;else echo setting('system', 'listpages') ?>"/>&nbsp;页</span> </td>
  </tr>
  </tbody>
  <?php else:?>
  <tr>
    <th width="120">生成前：</th>
    <td><input type="text" name="maxlimit" id="maxlimit" size="5" value="<?php echo $maxlimit;?>"/> 页  <img style="margin-bottom:1px"src="images/question.gif" width="16" height="16" class="tips hand" tips="留空则表示生成全部列表页" align="absmiddle" onload="$(this).attrTips('tips', 'tips_green')"/></td>
  </tr>
  <?php endif;?>
</table>
</form>