<?php $this->display('header');?>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="apps/freelist/js/freelist.js"></script>
<div class="bk_8"></div>
<div class="table_head">
	<input type="button" id="add" value="添加列表" class="button_style_4 f_l"/>
	<form onsubmit="tableApp.load($('#search_f'));return false;" action="" id="search_f" name="search_f" method="GET">
		<select id="modelset"style="width:100px;float:left;margin-right:5px;">
		<?php foreach($grouplist as $list):?>
			<option value="<?=$list['gid']?>" <?= $_GET['gid'] == $list['gid'] ? 'selected' : ''?>><?=$list['name']?></option>
		<?php endforeach;?>
		</select>
		<a href="javascript:;" onclick="ct.assoc.open('?app=freelist&amp;controller=group&amp;action=index','newtab')">分组管理</a>
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
      <th>列表名称</th>
      <th width="12%">分组</th>
      <th width="12%">生成频率</th>
      <th width="12%">生成时间</th>
      <th width="12%">操作</th>
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
		<input type="button" onclick="flist.update()" value="更 新" class="button_style_1"/>
		<input type="button" onclick="flist.stop()" value="停止自动更新" class="button_style_1"/>
		<input type="button" onclick="flist.del()" value="删 除" class="button_style_1"/>
	</p>
</div>
</div>
<!--右键菜单-->
<ul id="right_menu" class="contextMenu">
   <li class="edit"><a href="#flist.add">编辑</a></li>
   <li class="del"><a href="#flist.del">删除</a></li>
</ul>

<script type="text/javascript">
var app = '<?=$app?>';
var controller = '<?=$controller?>';
var row_template = '\
<tr id="tr_{flid}">\
	<td><input type="checkbox" class="checkbox_style" name="chk_row" id="chk_row_{flid}" value="{flid}" /></td>\
	<td class="t_l"><a href="javascript:;" class="edit"> {name}</a> </td>\
    <td class="t_l"> {groupname} </td>\
    <td class="t_c"> {frequency} </td>\
    <td class="t_c"> {published} </td>\
    <td class="t_c">\
    	<a target="_blank"  href="{url}" /><img title="查看" src="images/view.gif" class="hand view"/></a>\
    	<img title="基本设置" src="images/edit.gif" alt="基本设置" class="hand edit" />\
    	<img title="筛选器" src="images/edit.gif" alt="筛选器" class="hand filter" />\
    	<img title="更新" src="images/refresh.gif" alt="更新" class="hand update" />\
    	<img title="停止自动更新" src="images/lock.gif" alt="停止自动更新" class="hand stop" />\
    	<img title="删除" src="images/delete.gif" alt="删除" class="hand delete" />\
    </td>\
</tr>';

var tableApp = new ct.table('#item_list', {
    rowIdPrefix : 'tr_',
    rightMenuId : 'right_menu',
    pageVar : 'page',
	pageSize : 15,
    dblclickHandler : 'flist.add',
    rowCallback     : 'init_event',
    template : row_template,
	jsonLoaded : 'json_loaded',
    baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page'
});

$(function (){
	tableApp.load();
	$('#add').click(flist.add);
});

// 显示总条数
function json_loaded(json) {
	$('#pagetotal').html(json.total);
}

//init function
function init_event(id, tr) {
	tr.find('a.edit, img.edit').click(function (){flist.add(id);});
	tr.find('img.delete').click(function (){flist.del(id);});
	tr.find('img.filter').click(function(){flist.fadd(id);});
	tr.find('img.update').click(function(){flist.update(id);});
	tr.find('img.stop').click(function(){flist.stop(id);});
}
// 分组管理下拉列表
$('#modelset').modelset().bind('changed',function(e, t){
	tableApp.load('gid='+t.checked);
});

</script>
<?php $this->display('footer','system');?>