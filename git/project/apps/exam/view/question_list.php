<?php $this->display('header', 'system');?>
<script>
    var UPLOAD_URL = '<?php echo UPLOAD_URL?>'
</script>
<script type="text/javascript" src="uploader/cmstop.uploader.js"></script>
<script type="text/javascript" src="imageEditor/cmstop.imageEditor.js"></script>
<script type="text/javascript" src="js/cmstop.filemanager.js"></script>
<script type="text/javascript" src="apps/exam/js/question.js"></script>
<script type="text/javascript" src="apps/exam/js/option.js"></script>
<script type="text/javascript" src="tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="tiny_mce/editor.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/dropdown/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.dropdown.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.pagination.js"></script>
<div class="bk_8"></div>

<div class="tag_1 mar_t_8">
    <form method="GET" name="search_f" id="search_f" action="">
        <input type="hidden" name="action" value="<?=$action?>">
        <input type="hidden" name="app" value="<?=$app?>">
        <input type="hidden" name="controller" value="<?=$controller?>">
        <div class="f_l"><span>科目：<?=property_once('index_subjectid','subjectid',$pro_ids['subjectid'],$subjectid)?></span>
        <span>考点：<?=property_once('index_knowledgeid','knowledgeid',$pro_ids['knowledgeid'],$knowledgeid)?></span>
        <span>类型：<?=property_once('index_qtypeid','qtypeid',$pro_ids['qtypeid'],$qtypeid)?></span></div>
        <div class="search_icon search f_l" >

            <input type="text" name="keywords" id="keywords" value="<?=$keywords?>" size="15">
            <a id="submit" href="javascript:;">搜索</a>
        </div>
    </form>
</div>

<div class="mar_l_8 mar_t_8">
	<div class="box_10" style="width:98%">
		<h3><span class="b">表单工具</span>　点击按钮建立相应表单</h3>
		<ul id="form_type_choose" class="inline list_3 pad_10 lh_24">
			<li><a href="javascript:;" onclick="question.add('radio')">单选</a></li>
			<li><a href="javascript:;" onclick="question.add('checkbox')">多选</a></li>
			<li><a href="javascript:;" onclick="question.add('text')">单行文本</a></li>
			<li><a href="javascript:;" onclick="question.add('textarea')">多行文本</a></li>
			<li><a href="javascript:;" onclick="question.add('select')">下拉菜单</a></li>
			<li><a href="javascript:;" onclick="question.add('read')">阅读题</a></li>
		</ul>
		<div class="clear"></div>
	</div>

</div>
<div class="bk_8"></div>
<table width="99%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list" style="margin-left:6px;">
    <thead>
    <tr>
        <th width="25" class="t_l bdr_3"><input type="checkbox" id="check_all" /></th>
        <th width="3%">ID</th>
        <th width="50%">标题</th>
        <th width="10%">科目</th>
        <th width="10%">知识点</th>
        <th width="10%">题型</th>
        <th width="10%">管理操作</th>
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
        <span><input id="pagesize" value="20" size="2">条</span>
    </div>

    <div class="f_l">
        <p class="f_l">
<!--            <input type="button" onclick="field.del()" value="删 除" class="button_style_1"/>-->
        </p>
    </div>
</div>
<!--右键菜单-->
<ul id="right_menu" class="contextMenu">
    <li class="edit"><a href="#question.edit">编辑</a></li>
    <li class="del"><a href="#question.remove">删除</a></li>
</ul>

<script type="text/javascript">

    var manage_td     ='';
    var row_template  ='<tr id="row_{questionid}">';
    row_template +='	<td><input type="checkbox" class="checkbox_style" id="chk_row_{questionid}" value="{questionid}" /></td>';
    row_template +='	<td >{questionid}</td>';
    row_template +='	<td class="t_l"><a href="<?php echo WWW_URL;?>exam/question/{md5id}.html" target="_blank"> {subject}</a></td>';
    row_template +='	<td class="t_c">{_subject}</td>';
    row_template +='	<td class="t_c">{knowledge}</td>';
    row_template +='	<td class="t_c">{qtype}</td>';
    row_template +='<td class="t_c"> <img src="images/edit.gif" alt="编辑" title="编辑" width="16" height="16" class="hand" onclick="question.edit({questionid})">    <img src="images/del.gif" alt="删除" title="删除" width="16" height="16" class="hand" onclick="question.remove({questionid})"></td></tr>';

    var tableApp = new ct.table('#item_list', {
        rowIdPrefix : 'row_',
        pageSize : 15,
        jsonLoaded : 'json_loaded',
        template : row_template,
        baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=pages'
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
            //$('#search_f').submit();
            var index_subjectid = $('#index_subjectid').val();
            var index_knowledgeid = $('#index_knowledgeid').val();
            var index_qtypeid = $('#index_qtypeid').val();
            var param_keywords = encodeURIComponent($("#keywords").val());
            tableApp.load('subjectid='+index_subjectid+'&knowledgeid='+index_knowledgeid+'&qtypeid='+index_qtypeid+'&keywords='+param_keywords);
        })
    });
</script>

<?php $this->display('footer', 'system');?>