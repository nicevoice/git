<?php
$member = loader::model('member', 'member');
$admin = loader::model('admin/admin', 'system');
extract($member->get($_userid));
extract($admin->get($_userid), EXTR_OVERWRITE);
if (empty($photo))
{
	$photo = 'images/nopic.jpg';
} else {
	$photo = UPLOAD_URL.'avatar/'.$photo;
}
import('helper.iplocation');
$iplocation = new iplocation();
$iparea = $iplocation->get($lastloginip);
?>

<div class="box_10 mar_t_10 sortableItem" id="my_info">
    <h3 class="layout" style="cursor:move;"><span class="f_l b">我的信息</span><span class="f_r mar_t_2" ><span><img src="images/down_1.gif" height="14" width="14" class="hand" onclick="module.down(this)" /></span>&nbsp;<span><img src="images/up_1.gif" height="14" width="14" class="hand" onclick="module.up(this)"/></span>&nbsp;<img src="images/close_1.gif" alt="关闭" title="关闭" height="14" width="14"  class="hand" onclick="module.del(this)" /></span></h3>
    <div class="pad_5">
      <div class="f_l">
      	<img src="<?=$photo?> "alt="<?=$name?>" width="90" height="90" class="pic"/>
      </div>
      <div class="f_l">
        <div class="lh_24"><strong><a href="?app=system&controller=my&action=profile" class="c_blue"><?=$username?></a></strong>（<?=$name?>）</div>
        <table cellpadding="0" cellspacing="0" width="100%">
          <tr>
            <th width="55">部门：</th>
            <td><?php echo table('department', $departmentid, 'name');?></td>
            <th width="55">角色：</th>
            <td><?php echo table('role', $roleid, 'name');?></td>
            <th width="55"></th>
            <td></td>
          </tr>
          <tr>
            <th width="55">发稿：</th>
            <td><?=$posts?></td>
            <th width="55">评论：</th>
            <td><?=$comments?></td>
            <th width="55">PV：</th>
            <td><?=$pv?></td>
          </tr>
        </table>
        <p>最近登录：<span class="c_green"><?=$lastlogintime?></span>（<span class="c_green"><?=$lastloginip?> - <?=$iparea?></span>）&nbsp;&nbsp;登录：<span class="c_green"><?=$logintimes?></span>次</p>
      </div>
      <div class="clear"></div>
    </div>
  </div>