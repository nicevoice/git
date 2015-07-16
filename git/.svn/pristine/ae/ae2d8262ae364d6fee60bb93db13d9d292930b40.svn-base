<?php $this->display('header', 'system');?>
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
<form method="post" action="?app=<?=$app?>&controller=<?=$controller?>&action=<?=$action?>">
    <table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8">
        <thead>
        <tr>
            <th style="width: 50px;">用户名</th>
            <th onclick="tableApp.load('order=gold desc')">金币数</th>
            <th onclick="tableApp.load('order=level desc')">等级</th>
            <th onclick="tableApp.load('order=exper desc')">经验</th>
            <th onclick="tableApp.load('order=logtime desc')">注册时间</th>
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
<script type="text/javascript">
var row_template  ='<tr id="row_{menuid}" right_menu_id="right_menu_{menuid}" val="{menuid}">';
row_template +='	<td >{nickname}</td>';
row_template +='	<td class="t_c">{gold}</td>';
row_template +='	<td class="t_c">{level}</td>';
row_template +='	<td class="t_c">{exper}</td>';
row_template +='	<td class="t_c">{logtime}</tr>';

var tableApp = new ct.table('#item_list', {
    rowIdPrefix : 'row_',
    pageSize : 15,
    jsonLoaded : 'json_loaded',
    template : row_template,
    baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=member_page'
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
function add_dialog(id) {
    var id = id ? id  : 0;
    ct.form('添加菜单','?app=exam&controller=wechat&action=menu_add&menuid='+id,500,300,function(json){
        if (id > 0) {
            tableApp.updateRow(id, json.data);
        } else {
            tableApp.addRow(json.data);
        }

        return true;
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
        $.getJSON('?app=<?=$app?>&controller=<?=$controller?>&action=menu_delete',data,function(json){
            json.state
                ? (ct.ok('删除完毕'), tableApp.deleteRow(id), /*[extention] 更新缓存*/setCache())
                : ct.error(json.error);
        });
    });
}
var interval = setInterval(function(){tableApp.load();}, 180000);
window.onunload = function ()
{
	clearInterval(interval);
}
</script>

<?php $this->display('footer','system');?>