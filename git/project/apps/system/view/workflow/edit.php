<div class="bk_8"></div>
<form name="workflow_edit" id="workflow_edit" method="POST" action="?app=system&controller=workflow&action=edit&workflowid=<?=$workflowid?>">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
  <tr>
    <th><span class="c_red">*</span> 名称：</th>
    <td><input type="text" name="name" id="name" value="<?=$name?>" size="20"/></td>
  </tr>
  <tr>
    <th>描述：</th>
    <td><input type="text" name="description" value="<?=$description?>" size="45"/></td>
  </tr>
  <tr>
    <th>步骤：</th>
    <td><input type="hidden" name="steps" id="steps" value="<?=$steps?>"/> <input type="button" name="add_step" id="add_step" value="增加步骤" class="button_style_1" onclick="step_add()"/></td>
  </tr>
  <tr>
    <th></th>
    <td><ul id="roles"></ul></td>
  </tr>
</table>
</form>

<script type="text/javascript">
var role_select = '<?=$role_select?>';
var steps = 0;
var i = 0;
function step_add(role)
{
	i++;
	steps++;
	if (typeof(role) == 'undefined') var role = role_select.replace('{$i}', i).replace('{$i}', i);
	$('#roles').append('<li id="step_'+i+'">第<span>'+steps+'</span>步： '+role+' <img src="images/delete.gif" alt="删除" width="16" height="16" class="hand" onclick="step_delete(\''+i+'\');"/></li>');
	$('#steps').val(steps);
}

function step_delete(i)
{
	$('#step_'+i).remove();
	steps--;
	$('#steps').val(steps);
	$('#roles span').each(function(step, e){
		$(e).html(step+1);
	});
}
<?php foreach ($roles as $role){ ?>
step_add('<?=$role?>');
<?php } ?>
</script>