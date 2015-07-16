<div class="box_10 p_r mar_t_10 clear" id="my_new">
<h3 class="layout" style="cursor:move;"><span class="f_l b">最新内容</span><span class="f_r mar_t_2" ><span><img src="images/down_1.gif" height="14" width="14" class="hand" onclick="module.down(this)" /></span>&nbsp;<span><img src="images/up_1.gif" height="14" width="14" class="hand" onclick="module.up(this)"/></span>&nbsp;<img src="images/close_1.gif" alt="关闭" title="关闭" height="14" width="14"  class="hand" onclick="module.del(this)" /></span></h3>
<ul class="txt_list">
<?php 
$data = loader::model('admin/content')->select(null, '*', 'contentid DESC', 10);
if($data)
{  
   foreach ($data as $r)
   {
   	   $r['created'] = date('Y-m-d', $r['created']);
   	   $cat = table('category', $r['catid']);
   	   $r['caturl'] = $cat['url'];
   	   $r['catname'] = $cat['name'];
?>
   <li><span class="f_r date"><?=$r['created']?></span><span class="f_l"><a href="<?=$r['caturl']?>">[<?=$r['catname']?>]</a> <a href="javascript: ct.assoc.open('<?=content_url($r['contentid'], $r['modelid'])?>', 'newtab');"><?=$r['title']?></a></span></li>
<?php 
   }
}
?>
</ul>
<div class="clear"></div>
</div>