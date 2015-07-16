<?php $this->display('header','system');?>

<style type="text/css">
/** 下拉菜单 **/
div.dropmenu {
	position: relative;
}
div.dropmenu h3 {
	background: url(css/images/bg.gif) no-repeat scroll 0 -481px transparent;
	height: 22px;
	line-height: 24px;
	padding-left: 14px;
	cursor: pointer;
	width: 42px;
	color: #fff;
}
div.dropmenu h3 a {
	color: #fff;
}
div.dropmenu ul {
	display: none;
	position: absolute;
	left: 0;
	top: 24;
	background: #fff;
	border:1px solid #9EC6DD;
	z-index:999;
}
div.dropmenu li {
	border-bottom:1px solid #D0E6EC;
	line-height:24px;
	text-align:right;
	padding-right:6px;
}

#pup_model li {
	cursor: pointer;
	background: url(<?php echo IMG_URL?>js/lib/dropdown/bg.gif) no-repeat scroll 3px -46px transparent;
}
#pup_model li.over {
	color: #d00;
}
#pup_model li.picture {background-position: 3px -332px;}
#pup_model li.link {background-position: 3px -96px;}
#pup_model li.video {background-position: 3px -120px;}
#pup_model li.activity {background-position: 3px -248px;}
#pup_model li.vote {background-position: 3px -167px;}
#pup_model li.interview {background-position: 3px -143px;}
#pup_model li.survey {background-position: 3px -304px;}
#pup_model li.special {background-position: 3px -276px;}
</style>
<!--treeview-->
<link href="<?=IMG_URL?>js/lib/treeview/treeview.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.treeview.js"></script>

<!--tablesorter-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<!--contextmenu-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<!--pagination-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.pagination.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/dropdown/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.dropdown.js"></script>

<script type="text/javascript" src="apps/system/js/content.js"></script>

<!-- 时间选择器 -->
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.datepicker.js"></script>
<link href="<?=IMG_URL?>js/lib/datepicker/style.css" rel="stylesheet" type="text/css" />

  <div class="bk_8"></div>
  <div class="tag_1">
    <div class="search_icon search f_r">
      <form method="GET" name="search_f" id="search_f" action="" onsubmit="tableApp.load($('#search_f'));return false;">
        <input type="text" name="keywords" id="keywords" value="<?=$keywords?>" size="30"/>
        <a href="javascript:;" onclick="tableApp.load($('#search_f'));return false;" title="搜索">搜索</a><a href="javascript:;" class="more_search" onclick="content.search('<?=$catid?>', '<?=$modelid?>', <?=$status?>);" title="高级搜索">高级搜索</a>
      </form>
    </div>
    <div style="padding-left:80px;">
  	<div id="pup_model" class="dropmenu" style="float: left;padding-right: 5px;">
		<h3 style="padding-left: 28px;">发布</h3>
		<ul>
		<?php foreach (table('model') as $k=>$v):?>
			<li class="<?=$v['alias']?>" modelid="<?=$v['modelid']?>">发<?=$v['name']?></li>
		<?php endforeach;?>
		</ul>
  	</div>
      <div id="add_data">
	  <select id="changemodel" style="width:70px" marginTop="1" marginLeft="0">
	      <option value="0">全部</option>
	      <?php foreach (table('model') as $m) {?>
		  <option ico="<?=$m['alias']?>" value="<?=$m['modelid']?>" <?=$m['modelid'] == $modelid ? 'selected' : ''?>><?=$m['name']?></option>
		  <?php } ?>
	  </select>
      </div>
      <ul class="tag_list">
      <?php foreach ($statuss as $k=>$v) {?>
        <li><a href="?app=<?=$app?>&controller=<?=$controller?>&action=content&catid=<?=$catid?>&modelid=<?=$modelid?>&status=<?=$k?>" <?php if ($k == $status) { ?>class="s_3"<?php } ?>><?=$v['name']?></a></li>
      <?php } ?>
      </ul>
    </div>
  </div>
<?php $this->display("content/status/$status");?>

<script type="text/javascript">
var tableApp = new ct.table('#item_list', {
    rowIdPrefix : 'row_',
    rightMenuId : 'right_menu',
    pageField : 'page',
    pageSize : 15,
    dblclickHandler : 'content.edit',
    rowCallback     : 'init_row_event',
    template : row_template,
    baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=content&catid=<?=$catid?>&modelid=<?=$modelid?>&status=<?=$status?>'
});

function init_row_event(id, tr)
{
	tr.find('img.edit').click(function(){
		content.edit(id);
	});
	tr.find('img.delete').click(function(){
		<?php if ($status) { ?>
		content.remove(id);
		<?php }else{ ?>
		content.del(id);
		<?php } ?>
	});
    tr.find('a.title_list').attrTips('tips');
}

$('#changemodel').dropdown({
	changemodel:{
		onchange:function(modelid, name){
			window.location.href = '?app=system&controller=my&action=content&catid=<?=$catid?>&modelid='+modelid;
		}
	}
});

tableApp.load();

var interval = setInterval(function(){tableApp.reload();}, 180000);
window.onunload = function ()
{
	clearInterval(interval);
}

$.fn.dd = function (action)
{
	p = $.extend({
		width: 70
	}, action || {});
	this.each(function (i, e){
		$(this).find('ul').width(p.width);
		$(e).mouseover(function (){
			$(this).find('ul').show();
			$(document).one('click', function (){
				$('div.dropmenu ul').hide();
			});
			$(this).mouseout(function (){
				$(this).find('ul').hide();
			});
		});
	});
	return this;
}

$(function (){
	$('#pup_model li, #pup_model h3').click(function (){
		if(this.tagName == 'LI'){
			var modelid = $(this).attr('modelid');
			var model = this.className;
		}else {
			var modelid = $('#changemodel').val();
			if(modelid == 0) {
				modelid = 1;
				var model = 'article';
			}
		}
		ct.assoc.open('?app='+model+'&controller='+model+'&action=add', 'newtab');
	});
	$('#pup_model li').mouseover(function (){
		$('#pup_model li').css('color', '#000');
		$(this).css('color', '#d00');
	});
	$('strong.add_data_btn').mouseover(function (){
		$('#pub_model').show();
		$(document).one('click', function (){
			$('#pub_model').hide();
		});
	});
	$('#pup_model').dd();
});



</script>
<?php $this->display('footer');?>