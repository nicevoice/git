<?php $this->display('header', 'system');?>
<div class="bk_10"></div>
 <table width="98%" cellpadding="0" cellspacing="0" class="table_form">
 <tr><th width="60" >评分：</td><td>
 <input type="hidden" name="score" value="<?=$score?>" /><div id="score" style="height:20px;position:relative"></div></td></tr>
 <tr><th valign="top" style="padding-top:3px">评语：</td><td><textarea name="comment" style="width:340px;height:55px"></textarea></td></tr>
 </table>
<div class="lh_32 t_c">
 <input type="button" value="确定" onclick="ok();" class="button_style_1"/>
 <input type="button" value="取消" onclick="getDialog().dialog('close');" class="button_style_1"/>
</div>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.score.js"></script>
<script type="text/javascript">
function ok(){
	$.post('?app=system&controller=score&action=editor',{userid:<?=$userid?>,score:$('input[name="score"]:hidden').val(),comment:$('textarea[name="comment"]').val()},function(){
		getDialog().dialog('close');
	});
}
$('#score').score({
	callback:function(v){
		$('input[name="score"]:hidden').val(v);
	}
});
</script>
<?php $this->display('footer', 'system');
