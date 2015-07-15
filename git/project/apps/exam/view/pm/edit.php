<?php $this->display('header', 'system');?>


<script type="text/javascript" src="uploader/cmstop.uploader.js"></script>
<script type="text/javascript" src="imageEditor/cmstop.imageEditor.js"></script>
<script type="text/javascript" src="js/cmstop.filemanager.js"></script>
    <script type="text/javascript" src="tiny_mce/tiny_mce.js"></script>
    <script type="text/javascript" src="tiny_mce/editor.js"></script>

<div class="bk_10"></div>
<form id="setting" action="" method="POST">
<table class="table_form mar_l_8" cellpadding="0" cellspacing="0" width="98%">
	<caption><?=$subject?></caption>
    <input type="hidden" name="messageid" value="<?=$messageid?>">
	<tr >
		<th width="100px"> 卷名：</th>
		<td><input type="text" value="<?=$subject?>" name="subject" size="100"></td>
	</tr>
    <tr>
        <th>缩略图：</th>
        <td><?=element::image('thumb', $thumb, '45')?></td>
    </tr>
	
	<tr>
        <th width="100px">摘要：</th>
        <td width="540">
            <textarea name="description" style="margin: 0px; height: 110px; width: 620px;" ><?=$description?></textarea>
            </td>
    </tr>
	
    <tr>
        <th width="100px">消息：</th>
        <td width="540">
            <textarea name="message" id="message" style="visibility:hidden;height:450px;width:630px;"><?=$message?></textarea>
            </td>
    </tr>
    <tr>
        <th width="80"><?= element::tips('文章定时发布时间') ?> 上线：</th>
        <td width="300"><input type="text" name="created" id="created" class="input_calendar" value="<?= $created ?>" size="50" style="width: 200px;"/></td>
    </tr>
    <tr>
        <th colspan="2"><hr></th>
    </tr>
    <tr>
        <th width="80"></th>
        <td width="60">
            <input type="submit" value="保存" class="button_style_2" style="float:left"/>
        </td>
    </tr>

</table>
</form>
    <script type="text/javascript">$('.input_calendar').DatePicker({format: 'yyyy-M-d HH:mm:ss'});</script>
<script type="text/javascript">

$('#setting').ajaxForm(function(json){
		if (json.state) {
			ct.tips(json.info);
		} else {
			ct.error(json.error);
		}
	});	

	$('#message').editor(undefined, {'onchange_callback':'editCallback'});
		var editCallback = function() {
			window.changed = true;
			var message=tinyMCE.get('message').getContent();
			$('#message').val(message);
		};
</script>
<?php $this->display('footer', 'system');?>