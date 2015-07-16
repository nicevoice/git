<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>插入/编辑FLASH</title>
	
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/jquery-ui/dialog.css" />
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.ui.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/config.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/cmstop.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.dialog.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.cookie.js"></script>
<script type="text/javascript" src="uploader/cmstop.uploader.js"></script>
<!--tinymce-->
<script type="text/javascript" src="<?=ADMIN_URL?>tiny_mce/tiny_mce_popup.js"></script>
<script type="text/javascript" src="<?=ADMIN_URL?>tiny_mce/plugins/ct_media/js/flash.js"></script>
<script type="text/javascript" src="<?=ADMIN_URL?>tiny_mce/utils/mctabs.js"></script>
<script type="text/javascript" src="<?=ADMIN_URL?>tiny_mce/utils/validate.js"></script>
<script type="text/javascript" src="<?=ADMIN_URL?>tiny_mce/utils/form_utils.js"></script>
<script type="text/javascript" src="<?=ADMIN_URL?>tiny_mce/utils/editable_selects.js"></script>
<!--tinymce-->

<link rel="stylesheet" type="text/css" href="<?=ADMIN_URL?>css/admin.css"/>
<link rel="stylesheet" type="text/css" href="<?=ADMIN_URL?>tiny_mce/plugins/ct_media/css/media.css"/>
<style  type="text/css">
body{background-color:#FFFFFF}
fieldset{ margin:0px; padding:2px;width:98%}
select{ font-size:12px; border:}
.button_style_1{width:94px;}
.btn_float{float:right; margin-right:0; margin-left:3px;}
#vsearch{ float:right;}
.operation_area{background:none}
.mceActionPanel{ margin: 0 8px;}
</style>
<script type="text/javascript">
    mcTabs.init({
    	selection_class:'s_3'
    });
</script>


<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>
<body style="display:none">
	<div class="bk_8"></div>
    <form onSubmit="insertFlash(this);return false;" action="#" style="overflow:hidden">
		<div id="general_panel" class="panel current">
			<fieldset>
				<legend>常规</legend>
				<table border="0" cellpadding="4" cellspacing="0">
						<tr>
						<td align="center"><label for="src">文件/网址</label></td>
						  <td>
								<table border="0" cellspacing="0" cellpadding="0">
								  <tr>
									<td><input id="src" name="src" type="text" value="" onChange="generatePreview();" /></td>
									<td id="filebrowsercontainer">&nbsp;</td>
									<td>
										<span id="uploader" class="button" style="position: relative; overflow: hidden; display: inline-block;">上传FLASH</span>
									</td>
								  </tr>
								</table>
							</td>
						</tr>
						<tr>
							<td align="center"><label for="width">尺寸</label></td>
							<td>
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="text" id="width" name="width" value="" onChange="generatePreview('width');" /> x <input type="text" id="height" name="height" value="" class="size"  onchange="generatePreview('height');" /></td>
										<td>&nbsp;&nbsp;<input id="constrain" type="checkbox" name="constrain" class="checkbox" /></td>
										<td><label id="constrainlabel" for="constrain">锁定比例</label></td>
									</tr>
								</table>
							</td>
						</tr>
				</table>
			</fieldset>
			<div class="bk_8"></div>
			<fieldset>
				<legend>预览</legend>
				<div id="prev"></div>
			</fieldset>
		</div>
		<div class="bk_8"></div>
		<div class="mceActionPanel" align="center">
            <input type="button"  name="cancel" class="button_style_1 btn_float" value="取消" onClick="tinyMCEPopup.close();" />
			<input type="submit"  name="insert" class="button_style_1 btn_float" value="插入" />
		</div>
</form>
</body>
</html>