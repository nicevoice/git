<?php $this->display('header', 'system');?>
<?php
function score_list($userid, $year, $month = null, $day = null)
{
	$userid = intval($userid);
	$date = $year;
	if ($month)
	{
		$date .= '-'.$month;
		if ($day) $date .= '-'.$day;
	}
	import('helper.date');
	$odate = new date($date);
	$mintime = strtotime($odate->firstday_of_month());
	$maxtime = strtotime($odate->lastday_of_month());
	$db = & factory::db();
	return $db->select("SELECT * FROM #table_score WHERE userid=$userid AND created>$mintime AND created<$maxtime");
}

if (!isset($year) || !$year) $year = date('Y');
if (!isset($month) || !$month) $month = date('m');

$months = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
$member = loader::model('member','member')->getProfile($userid);
?>
<div class="bk_10"></div>
<div class="tag_1">
	<ul class="tag_list">
	<li><a href="?app=member&controller=index&action=profile&userid=<?=$member['userid']?>">用户资料</a></li>
		<?php if($member['groupid'] == 1) { ?>
	<li><a href="?app=system&controller=administrator&action=stat&userid=<?=$member['userid']?>">工作报表</a></li>
	<li class="s_3"><a href="?app=system&controller=score&action=view&userid=<?=$member['userid']?>">评分记录</a></li>
	<li><a href="?app=system&controller=administrator&action=priv&userid=<?=$member['userid']?>">权限</a></li>
		<?php } ?>
	</ul>
	<input type="button" value="修改资料" class="button_style_1" onclick="member.edit(<?=$member['userid']?>)"/>
	<input type="button" value="修改密码" class="button_style_1" onclick="member.password(<?=$member['userid']?>)"/>
	<input type="button" value="修改头像" class="button_style_1" onclick="member.avatar(<?=$member['userid']?>)"/>
	<input type="button" value="删除" class="button_style_1" onclick="member.del(<?=$member['userid']?>);"/>
	<input name="back" type="button" value="返回" class="button_style_1" onclick="javascript:history.go(-1);"/>
</div>
<div id="proids">
    <dl class="proids"><dt>年份：</dt><dd>
	<?php
	$i = 0;
	for ($y = date('Y'); ; $y--)
	{
		$i++;
		$selected = $y == $year ? 'class="checked"' : '';
		echo '<a href="?app=system&controller=score&action=view&userid='.$userid.'&year='.$y.'&month='.$month.'" '.$selected.'>'.$y.'年</a>';
		if ($i >= 10) break;
	}
?>
	</dd></dl>
	<dl class="proids"><dt>月份：</dt><dd>
	<?php
	foreach ($months as $m)
	{
		$selected = $m == $month ? 'class="checked"' : '';
		echo '<a href="?app=system&controller=score&action=view&userid='.$userid.'&year='.$year.'&month='.$m.'" '.$selected.'>'.(substr($m, 0, 1) == 0 ? substr($m, 1) : $m).'月</a>';
	}
?></dd></dl>   
 </div>

        <table width="98%" border="0" cellpadding="0" cellspacing="0" class="table_list" style="margin:10px">
          <tbody>
            <tr>
              <th width="10%">分数</th>
              <th width="30%">标题</th>
              <th width="30%">评语</th>
              <th width="10%">评分人</th>
              <th width="15%">评分时间</th>
            </tr>
<?php 
$total = 0;
$data = score_list($userid, $year, $month);
foreach ($data as $r)
{
	$total += $r['score'];
?>
            <tr>
              <td><?php echo '&nbsp;&nbsp;<span onclick="edit('.$r['scoreid'].')" style="cursor:pointer;background:url(images/star.gif) 0px '.-16*(5-$r['score']).'px no-repeat">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>';?></td>
              <td><?php 
              if($r['contentid']) {
             	 $c = table('content', $r['contentid']);
             	 $m = table('model', $c['modelid'], 'alias');
             	 echo '<a href="javascript:;" onclick="ct.assoc.open(\''.'?app='.$m.'&controller='.$m.'&action=view&contentid='.$r['contentid'].'\',\'newtab\')">'.table('content',$r['contentid'],'title').'</a>';
              }
              else echo '';?></td>
              <td><?=$r['comment']?></td>
              <td class="t_c"><a href="?app=member&controller=index&action=profile&userid=<?=$r['createdby']?>"><?=username($r['createdby'])?></a></td>
              <td><?=date('Y-m-d H:i:s',$r['created'])?></td>
            </tr>     
<?php
}
?> 
           <tr class="table_foot">
              <td colspan="5">合计：<?=$total?>分</td>
            </tr>
          </tbody>
        </table>
<script type="text/javascript" src="apps/member/js/member.js"></script>
<script type="text/javascript">
function edit(scoreid)
{
	ct.iframe({title:'?app=system&controller=score&action=score_edit&scoreid='+scoreid,width:450,height:'auto'}).bind('dialogclose',function(){
			window.location.reload();
		});
}
</script>
<?php $this->display('footer', 'system');?>