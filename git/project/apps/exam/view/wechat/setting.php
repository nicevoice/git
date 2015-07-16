<?php $this->display('header', 'system');?>


<style>
.table_info th {
    text-align: center;
}.table_info td {
    border: 1px solid #D0E6EC !important;
    border-width: 1px 0 0 1px;
    text-align: center;
}
</style>
<script type="text/javascript" src="apps/exam/js/automatic_exam.js"></script>
<div class="bk_10"></div>
<form id="setting" action="" method="POST">
<table class="table_form mar_l_8" cellpadding="0" cellspacing="0" width="98%">
	<caption>SEO设置</caption>
    <tr >
        <th width="100px"> 标题：</th>
        <td><input type="text" name="title" value="<?=$title?>" size="60" maxlength="255" class=""></td>
    </tr>
	<tr >
		<th width="100px"> 关键词：</th>
		<td><input type="text" name="keywords" value="<?=$keywords?>" size="60" maxlength="255" class="hover"></td>
	</tr>

    <tr>
        <th>描述：</th>
        <td><textarea name="description" cols="60" rows="3" class=""><?=$description?></textarea></td>
    </tr>
</table>
<table class="table_form mar_l_8" cellpadding="0" cellspacing="0" width="98%">
    <caption>试卷设置</caption>
    <tr>
        <th width="100px">考试时间（秒）：</th>
        <td><input type="text" name="time" value="<?=$time?>" size="20" maxlength="255" class="hover"></td>
    </tr>
    <tr>
        <th width="100px">开启：</th>
        <td><input type="radio" value="0" name="open">关闭 <input type="radio" value="1" name="open" checked="checked">开启</td>
    </tr>

</table>
<table class="table_form mar_l_8" cellpadding="0" cellspacing="0" width="98%">

    <tr>
		<th>&nbsp;</th>
		<td>
			<input type="submit" class="button_style" value="保存"/>
		</td>
	</tr>

</table>
</form>
<script type="text/javascript">
$('#setting').ajaxForm(function(json){
		if (json.state) {
			ct.tips(json.info);
		} else {
			ct.error(json.error);
		}
	});
</script>
<?php $this->display('footer', 'system');?>