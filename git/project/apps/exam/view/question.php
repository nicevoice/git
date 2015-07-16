<?php $this->display('header');?>
<style>
/** 分页样式 **/
.pages {
    height: 24px;
    zoom: 1;
    text-align: right;
    padding: 10px ;

}
.pages a {
    display: inline-block;
    padding: 0 9px;
    margin: 0 2px;
    height: 22px;
    line-height: 22px;
    overflow: hidden;
    vertical-align: middle;
    text-decoration: none;
    background: white;
    border: 1px solid #E3E3E3;
    text-align: center;
    -moz-border-radius: 2px;
    -webkit-border-radius: 2px;
    color: #666666;
}
.pages a.cur{
    display: inline-block;
    padding: 0 9px;
    height: 22px;
    line-height: 22px;
    overflow: hidden;
    vertical-align: middle;
    text-decoration: none;
    -moz-border-radius: 2px;
    -webkit-border-radius: 2px;
    background: #f35c03;
    border: 1px solid #f35c03;
    color: #ffffff;
    font-size: 14px;
    font-weight: 700;
}
.pages .current{
    display: inline-block;
    padding: 0 9px;
    height: 22px;
    line-height: 22px;
    overflow: hidden;
    vertical-align: middle;
    text-decoration: none;
    -moz-border-radius: 2px;
    -webkit-border-radius: 2px;
    background: #f35c03;
    border: 1px solid #f35c03;
    color: #ffffff;
    font-size: 14px;
    font-weight: 700;
    margin:1px;
}
.pages .next, .pages .prev{
    background: none;
    border:  1px solid #E3E3E3;
    color: #666666;
    font-size: 12px;
}

</style>
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
<div class="bk_8"></div>
<div class="tag_1">
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
	<form id="question_sort" name="question_sort" method="POST" action="?app=exam&controller=question&action=sort">
		<input name="contentid" type="hidden" value="<?=$contentid?>" />
		<div id="questions">
<?php foreach ($questions as $k=>$r){?>
		<?php $this->assign('n', $k+1); $this->assign($r); $this->display('question/'.$r['type'].'/form');?>
<?php }?>
		</div>
		<div style="padding:10px;"><input name="sort" type="submit" value="保存并发布" class="button_style_1" style="display:none;"></div>
	</form>
    <div class="pages"><span style="margin-right: 10px;">总共： <font style="color: red; font-size: 16px;"><?=$count?></font> 条记录 </span><?=$pages?></div>
</div>
<script type="text/javascript">
$("#question_sort").ajaxForm('question.sort_submit');
question.init();

$(function(){
    $('#submit').click(function(){

        $('#search_f').submit();
    })

})

</script>

<?php $this->display('footer', 'system');?>