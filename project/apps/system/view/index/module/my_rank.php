<div class="box_10 p_r mar_t_10 clear" id="rank_pv" >
<h3 class="layout" style="cursor:move;"><span class="f_l b">访问排行榜</span><span class="f_r mar_t_2" ><span><img src="images/down_1.gif" height="14" width="14" class="hand" onclick="module.down(this)" /></span>&nbsp;<span><img src="images/up_1.gif" height="14" width="14" class="hand" onclick="module.up(this)"/></span>&nbsp;<img src="images/close_1.gif" alt="关闭" title="关闭" height="14" width="14"  class="hand" onclick="module.del(this)" /></span></h3>
<div class="p_a tag_span tag_list_3">
<span class="s_4">今日</span>
<span>昨日</span>
<span>本周</span>
<span>本月</span></div>
<ul class="txt_list" id="rank_pv_today">
<?php 
$created_min = strtotime(date('Y-m-d 00:00:00'));
$data = loader::model('admin/content')->select("status=6 AND created>=$created_min", '*', 'pv DESC', 10);
if($data)
{ 
	foreach ($data as $r)
	{
   	   $cat = table('category', $r['catid']);
   	   $r['caturl'] = $cat['url'];
   	   $r['catname'] = $cat['name'];
?>
<li><span class="f_r c_red"><?=$r['pv']?></span><span class="f_l"><a href="<?=$r['caturl']?>">[<?=$r['catname']?>]</a> <a href="javascript: ct.assoc.open('<?=content_url($r['contentid'], $r['modelid'])?>', 'newtab');"><?=$r['title']?></a></span></li>
<?php 
	 }
}
?>
</ul>
	
<ul class="txt_list" id="rank_pv_yesterday" style="display:none;">
<?php 
$yesterday = strtotime('yesterday');
$created_min = strtotime(date('Y-m-d 00:00:00', $yesterday));
$created_max = strtotime(date('Y-m-d 23:59:59', $yesterday));
$data = loader::model('admin/content')->select("status=6 AND created>=$created_min AND created<=$created_max", '*', 'pv DESC', 10);
if($data)
{ 
	 foreach ($data as $r)
	 {
   	   $cat = table('category', $r['catid']);
   	   $r['caturl'] = $cat['url'];
   	   $r['catname'] = $cat['name'];
?>
<li><span class="f_r c_red"><?=$r['pv']?></span><span class="f_l"><a href="<?=$r['caturl']?>">[<?=$r['catname']?>]</a> <a href="javascript: ct.assoc.open('<?=content_url($r['contentid'], $r['modelid'])?>', 'newtab');"><?=$r['title']?></a></span></li>
<?php 
	 }
}
?>
</ul>
      		
<ul class="txt_list" id="rank_pv_week" style="display:none;">
<?php 
$created_min = strtotime('last monday');
$data = loader::model('admin/content')->select("status=6 AND created>=$created_min", '*', 'pv DESC', 10);
if($data)
{ 
	 foreach ($data as $r)
	 {
   	   $cat = table('category', $r['catid']);
   	   $r['caturl'] = $cat['url'];
   	   $r['catname'] = $cat['name'];
?>
<li><span class="f_r c_red"><?=$r['pv']?></span><span class="f_l"><a href="<?=$r['caturl']?>">[<?=$r['catname']?>]</a> <a href="javascript: ct.assoc.open('<?=content_url($r['contentid'], $r['modelid'])?>', 'newtab');"><?=$r['title']?></a></span></li>
<?php 
	 }
}
?>
</ul>

<ul class="txt_list" id="rank_pv_month" style="display:none;">
<?php 
$created_min = strtotime(date('Y-m-01 00:00:00'));
$data = loader::model('admin/content')->select("status=6 AND created>=$created_min", '*', 'pv DESC', 10);
if($data)
{ 
	 foreach ($data as $r)
	 {
   	   $cat = table('category', $r['catid']);
   	   $r['caturl'] = $cat['url'];
   	   $r['catname'] = $cat['name'];
?>
<li><span class="f_r c_red"><?=$r['pv']?></span><span class="f_l"><a href="<?=$r['caturl']?>">[<?=$r['catname']?>]</a> <a href="javascript: ct.assoc.open('<?=content_url($r['contentid'], $r['modelid'])?>', 'newtab');"><?=$r['title']?></a></span></li>
<?php 
	 }
}
?>
</ul>
   <div class="clear"></div>
</div>