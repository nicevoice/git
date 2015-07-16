<?php $this->display('header', 'system');?>
<!--contextmenu-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/suggest/style.css" />
<script src="<?=IMG_URL?>js/lib/cmstop.suggest.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/tree/style.css" />
<script src="<?=IMG_URL?>js/lib/cmstop.tree.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/list/style.css" />
<script src="<?=IMG_URL?>js/lib/cmstop.list.js" type="text/javascript"></script>

<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/editplus/style.css" />
<script src="<?=IMG_URL?>js/lib/cmstop.editplus.js" type="text/javascript"></script>
<script src="js/cmstop.editplus_plugin.js" type="text/javascript"></script>

<link rel="stylesheet" type="text/css" href="apps/system/css/template_edit.css"/>
<style type="text/css">
#navigator {
    width:auto;min-width:80px;float:left;background:#eee;border:1px solid #B9D0E7;height:21px;line-height:21px;padding:0 5px;
}
#filepath {
    width:auto;min-width:80px;float:left;background:#eee;border:1px solid #B9D0E7;height:21px;line-height:21px;padding:0 5px;
}
</style>
<!-- 时间选择器 -->
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.datepicker.js"></script>
<link href="<?=IMG_URL?>js/lib/datepicker/style.css" rel="stylesheet" type="text/css" />

<script src="apps/system/js/template_edit.js" type="text/javascript"></script>
<script src="js/cmstop.tabnav.js" type="text/javascript"></script>
<form method="POST" action="?app=system&controller=template&action=edit">
<input type="hidden" name="path" value="<?=$path?>" />
<table class="table_form mar_l_8" cellspacing="0" cellpadding="0" width="98%">
    <tr>
        <th width="80">别名：</th>
        <td>
            <div id="navigator">
            <?php echo $alias;?>
            </div>
            <span style="margin-left:3px;">/</span>
            <input value="<?php echo $filealias;?>" type="text" class="bdr" style="padding:3px;margin-top:1px;" maxlength="30" size="20" name="filealias" />
            <input value="<?=$pageid?>" type="hidden" name="pageid" />
            <span>(可输入中文，以描述模板用途)</span>
        </td>
    </tr>
    <tr>
        <th><span class="redstar">*</span> 路径：</th>
        <td>
            <div id="filepath">
            <?php echo preg_replace('#^root/#', '', $path);?>
            </div>
        </td>
    </tr>
</table>
<div class="mar_l_8">
	<div id="toolbox">
		<div class="toolbar">
			<a class="f_r new" style="margin:7px;" href=""><img height="16" width="16" src="images/space.gif"/></a>
			<ul id="tooltab">
				<li index="0"><a href="javascript:;" class="ctrl">剪辑</a></li>
				<li index="1"><a href="javascript:;" class="ctrl">区块</a></li>
			</ul>
		</div>
		<div id="cliparea" class="toolarea">
	    	<ul id="clipContainer" class="container"></ul>
	    </div>
	    <div class="toolarea">
	    	<div class="dropdown">
	    	<?=element::page_dropdown($pageid, 'id="page_dropdown"')?>
	    	</div>
	    	<div id="sectionarea">
	    		<ul id="sectionContainer" class="container"></ul>
	    	</div>
	    </div>
	</div>
	<textarea id="editor" class="ignore" name="code"><?php echo $code;?></textarea>
</div>
<div style="clear:both;padding:8px 90px;height:30px;">
	<input class="button_style_2" type="submit" value="保存"/>
	<?php if($pageid):?>
	<input class="button_style_2" type="button" value="预览" onclick="editplus.previewPage()"/>
	<input class="button_style_2" type="button" value="导出" onclick="editplus.exportTpl()"/>
	<?php endif;?>
</div>
</form>

<!--右键菜单，区块-->
<ul id="clip_menu" class="contextMenu">
   <li class=""><a href="#insertClip">插入</a></li>
   <li class="edit"><a href="#editClip">编辑</a></li>
   <li class="delete"><a href="#delClip">删除</a></li>
</ul>
<ul id="section_menu" class="contextMenu">
   <li class=""><a href="#insertSection">插入</a></li>
   <li class="edit"><a href="#setProperty">设置</a></li>
   <li class="view"><a href="#viewSection">查看</a></li>
</ul>

<script type="text/javascript">
editplus.init(<?=$pageid?>);
</script>
<?php $this->display('footer', 'system');