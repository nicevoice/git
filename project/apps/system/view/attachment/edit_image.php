<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$CONFIG['charset']?>" />
<title>图片编辑器</title>
<link href="css/admin.css" rel="stylesheet" type="text/css" />
<style>
body { overflow: hidden;margin: 0;padding: 0; height:100%;width:100%;}
.word_img { border: 1px solid transparent;position: absolute; top: 0;left: 0; cursor:move;}
img.hover { border: 1px solid lightblue; }
img.selected { border: 1px solid blue; }
#imgarea { overflow:hidden; }
#img { position: relative;left: 0;top: 0; }
#pointer { cursor:pointer;-moz-selection:none;-moz-user-select: none;}
#pointer::moz-selection { }
#pointer::selection {   }
.dialog_input { font-size: 12px; line-height: 12px; height: 14px; }
#transgifwrapper { overflow: hidden;border: 1px solid transparent;position: absolute;z-index: 100;background:transparent url(images/trans.png) repeat;}
</style>
<link rel="stylesheet" href="<?=IMG_URL?>js/lib/Jcrop/style.css" type="text/css" />
<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/jquery-ui/dialog.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.ui.js"></script>
<script type="text/javascript" src="apps/system/js/dialog_deprecated.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.Jcrop.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.colorPicker.js"></script>
<script type="text/javascript" src="js/file_manager/edit_image.js"></script>
</head>
<body>
<script type="text/javascript">
//历史队列
window.img_history = ["<?php echo $fileurl ? UPLOAD_URL.$fileurl : UPLOAD_URL.$file['filepath'].$file['filename'];?>"];
//临时文件存储
window.temporary_img_list = [];
//临时文字图片存储
window.temporary_word_imgs = [];
//当前在历史队列的位置
window.img_current = 1;
window.onresize = init_window;
//原始图片地址
var orig_img_src = "<?php echo $fileurl ? UPLOAD_URL.$fileurl : UPLOAD_URL.$file['filepath'].$file['filename'];?>";

function init_window()
{
	$('#bottom_buttons').css('margin','0px');
	$('#imgarea').height($(window).height()-110).width($('#imgarea').height());
	if ($('#img').length > 0 && $('#img').width() > 0)
	{
		var img = $('img');
		var p = $('#imgarea').width()/img.data('width');
		var p1 = $('#imgarea').height()/img.data('height');
		if (p1<p) p= p1;
		if (p > 1) p = 1;
		$(img).data('minp',p);
		zoom_to(p,200);
	}
	$(document.body).bind('selectstart',function(event)
	{ 
		if (event.target.tagName.toLowerCase() == 'input') return true;
		event.preventDefault(); 
		return false;
	});
}

//字体列表
var font_options = '<?php
	$handler = opendir(ROOT_PATH.'resources/fonts/');
	while( ($v = readdir($handler)) !== false)
	{
		if (!is_file(ROOT_PATH.'resources/fonts/'.$v) || $v == '.' || $v == '..') continue;
		
		$name = preg_replace('/\.ttf$/i','',$v);
		echo '<option value="'.$v.'">'.$name.'</option>';
		
	}
	closedir($handler);
?>';



$(function()
{
	init_window();
	//载入原图
	load_img(img_history[0]);
	//图片自动缩放到最适合工作区
	$('#pointer').bind('mousedown.resize',function(event)
	{
		var start_y = event.clientY;
		var start_top = parseInt($('#pointer').css('top'));
		$(document.body).bind('mousemove.pointer',function(event)
		{
			var end_y = event.clientY;
			var d = start_y - end_y;
			var top = start_top - d;
			if (top < 0) top = 0;
			if (top > 290) top = 290;
			$('#pointer').css('top',top);
			
			update_zoom();
		});
		
		$(document.body).bind('mouseup.pointer',function()
		{
			//console.log('up');
			$(document.body).unbind('.pointer');
		});
	}).bind('selectstart',function(){ return false; });
	//绑定返回事件
	$('#prev_history').fadeTo(0,0).click(function()
	{
		if (img_current == 1) return;
		img_current--;
		if (img_current < 1) img_current = 1;
		if (img_current == 1) $('#prev_history').fadeTo(300,0);
		load_img(img_history[img_current-1]);
		if (img_history.length != img_current) $('#next_history').fadeTo(300,1);
	});
	//绑定前进事件
	$('#next_history').fadeTo(0,0).click(function()
	{
		if (img_current == img_history.length) return;
		img_current++;
		if (img_current > img_history.length) img_current = img_history.length;
		if (img_current == img_history.length) $('#next_history').fadeTo(300,0);
		load_img(img_history[img_current-1]);
		if (img_current > 1) $('#prev_history').fadeTo(300,1);
	});
});
</script>

<div id="pop_1" style="background-color:#fbfeff;border:0;padding-top:10px;">
  <div class="pop_box_area">
    <div class="lh_24" style="padding-left:85px;">
      <input type="button" rel="topbt"  value="加字" class="button_style_1" onclick="disable_topbt(this);add_word()" />
      <input type="button" rel="topbt"  value="剪裁" rel="crop" class="button_style_1" onclick="disable_topbt(this);do_crop(event);" />
      <input type="button" rel="topbt"  value="大小" class="button_style_1" onclick="disable_topbt(this);do_scale()" />
      <input type="button" rel="topbt"  value="后退" class="button_style_1" id="prev_history" />
      <input type="button" rel="topbt"  value="前进" class="button_style_1" id="next_history" />
    </div>
    <div class="attachment_lib">
      <div class="f_l w_80 zoom" style="height:302px;">
      	<span class="pointer" id="pointer" style="top:0px;">100%</span>
      </div>
      <div class="f_l">
        <div style="position:relative;width:300px; height:300px; " class="box_10" id="imgarea"></div>
        <div style="height:40px;overflow:hidden;">
	        <div class="mar_5 vt_m lh_24" style="display:none;height:40px;overflow:hidden;" id="crop_bar">
	        	裁剪后尺寸：
        		<input id="croped_width" onkeyup="update_crop()" rel="crop" value="0" size="4" /> X <input id="croped_height" onkeyup="update_crop()" rel="crop" value="0" size="4" />&nbsp;&nbsp;
        		<input type="button" rel="crop" class="button_style_1" value="确定" onclick="do_crop(event);" />&nbsp;
        		<input type="button" class="button_style_1" value="取消" onclick="" />
	        </div>
	        <div class="mar_5 vt_m lh_24" style="display:none;height:40px;overflow:hidden;" id="add_word_bar">
        		<input type="button" rel="addword" class="button_style_1" value="修改" onclick="edit_addword()" />
        		<input type="button" rel="addword" class="button_style_1" value="删除" onclick="delete_addword()" />
        		<input type="button" rel="addword"  class="button_style_1" value="合并" onclick="add_word_complete()" />&nbsp;
        		<a href="javascript:addword_put_forward()"><img src="images/layer_forward.gif" border="0" rel="addword" alt="上移一层"  /></a>
        		<a href="javascript:addword_put_backward()"><img src="images/layer_backward.gif" border="0" rel="addword" alt="下移一层"  /></a>
	        </div>
	     </div>
       </div>
       <div class="clear"></div>
    </div>
    <div class="mar_5 t_c" id="bottom_buttons">
      <input type="button" value="确定" rel="crop" class="button_style_1" onclick="all_submit()" />
      <input type="button" value="取消" class="button_style_1" onclick="close_window()" />
    </div>
  </div>
</div>
</body>
</html>