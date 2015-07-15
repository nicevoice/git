<?php $this->display('header');?>
<script type="text/javascript" src="apps/field/js/design.js"></script>
<div class="mar_l_8 mar_t_8">
	<div class="box_10" style="width:98%">
		<h3><span class="b">字段设计</span>　点击按钮建立相应字段</h3>
		<ul id="form_type_choose" class="inline list_3 pad_10 lh_24">
<?php foreach($fields as $type => $field):?>
		<li><a href="javascript:;" onclick="design.add(<?=$pid?>, '<?=$type?>')"><?=$field?></a></li>
<?php endforeach;?>
		</ul>
		<div class="clear"></div>
	</div>
	<form id="design_sort" name="design_sort" method="POST" action="?app=field&controller=project&action=sort">
		<div id="design">
<?php foreach($fieldhtmls as $html):?>
	<?=$html?>
<?php endforeach;?>
		</div>
	</form>
</div>
<script type="text/javascript">
design.init();
</script>