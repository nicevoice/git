<?php
if ($workflowid)
{
	$workflow_step = loader::model('admin/workflow_step', 'system');
	$workflow_steps = $workflow_step->ls($workflowid);
	$roles = table('role');
?>
  <table cellpadding="0" cellspacing="1" style="color:#fff;margin: 0px 0 8px 8px;height:20px">
    <tr>
       <td width="80" style="color:#000;text-align:right;padding-right:10px">工作流：</td>
<?php
foreach ($workflow_steps as $k=>$roleid)
{
	$style = $roleid == $_roleid ? 'background-color:#FFCC00;padding-left:10px' : 'background-color:#bbb;padding-left:10px';
	echo '<td width="120" style="'.$style.'"><a href="javascript: url.role('.$roleid.');" style="color:#fff">'.($k+1).'、'.$roles[$roleid]['name'].'</a></td>';
}
?>
    </tr>
  </table>
<?php
}
?>
  <div id="workflow_x" class="th_pop" style="display:none;width:100px">
      <ul>
        <li><a href="javascript: tableApp.load('workflow_roleid=0');">全部</a></li>
      <?php foreach ($workflow_steps as $k=>$roleid){?>
        <li><a href="javascript: tableApp.load('workflow_roleid=<?=$roleid?>');"><?=$roles[$roleid]['name']?></a></li>
      <?php }?>
     </ul>
  </div>