
<div class="bk_8"></div>
<div class="tag_1">

    <div class="f_l"><!--<span>科目：<?/*=property_once('index_subjectid','subjectid',$pro_ids['subjectid'],$subjectid)*/?></span>-->
        <span>考点：<?=property_once('index_knowledgeid','knowledgeid',$pro_ids['knowledgeid'],$knowledgeid)?></span>
    </div>
    <div class="search_icon search">
        <input type="text" name="keywords" id="keywords" value="" size="15">
        <a id="submit" href="javascript:;">搜索</a>
    </div>
</div>
<form method="post" action="?app=<?=$app?>&controller=<?=$controller?>&action=<?=$action?>">
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8">
    <thead>
    <tr>
        <th width="20" align="left"><input type="checkbox" id="check_box" value="">
        </th>
        <th>序号</th>
        <th>标题</th>
        <th>科目</th>
        <th>题型</th>
        <th>考点</th>
        <th width="50" align="left">操作</th>
    </tr>
    </thead>
    <tbody id="item_list"></tbody>
</table>

<div class="table_foot">
    <div id="pagination" class="pagination f_r"></div>
    <div class="f_r"> 共有<span id="pagetotal">0</span>条记录&nbsp;&nbsp;&nbsp;每页
        <input type="text" name="pagesize" size=3 id="pagesize" value=""/> 条&nbsp;&nbsp;</div>
</div>
<div id="sel_id_<?=$s?>">

</div>
</form>
<script type="text/javascript">
    var param_status = '';
    var param_keywords = '';
    var manage_td     ='';
    var row_template  ='<tr id="row_{questionid}" right_menu_id="right_menu_{qtypeid}" val="{questionid}" subject="{subject}">';
    row_template +='	<td><input type="checkbox" class="checkbox_style" id="chk_row_{questionid}" value="{questionid}" /></td>';
    row_template +='	<td >{questionid}</td>';
    row_template +='	<td class="t_c">{subject}</td>';
    row_template +='	<td class="t_c">{_subject}</td>';
    row_template +='	<td class="t_c">{qtype}</td>';
    row_template +='	<td class="t_c">{knowledge}</td>';
    row_template +='<td class="t_c"><a href="javascript:void(0);" onclick="automatic.selexam({questionid}, <?=$s?>)">选择</a></td></tr>';

    var tableApp = new ct.table('#item_list', {
        rowIdPrefix : 'row_',
        pageSize : 10,
        jsonLoaded : 'json_loaded',
        template : row_template,
        baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=pages&exam=1&qtypeid=<?=$qtypeid?>&subjectid=<?=$subjectid?>'
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
            //var  index_subjectid = $('#index_subjectid').val();
            var  index_qtypeid = $('#index_qtypeid').val();
            var  index_knowledgeid = $('#index_knowledgeid').val();
            param_keywords = encodeURIComponent($("#keywords").val());
            tableApp.load('knowledgeid='+index_knowledgeid+'&keywords='+param_keywords);
        });
    });

</script>