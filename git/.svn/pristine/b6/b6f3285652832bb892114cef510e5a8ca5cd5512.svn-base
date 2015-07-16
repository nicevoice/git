<?php $this->display('topic/header');?>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="apps/comment/js/topic.js"></script>
<div class="bk_8"></div>
<div class="table_head">
	<input type="button" id="add" value="添加话题" class="button_style_4 f_l"/>
	<form onsubmit="tableApp.load($('#search_f'));return false;" action="" id="search_f" name="search_f" method="GET">
		<div class="search_icon search f_r">
			<input type="text" name="keywords" id="keywords" value="<?=$keywords?>" size="15"/>
			<a onclick="tableApp.load($('#search_f'));" href="javascript:;" id="submit">搜索</a>
		</div>
	<form>
</div>
<div class="bk_8"></div>
<table width="99%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list" style="margin-left:6px;">
  <thead>
    <tr>
      <th width="25" class="t_l bdr_3"><input type="checkbox" id="check_all" /></th>
	  <th width="60">ID</th>
      <th>话题名称</th>
	  <th width="12%">状态</th>
      <th width="12%">创建时间</th>
      <th width="15%">操作</th>
    </tr>
  </thead>
  <tbody id="list_body">
  </tbody>
</table>
<div class="clear"></div>
<div class="table_foot" style="width:98%">
<div id="pagination" class="pagination f_r"></div>
<div class="f_r">
	 共有<span id="pagetotal">0</span>条记录&nbsp;&nbsp;&nbsp;
</div>
<div class="f_l">
	<p class="f_l">
		<input type="button" onclick="topic.enable()" value="开启" class="button_style_1"/>
		<input type="button" onclick="topic.disable()" value="禁用" class="button_style_1"/>
		<input type="button" onclick="topic.del()" value="删 除" class="button_style_1"/>
	</p>
</div>
</div>
<!--右键菜单-->
<ul id="right_menu" class="contextMenu">
   <li class="edit"><a href="#topic.add">编辑</a></li>
   <li class="del"><a href="#topic.del">删除</a></li>
</ul>

<script type="text/javascript">
var app = '<?=$app?>';
var controller = '<?=$controller?>';
var row_template = '\
<tr id="tr_{topicid}">\
	<td><input type="checkbox" class="checkbox_style" name="chk_row" id="chk_row_{topicid}" value="{topicid}" /></td>\
	<td class="t_c"> {topicid} </td>\
	<td class="t_l"><a href="javascript:;" class="edit"> {title}</a> </td>\
    <td class="t_c"> {disabled} </td>\
    <td class="t_c"> {created} </td>\
    <td class="t_c">\
    	<a target="_blank"  href="{url}" /><img title="查看" src="images/view.gif" class="hand view"/></a>\
    	<img title="话题修改" src="images/edit.gif" alt="话题修改" class="hand edit" />\
    	<img title="话题开启" src="images/sh.gif" alt="话题开启" class="hand update" />\
    	<img title="话题关闭" src="images/lock.gif" alt="话题关闭" class="hand stop" />\
    	<img title="删除" src="images/delete.gif" alt="删除" class="hand delete" />\
    </td>\
</tr>';

var tableApp = new ct.table('#item_list', {
    rowIdPrefix : 'tr_',
    rightMenuId : 'right_menu',
    pageVar : 'page',
	pageSize : 15,
    dblclickHandler : 'topic.add',
    rowCallback     : 'init_event',
    template : row_template,
	jsonLoaded : 'json_loaded',
    baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=topic_page'
});

$(function (){
	tableApp.load();
	$('#add').click(topic.add);
});

// 显示总条数
function json_loaded(json) {
	$('#pagetotal').html(json.total);
}

//init function
function init_event(id, tr) {
	tr.find('a.edit, img.edit').click(function (){topic.add(id);});
	tr.find('img.delete').click(function (){topic.del(id);});
	tr.find('img.update').click(function(){topic.enable(id);});
	tr.find('img.stop').click(function(){topic.disable(id);});
}

</script>
<?php $this->display('footer','system');?>