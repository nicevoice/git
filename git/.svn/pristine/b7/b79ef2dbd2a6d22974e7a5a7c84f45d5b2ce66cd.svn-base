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
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/list/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.list.js"></script>
<div class="bk_8"></div>
<div> <input type="button" value="添加" class="button_style_4 f_l" onclick="window.open('?app=<?=$app?>&controller=<?=$controller?>&action=add', 'newtab');return false;"></div>

    <div class="bk_8"></div>
<form method="post" action="?app=<?=$app?>&controller=<?=$controller?>&action=<?=$action?>">
    <table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8">
        <thead>
        <tr>
            <th width="20" align="left">
                <input type="checkbox" id="check_box" value="">
            </th>
            <th style="width: 50px;">序号</th>
            <th>标题</th>
            <th>日期</th>
            <th>友盟推送</th>
            <th width="150" align="left">操作</th>
        </tr>
        </thead>
        <tbody id="item_list"></tbody>
    </table>

    <div class="table_foot">
        <div id="pagination" class="pagination f_r"></div>
        <div class="f_r"> 共有<span id="pagetotal">0</span>条记录&nbsp;&nbsp;&nbsp;每页
            <input type="text" name="pagesize" size=3 id="pagesize" value=""/> 条&nbsp;&nbsp;</div>

    </div>

</form>
<script type="text/javascript" src="apps/exam/js/automatic_exam.js"></script>

<script type="text/javascript">
var row_template  ='<tr id="row_{messageid}" right_menu_id="right_menu_{messageid}" val="{messageid}">';
row_template +='	<td><input type="checkbox" class="checkbox_style" id="chk_row_{messageid}" value="{messageid}" /></td>';
row_template +='	<td >{messageid}</td>';
row_template +='	<td class="t_c">{subject}</td>';
row_template +='	<td class="t_c">{created}</td>';
row_template +='	<td class="t_c">{umeng}</td>';
row_template +='<td class="t_c"><img src="images/edit.gif" alt="编辑" width="16" height="16" class="manage common_edit"/> &nbsp;<img src="images/delete.gif" alt="删除" width="16" height="16" class="manage del_row");"/></tr>';

var tableApp = new ct.table('#item_list', {
    rowIdPrefix : 'row_',
    pageSize : 15,
    jsonLoaded : 'json_loaded',
    template : row_template,
    rowCallback     : 'init_row_event',
    baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page'
});
function json_loaded(json) {
    $('#pagetotal').html(json.total);
}
$(function() {
    tableApp.load();
    $('#pagesize').val(tableApp.getPageSize());
    $('#pagesize').blur(function(){
        var p = $(this).val();
        tableApp.setPageSize(p);
        tableApp.load();
    });

});

function init_row_event(id, tr) {
    tr.find('img.del_row').click(function(){
        del_row(id,tr);
    });
    tr.find('img.common_edit').click(function(){
        window.open('?app=<?=$app?>&controller=<?=$controller?>&action=edit&id='+id, 'newtab');
    });

}
function del_row(id) {
    var msg;
    if (id === undefined) {
        id = tableApp.checkedIds();
        if (!id.length) {
            ct.warn('请选中要删除项');
            return;
        }
        msg = '确定删除选中的<b style="color:red">'+id.length+'</b>条记录吗？';
    } else {
        msg = '确定删除编号为<b style="color:red">'+id+'</b>的记录吗？';
    }
    ct.confirm(msg,function(){
        var data = 'id='+id;
        $.getJSON('?app=<?=$app?>&controller=<?=$controller?>&action=delete',data,function(json){
            json.state
                ? (ct.ok('删除完毕'), tableApp.deleteRow(id), /*[extention] 更新缓存*/setCache())
                : ct.error(json.error);
        });
    });
}

function send_umeng(id,mess)
{
	if(mess)
	{
		msg = '已经推送过友盟,是否再推送一次？['+mess+']';
		ct.confirm(msg,function(){
			var data = 'id='+id;
			$.getJSON('?app=<?=$app?>&controller=<?=$controller?>&action=sendumeng',data,function(json){
				json.state
					? (ct.ok('发送完成:'+json.info), '','')
					: ct.error(json.error);
			});
		});
		return;
	}
	var data = 'id='+id;
	$.getJSON('?app=<?=$app?>&controller=<?=$controller?>&action=sendumeng',data,function(json){
		json.state
			? (ct.ok('发送完成:'+json.info), '','')
			: ct.error(json.error);
	});
}

var interval = setInterval(function(){tableApp.load();}, 180000);
window.onunload = function ()
{
	clearInterval(interval);
}
</script>

<?php $this->display('footer', 'system');