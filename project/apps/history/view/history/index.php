<?php $this->display('header');?>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="apps/history/js/history.js"></script>
<div class="suggest mar_t_10 mar_l_8" style="width: 98%;">
  <h2>友情提示</h2>
  <p>
  	像网易新闻一样，历史页面可以每天保存重要页面（如首页），在前台用户可以通用一个日历控件查看此页面的旧版本。<br/>
  	使用说明：<br/>
  	1.添加任务：让计划任务功能定时的保存页面。<br/>
  	2.添加日历：点“管理”栏最右边的按钮，复制其中一种调用代码到模板的任意位置
  </p>
</div>
<div class="bk_8"></div>
<div class="table_head">
	<input type="button" id="add" value="新建任务" class="button_style_4 f_l"/>
</div>
<div class="bk_8"></div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list" style="margin-left:6px;">
  <thead>
    <tr>
      <th width="25" class="t_l bdr_3"><input type="checkbox" id="check_all" /></th>
      <th>任务名称</th>
      <th width="12%">运行时段</th>
      <th width="12%">状态</th>
      <th width="12%">最后编辑</th>
      <th width="12%">管理</th>
    </tr>
  </thead>
  <tbody id="list_body">
  </tbody>
</table>
<div class="table_foot">
	<p class="f_l">
		<input type="button" onclick="histy.del()" value="删 除" class="button_style_1"/>
	</p>
</div>
<!--右键菜单-->
<ul id="right_menu" class="contextMenu">
   <li class="edit"><a href="#histy.save">编辑</a></li>
   <li class="del"><a href="#histy.del">删除</a></li>
</ul>

<script type="text/javascript">
var app = '<?=$app?>';
var controller = '<?=$controller?>';
var row_template = '\
<tr id="tr_{hid}">\
	<td><input type="checkbox" class="checkbox_style" name="chk_row" id="chk_row_{hid}" value="{hid}" /></td>\
	<td class="t_l"><a href="javascript:;" class="edit"> {name}</a> </td>\
    <td class="t_l"> {hour} </td>\
    <td class="t_c"> {disabledStr} </td>\
    <td class="t_c"> <a href="javascript: url.member({userid});">{username}</a> </td>\
    <td class="t_c">\
    	<img src="images/edit.gif" title="编辑" class="hand edit"/>\
    	<img title="删除" src="images/delete.gif" alt="删除" class="hand delete" />\
    	<img title="调用代码" src="images/dialog.gif" alt="调用代码" class="hand code" alias="{alias}" />\
    </td>\
</tr>';

var tableApp = new ct.table('#item_list', {
    rowIdPrefix : 'tr_',
    rightMenuId : 'right_menu',
    pageField : 'page',
    dblclickHandler : 'histy.save',
    rowCallback     : 'init_event',
    template : row_template,
    baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page'
});

$(function (){
	tableApp.load();
	$('#add').click(histy.save);
	$('#year').width(86);
});
//init function
function init_event(id, tr)
{
	tr.find('a.edit, img.edit').click(function (){
		histy.save(id);
	});
	tr.find('img.code').click(histy.code);
	tr.find('img.delete').click(function (){
		histy.del(id);
	});
}
</script>
<?php $this->display('footer','system');?>