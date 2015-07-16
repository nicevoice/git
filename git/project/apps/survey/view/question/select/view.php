<dl class="list_2">
<dt class="c_blue b f_14"><span><?=$sort?></span>. <?=$subject?></dt>
<dd><?=$description?></dd>
<dd>

<select name="question[<?=$questionid?>]">
<?php foreach ($option as $o){?>
<option value="<?=$o['optionid']?>" <?=in_array($o['optionid'], $optionid[$questionid]) ? 'selected' : ''?>><?=$o['name']?></option>
<?php }?>
</select>
</dd>
</dl>