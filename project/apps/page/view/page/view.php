<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$CONFIG['charset']?>" />
<title><?=$head['title']?></title>
<link rel="stylesheet" type="text/css" href="css/admin.css"/>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/config.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/cmstop.js"></script>
<style type="text/css">
.inline li {
    margin:0 3px;
}
.table_info img {
    margin:0 4px;
}
</style>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.form.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/validator/style.css"/>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.validator.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.contextMenu.js"></script>

<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/jquery-ui/dialog.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.ui.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.dialog.js"></script>

<script type="text/javascript" src="uploader/cmstop.uploader.js"></script>
<script type="text/javascript" src="imageEditor/cmstop.imageEditor.js"></script>
<script type="text/javascript" src="js/cmstop.filemanager.js"></script>

<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/suggest/style.css" />
<script src="<?=IMG_URL?>js/lib/cmstop.suggest.js" type="text/javascript"></script>

<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/list/style.css" />
<script src="<?=IMG_URL?>js/lib/cmstop.list.js" type="text/javascript"></script>

<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/autocomplete/style.css" />
<script src="<?=IMG_URL?>js/lib/cmstop.autocomplete.js" type="text/javascript"></script>

<link href="<?=IMG_URL?>js/lib/tree/style.css" rel="stylesheet" type="text/css" />
<script src="<?=IMG_URL?>js/lib/cmstop.tree.js" type="text/javascript"></script>

<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/editplus/style.css" />
<script src="<?=IMG_URL?>js/lib/cmstop.editplus.js" type="text/javascript"></script>
<script src="js/cmstop.editplus_plugin.js" type="text/javascript"></script>

<script src="<?=IMG_URL?>js/lib/jquery.colorPicker.js" type="text/javascript"></script>
<script type="text/javascript" src="js/cmstop.tabnav.js"></script>
<script type="text/javascript" src="apps/page/js/scrolltable.js"></script>
<link rel="stylesheet" type="text/css" href="apps/page/css/page.css" />
<script type="text/javascript" src="apps/page/js/page.js"></script>
<script type="text/javascript" src="apps/system/js/psn.js"></script>
<script type="text/javascript" src="apps/page/js/page_priv.js"></script>
<script type="text/javascript" src="apps/page/js/section_priv.js"></script>
<script type="text/javascript" src="tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="tiny_mce/editor.js"></script>
<!-- 时间选择器 -->
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.datepicker.js"></script>
<link href="<?=IMG_URL?>js/lib/datepicker/style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.treetable.js"></script>
<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/treetable/style.css" />
<script type="text/javascript">
$(ct.listenAjax);
</script>
</head>
<body scroll="no">
<!--右侧开始-->
<div class="bk_10"></div>
<div class="tag_1" style="margin-bottom:0;">
	<ul class="tag_list" id="pagetab">
		<li func="property"><a href="javascript:;">属性</a></li>
		<li func="section"><a href="javascript:;">区块</a></li>
	</ul>
	<span style="margin-left: 5px;">
        <?php if (priv::aca('page', 'page', 'visualedit') && (priv::page($page['pageid']) || priv::section_page($page['pageid']))): ?>
		<input type="button" value="可视编辑" class="button_style_1" style="width:70px" onclick="page.visualEdit(); return false;" />
        <?php endif; ?>
		<?php if ($haspriv):?>
        <input type="button" value="源码编辑" class="button_style_1" style="width:70px" onclick="page.templateEdit(); return false;" />
		<input type="button" value="添加子页" class="button_style_1" style="width:70px" onclick="page.addPage();return false;"  />
		<input type="button" value="查看" class="button_style_1" onclick="window.open('<?=$page['url']?>','_blank'); return false;" />
		<input type="button" value="生成" class="button_style_1" onclick="page.publishPage(); return false;" />
		<input type="button" value="权限" class="button_style_1" onclick="pagepriv.set(<?=$pageid?>);" />
		<input type="button" value="修改" class="button_style_1" onclick="page.pageSetting(); return false;" />
		<input type="button" value="删除" class="button_style_1" onclick="page.delPage(); return false;" />
        <?php endif;?>
	</span>
	<div class="search_icon search" style="float:right; margin-right:10px;">
		<input type="text" name="searchSection" value="按ID搜索区块" id="searchSection" onblur=" this.value || (this.value = '按ID搜索区块')" onfocus="this.value == '按ID搜索区块' && (this.value = '')" />
		<a href="javascript:search();">搜索</a>
	</div>
</div>
<div class="bk_10"></div>
<div id="bodyContainer">
	<div class="f_r" id="viewBox"></div>
	<div id="sectionPanel" class="f_l">
		<h3>
			<?php if ($haspriv):?>
			<a href="javascript:page.addSection();" class="new f_r mar_5">
				<img src="images/space.gif" alt="添加区块" height="16" width="16" />
			</a>
			<?php endif;?>
    		<a class="search_11 f_r" style="margin-top:4px" onfocus="this.blur()" onclick="page.searchSection(this);" href="javascript:;">
				<img height="16" width="16" alt="搜索" src="images/space.gif"/>
			</a>
			<span class="f_l">区块列表</span>
		</h3>
		<div id="sectionBox">
			<div class="bk_5"></div>
			<ul id="sectionList"></ul>
			<div class="clear"></div>
		</div>
	</div>
	<div class="clear"></div>
</div>
<div class="clear"></div>

<ul id="section_menu" class="contextMenu">
   <li class="edit"><a href="#property">设置</a></li>
   <li class="delete"><a href="#del">删除</a></li>
   <li class="edit"><a href="#move">移动</a></li>
</ul>

<ul id="section_item_menu" class="contextMenu">
   <li class="view"><a href="#viewitem">查看</a></li>
   <li class="edit"><a href="#edititem">编辑</a></li>
   <li class="edit"><a href="#replaceitem">替换</a></li>
   <li class="delete"><a href="#delitem">删除</a></li>
   <li class=""><a href="#moveitemleft">左移</a></li>
   <li class=""><a href="#moveitemright">右移</a></li>
</ul>
<ul id="section_cell_menu" class="contextMenu">
   <li class="new"><a href="#additem">添加项</a></li>
   <li class="delete"><a href="#delrow">删除行</a></li>
   <li><a href="#uprow">上移行</a></li>
   <li><a href="#downrow">下移行</a></li>
   <li class="new"><a href="#addrowafter">添加行</a></li>
</ul> 
<script type="text/javascript">
var searchSection	= $('#searchSection');
page.init(<?=$_GET['pageid']?>);
$(document).ready(function() {
	searchSection.bind('keydown',function(event) {
		if (event.keyCode == 13) {
			search();
		}
	});
});
function search(){
	var sectionList	= $('#sectionList');
	var id	= searchSection.val();
	var state = false;
	$.each(sectionList.find('li'),function(k,v) {
		if (id == v.id.split('_')[1]) {
			state = true;
			page.viewSection(id);
		}
	});
	if (state) {
		return
	}
	$.getJSON('?app=page&controller=section&action=view&sectionid='+id, null, function(json) {
		if (json.state) {
			ct.assoc.open('?app=page&controller=page&action=view&pageid='+json.pageid+'&sectionid='+id, 'newtab');
		} else {
			ct.error(json.error);
		}
	});
}
</script>
<?php $this->display('footer', 'system');