<?php
	$this->display('header', 'system');
	include_once ROOT_PATH.'version.php';
?>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/effects.core.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/effects.clip.js"></script>
<script type="text/javascript" src="apps/system/js/module.js"></script>
<div style="width:250px;float:left;margin:8px 0 0 10px; color:#9AC4DC">系统版本：<?php echo CMSTOP_VERSION;?> <?php echo CMSTOP_RELEASE;?></div>
<div style="width:150px;float:right;margin:8px 10px 0 0;text-align:right"><a onclick="module.restore()" href="javascript:;">恢复面板默认设置</a></div>
<div style="clear:both"></div>
    <div class="f_l module_column" id="module_left" style="width:48%;padding:0 10px 10px 10px">
<?php 
foreach ($modules['left'] as $module) 
{
	 $this->display('index/module/'.$module);
}
?>
    </div>
    
    <div class="f_r module_column" id="module_right" style="width:47%;padding:0 10px 10px 10px">
<?php 
foreach ($modules['right'] as $module) 
{
	 $this->display('index/module/'.$module);
}
?>     
    </div>
<style type="text/css">
	.module_column { padding-bottom:100px;}
	.drag_box{ margin-bottom:10px; margin-right:10px;}
	.movehand{cursor:move;}
    .layout{cursor:move;}
	.layout_view_line{border:#0066FF dotted 2px;  background:#DAE7F3;}
	.ui-sortable-placeholder { border:#0066FF dotted 2px;  background:#DAE7F3; visibility: visible !important; h}	
	.ui-sortable-placeholder * { visibility: hidden; }
	#my_note, #my_check, #my_new, #rank_pv {height:263px}
	}
</style>
<script type="text/javascript">
module.set({
	'save_url': '?app=system&controller=index&action=module_save&page=index',
	'restore_url': '?app=system&controller=index&action=module_restore&page=index'
});

var sortableChange = function(e, ui){
	if(ui.sender){
		ui.helper.css("width", ui.placeholder.width());
	}
};

$("div.module_column").sortable({
	connectWith: '.module_column',
	opacity: 0.8,
	revert: true,
	change: sortableChange,
	stop: module.save
});

$("div.module_column").disableSelection();

module.init();
</script>
<?php $this->display('footer', 'system');