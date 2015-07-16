<?php $this->display('header', 'system');?>

<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.pagination.js"></script>
<script type="text/javascript" src="apps/system/js/content.js"></script>
<script type="text/javascript" src="apps/space/js/space.js"></script>

<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/autocomplete/style.css" />
<script src="<?=IMG_URL?>js/lib/cmstop.autocomplete.js" type="text/javascript"></script>
<script type="text/javascript" src="uploader/cmstop.uploader.js"></script>
<script type="text/javascript" src="imageEditor/cmstop.imageEditor.js"></script>
<script type="text/javascript" src="js/cmstop.filemanager.js"></script>

<!--treeview-->
<link href="<?=IMG_URL?>js/lib/treeview/treeview.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.treeview.js"></script>

<div class="bk_8"></div>
<div class="tag_1">
	<ul class="tag_list">
		<li class="s_3"><a class="s_3" href="?app=space&controller=index&action=panel&spaceid=<?php echo $spaceid;?>">查看</a></li>
	</ul>
	<input type="button" onclick="space.published('<?php echo $author;?>')" class="button_style_1" value="发布文章" name="published">
	<input type="button" onclick="space.edit(<?php echo $spaceid;?>)" class="button_style_1" value="修改" name="edit">
	<?php if ($status <3) {?>
		<input type="button" onclick="space.action(<?php echo $spaceid;?>,3,1)" class="button_style_1" value="开通">
	<?php } ?>
	<?php if ($status == 3) {?>
		<input type="button" onclick="space.action(<?php echo $spaceid;?>,4,1)" class="button_style_1" value="推荐">
		<input type="button" onclick="space.action(<?php echo $spaceid;?>,0,1)" class="button_style_1" value="禁用">
	<?php } ?>
	<?php if ($status == 4) {?>
		<input type="button" onclick="space.action(<?php echo $spaceid;?>,3,1)" class="button_style_1" value="取消推荐">
		<input type="button" onclick="space.action(<?php echo $spaceid;?>,0,1)" class="button_style_1" value="禁用">
	<?php } ?>
	<input type="button" onclick="space.del(<?php echo $spaceid;?>)" class="button_style_1" value="删除" name="remove">
</div>
<table width="610" cellspacing="0" cellpadding="0" border="0" class="table_form mar_l_8">
	<tbody><tr>
		<td width="90" align="center" rowspan="4" style="text-align:center;"><img src="<?php echo thumb($photo,96,96);?>" alt="<?php echo $author;?>" height="96" width="96"/></td>
		<th width="80">专栏名：</th><td width="130"><a href="<?php echo SPACE_URL.$alias;?>" target="_blank"><?php echo $name;?></a></td>
		</tr>
		<tr>
		<th>地址：</th><td colspan="3"><a href="<?php echo SPACE_URL.$alias;?>" target="_blank"><?php echo SPACE_URL.$alias;?></a></td>
		</tr>
	<tr>
		<th>作者名：</th><td width="130"><?php echo $author;?></td>
		<th>用户名：</th><td width="130"><?php echo $username;?></td>
	</tr>
	<tr>
		<th>状态：</th><td><?php echo $status_name;?></td>
		<th>稿件数：</th><td><?php echo $posts;?></td>
	</tr>
	<tr>
		<td align="center" style="text-align:center;"><?php echo $author;?></td>
		<th>评论：</th><td><?php echo $comments;?></td>
		<th>PV：</th><td><?php echo $pv;?></td>
	</tr>
	<tr>
		<td></td>
		<th>开通时间：</th><td><?php echo $created;?></td>
		<th>修改时间：</th><td><?php echo $modified;?></td>
	</tr>
</tbody></table>

	<div id="published_x" class="th_pop" style="display:none;width:150px">
     <div>
        <a href="javascript: tableApp.load('published_min=<?=date('Y-m-d', TIME)?>');">今日</a> |
        <a href="javascript: tableApp.load('published_min=<?=date('Y-m-d', strtotime('yesterday'))?>&published_max=<?=date('Y-m-d', strtotime('yesterday'))?>');">昨日</a> | 
        <a href="javascript: tableApp.load('published_min=<?=date('Y-m-d', strtotime('last monday'))?>');">本周</a> | 
        <a href="javascript: tableApp.load('published_min=<?=date('Y-m-01', strtotime('this month'))?>');">本月</a>
     </div>
     <ul>
       <?php 
       for ($i=2; $i<=7; $i++) { 
       	  $publishdate = date('Y-m-d', strtotime("-$i day"));
       ?>
        <li><a href="javascript: tableApp.load('published_min=<?=$publishdate?>&published_max=<?=$publishdate?>');"><?=$publishdate?></a></li>
       <?php } ?>
     </ul>
  </div>
  <div id="weight_x" class="th_pop" style="display:none;width:60px">
     <ul>
        <li><a href="javascript: tableApp.load('weight_min=0&weight_max=29');">0-29</a></li>
        <li><a href="javascript: tableApp.load('weight_min=30&weight_max=49');">30-49</a></li>
        <li><a href="javascript: tableApp.load('weight_min=50&weight_max=59');">50-59</a></li>
        <li><a href="javascript: tableApp.load('weight_min=60&weight_max=69');">60-69</a></li>
        <li><a href="javascript: tableApp.load('weight_min=70&weight_max=79');">70-79</a></li>
        <li><a href="javascript: tableApp.load('weight_min=80&weight_max=89');">80-89</a></li>
        <li><a href="javascript: tableApp.load('weight_min=90&weight_max=100');">90-100</a></li>
     </ul>
  </div>
  <table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8">
    <thead>
      <tr>
        <th class="t_l bdr_3">标题</th>
        <th width="80">管理操作</th>
        <th width="60" class="ajaxer"><div name="pv">点击</div></th>
        <th width="60" class="ajaxer"><em class="more_pop" name="weight_x"></em><div name="weight">权重</div></th>
        <th width="120" class="ajaxer"><em class="more_pop" name="published_x"></em><div name="published">发布时间</div></th>
      </tr>
    </thead>
    <tbody id="list_body">
    </tbody>
  </table>
  <div class="table_foot">
    <div id="pagination" class="pagination f_r"></div>
  </div>

		
<script type="text/javascript">
var row_template = '<tr id="row_{contentid}" right_menu_id="right_menu_{modelalias}" modelid="{modelid}" model="{modelalias}">\
	                	<td><a title="查看内容" href="javascript:content.view({contentid});"><div class="icon {modelalias}"></div></a><a href="javascript: content.category({catid});" class="c_blue">[{catname}]</a> {thumb_icon}<a href="javascript: content.edit({contentid});" style="color:{color}" tips="ID：{contentid}<br />点击：{pv}次<br />评论：{comments}条<br />来源：{source_name}<br />Tags：{tags}<br />创建：{createdbyname}（{created}）<br />修改：{modifiedbyname}（{modified}）<br />审核：{checkedbyname}（{checked}）" class="title_list">{title}</a> {note} {lock}</td>\
	                 	<td class="t_c"><a href="{url}" target="_blank"><img src="images/view.gif" alt="访问" width="16" height="16" /></a> &nbsp;<img src="images/edit.gif" alt="编辑" width="16" height="16" class="hand edit"/> &nbsp;<img src="images/delete.gif" alt="删除" width="16" height="16" class="hand delete" /></td>\
	                	<td class="t_r">{pv}</td>\
	                	<td class="t_r">{weight}</td>\
	                	<td class="t_c">{published}</td>\
	                </tr>';
</script>
<script type="text/javascript">

var tableApp = new ct.table('#item_list', {
		rowIdPrefix : 'row_',
		pageSize : 15,
		rowCallback: 'init_row_event',
		jsonLoaded : 'json_loaded',
		template : row_template,
		baseUrl  : '?app=system&controller=content&action=page&status=6&spaceid=<?=$spaceid?>'
});

function json_loaded(json) {
	
}
function init_row_event(id, tr) {
	tr.find('img.edit').click(function(){
		content.edit(id);
	});
	tr.find('img.delete').click(function(){
		$.getJSON('?app=article&controller=article&action=remove&contentid='+id, function(response){
			if (response.state){
				tableApp.deleteRow(id);
				ct.ok('操作成功');
			}
			else{
				ct.error(response.error);
			}
		});
	});
	tr.find('a.title_list').attrTips('tips');
}

$(function() {
	tableApp.load();
	$('#pagesize').val(tableApp.getPageSize());
	$('#pagesize').blur(function(){
		var p = $(this).val();
		tableApp.setPageSize(p);
		tableApp.load();
	});
	$('img.tips').attrTips('tips', 'tips_green', 200, 'top');
});
</script>
<?php $this->display('footer', 'system');?>