<?php $this->display('header','system');?>
<div class="bk_10"></div>
<div class="tag_1">
	<ul class="tag_list">
		<li class="s_3"><a href="?app=contribution&controller=index&action=view&contributionid=<?=$contributionid?>" class="s_3">查看</a></li>
	</ul>
	<?php if($status == 3) { ?>
	<input type="button" name="publish" value="发布" class="button_style_1" onclick="contribution.publish(<?=$contributionid?>)"/>
	<input type="button" name="reject" value="退稿" class="button_style_1" onclick="contribution.reject(<?=$contributionid?>)"/>
	<?php } ?>
	<input type="button" name="remove" value="删除" class="button_style_1" onclick="contribution.remove(<?=$contributionid?>)"/>
</div>
<div class="bg_2">
	<div class="f_l w_600 mar_r_8">
		<table width="500" border="0" cellspacing="0" cellpadding="0" class="table_form mar_t_10 mar_l_8">
			<tr><th width="70">标题：</th><td><?=$title?></td></tr>
			<tr><th>Tags：</th><td><?=$tags?></td></tr>
			<?php if($contentid) {?>
			<?php $url =  table('content',$contentid,'url');?>
			<tr><th>内容地址：</th><td><a href="<?=$url?>" target="_blank"><?=$url?></a></td></tr>
			<?php } ?>
		</table>
		<div class="title mar_l_8">正文</div>
		<div class="content"><?=$content?></div>
		
	</div>
	<div class="f_l w_200 box_6">
		<h3><span class="b">稿件属性</span></h3>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_form">
			<tr><th width="70">ID：</th><td><?=$contributionid?></td></tr>
			<tr><th>栏目：</th><td><a href="<?=$caturl?>" target="_blank"><?=$catname?></a></td></tr>
			<tr><th>作者：</th><td><?=$author?></td></tr>
			<tr><th>投稿人：</th><td><a href="?app=member&controller=index&action=profile&userid=<?=$createdby?>" target="_blank"><?=$createdbyname?></a> &nbsp;</td></tr>
			<tr><th>邮箱：</th><td><?=$email?></td></tr>
			<tr><th>来源：</th><td><?=$sourcename?></td></tr>
			<tr><th>来源地址：</th><td><?=$sourcurl?></td></tr>
			<tr><th>投稿时间：</th><td><?=$created?></td></tr>
			<tr><th>状态：</th><td><?=table('status', $status, 'name')?></td></tr>
			</table>
	</div>
	<div class="clear"></div>
</div>
</div>
<script type="text/javascript" src="apps/contribution/js/contribution.js"></script>
<script type="text/javascript">
var contentid = <?php echo intval($contentid); ?>;
if(contentid) {
	$('.tag_1 input').remove();
}

</script>
<?php $this->display('footer','system');
