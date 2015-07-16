<?php $this->display('header', 'system');?>
<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/treeview/style.css"/>
<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/autocomplete/style.css" />
<script src="<?=IMG_URL?>js/lib/cmstop.autocomplete.js" type="text/javascript"></script>
<script type="text/javascript">
$.validate.setConfigs({
	xmlPath:'apps/system/validators/my/menu/'
});
function addNewTab() {
	ct.form('添加常用操作','?app=system&controller=my&action=add',400,160,function(json){
		tableApp.addRow(json.data);
		return true;
	});
}
function renew() {
	$.get("?app=system&controller=index&action=cache_nav", function(data){
		ct.ok("操作成功");
		setTimeout(function(){location.reload();},1000);
	});
}
$(function() {
	$("#tabname").autocomplete({
		url:"?app=system&controller=index&action=suggest&q=%s", 
		itemSelected: function(a, item) {
			window.location = item.url;
		}
	});
	$('#admin_map ul li:last-child').addClass('last');
	var a = $('#viewNav').click(function(){
		if (a.hasClass('open'))
		{
			a.removeClass('open');
			$('#admin_map').hide();
		}
		else
		{
			a.addClass('open');
			$('#admin_map').show();
		}
	});
});
</script>
<div style="height:20px;clear:both"></div>
<div class="box_7" style="width:250px;padding-left:20px;">
	<input id="tabname" url="?app=system&controller=index&action=suggest&q=%s" type="text" size="40" class="bdr_6">
</div>

<div class="bk_10"></div>
<div class="box_7" style="margin-left:10px;width:730px">
	<div class="f_l w_350">
		<h3 >常用操作<img src="images/add_1.gif" class="manage"  width="16" height="16" alt="增加常用操作设置" title="常用操作设置" onclick="addNewTab()"/></h3>
		<ul class="txt_list list_1" id="selfNav">
		<?php foreach($nav_self as $k => $v) {?>
			<li><a href="<?php echo $v['url']; ?>" title="<?php echo $v['name']; ?>"><?php echo str_cut($v['name'],30); ?></a></li>
		<?php }?>
		</ul>
	</div>
	<div class="f_l w_350 list_1">
		<h3>最近操作</h3>
		<ul class="txt_list">
		<?php foreach($nav_recent as $k => $v) { ?>
			<li><a href="<?php echo $v['url']; ?>" title="<?php echo $v['name']; ?>"><?php echo str_cut($v['name'],30); ?></a></li>
		<?php }?>
		</ul>
	</div>
	<div class="clear"></div>
</div>
<div class="bk_8"></div>
<div class="box_7" style="margin-left:14px;width:90%">
	<h3><a href="javascript:;" id="viewNav">查看菜单导航</a><span class="c_orange f_12 n hand" onclick="renew();">【更新菜单】</span></h3>
	<!--菜单导航-->
	<div id="admin_map" style="display:none">
		<?php foreach($nav_all as $k1 => $v1) {?>
		<div class="f_l filetree treeview">
			<h4><a href="<?php echo $v1['url']?>"><?php echo $v1['name']?></a></h4>
			<?php if(is_array($v1['submenus'])){?>
			<ul>
				<?php foreach($v1['submenus'] as $k2 => $v2) {?>
				<li>
					<h5><a href="<?php echo $v2['url']?>"><?php echo $v2['name']?></a></h5>
					<?php if(is_array($v2['submenus'])){?>
					<ul>
						<?php foreach($v2['submenus'] as $k3 => $v3) {?>
							<li><a href="<?php echo $v3['url']?>"><?php echo $v3['name']?></a></li>
						<?php }?>
					</ul>
					<?php }?>
				</li>
				<?php }?>
			</ul>
			<?php }?>
		</div>
		<?php }?>
		<div class="clear"></div>
	</div>
	<div class="clear bk_10"></div>
</div>

<?php $this->display('footer');