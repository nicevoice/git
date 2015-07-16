
<div style="padding: 10px;">
    <input type="text" name="" id="q_key" value="" size="40"><input type="button" onclick="question.get('<?=$type?>','<?=$subjectid?>')" value="搜索" class="button_style_1">
   科目： <?= $catname?>
</div>
<hr>
<div class="bk_10"></div>
<form method="post" id="lists-band" action="?app=<?=$app?>&controller=<?=$controller?>&action=<?=$action?>">
<?php
foreach($questions as $k=>$q):
?>
<dl class="list_2">
<dt class="c_blue b f_14"><span><?=$sort?></span><?=$q['subject']?><span><input type="checkbox" value="<?=$q['questionid']?>" name="questionid[]"></span></dt>
</dl>
<?php endforeach;?>
</form>