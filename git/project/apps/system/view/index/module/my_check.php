<div class="box_10 p_r mar_t_10 clear" id="my_check">
<h3 class="layout" style="cursor:move;"><span class="f_l b">稿件审核</span><span class="f_r mar_t_2" ><span><img src="images/down_1.gif" height="14" width="14" class="hand" onclick="module.down(this)" /></span>&nbsp;<span><img src="images/up_1.gif" height="14" width="14" class="hand" onclick="module.up(this)"/></span>&nbsp;<img src="images/close_1.gif" alt="关闭" title="关闭" height="14" width="14"  class="hand" onclick="module.del(this)" /></span></h3>
<div class="p_a tag_span tag_list_3">
<span class="s_4">编辑送审</span>
<span>用户投稿</span>
</div>            
<ul class="txt_list">
<?php 
$data = loader::model('admin/content')->select("status=3 AND iscontribute=0", '*', 'contentid DESC', 10);
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
   
<ul class="txt_list" style="display:none;">
<?php 
$data = loader::model('admin/content')->select("status=3 AND iscontribute=1", '*', 'contentid DESC', 10);
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