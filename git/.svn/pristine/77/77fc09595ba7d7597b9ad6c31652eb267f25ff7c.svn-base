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
			<li><a href="?app=survey&controller=report&action=index&contentid=<?=$contentid?>" class="all_report_title s_6">全局报表</a></li>
			<li><a href="?app=survey&controller=report&action=question&contentid=<?=$contentid?>" class="subentry_report_title">分项报表</a></li>
			<li><a href="?app=survey&controller=report&action=answer&contentid=<?=$contentid?>" class="all_data_title">全部数据</a></li>
		</ul>
	</div>
	<div id="report_list" style="margin-left:100px;" class="cat_m_r">
	    <div class="table_foot">
		  <div class="f_l">目前共有<span class="c_red"><?=$answers?></span>人参与</div><div class="f_r"><a href="javascript:;" onclick="survey.exportdata(<?=$contentid?>);">导出为EXCEL</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:;" onclick="window.print()" class="print">打印</a></div>
		</div>
		<div class="mar_t_10">
<?php 
$i = 1;
foreach ($question as $questionid=>$q)
{
?>
<dl class="list_2">
   <dt><?=$i?>.&nbsp;&nbsp;<a href="javascript:;" onclick="report.question('<?=$q['contentid']?>', '<?=$q['questionid']?>')"><?=$q['subject']?></a><?php if ($q['type'] == 'radio' || $q['type'] == 'checkbox' || $q['type'] == 'select'){?>（共<?=$q['votes']?>票<?php if ($q['records']){ ?>，补充<?=$q['records']?>条<?php } ?>）<?php } ?></dt>
<?php 
if ($q['type'] == 'text' || $q['type'] == 'textarea')
{
?>
	<dd>有效数据 <?=$q['records']?> 条</dd>
<?php 
}
elseif ($q['type'] == 'radio' || $q['type'] == 'checkbox' || $q['type'] == 'select') 
{
    foreach ($q['option'] as $o)
    {
?>
    <dd><a href="javascript:;" onclick="report.option(<?=$o['optionid']?>)"><?=$o['name']?></a></dd>
	<dd><div class="bar-chart"><span><img src="images/space.gif" height="16" width="<?=2*$o['percent']?>" alt="<?=$o['name']?>"/></span></div> &nbsp;<?=$o['percent']?>% (<?=$o['votes']?>)</dd>
<?php
    }
}
?>
</dl>
<?php
	$i++;
}
?>
		</div>
	</div>
</div>

<?php $this->display('footer', 'system');?>