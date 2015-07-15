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
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>css/exam/login.css"/>
<div class="bk_8"></div>
<form method="post" action="?app=<?=$app?>&controller=<?=$controller?>&action=<?=$action?>">
    <table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8">
        <thead>
        <tr>
            <th width="200">用户昵称</th>
            <th>正确率</th>
            <th>提交时间</th>
            <th>答题时间</th>
            <th width="150" align="left">查看报告</th>
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
row_template +='	<td class="t_c" ><a href="<?=BBS_URL?>space-uid-{createdby}.html" target="_blank"> {createdbyname}</a></td>';
row_template +='	<td class="t_c">{correct}%</td>';
row_template +='	<td class="t_c">{created}</td>';
row_template +='	<td class="t_c">{examtime}/{exam_time}分钟</td>';
row_template +='<td class="t_c"><a href="javascript:void(0);" onclick="report({answerid})">查看</a>&nbsp;&nbsp;&nbsp;</td></tr>';

var tableApp = new ct.table('#item_list', {
    rowIdPrefix : 'row_',
    pageSize : 15,
    jsonLoaded : 'json_loaded',
    template : row_template,
    baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=answer_page&contentid=<?=$contentid?>'
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

var interval = setInterval(function(){tableApp.load();}, 180000);
window.onunload = function ()
{
	clearInterval(interval);
}

function report(answerid)
{
    $.ajax({
        url: "?app=<?=$app?>&controller=<?=$controller?>&action=answer_report&answerid="+answerid,
        cache: false,
        success: function(html){
            $("#popup_login .layerBox").html(html);
            $("#popup_login").removeClass('fn-dn');
        }
    });
}
</script>
<div id="popup_login" class="fn-dn">
    <div class="popup_cover"
         style="display:block;background:#000;position:fixed;_position:absolute;z-index:1000;top:0;_top:expression(eval(document.documentElement.scrollTop));left:0;height:1000px;width:100%;opacity:0.3;filter:alpha(opacity=30);"></div>
    <div class="popup_bg_contest zfLayer"
         style="position: fixed; z-index: 1001; left: 50%; top: 50%;margin-top: -191px;margin-left: -328px;">
        <div class="zf_c">
            <div class="layerBox">

            </div>
        </div>
    </div>
</div>
<?php $this->display('footer', 'system');