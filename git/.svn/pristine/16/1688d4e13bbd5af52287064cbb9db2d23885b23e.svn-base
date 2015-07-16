<?php 
$workflowid = table('category', $catid, 'workflowid');
if($workflowid && ($status == 2 || $status == 3))
{ 
	$workflow_step = loader::model('admin/workflow_step', 'system');
	$workflow_steps = $workflow_step->ls($workflowid);
	$roles = table('role');
?>
      <tr>
        <th>工作流：</th>
        <td>
  <table cellpadding="0" cellspacing="1" style="height:20px">
    <tr>
<?php
foreach ($workflow_steps as $k=>$roleid)
{
	$style = $roleid == $workflow_roleid ? 'background-color:#FFCC00;padding-left:10px' : 'background-color:#bbb;padding-left:10px';
	echo '<td width="120" style="'.$style.'"><a href="javascript: url.role('.$roleid.');" style="color:#fff">'.($k+1).'、'.$roles[$roleid]['name'].'</a></td>';
}
?>
    </tr>
  </table>
        </td>
      </tr>
<?php 
} 
?>