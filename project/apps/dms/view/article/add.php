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
.remove {width: 16px; height: 16px; background: url('<?=IMG_URL?>css/images/icons.gif') no-repeat 0 -38px; float: left; cursor: pointer}
</style>
<body>
<?php $this->display('sider');?>
<div class="dms_search">
</div>
<div id="dms_content" class="dms_content">
	<div class="dms_inner">
		<div class="bk_8"></div>
		<form name="article_add" id="article_add" method="POST" action="?app=dms&controller=article&action=add" onsubmit="return false;">
			<table width="98%" border="0" id="tabledata" cellspacing="0" cellpadding="0"   class="table_form mar_l_8">
				<tr>
					<th width="80"><span class="c_red">*</span> 标题：</th>
					<td><input type="text" name="title" value="" size="60" /></td>
				</tr>
				<tr>
					<th> Tags：</th>
					<td><input type="text" name="tags" value="" size="60" /></td>
				</tr>
				<tr>
					<th>来源：</th>
					<td class="c_077ac7">
					  <input type="text" name="source" value="<?=$source?>" size="15"/>
					  &nbsp;&nbsp;&nbsp;&nbsp;
					  <label for="author">作者： </label>
					  <input type="text" name="author" value="<?=$author?>" size="15"/>
					</td>
				</tr>
				<tr>
					<th>&nbsp;&nbsp;</th>
					<td width="540" style="position:relative">
						<textarea name="content" id="content" style="visibility:hidden;height:350px;width:630px;"></textarea>
					</td>
					<td class="vtop"></td>
				</tr>
				<tr>
					<th>&nbsp;&nbsp;摘要：</th>
					<td width="540" style="position:relative">
						<textarea name="description" style="height:40px;width:621px; background:#fff;"></textarea>
					</td>
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
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.lightbox.js"></script>
<script type="text/javascript" src="js/related.js"></script>
<script type="text/javascript" src="apps/page/js/section.js"></script>
<script type="text/javascript" src="apps/special/js/push.js"></script>
<script type="text/javascript" src="apps/dms/js/group.js"></script>
<link rel="stylesheet" type="text/css" href="css/imagesbox.css" media="screen" />
<script type="text/javascript" src="uploader/cmstop.uploader.js"></script>
<script type="text/javascript" src="imageEditor/cmstop.imageEditor.js"></script>
<script type="text/javascript" src="js/cmstop.filemanager.js"></script>
<script src="tiny_mce/tiny_mce.js" type="text/javascript"></script>
<script src="tiny_mce/editor.js" type="text/javascript"></script>
<script src="js/related.js" type="text/javascript"></script>
<script type="text/javascript">
$(function(){
	var ed = null;
	$("#multiUp").uploader({
			script : '?app=editor&controller=filesup&action=upload',
			fileDataName : 'multiUp',
			fileExt : '<?=$allow?>',
			buttonImg : 'images/multiup.gif',
			complete:function(response, data){
				response =(new Function("","return "+response))();
				if(response.state) {
					ed || (ed = tinyMCE.get('content'));
					ed.execCommand('mceInsertContent', false, response.code);
					ct.ok(response.msg);
				} else {
					ct.error(response.msg);
				}
			}
	});
});

function get_thumb(){
	var content = tinyMCE.activeEditor.getContent(),
	reg = /<img\s+[^>]*src\s*=\s*(["\']?)([^>"\']+)\1[^>]*[\/]?>/img,imgs;
	while(imgs = reg.exec(content)){
		if(imgs[2].indexOf('/images/ext/') != -1) continue;
		$.post('?app=article&controller=article&action=thumb',{url:imgs[2]},function(url){
			$('input[name="thumb"]:text').val(url).nextAll('button:last').show();
		});
		break;
	}
}

var removeField = function(obj) {
	ct.confirm('确定要删除吗?', function() {
		$(obj).parent().parent().remove();	
	});
}

$('#content').editor('mini');

var templateIndex	= 0;

var expandRowAdd = function(obj) {
	var templateRow	= '<tr class="expandinput">\
<th width="60"></th><td><input class="expandkey" type="text" name="expand[' + templateIndex + '][key]" value="变量名" onfocus="this.value == \'变量名\' && (this.value = \'\')" onblur=" this.value || (this.value = \'变量名\')" /><input type="text" class="expandvalue" name="expand[' + templateIndex + '][value]" value="值" onfocus="this.value == \'值\' && (this.value = \'\')" onblur=" this.value || (this.value = \'值\')" /><span class="remove" onclick="removeField(this);"></span></td>\
</tr>';
	var target = $(obj).parent().parent().parent('tr').parent().find('tr');
	templateIndex++;
	target.eq(target.length-1).before(templateRow);
}
form	= $('form');
$(function(){
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
});
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
</script>
<?php $this->display('footer');