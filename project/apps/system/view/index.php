<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta content="IE=EmulateIE7" http-equiv="X-UA-Compatible" />
<meta http-equiv="Content-Type" content="text/html; charset=<?=$CONFIG['charset']?>" />
<title><?=$head['title']?>后台管理首页</title>
<link rel="stylesheet" type="text/css" href="css/backend.css" />
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/cmstop.js"></script>
<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/tree/style.css" />
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.tree.js"></script>
<script type="text/javascript" src="js/cmstop.tabview.js"></script>
<script type="text/javascript" src="js/cmstop.superassoc.js"></script>
<script type="text/javascript">

$(function(){
	if (window.top != self) {
		window.open(location, '_blank');
		ct.assoc.close();
	} else {
		superAssoc.init();
	}
	$('#logout').click(function(){
		$.getJSON('?app=system&controller=admin&action=logout', function(json){
			if (json.state == true) {
				if (json.synclogout) {
					var len = json.synclogout.length;
					for(var k=0; k<len; k++) {
						$.getScript(json.synclogout[k]);
					}
					setTimeout(function (){
						location.href = '<?=WWW_URL?>';
					}, 500);
				} else {
					location.href = '<?=WWW_URL?>';
				}
			}
		});
	});
});
</script>
</head>
<body scroll="no">
<!--头部开始-->
<div id="head">
  <h1>管理后台</h1>
  <div id="menu_position">
    <ul id="menu">
      <?php if (is_array($topmenus)) foreach ($topmenus as $m):?>
      <li id="menu<?=$m['menuid']?>" url="<?php echo $m['url'];?>"><a href="javascript:;"><?php echo $m['name'];?></a>
        <?php if(isset($submenus[$m['menuid']]) && $submenus[$m['menuid']]):?>
        <ul>
          <?php foreach ($submenus[$m['menuid']] as $menu):?>
          <li id="menu<?php echo $menu['menuid'];?>"><a href="javascript:;"><?php echo $menu['name'];?></a></li>
          <?php endforeach; ?>
          <li class="menubtm"></li>
        </ul>
        <?php endif; ?>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <!--账户信息-->
  <div class="user">
    <?php echo $_username;?>（<?php echo table('role', $_roleid, 'name');?>），<a id="logout" href="javascript:;">退出</a></div>
</div>
<!--头部结束-->
<div id="main">
  <!--左侧开始-->
  <div id="left">
    <h2>
      <span style="float:right;"><a href="javascript:superAssoc.refresh();" onfocus="this.blur()" class="refresh"><img src="images/space.gif" alt="刷新菜单" title="刷新菜单" height="18" width="16" /></a></span>
      <label id='root_menu_name'></label>
    </h2>
    <div id="browser"></div>
  </div>
  <!--左侧结束-->
  <!--右侧开始-->
  <div id="right">
    <div id="tab_container" target="#frame_container"></div>
    <div>
    	<div id="shortcut">
			<span title="网编工具箱" id="get_toolbox">
				<img width="16" height="16" src="images/toolbox.gif" />
			</span>
    		<span target="shortcut" title="添加常用操作" callback="fillData">
    			<img width="16" height="16" src="images/add.gif" />
    		</span>
    		<span target="note" title="编辑便笺" callback="getNote">
    			<img width="16" height="16" src="images/notepaper.gif" />
    		</span>
    		<span title="网站首页" onclick="window.open('<?=WWW_URL?>')">
    			<img width="16" height="16" src="images/home.gif" />
    		</span>
    	</div>
    	<div id="position"></div>
    </div>
    <div id="frame_container" style="width:100%;"></div>
  </div>
</div>
<div name="shortcut" class="popup">
	<div class="title">添加常用操作</div>
	<div class="poparea">
	<form action="?app=system&controller=index&action=addtab" method="POST">
		<label>标题：<input name="name" style="width:200px" type="text" class="bdr"/></label>
	    <label>网址：<input name="url" style="width:200px" type="text" class="bdr"/></label>
		<p><input type="submit" value="保存" class="button_style_1 mar-l-40" /><button type="button" class="button_style_1 closer">取消</button></p></form>
	</div>
</div>
<div name="note" class="popup">
	<div class="title"><span class="time"></span>编辑便签</div>
	<div class="poparea">
		<form action="?app=system&controller=my&action=note" method="POST">
			<textarea name="note"></textarea>
			<p><input type="submit" value="保存" class="button_style_1" /><button type="button" class="button_style_1 closer">取消</button></p>
		</form>
	</div>
</div>
<div name="toolbox" id="toolbox" class="popup">
	<div class="title"><span class="time"></span>网编工具栏</div>
	<div class="poparea toolbox">
		<p>网编工具箱是一个书签工具栏按钮</p>
		<p>能帮您快捷的转载文章或进行管理操作</p>
		<p>将下面的链接拖动到您的书签工具栏,</p>
		<p>或右键加入收藏夹中</p>
		<a class="toolbox" title="网编工具箱" onclick="return false;"  href="javascript:void((function(){cmstop_toolbox_ver=2;cmstop_toolbox_domain_admin='<?=ADMIN_URL?>';cmstop_toolbox_cmd='start';if(typeof(cmstop_toolbox)!='undefined'){cmstop_toolbox.ready(cmstop_toolbox_cmd);return}var%20e=document.createElement('script');e.setAttribute('src',cmstop_toolbox_domain_admin+'js/cmstop.toolbox.js');e.setAttribute('charset','utf-8');document.body.appendChild(e)})())"><span>网编工具箱</span></a>
	</div>
	<div class="poparea toolbox-ie">
		<p>网编工具箱是一个书签工具栏按钮</p>
		<p>能帮您快捷的转载文章或进行管理操作</p>
		<p>在IE浏览器下,您可以将网编工具箱加入收藏夹中</p>
		<p>或者下载并安装右键版本</p>
		<p>&nbsp;</p>
		<a title="网编工具箱(右键版)" href="?app=system&controller=ietoolbox&action=index">网编工具箱(右键版)</a>
		<a class="toolbox" title="网编工具箱" onclick="return false;"  href="javascript:void((function(){cmstop_toolbox_ver=2;cmstop_toolbox_domain_admin='<?=ADMIN_URL?>';cmstop_toolbox_cmd='start';if(typeof(cmstop_toolbox)!='undefined'){cmstop_toolbox.ready(cmstop_toolbox_cmd);return}var%20e=document.createElement('script');e.setAttribute('src',cmstop_toolbox_domain_admin+'js/cmstop.toolbox.js');e.setAttribute('charset','utf-8');document.body.appendChild(e)})())"><span>网编工具箱</span></a>
	</div>
</div>
<script type="text/javascript">
// stat
$.event.add(window,'load',function(){
	$.getScript('<?=$client_url?>');
});

//cmstop_toolbox
$(document).ready(function(){
	var timeout = false;
	var span = $('#get_toolbox');
	var toolBox = {
		obj : $('div#toolbox'),
		show : function() {
			toolBox.obj.show().css({
				'top':span.outerHeight(true)+span.offset().top+1,
				'left':(span.offset().left - toolBox.obj.outerWidth() + span.outerWidth()),
				'visibility':'visible'
			});
			$('#get_toolbox').addClass('sc_now');

			toolBox.obj.bind('mouseenter',function() {
				if (timeout !== false) {
					clearTimeout(timeout);
					timeout = false;
				}
			}).bind('mouseleave', function() {
				if (timeout === false) {
					timeout = setTimeout(function() {
						toolBox.hide();
					},1350);
				}
			});
		},
		hide : function() {
			if (timeout !== false) {
				clearTimeout(timeout);
				timeout = false;
			}
			$('body').unbind('mouseover.toolbox');
			$('body').unbind('click.toolbox');
			$('#get_toolbox').removeClass('sc_now');
			toolBox.obj.hide().css({'visibility':'hidden','display':'block'});
		}
	};

	span.bind('click', function() {
		toolBox.show();
		setTimeout(function() {
			$('body').one('click.toolbox', function() {
				toolBox.hide();
			});
		}, 500);
	});
	if (cmstop.IE > 6) {
		toolBox.obj.find('div.poparea').eq(0).hide();
		toolBox.obj.find('div.poparea').eq(1).show();
	} else {
		toolBox.obj.find('div.poparea').eq(1).hide();
		toolBox.obj.find('div.poparea').eq(0).show();
	}
});
</script>
</body>
</html>