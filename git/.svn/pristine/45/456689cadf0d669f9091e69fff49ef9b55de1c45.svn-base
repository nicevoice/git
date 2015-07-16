<div class="box_10 p_r mar_t_10 clear" id="my_note">
<h3 class="layout" style="cursor:move;"><span class="f_l b">最新批注</span><span class="f_r mar_t_2" ><span><img src="images/down_1.gif" height="14" width="14" class="hand" onclick="module.down(this)" /></span>&nbsp;<span><img src="images/up_1.gif" height="14" width="14" class="hand" onclick="module.up(this)"/></span>&nbsp;<img src="images/close_1.gif" alt="关闭" title="关闭" height="14" width="14"  class="hand" onclick="module.del(this)" /></span></h3>
<ul class="txt_list">
<?php 
$data = loader::model('admin/content_note')->top(10);
if($data)
{
  	foreach ($data as $r)
  	{
   	   $cat = table('category', $r['catid']);
   	   $r['caturl'] = $cat['url'];
   	   $r['catname'] = $cat['name'];
?>
  <li><span class="f_r"><a href="javascript: url.member(<?=$r['userid']?>)"><?=username($r['userid'])?></a></span><span class="f_l"><a href="<?=$r['caturl']?>">[<?=$r['catname']?>]</a> <a id="content_<?=$r['contentid']?>" href="javascript: ct.assoc.open('?app=<?=$r['model']?>&controller=<?=$r['model']?>&action=view&contentid=<?=$r['contentid']?>','newtab')" class="note"><?=$r['title']?></a></span><div id="content_<?=$r['contentid']?>_note" style="display:none"><?=$r['createdbyname']?>：<?=$r['note']?> <span class='date'><?=$r['created']?></span></div></li>
<?php 
  	}
}
?>
</ul>
<div class="clear"></div>
</div>
<script type="text/javascript">
$('#note a.note').each(function (){
	//$(this).Tips_html($(this).id+'_note', 'tips_green', 300, 'top');
});
</script>