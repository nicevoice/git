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

<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/navigator/style.css"/>
<link rel="stylesheet" type="text/css" href="apps/system/css/template_edit.css"/>
<style type="text/css">
#navigator {
    width:300px;float:left;
}
#filepath {
    width:auto;min-width:80px;background:#eee;border:1px solid #B9D0E7;height:21px;line-height:21px;padding:0px 5px;
}
</style>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.navigator.js"></script>
<script src="js/cmstop.tabnav.js" type="text/javascript"></script>
<script src="apps/system/js/template_edit.js" type="text/javascript"></script>
<!-- 时间选择器 -->
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.datepicker.js"></script>
<link href="<?=IMG_URL?>js/lib/datepicker/style.css" rel="stylesheet" type="text/css" />
<form method="POST" action="?app=system&controller=template&action=add">
<table class="mar_l_8 table_form" cellspacing="0" cellpadding="0" width="98%" border="0">
    <tr>
        <th width="80">别名：</th>
        <td>
            <div id="navigator"></div>
            <span style="margin-left:3px;">/</span>
            <input type="text" class="bdr" style="padding:3px;margin-top:1px;" maxlength="30" size="20" name="filealias" />
            <span>(可输入中文，以描述模板用途)</span>
        </td>
    </tr>
    <tr>
        <th><span class="redstar">*</span> 路径：</th>
        <td>
            <input readonly="readonly" name="filepath" id="filepath" />
            <span style="margin-left:3px;">/</span>
            <input type="text" class="bdr" style="padding:3px;margin-top:1px;" maxlength="30" size="20" name="filename"/>
            <span>.html</span>
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
	<textarea id="editor" wrap="off" class="ignore" name="code"></textarea>
</div>
<div style="clear:both;padding:8px 90px;height:30px;"><input class="button_style_2" type="submit" value="保存" name="sub"/></div>
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
function setdir(path){
    $.getJSON('?app=system&controller=template&action=chdir&dir='+encodeURIComponent(path),
    function(json){
        nav.trigger('setNav', [json.path,json.alias]);
        $('#filepath').val(json.path);
    });
}
var nav = $('#navigator').navigator({
	dirUrl:'?app=system&controller=template&action=dir',
	refreshButton:0
}).bind('cd',function(e, path){
	setdir(path);
});
setdir('<?php echo $path;?>');
editplus.init();
</script>
<?php $this->display('footer', 'system');