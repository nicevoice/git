<?php $this->display('header');?>

<div class="bk_10"></div>
<div class="tag_1">
  <ul class="tag_list">
    <li><a href="?app=activity&controller=activity&action=view&contentid=<?=$contentid?>" >查看</a></li>
<?php 
if(priv::aca('activity', 'activity', 'viewsigns')) { 
?>
    <li  class="s_3"><a href="javascript: activity.viewsign(<?=$contentid?>);">管理报名者</a></li>
<?php 
}
?>
   </ul>
<?php 
if(priv::aca('activity', 'activity', 'edit')) { 
?>
  <input type="button" name="edit" value="修改" class="button_style_1" onclick="content.edit(<?=$contentid?>)"/>
<?php 
}
if($status == 6 && priv::aca('activity', 'activity', 'createhtml')) { 
?>
  <input type="button" name="createhtml" value="生成" class="button_style_1" onclick="content.createhtml(<?=$contentid?>)"/>
<?php 
}

if ($status >= 5 && priv::aca('activity', 'activity', 'unpublish')) { 
?>
  <input type="button" name="unpublish" value="撤稿" class="button_style_1" onclick="content.unpublish(<?=$contentid?>)"/>
<?php 
}
if ($status > 0 && priv::aca('activity', 'activity', 'remove')) { 
?>
  <input type="button" name="remove" value="删除" class="button_style_1" onclick="content.remove(<?=$contentid?>)"/>
<?php 
}
if ($status == 0) { 
	if (priv::aca('activity', 'activity', 'delete')){
?>
  <input type="button" name="delete" value="删除" class="button_style_1" onclick="content.del(<?=$contentid?>)"/>
<?php 
    }
    if (priv::aca('activity', 'activity', 'restore')) { 
?>
  <input type="button" name="restore" value="还原" class="button_style_1" onclick="content.restore(<?=$contentid?>)"/>
<?php 
    }
}
if (($status == 1 || ($workflowid && $status == 2)) && priv::aca('activity', 'activity', 'approve')) { 
?>
  <input type="button" name="approve" value="送审" class="button_style_1" onclick="content.approve(<?=$contentid?>)"/>
<?php 
}
if ($status == 3) {
	if (priv::aca('activity', 'activity', 'pass')) { 
?>
  <input type="button" name="pass" value="通过" class="button_style_1" onclick="content.pass(<?=$contentid?>)"/>
<?php 
    }
    if (priv::aca('activity', 'activity', 'reject')) { 
?>
  <input type="button" name="reject" value="退稿" class="button_style_1" onclick="content.reject(<?=$contentid?>)"/>
<?php 
    }
}
if (priv::aca('activity', 'activity', 'move')){
?>
  <input type="button" name="move" value="移动" class="button_style_1" onclick="content.move(<?=$contentid?>)"/>
<?php 
}
if (priv::aca('activity', 'activity', 'reference')){
?>
  <input type="button" name="reference" value="引用" class="button_style_1" onclick="content.reference(<?=$contentid?>)"/>
<?php 
}
?>
  <input type="button" name="note" value="批注" class="button_style_1" onclick="content.note(<?=$contentid?>)"/>
  <input type="button" name="version" value="版本" class="button_style_1" onclick="content.version(<?=$contentid?>)"/>
  <input type="button" name="log" value="日志" class="button_style_1" onclick="content.log(<?=$contentid?>)"/>
</div>
<div>
   <div class="f_l w_80 tag_list_2" style="height:400px;">
	 <ul >
		<li><a href="?app=<?=$app?>&controller=activity&action=viewsigns&contentid=<?=$contentid?>&state=0" <?php if($state == 0) { ?> class="s_6" <?php } ?> >待审</a></li>
		<li><a href="?app=<?=$app?>&controller=activity&action=viewsigns&contentid=<?=$contentid?>&state=1" <?php if($state == 1) { ?> class="s_6 " <?php } ?> >已审</a></li>
		<li><a href="javascript:sign.exports(<?=$contentid?>)" >导出Excel</a></li>
	 </ul>
    </div>

<?php $this->display("signstate/$state");?>
</div>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/dropdown/style.css"/>
<style type="text/css">
#nopage {
background-color:#FFFDD7;
border:1px solid #FDBD77;
display:none;
margin:10px;
padding:16px;
width:300px;
}
</style>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.dropdown.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.pagination.js"></script>
<script type="text/javascript" >
   var contentid = <?=$contentid?>;
   var state = <?=$state?>;
</script>
<script type="text/javascript" src="apps/activity/js/sign.js"> </script>
<script type="text/javascript">
var tableApp = new ct.table('#item_list', {
    rowIdPrefix : 'row_',
    rightMenuId : 'right_menu',
    pageField : 'page',
    pageSize :15,
    rowCallback     : 'init_row_event',
    template : row_template,
    jsonLoaded : controlwidth,
    baseUrl  : '?app=<?=$app?>&controller=sign&action=page&contentid=<?=$contentid?>&state=<?=$state?>'
});

function init_row_event(id, tr)
{
	tr.find('img.edit').click(function(){
		sign.edit(id);
	});
	tr.dblclick(function(){sign.edit(id);});
	tr.find('img.view').click(function(){
		sign.view(id);
	});
	tr.find('img.pass').click(function(){
		sign.pass(id);
	});
	tr.find('img.delete').click(function(){
		sign.del(id);
	});
	tr.find('a.photo').mouseover(function(){
		
	})
    tr.find('a.title_list,a.photo').attrTips('tips');
    var tds = tr.children('td');
    if(tds.length > 7)
    {
    	var offset = tds.length-8;
    	tds.slice(4,5+offset).remove();
    }
    tds.each(function(){
    	var tda = $(this).children('a');
    	if(typeof tda.get(0) !='undefined' && tda.attr('href')=='' || tda.attr('href')==UPLOAD_URL)
    	{
    		$(this).children('a').replaceWith('-');
    	}
    })
}
function controlwidth(data){
	if(!data.total)
	{
		$('#item_list').parent().html('<div id="nopage" style="display: block;"><p>目前没有<?php if($state==0) {?>待审<?php }else{ ?>已审<?php };?>的报名者</p></div>');
	}
	var thlength = $('#item_list th').length;
	if(thlength > 7)
	{
		var offset = thlength-8;
		$('#item_list th').slice(4,5+offset).remove();
	}
}

tableApp.load();


var interval = setInterval(function(){tableApp.load();}, 180000);
window.onunload = function ()
{
	clearInterval(interval);
}
</script>
<?php $this->display('footer');?>