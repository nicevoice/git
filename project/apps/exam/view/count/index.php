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
<div class="tag_1">
    <form method="GET" name="search_f" id="search_f" action="">
        <input type="hidden" name="action" value="<?=$action?>">
        <input type="hidden" name="app" value="<?=$app?>">
        <input type="hidden" name="controller" value="<?=$controller?>">
        <div class="f_l"><span>科目：<?=property_once('subjectid','subjectid',$pro_ids['subjectid'],$subjectid)?></span>
        </div>
        <div class="search_icon search f_l" >
            <input type="text" name="keywords" id="keywords" value="<?=$keywords?>" size="15">
            <a id="submit" href="javascript:;">搜索</a>
        </div>
    </form>
</div>

<form method="post" action="?app=<?=$app?>&controller=<?=$controller?>&action=<?=$action?>">
    <table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8">
        <thead>
        <tr>
            <th width="20" align="left">
                <input type="checkbox" id="check_box" value="">
            </th>
            <th style="width: 50px;">序号</th>
            <th>标题</th>
            <th>栏目</th>
            <th>参与人数</th>
            <th>日期</th>
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
var row_template  ='<tr id="row_{contentid}" right_menu_id="right_menu_{contentid}" val="{contentid}">';
row_template +='	<td><input type="checkbox" class="checkbox_style" id="chk_row_{contentid}" value="{contentid}" /></td>';
row_template +='	<td >{contentid}</td>';
row_template +='	<td class="t_c">{title}</td>';
row_template +='	<td class="t_c">{subject}</td>';
row_template +='	<td class="t_c">{count}</td>';
row_template +='	<td class="t_c">{created}</td>';
row_template +='<td class="t_c"><a href="?app=<?=$app?>&controller=<?=$controller?>&action=exam_count&contentid={contentid}">统计</a>&nbsp;&nbsp;&nbsp;</td></tr>';

var tableApp = new ct.table('#item_list', {
    rowIdPrefix : 'row_',
    pageSize : 15,
    jsonLoaded : 'json_loaded',
    template : row_template,
    baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=exam_page'
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


    $('#submit').click(function(){
        var subjectid = $('#subjectid').val();
        param_keywords = encodeURIComponent($("#keywords").val());
        tableApp.load('&subjectid='+subjectid+'&keywords='+param_keywords);
    });
});

var interval = setInterval(function(){tableApp.load();}, 180000);
window.onunload = function ()
{
	clearInterval(interval);
}
</script>

<?php $this->display('footer', 'system');