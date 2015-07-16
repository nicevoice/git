<dl id="<?=$questionid?>" class="list_2">
<dt class="c_blue b f_14">
<span class="f_r">
<img src="images/edit.gif" alt="编辑" title="编辑" width="16" height="16" class="hand" onclick="question.edit(<?=$questionid?>)" />
<img src="images/del.gif" alt="删除" title="删除" width="16" height="16" class="hand" onclick="question.remove(<?=$questionid?>)" />
<img src="images/up.gif" alt="向上" title="向上" width="16" height="16" class="hand" onclick="question.up(<?=$questionid?>)" />
<img src="images/down.gif" alt="向下" title="向下" width="16" height="16" class="hand" onclick="question.down(<?=$questionid?>)" />
</span>
<span id="no_<?=$questionid?>"><?=$n?></span>. <?=$subject?>
</dt>
<dd><?=$description?></dd>
<?php foreach ($option as $o){?>
<dd><input type="radio" name="option[]" value="<?=$o['optionid']?>" class="bdr_5" /> <?=$o['name']?></dd>
<?php }?>
</dl>