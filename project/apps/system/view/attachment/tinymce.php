<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$CONFIG['charset']?>" />
<title>资源库</title>
<link href="css/admin.css" rel="stylesheet" type="text/css" />
<link href="<?php echo IMG_URL?>js/lib/pagination/style.css" rel="stylesheet" type="text/css" />
<link href="<?php echo IMG_URL?>js/lib/treeview/treeview.css" rel="stylesheet" type="text/css" />
<link href="apps/system/css/attachment.css" rel="stylesheet" type="text/css" />
<!--support-->
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/config.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/cmstop.js"></script>
<!--dialog-->
<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/jquery-ui/dialog.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.ui.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.dialog.js"></script>

<script type="text/javascript" src="imageEditor/cmstop.imageEditor.js"></script>
<script type="text/javascript" src="uploader/cmstop.uploader.js"></script>

<!--contextmenu-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<!-- 时间选择器 -->
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.datepicker.js"></script>
<link href="<?=IMG_URL?>js/lib/datepicker/style.css" rel="stylesheet" type="text/css" />

<!--photo preview -->
<script type="text/javascript" src="js/file_manager/photo.js"></script>
<script type="text/javascript" src="js/file_manager/file.js"></script>
<?php if ($_GET['select'] || $_GET['tinymce']):?>
<script type="text/javascript" src="js/file_manager/for_tinymce.js"></script>
<script>
var for_tinymce = true;
</script>
<?php endif; ?>
</head>
<body>
<div class="pop_box_area" id="window_all">
  <?php if($_GET['select']):?>
    <style type="text/css">
        .dialog-box { border: none; }
        .dialog-box .tabs, .dialog-box .head { height: 30px; line-height: 26px; }
        .dialog-box .tabs li { height: 25px; line-height: 24px; }
        .operation_area { background-image: none; border-bottom: none; }
    </style>
	<div class="tag_1" style="margin-top:5px;">
		<ul class="tag_list">
			<li id="local_tab"><span><a href="?app=editor&controller=image&action=index" onmousedown="return false;">本地上传</a></span></li>
			<li class="s_3"><span><a href="javascript:void(0);">资源库</a></span></li>
            <li id="remote_tab"><span><a href="?app=editor&controller=image&action=index#tab=remote_tab" onmousedown="return false;">网络图片</a></span></li>
		</ul>
	</div>
  <?php endif; ?>
  <div class="operation_area" id="window_top">
    <div class="f_r" style="width:135px">
      <button style="float:right;display:block;" type="button" class="button_style_1" onclick="refresh_data();">刷新</button>
      <span id="mul_upload" class="button" >批量上传</span>
    </div>
    <div class="f_l search_icon" style="width:470px;">
      <table cellpadding="0" cellspacing="0">
        <tr>
          <td width="90"><input type="text" size="12" id="search_keyword" class="search_input_text" onkeypress="if (event.keyCode==13) search_file();" /></td>
          <td><input id="my_documents" type="checkbox" style="display:none" /></td>
          <td width="16"><input class="checkbox" id="only_children" type="checkbox" value="" /></td>
          <td class="t_c" width="55">当前目录</td>
            <td width="85"><input id="calendar_from" type="text" class="input_calendar search_input_text" value="" size="12" style="width:85px;" /></td>
            <td class="t_c" width="15">至</td>
            <td width="85"><input id="calendar_to" type="text" class="input_calendar search_input_text" value="" size="12" style="width:85px;" /></td>
          <td class="t_c" width="60"><input type="button" value="搜索" class="button_style_1" onclick="search_file()" /></td>
        </tr>
      </table>
    </div>
    <div class="clear"></div>
  </div>
  <div class="attachment_lib" id="window_main">
    <div class="f_l attachment_l">
      <div class="box_10" id="window_left_top">
        <h3>菜单</h3>
        <ul>
          <li><a href="javascript:;" id="my_docs_button" onclick="show_my_documents()">我的文档</a></li>
          <li class="attachment_folder">常用目录
            <ol id="recent_dir">
            </ol>
          </li>
        </ul>
        <div class="clear"></div>
      </div>
      <div class="box_10 mar_5" id="window_left_main">
        <h3><span class="f_r dis_b"><a href="javascript:new_folder()" class="new mar_5"><img src="images/space.gif" alt="新建目录" title="新建目录" height="16" width="16" /></a></span>
          <span class="f_l dis_b">
          目录</span></h3>
        <div class="mar_5 t_c">
          <select style="width:164px;" id="folder_select" onchange="select_folder(this)">
          </select>
        </div>
        <ul>
          <li class="attachment_folder"><span class=" f_r"><img src="images/upon.gif" alt="向上" width="16" height="14" class="hand" onclick="cd('..');return false;" style="margin-right:10px;"/></span> <span id="current_folder_name" style="cursor:pointer;float:left;clear:left;display:block;width:120px;overflow:hidden;" onclick="cd(file_config.cur_fid)">/</span>
            <ol id="current_dir" style="clear:both;overflow-y:auto;overflow-x:hidden;">
            </ol>
          </li>
        </ul>
      </div>
      <div class="bk_5"></div>
    </div>
    <div class="box_10 attachment_r" id="window_right">
      <div class="box_11 lh_24">
        <div class="f_l"><span class="show_st hand" onclick="list_type('thumb');return false;" id="vt_thumb">缩图显示</span> | <span class="show_list hand" onclick="list_type('list');return false;" id="vt_list">列表显示</span>
          <?php if(!$single):?>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class="hand" onclick="select_all()">全选</span> / <span class="hand" onclick="select_inverse()">反选</span> &nbsp;&nbsp;&nbsp;&nbsp;<span id="select_notice"></span>
          <?php endif;?>
        </div>
        <div class="pagination f_r" id="pagination"></div>
        <div class="clear"></div>
      </div>
      <div class="box_11 attachment_area" id="scroll_div">
        <ul class="st_list" id="file_list">
        </ul>
        <div id="show_more" ></div>
      </div>
      <?php if ($_GET['select']): ?>
      <div class="lh_32 t_c" style="float:right; margin-top:2px;">
        <input type="button" value="插入" onclick="window_insert()" class="button_style_1" style="float:left;width:84px;"/>
        <input type="button" value="取消" onclick="tinyMCEPopup.close();" class="button_style_1" style="float:left;width:84px;"/>
      </div>
      <?php else: ?>
      <div class="lh_32 t_c">
        <?php if ($_GET['tinymce']): ?>
        <input type="button" value="插入" onclick="window_insert()" class="button_style_1"/>
        <?php else: ?>
        <?php if (priv::aca('system', 'attachment', 'paste_file')): ?><input type="button" onclick="cut_file()" value="剪切" class="button_style_1"/><?php endif; ?>
        <?php if (priv::aca('system', 'attachment', 'paste_file')): ?><input type="button" onclick="copy_file()" value="复制" class="button_style_1"/><?php endif; ?>
        <?php if (priv::aca('system', 'attachment', 'paste_file')): ?><input type="button" onclick="paste_file()" value="粘贴" class="button_style_1"/><?php endif; ?>
        <?php if (priv::aca('system', 'attachment', 'delete_file')): ?><input type="button" onclick="delete_file()" value="删除" class="button_style_1"/><?php endif; ?>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
    <div class="clear"></div>
  </div>
</div>
</div>
<!-- 右键菜单 -->
<ul id="right_menu" class="contextMenu">
  <?php if ($_GET['tinymce']):?>
  <li class="insert"><a href="javascript:window_insert();">插入</a></li>
  <?php elseif($_GET['select']):?>
  <li class="insert"><a href="javascript:select_ok();">选择</a></li>
  <?php endif;?>
  <li class="open" rel="single"><a href="javascript:open_file();">查看</a></li>
  <?php if (priv::aca('system', 'attachment', 'imageEditor') || priv::aca('system', 'attachment', 'edit_image')): ?><li class="edit" rel="single" imageonly="yes"><a href="javascript:;" onclick="edit_file();">编辑</a></li><?php endif; ?>
  <?php if (priv::aca('system', 'attachment', 'rename_file')): ?><li class="rename" rel="single"><a href="javascript:rename_file();">重命名</a></li><?php endif; ?>
  <?php if (priv::aca('system', 'attachment', 'download_file')): ?><li class="download" rel="multi"><a href="javascript:download_file();">下载</a></li><?php endif; ?>
  <?php if (priv::aca('system', 'attachment', 'download_file')): ?><li class="copy" rel="single"><a href="javascript:copy_link();">复制链接</a></li><?php endif; ?>
  <?php if (priv::aca('system', 'attachment', 'paste_file')): ?><li class="cut" rel="multi"><a href="javascript:cut_file();">剪切</a></li><?php endif; ?>
  <?php if (priv::aca('system', 'attachment', 'paste_file')): ?><li class="copy" rel="multi"><a href="javascript:copy_file();">复制</a></li><?php endif; ?>
  <?php if (priv::aca('system', 'attachment', 'paste_file')): ?><li class="paste" ><a href="javascript:paste_file();">粘贴</a></li><?php endif; ?>
  <?php if (priv::aca('system', 'attachment', 'delete_file')): ?><li class="delete" rel="multi"><a href="javascript:delete_file();">删除</a></li><?php endif; ?>
  <li class="refresh"><a href="javascript:refresh_data();">刷新</a></li>
</ul>
<!-- 文件夹右键菜单 -->
<ul id="folder_right_menu" class="contextMenu">
  <?php if (priv::aca('system', 'attachment', 'ls')): ?><li class="edit"><a href="#cd">打开</a></li><?php endif; ?>
  <?php if (priv::aca('system', 'attachment', 'download_folder')): ?><li class="download"><a href="#download_folder">下载</a></li><?php endif; ?>
  <?php if (priv::aca('system', 'attachment', 'rename_folder')): ?><li class="rename"><a href="#rename_folder">重命名</a></li><?php endif; ?>
  <?php if (priv::aca('system', 'attachment', 'rmdir')): ?><li class="delete"><a href="#delete_folder">删除</a></li><?php endif; ?>
</ul>
<script type="text/javascript">
//初始化窗口布局
$(function(){
	var $window_all = $('#window_all');
	var $window_main = $('#window_main');
	var $window_top = $('#window_top');
	var $window_left_main = $('#window_left_main');
	var $window_left_top = $('#window_left_top');
	var window_top_height = $window_top.height();
	var $window_right = $('#window_right');
	var $right_divs = $window_right.find('>div');
	var $right_main = $right_divs.eq(1);
	var $right_top_height = $right_divs.eq(0).outerHeight();
	var $right_bot_height = $right_divs.eq(2).outerHeight();
	function adapt() {
		// 计算高度
		var totalHeight = document.documentElement.clientHeight;
        (($.browser.msie && $.browser.version == 8) || $.browser.chrome || $.browser.safari) && (totalHeight -= 25);
		$window_all.height(totalHeight);
		var window_main_height = totalHeight - window_top_height - 42;
		$window_main.height(window_main_height);
		$window_right.height(window_main_height);
		$right_main.height(window_main_height - $right_top_height - $right_bot_height - 6);
		$right_main.width($window_right.width()-10);
	
		var window_left_main_height = window_main_height - $window_left_main.offset().top;
		$window_left_main.height(window_left_main_height);
		$window_left_main.find('ul').height(window_left_main_height-50).css('overflow-y','auto');
		
		// 计算li的空白边距
		var min_margin = 5; //最小边距
		var li_width = 100; //元素宽度
		var ul_width = $('#file_list').width() - 1; //显示区域宽度
		var extra_width = ul_width % (li_width + min_margin); //按最小边距情况下 剩余的右边宽度
		var li_num = Math.floor(ul_width/(li_width+min_margin));	//按最小边距情况下 每行显示的元素个数
		var li_margin = min_margin + Math.floor(extra_width/li_num); //把空白平均分配给每个元素
		$('#new_style').remove(); // 清除以前的样式 ( fix bug in IE )
		$('<style id="new_style">.attachment_area li{ margin-right:'+li_margin+'px;}</style>').appendTo(document.body); //新增样式，覆盖以前的
	}
	$(window).bind('resize', adapt);
	adapt();
	refresh_data();
});

var file_config = 
{
	page_total		: <?=$total?>,
	page_size		: <?=$pagesize?>,
	cur_page		: 1,
	cur_fid			: 0,
	list_type		: 'thumb',//option : list, thumb
	cur_dir			: '0',
	add_mod			: 'append',//option:append, prepend
	sort_type		: 'date' //option:date, name, size, last_modify
};

var file_data=[]; //cached data
var current_data; //current data
var ext_limit = '<?php echo $_GET['ext_limit'];?>'; //
var current_vars = { ext_limit:ext_limit }; //current list vars(search conditions, page numbers,,,,)

//图标模板
var row_thumb_template = '<li '
+'id="file_{aid}" '
+'aid="{aid}" '
+'src="<?php echo UPLOAD_URL; ?>{filepath}{filename}" '
+'thumb="{src}" '
+'onclick="file_click(this,event)" '
+'onmouseover="file_mouseover(this)" '
+'onmouseout="file_mouseout(this)" '
+'class="list_thumb" rel="{preview}">'
+'<div class="thumb" ondblclick="img_dblclick(this)" onmouseover="show_notice(this)">'
+'<div description="{description}" class="icon"><img src="{src}" class="icon" description="{description}"  /></div>'
+'</div>'
+'<span ondblclick="filename_dblclick(this)" class="filename" title="{alias}">{alias}</span>'
+'<strong class="namecheck"><input name="file_chk" id="radio_{aid}" class="checkbox" value="{filepath}{filename}" type="<?php echo $single ? 'radio' : 'checkbox'; ?>" aid="{aid}" /></strong>'
+'</li>';
//列表模板
var row_list_template = '<li '
+'id="file_{aid}" '
+'aid="{aid}" '
+'src="<?php echo UPLOAD_URL; ?>{filepath}{filename}" '
+'thumb="{src}" '
+'onclick="file_click(this,event)" '
+'onmouseover="file_mouseover(this)" '
+'onmouseout="file_mouseout(this)" '
+'class="list_list" rel="{preview}">'
+'<span ondblclick="filename_dblclick(this)" class="filename" value="{filename}" title="{alias}">{alias}</span>'
+'<strong class="namecheck"><input name="file_chk"  class="checkbox" value="{filepath}{filename}" type="<?php echo $single ? 'radio' : 'checkbox'; ?>" aid="{aid}"  /></strong>'
+'</li>';
//文件夹模板
var dir_row_template = '<li id="dir_{fid}" fid="{fid}"><a href="#{fid}">{name}</a></li>';

//默认是图标模板
var row_template = row_thumb_template;

var select_single = <?php echo $single ? 'true' : 'false'; ?>

var img_click_delay = false;

$(function(){
    var up = $.uploader('#mul_upload', {
        script : '?app=system&controller=upload&action=upload',
        jsonType:1,
        selectend:function(){
            up.set('scriptData', {
                fid:file_config.cur_fid
            });
        },
        complete:function(json, data) {
            if (json.state) {
                refresh_data();
            } else {
                ct.tips('文件“'+file.name+'”上传失败，'+json.error, 'error');
            }
        },
        allcomplete:function(data) {
            ct.ok("所有文件上传完成");
            refresh_data();
        },
        error:function(data) {
            ct.error(data.error.type+':'+data.error.info);
        }
    });
	
	$("#calendar_from, #calendar_to").DatePicker({'format':'yyyy-MM-dd'});
	
	//定义快捷键
	$(document).bind('keydown',function(event)
	{
		var target = event.target ? event.target:event.srcElement;
		target = target.tagName.toLowerCase();
		// ctrl + A 全选
		if (event.keyCode == 65 && event.ctrlKey && target == 'html')
		{
			select_all();
			event.preventDefault();
			return false;
		}
		// backspace 删除
		if (event.keyCode == 8 && target == 'html')
		{
			delete_file();
			event.preventDefault();
			return false;
		}
	});

	//平滑翻页判断
	$('#scroll_div').bind('scroll',function()
	{
		$('#right_menu').css('display','none');
		check_more();
	});
	//平滑翻页补充
	setInterval(check_more,1000);
	
	//显示图片描述信息
	$(document.body).mousemove(function(event)
	{
		$('#notice').css(
		{
			left:event.clientX+10,
			top:event.clientY+10
		});
		try
		{
			clearTimeout(window.notice_timer);
		} catch(e) { }
		$('#notice').hide();
		if (window.allow_notice)
		{
			window.notice_timer = setTimeout(function()
			{
				$('#notice').show();
			},1000); //鼠标停留1000，自动显示
		}
	});
	$('#notice').css('opacity',0.95);
	
	
	//右键菜单
	$('#scroll_div').bind('contextmenu',function(event) //阻止默认操作
	{
		event.preventDefault();
		return false;
	}).mousedown(function(event)
	{
		$(this).mouseup(function(event)
		{
			$(this).unbind('mouseup');
			if (event.button == 2)
			{
			
				$(document).unbind('click');
				var target = event.target?event.target:event.srcElement;
				
				if ($(target).parents('ul#file_list').length > 0 && $(target).parents('li').length > 0) //在文件上点击右键
				{
					var o = $(target).parents('li');
					if (!o.hasClass('selected'))
					{
						select_single || $('#file_list>li.selected').trigger('click');
						o.trigger('click');
					}
					
					if ($('li.selected').length > 1)
					{
						$('#right_menu > li[rel=multi]').show();
						$('#right_menu > li[rel=single]').hide();
					}
					else
					{
						$('#right_menu > li[rel=single]').show();
						$('#right_menu > li[rel=multi]').show();
						if (o.attr('rel') == 'preview')
							$('#right_menu > li[imageonly]').show();
						else
							$('#right_menu > li[imageonly]').hide();
					}
				}
				else // 在空白地方点击右键
				{
					$('#right_menu > li[rel=single]').hide();
					$('#right_menu > li[rel=multi]').hide();
					$('#file_list>li.selected').removeClass('selected');
				}
				
				var menu = $('#right_menu');
				menu.hide().css(
				{
					left:event.clientX+3,
					top:event.clientY+3
				}).fadeIn(100).find('a').mouseover( function() 
				{
					$(menu).find('LI.hover').removeClass('hover');
					$(this).parent().addClass('hover');
				}).mouseout( function() 
				{
					$(menu).find('LI.hover').removeClass('hover');
				});
				
				setTimeout( function() 
				{ // Delay for Mozilla
					$(document).click( function(event) 
					{
						var target = event.target?event.target:event.srcElement;
						$(document).unbind('click').unbind('keypress');
						$(menu).fadeOut(100);
						return ($(target).parents('#right_menu').length > 0);
					});
				}, 0);
			}
		});
	})	
});


</script>
<div id="notice" ></div>
</body>
</html>
