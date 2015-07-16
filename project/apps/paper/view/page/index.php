<?$this->display('header'); ?>
<script type="text/javascript" src="apps/paper/js/page.js"></script>
<script type="text/javascript" src="js/template.js"></script>
<script type="text/javascript" src="uploader/cmstop.uploader.js"></script>
<script type="text/javascript" src="imageEditor/cmstop.imageEditor.js"></script>
<script type="text/javascript" src="js/cmstop.filemanager.js"></script>
<div class="mar_l_8">
	<div class="mar_t_10" style="position: relative;">
		<strong>当前状态：</strong><span id="disabled" class="c_red"><?=$disabled?></span>
		<input id="stateBtn" type="button" class="button_style_1" value="更改状态"/>
		<ul id="stateUl">
			<li id="sleep">休　眠</li>
			<li id="publish">发布本期</li>
		</ul>
		<input type="button" id="prevView" value="预 览" class="button_style_1"/>
		<input url="<?=$edition['url']?>" type="button" id="access" value="查看前台" class="button_style_1"/>
		<input type="button" id="add" value="添加版面" class="button_style_1" style="margin-left: 50px;"/>
	</div>
	<div id="pageDiv" class="clearfix mar_t_8"><!-- page容器 -->
	
	</div>	
</div>
<?php $this->display('footer','system');?>
<script type="text/javascript">
var eid = <?=intval($_GET['id'])?>;
var tpl = '\
	<div class="item" rel="{pageid}">\
		<img title="删除本版" class="delete" src="images/delete.gif"/>\
		<ul>\
		    <li class="page_pic">\
			    <a href="javascript:;" url="{url}" title="查看前台"><img width="120" height="165" alt="{name}" src="<?=UPLOAD_URL?>{image}"/></a>\
		    </li>\
		    <li field="name">版面名：<span>{name}</span><input type="text" value="{name}"/></li>\
		    <li field="pageno">版面号：<span>{pageno}</span><input type="text" value="{pageno}"/></li>\
		    <li field="editor">主　编：<span>{editor}</span><input type="text" value="{editor}"/></li>\
		    <li field="arteditor">美　编：<span>{arteditor}</span><input type="text" value="{arteditor}"/></li>\
		    <li class="count">文章数：<span>{count}</span></li>\
		    <li>\
			    <span>内　容：</span>\
			    <input type="button" class="relate button_style_1" value="内容关联"/>\
		    </li>\
		    <li class="imageLi" path="{image}">\
				  &nbsp;&nbsp;&nbsp;&nbsp;截图：\
				<div id="image_{pageid}"></div>\
		    </li>\
		    <li class="pdfLi" path="{pdf}">\
			    &nbsp;&nbsp;&nbsp;&nbsp;PDF：\
			    <div id="pdf_{pageid}"></div>\
		    </li>\
		</ul>\
	</div>';
$(function (){
	page.load();
	page.button();	//各按钮处理
	ct.nav([
		{text: '扩展'},
		{text: '报纸', url: '?app=paper&controller=paper&action=index'},
		{text: '<?=$paper['name']?>', url: '?app=paper&controller=edition&action=index&id=<?=$paper['paperid']?>'},
		{text: '总第<?=$edition['total_number']?>期'}
	]);
});
</script>
<img id="okBtn" src="images/ok.png"/>
