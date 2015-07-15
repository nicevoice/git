<?php $this->display('header');?>
<?php $workflowid = table('category', $catid, 'workflowid');?>
<script type="text/javascript" src="apps/survey/js/report.js"></script>

<style type="text/css" media="print">
<!--
	.pop_box h3, .pop_box .btn_area, .print, .tag_list_2, .tag_1, .bk_10 {display: none;}
-->
</style>

<div class="bk_10"></div>
<div class="tag_1">
	<ul class="tag_list">
<?php 
if(priv::aca('survey', 'survey', 'view')) { 
?>
		<li><a href="?app=survey&controller=survey&action=view&contentid=<?=$contentid?>">查看</a></li>
<?php 
}
if(priv::aca('survey', 'question', 'index')) { 
?>
		<li><a href="?app=survey&controller=question&action=index&contentid=<?=$contentid?>">设计表单</a></li>
<?php 
}
?>
		<li><a href="?app=survey&controller=report&action=index&contentid=<?=$contentid?>" class="s_3">分析报告</a></li>
	</ul>
<?php 
if(priv::aca('survey', 'survey', 'edit')) { 
?>
  <input type="button" name="edit" value="修改" class="button_style_1" onclick="content.edit(<?=$contentid?>)"/>
<?php 
}
if($status == 6 && priv::aca('survey', 'survey', 'createhtml')) { 
?>
  <input type="button" name="createhtml" value="生成" class="button_style_1" onclick="content.createhtml(<?=$contentid?>)"/>
<?php 
}
if ($status < 6 && priv::aca('survey', 'survey', 'publish')) { 
?>
  <input type="button" name="publish" value="发布" class="button_style_1" onclick="content.publish(<?=$contentid?>)"/>
<?php 
}

if ($status == 6 && priv::aca('survey', 'survey', 'data_clear')) { 
?>
  <input type="button" name="clear" value="清空" class="button_style_1" onclick="survey.data_clear(<?=$contentid?>)" />
<?php 
}
if ($status > 0 && priv::aca('survey', 'survey', 'remove')) { 
?>
  <input type="button" name="remove" value="删除" class="button_style_1" onclick="content.remove(<?=$contentid?>)"/>
<?php 
}
if ($status == 0) { 
	if (priv::aca('survey', 'survey', 'delete')){
?>
  <input type="button" name="delete" value="删除" class="button_style_1" onclick="content.del(<?=$contentid?>)"/>
<?php 
    }
    if (priv::aca('survey', 'survey', 'restore')) { 
?>
  <input type="button" name="restore" value="还原" class="button_style_1" onclick="content.restore(<?=$contentid?>)"/>
<?php 
    }
}
if (($status == 1 || ($workflowid && $status == 2)) && priv::aca('survey', 'survey', 'approve')) { 
?>
  <input type="button" name="approve" value="送审" class="button_style_1" onclick="content.approve(<?=$contentid?>)"/>
<?php 
}
if ($status == 3) {
	if (priv::aca('survey', 'survey', 'pass')) { 
?>
  <input type="button" name="pass" value="通过" class="button_style_1" onclick="content.pass(<?=$contentid?>)"/>
<?php 
    }
    if (priv::aca('survey', 'survey', 'reject')) { 
?>
  <input type="button" name="reject" value="退稿" class="button_style_1" onclick="content.reject(<?=$contentid?>)"/>
<?php 
    }
}
if (priv::aca('survey', 'survey', 'move')){
?>
  <input type="button" name="move" value="移动" class="button_style_1" onclick="content.move(<?=$contentid?>)"/>
<?php 
}
if (priv::aca('survey', 'survey', 'reference')){
?>
  <input type="button" name="reference" value="引用" class="button_style_1" onclick="content.reference(<?=$contentid?>)"/>
<?php 
}
?>	<input type="button" name="note" value="批注" class="button_style_1" onclick="content.note(<?=$contentid?>)"/>
	<input type="button" name="version" value="版本" class="button_style_1" onclick="content.version(<?=$contentid?>)"/>
	<input type="button" name="log" value="日志" class="button_style_1" onclick="content.log(<?=$contentid?>)"/>
</div>

<div>
	<div class="f_l w_80 tag_list_2" style="height:600px;">
		<ul>
			<li><a href="?app=survey&controller=report&action=index&contentid=<?=$contentid?>" class="all_report_title">全局报表</a></li>
			<li><a href="?app=survey&controller=report&action=question&contentid=<?=$contentid?>" class="subentry_report_title s_6">分项报表</a></li>
			<li><a href="?app=survey&controller=report&action=answer&contentid=<?=$contentid?>" class="all_data_title">全部数据</a></li>
		</ul>
	</div>
	<div id="report_list" style="margin-left:100px;" class="cat_m_r">
<div class="box_10 f_l w_160 mar_r_8">
<h3>题目</h3>
<ul class="list_4">
<?php
$i = 1;
$type = $b = '';
foreach ($question as $q)
{
	if ($questionid == $q['questionid']) 
	{
		$type = $q['type'];
		$b = 'b';
	}
	else 
	{
		$b = '';
	}
?>
	<li class="question_list_270 <?=$b?>"><a href="?app=survey&controller=report&action=question&contentid=<?=$contentid?>&questionid=<?=$q['questionid']?>"><?=$i?>. <?=$q['subject']?></a></li>
<?php
	$i++;
}
?>
</ul>
</div>

<div id="report_show" class="f_l">

<?php
if (in_array($type, array('radio', 'checkbox', 'select')))
{
?>

<div class="tag_1" ><a style="margin:3px 0px 0px 5px" class="f_l" href="javascript:;" onclick="survey.exportdata(<?=$contentid?>,<?=$questionid?>)">导出此项</a> 
 </div>

<script type="text/javascript">
function pieHtml(url, width, height, title) {
	var swf_url = 'images/pie.swf?piedata='+encodeURIComponent(url);
	var code = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" width="'+width+'" height="'+height+'" title="'+title+'">';
		code += '<param name="movie" value="'+swf_url+'" />';
		code += '<param name="quality" value="high" />';
		code += '<param name="wmode" value="transparent" />';
		code += '<embed src="'+swf_url+'" quality="high" wmode="transparent" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="'+width+'" height="'+height+'"></embed>';
		code += '</object>';
	return code;
}
var url = '<?=ADMIN_URL?>?app=survey&controller=report&action=question&questionid=<?=$questionid?>&report=1';
$('#report_show').append(pieHtml(url, 800, 450, '报告'));
</script>
<?php 
}
else
{
?>
<div class="tag_1" style="padding-top:2px;"><a class="f_l" href="javascript:;" onclick="survey.exportdata(<?=$contentid?>,<?=$questionid?>)">导出此项</a> 
<div class="search_icon search f_r">
      <form method="GET" name="search_f" id="search_f" action="" onsubmit="tableApp.load($('#search_f'));return false;">
        <input type="text" name="keywords" id="keywords" value="<?=$keywords?>" size="20"/>
        <a href="javascript:;" onclick="tableApp.load($('#search_f'));return false;" title="搜索">搜索</a>
      </form>
    </div>
 </div>

  <table width="650" cellpadding="0" cellspacing="0" id="item_list" class="table_list">
    <thead>
      <tr>
        <th class="t_l bdr_3">数据</th>
        <th width="70">用户名</th>
        <th width="70">提交时间</th>
        <th width="120">IP</th>
      </tr>
    </thead>
    <tbody id="list_body">
    </tbody>
  </table>
  
  <div class="table_foot">
    <div id="pagination" class="pagination f_r"></div>
  </div>

<script type="text/javascript">
var row_template = '<tr id="row_{answerid}">\
	                	<td>{content}</td>\
	                	<td class="t_c"><a href="javascript: url.member({createdby});">{createdbyname}</a></td>\
	                	<td>{created}</td>\
	                	<td>{ip}<br />{iparea}</td>\
	                </tr>';

function init_row_event(id, tr)
{

}
</script>

<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css"/>

<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.pagination.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript">
var tableApp = new ct.table('#item_list', {
    rowIdPrefix : 'row_',
    rightMenuId : null,
    pageField : 'page',
    pageSize : 15,
    dblclickHandler : 'report.view',
    rowCallback     : 'init_row_event',
    template : row_template,
    baseUrl  : '?app=survey&controller=report&action=question&questionid=<?=$questionid?>&report=1'
});

tableApp.load();
</script>
<?php 
}
?>
</div>
    </div>
</div>
<?php $this->display('footer', 'system');?>