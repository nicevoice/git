<dl id="<?=$fieldid?>" class="list_2">
<dt class="c_blue b f_14">
<span class="f_r">
<img src="images/edit.gif" alt="编辑" title="编辑" width="16" height="16" class="hand" onclick="design.edit(<?=$fieldid?>)" />
<img src="images/del.gif" alt="删除" title="删除" width="16" height="16" class="hand" onclick="design.remove(<?=$fieldid?>)" />
<img src="images/up.gif" alt="向上" title="向上" width="16" height="16" class="hand" onclick="design.up(<?=$fieldid?>)" />
<img src="images/down.gif" alt="向下" title="向下" width="16" height="16" class="hand" onclick="design.down(<?=$fieldid?>)" />
</span>
<span id="no_<?=$fieldid?>"></span><?=$setting['fieldname']?>
</dt>
<dd><input type="text" name="field[<?=$setting['var']?>]" value="<?=$setting['defaultvalue']?>" style="width:<?=$setting['inputsize']?>px" maxlength="<?=$setting['maxnum']?>" /></dd>
</dl>