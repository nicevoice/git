<?php $this->display('header');?>
<form name="add" id="property_add" method="POST" action="?app=<?=$app?>&controller=<?=$controller?>&action=<?=$action?>">
<table border="0" cellspacing="0" cellpadding="0" class="table_form yyss">
    <tr>
        <th width="120">上一级属性：</th>
        <td>&nbsp;<?=property_once('parentid','parentid',100000, 100000)?></td>
    </tr>
  <tr>
    <th width="120">属性名：</th>
    <td>&nbsp;<input type="text" name="name" id="name" size="20"/></td>
  </tr>
    <tr>
        <th>标题：</th>
        <td>&nbsp;<input type="text" name="title" id="title" value="<?=$title?>" size="40"/></td>
    </tr>
    <tr>
        <th>描述：</th>
        <td>&nbsp;<textarea name="description" cols="60" rows="3"><?=$description?></textarea></td>
    </tr>
    <tr>
        <th>关联内容ID：</th>
        <td>&nbsp;<input type="text" name="linkcontentid" value="<?=$linkcontentid?>" size="5" maxlength="10"/> 默认为0，其他数值将关联对应内容</td>
    </tr>
  <tr>
  	<th>排序：</th>
  	<td>&nbsp;<input type="text" name="sort" value="0" size="3" maxlength="2"/> 值越大排序越靠后</td>
  </tr>
  <tr>
  	<th></th>
  	<td><input type="submit" value="保存" class="button_style_2"/></td>
  </tr>
</table>
</form>

<script type="text/javascript">
$('#property_add').ajaxForm('property.add_submit');
</script>
<?php $this->display('footer', 'system');