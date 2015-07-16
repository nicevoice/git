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
			<li><a href="?app=survey&controller=report&action=question&contentid=<?=$contentid?>" class="subentry_report_title">分项报表</a></li>
			<li><a href="?app=survey&controller=report&action=answer&contentid=<?=$contentid?>" class="all_data_title s_6">全部数据</a></li>
		</ul>
	</div>
	<div id="report_list" style="margin-left:100px;" class="cat_m_r">

<div class="f_l">
<div class="table_foot">
		  <div class="f_l">目前参与人数：<span class="c_red"><?=$answers?></span>&nbsp;&nbsp;访问人数：<span class="c_red"><?=$pv?></span></div><div class="f_r"><a href="javascript:;" onclick="survey.exportdata(<?=$contentid?>);">导出为EXCEL</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:;" onclick="report.search(<?=$contentid?>)" class="print">高级检索</a></div>
</div>
  <table cellpadding="0" cellspacing="0" id="item_list" class="table_list">
    <thead>
      <tr>
        <th class="t_l bdr_3" width="50">ID</th>
        <th width="100">用户名</th>
        <th width="120">提交时间</th>
        <th width="200">IP</th>
        <th width="150">管理</th>
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
	                	<td>{answerid}</td>\
	                	<td class="t_c"><a href="javascript: url.member({createdby});">{createdbyname}</a></td>\
	                	<td>{created}</td>\
	                	<td>{ip}（{iparea}）</td>\
	                	<td class="t_c"><img  class="hand view" src="images/view.gif" title="查看"/>&nbsp;<img class="hand delete" title="删除" src="images/delete.gif" /></td>\
	                </tr>';

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
    baseUrl  : '?app=survey&controller=report&action=answer&contentid=<?=$contentid?>&report=1'
});
function init_row_event(id,tr)
{
	tr.find('.view').click(function(){
		report.view(id);
	});
	tr.find('.delete').click(function(){
		ct.confirm('确定要删除这份答卷？',function(){
			$.get('?app=survey&controller=report&action=delete&contentid='+contentid+'&answerid='+id,function(response){
				if(response) tableApp.load();
				else ct.warn('删除失败');
			});
		});
	})
}

tableApp.load();
</script>
</div>
    </div>
</div>
<?php $this->display('footer', 'system');?>