<?php $this->display('header', 'system');?>
<!--tablesorter-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css" />
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<!--contextmenu-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.contextMenu.js"></script>

<script type="text/javascript" src="uploader/cmstop.uploader.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/navigator/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.navigator.js"></script>

<style type="text/css">
.overlay {
	position:fixed;top:0;left:0;
	z-index:9998;
	height:100%;
	width:100%;
	background-color:black;
	opacity:0.5;
	filter:Alpha(Opacity=50);
}
.test-box {
	position:fixed;
	width:700px;
	height:100px;
	top:50%;
	left:50%;
	margin-left:-370px;
	margin-top:-80px;
	padding:20px;
	background-color:#fff;
	z-index:9999;
	border:1px solid #ccc;
	border-radius:5px;
	text-align:center;
	*filter:progid:DXImageTransform.Microsoft.Glow(color=#000000,strength=6);
	box-shadow:3px 3px 5px #000;
}
.test-box.haserror {
	height:250px;
	margin-top:-155px;
}
.test-box .control {
	cursor:pointer;
	font-size:16px; font-family:Arial, Verdana, "宋体";
	font-weight:bold;
	padding:14px;
	border:1px solid #C3E1F0;
	background-color:#F2F8FD;
	border-radius:5px;
	text-align:center;
	width:80px;
	height:30px;
	line-height:30px;
	float:right;
}
.test-box .control:hover {
	border-color:#FDBD77;
	background-color:#FFFDD7;
	color: #FF3300;
}
.test-box .current {
	margin:5px auto;
	text-align:left;
	font-size:14px; font-family:Arial, Verdana, "宋体";
	width:650px;
	height:20px;
	line-height:20px;
}
.test-box .output {
	width:640px;
	height:150px;
	margin:0 auto;
	overflow-y:auto;
	padding:5px;
	text-align:left;
	background:#000;
	display:none;
}
.test-box.haserror .output {
	display:block;
}
.test-box .output p {
	font-size:12px; font-family:Arial, Verdana, "宋体";
	color:#fff;
}
.test-box .close {
	width:30px;
	height:20px;
	font-size:18px;
	line-height:20px;
	text-align:center;
	right:0;
	top:0;
	position:absolute;
	background-color:#ccc;
	cursor:pointer;
}
.test-box .progress-control:after {
	content: ".";
	display: block;
	height: 0;
	font-size: 0;
	clear: both;
	visibility: hidden;
}
.test-box .progress-control {
	width:100px;
	margin:0 auto;
}
.test-box .progress-control.wide {
	width:650px;
}
.test-box .progress {
	font-size:12px; font-family:Arial, Verdana, "宋体";
	padding:14px;
	border:1px solid #C3E1F0;
	background-color:#F2F8FD;
	border-radius:5px;
	text-align:center;
	width:500px;
	float:left;
}
.test-box .progress .bar {
	height:28px;
	border:1px solid #ccc;
	background:#fff;text-align:left;position:relative;
}
.test-box .progress .bar .percent {
	position:absolute; top:0; left:50%;
	width:80px;
	height:28px;
	margin-left:-40px;
	line-height:28px;text-align:center;
	font-family:Tahoma, Verdana, Arial; font-size:14px;
	font-weight:bold; color:#f60;
}
.test-box .progress .bar .indicator {
	background-color:#DDEEF1;
	height:28px;width:10%;
	border-right:1px solid #9FCCE9;
}
</style>
<script type="text/javascript" src="apps/system/js/template.js"></script>
<div class="bk_10"></div>
<div class="table_head" style="min-width:800px;">
  <div class="search_icon search f_r mar_r_8">
      <form onsubmit="return false;" id="search_f">
        <input type="text" name="keywords" value="" size="30"/>
        <a href="javascript:;" style="outline:none" onfocus="this.blur()" onclick="App.table.load($('#search_f'));return false;" title="搜索">搜索</a>
      </form>
  </div>
  <button type="button" class="button_style_4 f_l" onclick="App.add();return false;">新建模板</button>
  <button type="button" class="button_style_4 f_l" onclick="App.test();return false;">模板检测</button>
  <div id="uploadify" class="button f_l">上传模板</div>
  <div class="f_l w_400" id="navigator"></div>
</div>
<div class="bk_8"></div>
<table width="98%" id="item_list" class="table_list mar_l_8" cellspacing="0" cellpadding="0" style="empty-cells:show;table-layout:fixed;">
  <thead>
    <tr>
      <th width="260" class="bdr_3">名称</th>
      <th class="t_c">别名</th>
      <th width="80">管理操作</th>
    </tr>
  </thead>
  <tbody></tbody>
</table>

<!--右键菜单-->
<ul id="right_menu_folder" class="contextMenu">
   <li class="edit"><a href="#App.open">打开</a></li>
   <li class="delete"><a href="#App.del">删除</a></li>
   <li class="edit"><a href="#App.alias">别名</a></li>
</ul>
<ul id="right_menu_file" class="contextMenu">
   <li class="edit"><a href="#App.edit">编辑</a></li>
   <li class="delete"><a href="#App.del">删除</a></li>
   <li class="edit"><a href="#App.alias">别名</a></li>
</ul>
<script type="text/javascript">
App.init('?app=<?=$app?>&controller=<?=$controller?>');
</script>
<?php $this->display('footer', 'system');