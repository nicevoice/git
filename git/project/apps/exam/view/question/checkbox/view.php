<dl class="list_2">
<dt class="c_blue b f_14"><span><?=$sort?></span>. <?=$subject?></dt>
<dd><?=$description?></dd>
<?php foreach ($option as $o){?>
<dd><input type="checkbox" name="option[<?=$o['optionid']?>]" value="<?=$o['optionid']?>" <?=in_array($o['optionid'], $optionid[$questionid]) ? 'checked' : ''?> class="bdr_5" /><?=$o['name']?></dd>
<?php }?>
</dl>