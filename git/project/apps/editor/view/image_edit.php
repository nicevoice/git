<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>{#advimage_dlg.dialog_title}</title>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/jquery-ui/dialog.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.ui.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/config.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/cmstop.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.dialog.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.cookie.js"></script>

	<script type="text/javascript" src="<?=ADMIN_URL?>tiny_mce/tiny_mce_popup.js"></script>
	<script type="text/javascript" src="<?=ADMIN_URL?>tiny_mce/utils/mctabs.js"></script>
	<script type="text/javascript" src="<?=ADMIN_URL?>tiny_mce/utils/validate.js"></script>
	<script type="text/javascript" src="<?=ADMIN_URL?>tiny_mce/utils/form_utils.js"></script>
	<script type="text/javascript" src="<?=ADMIN_URL?>tiny_mce/utils/editable_selects.js"></script>
	<script type="text/javascript" src="<?=ADMIN_URL?>tiny_mce/plugins/advimage/js/image.js"></script>

    <script type="text/javascript" src="uploader/cmstop.uploader.js"></script>
    <script type="text/javascript" src="imageEditor/cmstop.imageEditor.js"></script>
	<script type="text/javascript" src="js/cmstop.filemanager.js"></script>
    <script type="text/javascript" src="<?=ADMIN_URL?>tiny_mce/plugins/advimage/js/upload.js"></script>
	<link rel="stylesheet" type="text/css" href="<?=ADMIN_URL?>css/admin.css"/>
	<link href="<?=ADMIN_URL?>tiny_mce/plugins/advimage/css/advimage.css" rel="stylesheet" type="text/css" />
<style  type="text/css">
body{background-color:#FFFFFF}
fieldset{ margin:0px; padding:4px;}
select{ font-size:12px;}
.button_style_1{width:94px; margin-right:0;}
.btn_float{float:right; margin-right:0; margin-left:3px;}
.mceActionPanel{margin: 0 8px;}
</style>
<script type="text/javascript">
    mcTabs.init({
    	selection_class:'s_3'
    });
</script>	
	
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>
<body id="advimage" style="display: none">
    <form onsubmit="ImageDialog.insert();return false;" action="#"> 
		<div class="tag_1" style="margin-top:5px;">
			<ul class="tabs tag_list">
				<li id="general_tab" class="s_3"><span><a href="javascript:mcTabs.displayTab('general_tab','general_panel');" onmousedown="return false;">常规</a></span></li>
				<li id="appearance_tab"><span><a href="javascript:mcTabs.displayTab('appearance_tab','appearance_panel');" onmousedown="return false;">外观</a></span></li>
			</ul>
		</div>

		<div class="panel_wrapper">
			<div id="general_panel" class="panel current">
				<fieldset>
						<legend>常规</legend>

						<table class="properties">
							<tr>
								<td class="column1"><label id="srclabel" for="src">图片地址</label></td>
								<td colspan="2">
								<table border="0" cellspacing="0" cellpadding="0">
									<tr> 
									  <td><input name="src" type="text" id="src" value="" class="mceFocus" onchange="ImageDialog.showPreviewImage(this.value);" /></td> 
									  <td id="srcbrowsercontainer">&nbsp;</td>
									  <td><div id="uploadimg" class="uploader"></div></td>
									</tr>
								  </table></td>
							</tr>
							<tr>
								<td><label for="src_list">{#advimage_dlg.image_list}</label></td>
								<td><select id="src_list" name="src_list" onchange="document.getElementById('src').value=this.options[this.selectedIndex].value;document.getElementById('alt').value=this.options[this.selectedIndex].text;document.getElementById('title').value=this.options[this.selectedIndex].text;ImageDialog.showPreviewImage(this.options[this.selectedIndex].value);"><option value=""></option></select></td>
							</tr>
							<tr> 
								<td class="column1"><label id="altlabel" for="alt">图片说明</label></td> 
								<td colspan="2"><input id="alt" name="alt" type="text" value="" /></td> 
							</tr> 
							<tr> 
								<td class="column1"><label id="titlelabel" for="title">标题</label></td> 
								<td colspan="2"><input id="title" name="title" type="text" value="" /></td> 
							</tr>
						</table>
				</fieldset>

				<fieldset>
					<legend>预览</legend>
					<div id="prev"></div>
				</fieldset>
			</div>

			<div id="appearance_panel" class="panel">
				<fieldset>
					<legend>外观</legend>

					<table border="0" cellpadding="4" cellspacing="0">
						<tr> 
							<td class="column1"><label id="alignlabel" for="align">对齐方式</label></td> 
							<td><select id="align" name="align" onchange="ImageDialog.updateStyle('align');ImageDialog.changeAppearance();"> 
									<option value="">空</option> 
									<option value="baseline">基线</option>
									<option value="top">上方</option>
									<option value="middle">居中</option>
									<option value="bottom">下方</option>
									<option value="text-top">文字上方</option>
									<option value="text-bottom">文字底部</option>
									<option value="left">居左</option>
									<option value="right">居右</option>
								</select> 
							</td>
							<td rowspan="6" valign="top">
								<div class="alignPreview">
									<img id="alignSampleImg" src="<?=ADMIN_URL?>tiny_mce/plugins/advimage/img/sample.gif" alt="示例图片" />
									Lorem ipsum, Dolor sit amet, consectetuer adipiscing loreum ipsum edipiscing elit, sed diam
									nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.Loreum ipsum
									edipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam
									erat volutpat.
								</div>
							</td>
						</tr>

						<tr>
							<td class="column1"><label id="widthlabel" for="width">尺寸</label></td>
							<td class="nowrap">
								<input name="width" type="text" id="width" value="" size="5" maxlength="5" class="size" onchange="ImageDialog.changeHeight();" /> x 
								<input name="height" type="text" id="height" value="" size="5" maxlength="5" class="size" onchange="ImageDialog.changeWidth();" /> px
							</td>
						</tr>

						<tr>
							<td>&nbsp;</td>
							<td><table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input id="constrain" type="checkbox" name="constrain" class="checkbox" /></td>
										<td><label id="constrainlabel" for="constrain">锁定比例</label></td>
									</tr>
								</table></td>
						</tr>

						<tr>
							<td class="column1"><label id="vspacelabel" for="vspace">水平间距</label></td> 
							<td><input name="vspace" type="text" id="vspace" value="" size="3" maxlength="3" class="number" onchange="ImageDialog.updateStyle('vspace');ImageDialog.changeAppearance();" onblur="ImageDialog.updateStyle('vspace');ImageDialog.changeAppearance();" />
							</td>
						</tr>

						<tr> 
							<td class="column1"><label id="hspacelabel" for="hspace">垂直间距</label></td> 
							<td><input name="hspace" type="text" id="hspace" value="" size="3" maxlength="3" class="number" onchange="ImageDialog.updateStyle('hspace');ImageDialog.changeAppearance();" onblur="ImageDialog.updateStyle('hspace');ImageDialog.changeAppearance();" /></td> 
						</tr>

						<tr>
							<td class="column1"><label id="borderlabel" for="border">边框</label></td> 
							<td><input id="border" name="border" type="text" value="" size="3" maxlength="3" class="number" onchange="ImageDialog.updateStyle('border');ImageDialog.changeAppearance();" onblur="ImageDialog.updateStyle('border');ImageDialog.changeAppearance();" /></td> 
						</tr>

						<tr>
							<td><label for="class_list">类名</label></td>
							<td colspan="2"><select id="class_list" name="class_list" class="mceEditableSelect"><option value=""></option></select></td>
						</tr>

						<tr>
							<td class="column1"><label id="stylelabel" for="style">样式</label></td> 
							<td colspan="2"><input id="style" name="style" type="text" value="" onchange="ImageDialog.changeAppearance();" /></td> 
						</tr>

						<!-- <tr>
							<td class="column1"><label id="classeslabel" for="classes">{#advimage_dlg.classes}</label></td> 
							<td colspan="2"><input id="classes" name="classes" type="text" value="" onchange="selectByValue(this.form,'classlist',this.value,true);" /></td> 
						</tr> -->
					</table>
				</fieldset>
			</div>

			<div id="advanced_panel" class="panel">
				<fieldset>
					<legend>交替图像</legend>

					<input type="checkbox" id="onmousemovecheck" name="onmousemovecheck" class="checkbox" onclick="ImageDialog.setSwapImage(this.checked);" />
					<label id="onmousemovechecklabel" for="onmousemovecheck">替换图像</label>

					<table border="0" cellpadding="4" cellspacing="0" width="100%">
							<tr>
								<td class="column1"><label id="onmouseoversrclabel" for="onmouseoversrc">鼠标划入</label></td> 
								<td><table border="0" cellspacing="0" cellpadding="0"> 
									<tr> 
									  <td><input id="onmouseoversrc" name="onmouseoversrc" type="text" value="" /></td> 
									  <td id="onmouseoversrccontainer">&nbsp;</td>
									</tr>
								  </table></td>
							</tr>
							<tr>
								<td><label for="over_list">{#advimage_dlg.image_list}</label></td>
								<td><select id="over_list" name="over_list" onchange="document.getElementById('onmouseoversrc').value=this.options[this.selectedIndex].value;"><option value=""></option></select></td>
							</tr>
							<tr> 
								<td class="column1"><label id="onmouseoutsrclabel" for="onmouseoutsrc">鼠标划出</label></td> 
								<td class="column2"><table border="0" cellspacing="0" cellpadding="0"> 
									<tr> 
									  <td><input id="onmouseoutsrc" name="onmouseoutsrc" type="text" value="" /></td> 
									  <td id="onmouseoutsrccontainer">&nbsp;</td>
									</tr> 
								  </table></td> 
							</tr>
							<tr>
								<td><label for="out_list">{#advimage_dlg.image_list}</label></td>
								<td><select id="out_list" name="out_list" onchange="document.getElementById('onmouseoutsrc').value=this.options[this.selectedIndex].value;"><option value=""></option></select></td>
							</tr>
					</table>
				</fieldset>

				<fieldset>
					<legend>其它</legend>

					<table border="0" cellpadding="4" cellspacing="0">
						<tr>
							<td class="column1"><label id="idlabel" for="id">ID</label></td> 
							<td><input id="id" name="id" type="text" value="" /></td> 
						</tr>

						<tr>
							<td class="column1"><label id="dirlabel" for="dir">书写语言方向</label></td> 
							<td>
								<select id="dir" name="dir" onchange="ImageDialog.changeAppearance();"> 
										<option value="">空</option> 
										<option value="ltr">从左到右</option> 
										<option value="rtl">从右到左</option> 
								</select>
							</td> 
						</tr>

						<tr>
							<td class="column1"><label id="langlabel" for="lang">语言编码</label></td> 
							<td>
								<input id="lang" name="lang" type="text" value="" />
							</td> 
						</tr>

						<tr>
							<td class="column1"><label id="usemaplabel" for="usemap">图像热点</label></td> 
							<td>
								<input id="usemap" name="usemap" type="text" value="" />
							</td> 
						</tr>

						<tr>
							<td class="column1"><label id="longdesclabel" for="longdesc">链接描述</label></td>
							<td><table border="0" cellspacing="0" cellpadding="0">
									<tr>
									  <td><input id="longdesc" name="longdesc" type="text" value="" /></td>
									  <td id="longdesccontainer">&nbsp;</td>
									</tr>
								</table></td> 
						</tr>
					</table>
				</fieldset>
			</div>
		</div>

		<div class="mceActionPanel">
            <input type="button" class="button_style_1 btn_float"   name="cancel" value="取消" onclick="tinyMCEPopup.close();" />
            <input type="submit" class="button_style_1 btn_float"   name="insert" value="插入" />
		</div>
    </form>
</body> 
</html> 
