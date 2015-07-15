<?php $this->display('header');?>

<div class="bk_10"></div>
<div class="tag_1">
	<ul class="tag_list">
		<li class="s_3"><a href="?app=link&controller=link&action=view&contentid=<?=$contentid?>" class="s_3">查看</a></li>
	</ul>
	<input type="button" name="edit" value="修改" class="button_style_1" onclick="content.edit(<?=$contentid?>)"/>
	<?php if($status >= 5) { ?>
	<input type="button" name="section" value="推荐" class="button_style_1" onclick="content.section(<?=$contentid?>)"/>
	<?php } ?>
	<?php if($status < 6) { ?>
	<input type="button" name="publish" value="发布" class="button_style_1" onclick="content.publish(<?=$contentid?>)"/>
	<?php } ?>
	<input type="button" name="remove" value="删除" class="button_style_1" onclick="content.remove(<?=$contentid?>)"/>
	<?php if($status == 0) { ?>
	<input type="button" name="restore" value="还原" class="button_style_1" onclick="content.restore(<?=$contentid?>)"/>
	<?php } ?>
	<?php if($status == 1) { ?>
	<input type="button" name="approve" value="送审" class="button_style_1" onclick="content.approve(<?=$contentid?>)"/>
	<?php } ?>
	<?php if($status == 3) { ?>
	<input type="button" name="pass" value="通过" class="button_style_1" onclick="content.pass(<?=$contentid?>)"/>
	<input type="button" name="reject" value="退回" class="button_style_1" onclick="content.reject(<?=$contentid?>)"/>
	<?php } ?>
	<input type="button" name="move" value="移动" class="button_style_1" onclick="content.move(<?=$contentid?>)"/>
	<input type="button" name="reference" value="引用" class="button_style_1" onclick="content.reference(<?=$contentid?>)"/>
	<input type="button" name="note" value="批注" class="button_style_1" onclick="content.note(<?=$contentid?>)"/>
	<input type="button" name="version" value="版本" class="button_style_1" onclick="content.version(<?=$contentid?>)"/>
	<input type="button" name="log" value="日志" class="button_style_1" onclick="content.log(<?=$contentid?>)"/>
</div>
<div class="bg_2">
	<div class="f_l w_600 mar_r_8">
		<table width="500" border="0" cellspacing="0" cellpadding="0" class="table_form mar_t_10 mar_l_8">
		<tr>
			<th width="60">标题：</th>
			<td><a href="<?=$url?>" target="_blank" style="color:<?=$color?>"><?=$title?></a></td>
		</tr>
		<tr>
			<th>网址：</th>
			<td><a href="<?=$url?>" target="_blank"><?=$url?></a></td>
		</tr>
		<tr>
			<th>Tags：</th>
			<td><?=$tags_view?></td>
		</tr>
		<?php if($thumb){ ?>
		<tr>
			<th>缩略图：</th>
			<td><a href="<?=$thumb?>" target="_blank"><img src="<?=$thumb?>"></a></td>
		</tr>
		<?php } ?>
		</table>
		<?php if($description){ ?>
		<div class="title mar_l_8">摘　要</div>
		<div class="content"><?=$description?></div>
		<?php } ?>
	</div>
	<div class="f_l w_200 box_6">
		<h3><span class="b">链接属性</span></h3>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_form">
			<?php if($contentid){?>
			<tr><th>ID：</th><td><?=$contentid?></td></tr>
			<?php } ?>
			<?php if($catname){?>
			<tr><th>栏目：</th><td><a href="<?=$caturl?>" target="_blank"><?=$catname?></a></td></tr>
			<?php } ?>
			<?php if($published){?>
			<tr><th>定时：</th><td><?=$published?>&nbsp;</td></tr>
			<?php } ?>
			<?php if($unpublished){?>
			<tr><th>撤稿：</th><td><?=$unpublished?>&nbsp;</td></tr>
			<?php } ?>
			<?php if($weight){?>
			<tr><th>权重：</th><td><?=$weight?></td></tr>
			<?php } ?>
			<?php if($contentid){?>
			<tr><th>推荐位：</th><td>&nbsp;</td></tr>
			<?php } ?>
			<?php if($editor){?>
			<tr><th>编辑：</th><td><?=$editor?></td></tr>
			<?php } ?>
			<?php if($createdby){?>
			<tr><th>录入：</th><td><a href="javascript: url.member(<?=$createdby?>);"><?=$createdbyname?></a></td></tr>
			<?php } ?>
			<?php if($created){?>
			<tr><th>录入时间：</th><td><?=$created?></td></tr>
			<?php } ?>
			<?php if($modifiedby){?>
			<tr><th>修改：</th><td><a href="javascript: url.member(<?=$modifiedby?>);"><?=$modifiedbyname?></a>&nbsp;</td></tr>
			<?php } ?>
			<?php if($modified){?>
			<tr><th>修改时间：</th><td><?=$modified?>&nbsp;</td></tr>
			<?php } ?>
			<?php if($checkedbyname){?>
			<tr><th>审核：</th><td><a href="javascript: url.member(<?=$checkedby?>)"><?=$checkedbyname?></a>&nbsp;</td></tr>
			<?php } ?>
			<?php if($checked){?>
			<tr><th>审核时间：</th><td><?=$checked?>&nbsp;</td></tr>
			<?php } ?>
			<?php if($lockedby){?>
			<tr><th>锁定：</th><td><a href="javascript: url.member(<?=$lockedby?>)"><?=$lockedbyname?></a>&nbsp;</td></tr>
			<?php } ?>
			<?php if($locked){?>
			<tr><th>锁定时间：</th><td><?=$locked?>&nbsp;</td></tr>
			<?php } ?>
			<?php if($workflow_roleid){?>
			<tr><th>工作流：</th><td><?=$workflow_roleid ? table('role', $workflow_roleid, 'name') : ''?>&nbsp;</td></tr>
			<?php } ?>
			<?php if($status){?>
			<tr><th>状态：</th><td><?=table('status', $status, 'name')?></td></tr>
			<?php } ?>
		</table>
	</div>
	<div class="clear"></div>
</div>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/dropdown/style.css"/>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.treeview.js"></script>
<script type="text/javascript">
var app = '<?=$app?>';
var controller = '<?=$controller?>';
var action = '<?=$action?>';
var catid = '<?=$catid?>';
var modelid = '<?=$modelid?>';
var contentid = <?=$contentid?>;
var baseUrl = '?app=<?=$app?>&controller=<?=$controller?>';
</script>
<?php $this->display('footer', 'system');