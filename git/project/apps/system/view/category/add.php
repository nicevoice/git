<form name="add" id="category_add" method="POST" action="?app=system&controller=category&action=add">
<input name="parentid" type="hidden" value="<?=$catid?>"/>
<table border="0" cellspacing="0" cellpadding="0" class="table_form yyss">
  <tr>
    <th width="120">栏目名：</th>
    <td><input type="text" name="name" id="name" size="20"/></td>
  </tr>
  <tr>
    <th>英文名：</th>
    <td><input type="text" name="alias" id="alias" size="20"/></td>
  </tr>
  <tr>
    <th>工作流：</th>
    <td><?=element::workflow('workflowid', $workflowid)?></td>
  </tr>
    <tr>
        <th>栏目扩展：</th>
        <td><?= element::property_once('typeid','typeid',$typeid) ?></td>
    </tr>
    <tr>
        <th>内容扩展：</th>
        <td><?= element::property_once('subtypeid','subtypeid',$subtypeid) ?></td>
    </tr>
    <tr>
        <th>地区属性：</th>
        <td><?= element::property_once('zoneid','zoneid',$zoneid) ?></td>
    </tr>
<?php if($catid):?>
  <tr>
	<th>扩展字段：</th>
	<td id="field_c"></td>
  </tr>
<?php endif;?>
  <tr>
    <th class="vtop pt">内容模型：</th>
    <td>
    <table cellspacing="2" cellpadding="2">
       <tr>
          <th width="30" style="text-align:left">允许</th>
          <th width="60" style="text-align:left">模型</th>
          <th style="text-align:left">内容页模板</th>
       </tr>
<?php
foreach (table('model') as $modelid=>$m) {
?>
       <tr>
          <td><input type="checkbox" name="model[<?=$modelid?>][show]" value="1" <?=$model[$modelid]['show'] ? 'checked' : ''?> /></td>
          <td><?=$m['name']?></td>
          <td><?php if (!in_array($m['alias'], array('link', 'special'))) echo element::template('template_show_'.$m['modelid'], 'model['.$m['modelid'].'][template]', $model[$modelid]['template'], 40)?></td>
       </tr>
<?php
}
?>
    </table>
    </td>
  </tr>
  <tr>
    <th>栏目首页模板：</th>
    <td>&nbsp;<?=element::template('template_index', 'template_index', $template_index, 50)?></td>
  </tr>
  <tr>
    <th>列表页模板： </th>
    <td>&nbsp;<?=element::template('template_list', 'template_list', $template_list, 50)?></td>
  </tr>
  <tr>
    <th>发布点：</th>
    <td><?=element::psn('path', 'path', $path, 40, 'dir')?></td>
  </tr>
  <tr>
    <th>生成栏目首页：</th>
    <td><label><input type="radio" name="iscreateindex" value="1" class="radio_style" <?php if ($iscreateindex == 1) echo 'checked';?> /> 是</label>&nbsp;&nbsp;
        <label><input type="radio" name="iscreateindex" value="0" class="radio_style" <?php if ($iscreateindex == 0) echo 'checked';?>> 否</label>
    </td>
  </tr>
  <tr>
    <th>栏目首页URL规则：</th>
    <td>&nbsp;<input type="text" name="urlrule_index" value="<?php echo $urlrule_index;?>" size="62"/></td>
  </tr>
  <tr>
    <th>列表页URL规则：</th>
    <td>&nbsp;<input type="text" name="urlrule_list" value="<?php echo $urlrule_list;?>" size="62"/></td>
  </tr>
  <tr>
    <th>内容页URL规则：</th>
    <td>&nbsp;<input type="text" name="urlrule_show" value="<?php echo $urlrule_show;?>" size="62"/></td>
  </tr>
  <tr>
    <th>列表页每页信息数：</th>
    <td>&nbsp;<input type="text" name="pagesize" value="<?php echo $pagesize;?>" size="5"/> <span class="c_gray"> 每个栏目可独立设置，留空则取 <a href="javascript:;" onclick="ct.assoc && ct.assoc.open('?app=system&controller=setting&action=optimize', '_blank');">系统设置</a> 中的值</span></td>
  </tr>
  <tr>
    <th>允许用户投稿：</th>
    <td><label><input type="radio" name="enablecontribute" value="1" class="radio_style" <?php if ($enablecontribute == 1) echo 'checked';?> /> 是</label>&nbsp;&nbsp;
        <label><input type="radio" name="enablecontribute" value="0" class="radio_style" <?php if ($enablecontribute == 0) echo 'checked';?>> 否</label>
    </td>
  </tr>
  <tr>
	 <th>允许评论：</th>
	 <td>
		<label><input type="radio" name="allowcomment" value="1" class="radio_style" <?php if ($allowcomment) echo 'checked';?> /> 是</label>&nbsp;&nbsp;
        <label><input type="radio" name="allowcomment" value="0" class="radio_style" <?php if (! $allowcomment) echo 'checked';?> > 否</label>
	 </td>
  </tr>
  <tr>
  	<th>关键词：</th>
  	<td>&nbsp;<input type="text" name="keywords" value="" size="60" maxlength="255"/></td>
  </tr>
  <tr>
  	<th>描述：</th>
  	<td>&nbsp;<textarea name="description" cols="60" rows="3"></textarea></td>
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
$('#category_add').ajaxForm('category.add_submit');

var field = {
	get: function()
	{
		var _this = this;
		var html = '<input type="checkbox" id="fieldextend" /><label for="fieldextend">子栏目继承</label>';
		$.get('?app=field&controller=project&action=get_project_api&catid=<?=$catid?>', function(response){
			if(response) {
				$('#field_c').html(response+html);
			}
		});
	},
	set: function(pid, parentid, extend)
	{
		$.get('?app=field&controller=project&action=set_project_api', {pid:pid, parentid:parentid, extend:extend});
	}
};
field.get();

// 关联字段
$(function() {
	$("#category_add").submit(function() {
		var extend = $("#fieldextend").attr("checked") ? 1 : 0;
		field.set($("#project").val(), <?=$catid ? $catid : 0?>, extend);
	});
});
</script>