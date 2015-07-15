<div class="title">
	<div class="f_r">
	<?php if (in_array($section['type'],array('hand','auto','html'))):?>
		<?php if ($section['published']):?>
		上次生成时间：<?=date('Y-m-d H:i:s', $section['published']);?>
		<?php else:?>
		未生成
		<?php endif;?>
	<?php else:?>
		<?php if ($section['published']):?>
		上次抓取时间：<?=date('Y-m-d H:i:s', $section['published']);?>
		<?php else:?>
		未抓取
		<?php endif;?>
	<?php endif;?>
	</div>
	<div class="f_l"><?=$section['name']?>（预览）</div>
</div>
<div class="mar_5" style="border:1px solid #9C6; padding:8px;<?=($section['width'] > 0)?'width:'.($section['width']+16).'px;':''?>"<?=$section['width']?' title="区块宽度'.$section['width'].'px"' : ''?>>
<?=$html?>
</div>