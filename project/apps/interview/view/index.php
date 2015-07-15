<?php $this->display('header');?>

<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/dropdown/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.dropdown.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.pagination.js"></script>

  <div class="bk_8"></div>
  <div class="table_head tag_1">
<?php 
if (priv::aca('interview', 'interview', 'search')){
?>
    <div class="search search_icon f_r">
      <form method="GET" name="search_f" id="search_f" action="">
        <input type="hidden" name="catid" value="<?=$catid?>" />
        <input type="hidden" name="status" value="<?=$status?>" />
        <input type="hidden" name="modelid" value="<?=$modelid?>" />
        <input type="text" name="keywords" id="keywords" value="<?=$keywords?>" size="30"/>
        <a href="javascript:;" onclick="tableApp.load($('#search_f'));return false;" title="搜索">搜索</a><a href="javascript:;" class="more_search" onclick="interview.search(<?=$catid?>, <?=$modelid?>);" title="高级搜索">高级搜索</a>
      </form>
    </div>
<?php 
}
?>
    <!--发布信息-->
    <div style="padding-left:80px;">
     <div id="add_data">
    <?=element::model_change($catid,$modelid)?>
      </div>
<?php 
if (priv::aca('interview', 'interview', 'add')){
?>
      <a href="javascript:interview.add(<?=$catid?>, <?=$modelid?>);"><strong class="add_data_btn">发布</strong></a>
<?php 
}
?>
      <ul class="tag_list">
<?php foreach ($statuss as $k=>$v) {
if ($k > 3 || ($k == 0 && (priv::aca('interview', 'interview', 'delete') || priv::aca('interview', 'interview', 'restore'))) 
  || ($k == 1 && (priv::aca('interview', 'interview', 'add') || priv::aca('interview', 'interview', 'approve')))
  || ($k == 2 && (priv::aca('interview', 'interview', 'pass') || priv::aca('interview', 'interview', 'reject') || priv::aca('interview', 'interview', 'approve')))
  || ($k == 3 && (priv::aca('interview', 'interview', 'pass') || priv::aca('interview', 'interview', 'reject'))))
{
?>
        <li><a href="?app=<?=$app?>&controller=<?=$controller?>&action=index&catid=<?=$catid?>&modelid=<?=$modelid?>&status=<?=$k?>" <?php if ($k == $status) { ?>class="s_3"<?php } ?>><?=$v['name']?></a></li>
<?php } } ?>
      </ul>
    </div>
  </div>
<?php $this->display("status/$status");?>

<script type="text/javascript">
var tableApp = new ct.table('#item_list', {
    rowIdPrefix : 'row_',
    rightMenuId : 'right_menu',
    pageField : 'page',
    pageSize : 15,
    dblclickHandler : 'interview.edit',
    rowCallback     : 'init_row_event',
    template : row_template,
    baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page&catid=<?=$catid?>&status=<?=$status?>'
});

function init_row_event(id, tr)
{
	tr.find('img.edit').click(function(){
		interview.edit(id);
	});
	tr.find('img.delete').click(function(){
		<?php if ($status) { ?>
		interview.remove(id);
		<?php }else{ ?>
		interview.del(id);
		<?php } ?>
	});
    tr.find('a.title_list').attrTips('tips');
    
}

tableApp.load();

setInterval(function(){tableApp.load();}, 180000);

$('#changemodel').dropdown({
	changemodel:{
		onchange:function(model, name){
			window.location.href = '?app='+model+'&controller='+model+'&action=index&catid=<?=$catid?>';
		}
	}
});

</script>


<?php $this->display('footer');
