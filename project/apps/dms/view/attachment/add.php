<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$CONFIG['charset']?>" />
<title><?=$head['title']?></title>
<link rel="stylesheet" type="text/css" href="css/admin.css"/>
<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/jquery-ui/dialog.css" />
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/validator/style.css"/>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.ui.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/config.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/cmstop.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.validator.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.dialog.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.cookie.js"></script>
<link href="<?=IMG_URL?>js/lib/treeview/treeview.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.treeview.js"></script>
<link href="<?=IMG_URL?>js/lib/tree/style.css" rel="stylesheet" type="text/css" />
<script src="<?=IMG_URL?>js/lib/cmstop.tree.js" type="text/javascript"></script>
<script type="text/javascript" src="apps/system/js/content.js"></script>
<!-- 时间选择器 -->
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.datepicker.js"></script>
<link href="<?=IMG_URL?>js/lib/datepicker/style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript">
$.validate.setConfigs({
    xmlPath:'apps/<?=$app?>/validators/'
});
$(function(){
    ct.listenAjax();
});
</script>

<link href="apps/dms/css/style.css" rel="stylesheet" type="text/css" />

</head>
<style text="text/css">
.expandinput input{width: 108px; margin: 2px 16px; float: left;}
.remove {width: 16px; height: 16px; background: url('<?=IMG_URL?>css/images/icons.gif') no-repeat 0 -38px; float: left;}
</style>
<body>
<?php $this->display('sider');?>
<div class="dms_search">
</div>
<div id="dms_content" class="dms_content">
	<div class="dms_inner">
		<div class="bk_8"></div>
		<form id="attachment_add" method="POST" action="?app=dms&controller=attachment&action=add">
			<table width="98%" border="0" id="tabledata" cellspacing="0" cellpadding="0"   class="table_form mar_l_8">
				<tr>
					<th width="80"><span class="c_red">*</span> 标题：</th>
					<td><?=element::title('title', $title, $color)?></td>
				</tr>
				<tr>
					<th> Tags：</th>
					<td><?=element::tag('tags', $tags)?></td>
				</tr>
				<tr>
					<th>来源：</th>
					<td class="c_077ac7">
					  <input type="text" name="source" autocomplete="1" value="<?=$source?>" url="?app=system&controller=source&action=suggest&q=%s" size="15"/>
					  &nbsp;&nbsp;&nbsp;&nbsp;
					  <label for="editor">作者： </label>
					  <input type="text" name="author" value="<?=$author?>" size="15"/>
					</td>
				</tr>
				<tr>
					<th><span class="c_red">*</span> 附件： </th>
					<td>
						<div id="uploadify" class="uploader" style="float:left"></div>
					</td>
				</tr>
				<tr>
					<th></th>
					<td id="fileinfo"></td>
				</tr>
				<tr>
					<td colspan="2"><div class="mar_l_8 hand title">扩展字段<span onclick="expandRowAdd(this);" style="padding-left:12px;">+</span></div></td>
				</tr>
				<tr>
					<th>&nbsp;</th>
					<td>
						<input type="submit" value="保存" class="button_style_2" style="float:left"/>
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
<link href="<?=IMG_URL?>js/lib/autocomplete/style.css" rel="stylesheet" type="text/css" />
<link href="<?=IMG_URL?>js/lib/colorInput/style.css" rel="stylesheet" type="text/css" />
<script src="<?=IMG_URL?>js/lib/cmstop.autocomplete.js" type="text/javascript"></script>
<script src="<?=IMG_URL?>js/lib/cmstop.colorInput.js" type="text/javascript"></script>
<script type="text/javascript" src="uploader/cmstop.uploader.js"></script>
<script type="text/javascript" src="imageEditor/cmstop.imageEditor.js"></script>
<script type="text/javascript" src="js/cmstop.filemanager.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.lightbox.js"></script>
<script type="text/javascript" src="js/related.js"></script>
<script type="text/javascript" src="apps/page/js/section.js"></script>
<script type="text/javascript" src="apps/special/js/push.js"></script>
<script type="text/javascript" src="apps/dms/js/group.js"></script>
<link rel="stylesheet" type="text/css" href="css/imagesbox.css" media="screen" />
<script type="text/javascript">
var templateRow	= '<tr class="expandinput">\
<th width="60"></th><td><input class="expandkey" type="text" name="expand[key][0]" value="变量名" onfocus="this.value == \'变量名\' && (this.value = \'\')" onblur=" this.value || (this.value = \'变量名\')" /><input class="expandvalue" type="text" name="expand[value][0]" value="值" onfocus="this.value == \'值\' && (this.value = \'\')" onblur=" this.value || (this.value = \'值\')" /><span class="remove" onclick="$(this).parent().parent().remove();"></span></td>\
</tr>';
var expandRowAdd = function(obj) {
	var target = $(obj).parent().parent().parent('tr').parent().find('tr');
	target.eq(target.length-1).before(templateRow);
}
$("#uploadify").uploader({
	script		: '?app=dms&controller=attachment&action=upload',
	fileDesc	: '附件',
	fileExt		: '*.*',
	buttonImg	: 'images/multiup.gif',
	complete:function(response, data){
		if(response != 0){
			var info		= response.split('|');
			var fileName	= data.file.name;
			var filePath	= info[1];
			data.file.type	= data.file.type.substr(1);
			var fileExt		= info[2] ? '<img src="'+info[2]+'" alt="'+data.file.type+'" />' : data.file.type;
			var fileSize	= Math.round(data.file.size/ 1024*100)/100 + 'kb';
			var table = $('#fileinfo').empty().append('<table>').find('table');
			table.append('<tr><td>文件名:</td><td>'+fileName+'<input type="hidden" name="path" value="'+filePath+'" /></td>');
			table.append('<tr><td>文件类型:</td><td>'+fileExt+'<input type="hidden" name="ext" value="'+data.file.type+'" /></td>');
			table.append('<tr><td>文件大小:</td><td>'+fileSize+'<input type="hidden" name="size" value="'+fileSize+'" /></td>');
		}else{
			ct.error('上传失败');
		}
	},
	error:function(data)
	{
		ct.error(data.error.type +':'+ data.error.info);
	}
});
var expandRowAdd = function(obj) {
	var target = $(obj).parent().parent().parent('tr').parent().find('tr');
	target.eq(target.length-1).before(templateRow);
}

function submit_ok(json) {
	if(json.state) {
		ct.confirm('恭喜，内容添加成功。', function() {
			ct.assoc.close();
		}, function() {
			ct.assoc.close();
		});
	} else {
		ct.error(json.error||'添加失败');
	}
}
$(document).ready(function() {
	form	= $('form');
	form.ajaxForm('submit_ok', null, function() {
		var expandkey	= form.find('.expandkey');
		var expandvalue	= form.find('.expandvalue');
		$.each(expandkey, function(k,v) {
			v = $(v);
			if (v.val() == '变量名') {
				v.parent().remove();
			}
		});
		$.each(expandvalue, function(k,v) {
			v = $(v);
			if (v.val() == '值') {
				v.parent().remove();
			}
		});
	});
	window.onbeforeunload = function() {
		return;
	}
});
</script>
<?php $this->display('footer');