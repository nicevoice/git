<?php $this->display('header', 'system');?>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>apps/comment/js/comment.table.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.pagination.js"></script>
<script type="text/javascript" src="apps/comment/js/comment.js"></script>
<div class="bk_10"></div>
<div class="tag_1 mar_l_8">
	<ul class="tag_list">
		<li><a href="javascript:tableApp.load('type=count');" class="s_3" id="most">敏感评论</a></li>
	</ul>
</div>
<div class="bk_10" id="cstart"></div>
<div id="content" class="mar_l_8"></div>
<div class="clear"></div>
<div class="bk_10"></div>
<div class="table_foot mar_l_8" style="width:80%">
	<span class="f_l">
		<input type="checkbox" id="selectAll">
		<input type="button" id="delete" onclick="comment.multiDel();" value="删除" class="button_style_1">
	</span>
	<span id="pagination" class="pagination f_r"></span>
	<span class="f_r"><label id="status"></label>共有<span id="pagetotal">0</span>条记录&nbsp;&nbsp;&nbsp; 每页<input value="<?php echo $pagesize;?>" id="pagesize" size="4">条</span>
</div>
<ul id="ip_menu" class="contextMenu">
	<li class="delete"><a href="#comment.ipDeleteAll">删除所有</a></li>
	<li class="new"><a href="#comment.ipDisallow">锁定ip</a></li>
	<li class="edit"><a href="#comment.ipEdit">修改ip</a></li>
</ul> 
<script type="text/javascript">
var ipTime = <?php echo $SETTING['iptime'];?>;
var row_template = ' <div class="comment_list mar_5" id="div_{commentid}">';
	row_template +=' <ul class="f_l">';
	row_template +='	<li><span class="f_r"><a href="javascript:void(0);" class="u" onclick="comment.url({createdby})">{username}</a> <a href="javascript:void(0);" class="u ip"  value= "{commentid}"id="ip_{commentid}">{ip}</a><span class="c_gray">({location})</span><span class="date">{created}</span></span><input type="checkbox" name="commentid[{commentid}]" id="checkbox3" class="mar_3 checkbox_style" value="{commentid}"/><span>{warn}</span><a href="{url}" target="_blank" class="c_blue b">{title}</a>  <a  href="javascript:void(0);" onclick="comment\.get_by_contentid({topicid})"  title="全部评论"><img src="images/dialog.gif" style="vertical-align:bottom"></a></li>';
	row_template +='	<li><div ondblclick="comment.setTextArea(this,{commentid})" id="content{commentid}" style="line-height:20px;padding:3px;overflow:hidden;margin-right:5px">{content}</div> </li>';
	row_template +=' </ul>';
	row_template +=' <ol class="f_l"  style="margin-left:10px"><li></li>';
	row_template +='	<li><a href="javascript:comment.sensitive_reset({commentid})" class="check" value="{commentid}"><img src="images/sh.gif" title="通过" height="16" width="16" class="hand" name="通过"/></a></li><li><a href="javascript:comment.del({commentid})" class="del" value="{commentid}"><img src="images/del.gif" title="删除" height="16" width="16" class="hand" name="删除"/></a></li><li><a href="javascript:comment.edit({commentid})" class="edit"><img src="images/edit.gif" title="双击修改" height="16" width="16" class="hand" name="修改"/></a></li></ol>';
	row_template +='<div class="clear"></div>';
	row_template +='</div>';

var tableApp = new ct.table('#content', {
	rowIdPrefix : 'div_',
	pageSize : 15,
	rowCallback: 'init_row_event',
	jsonLoaded : 'json_loaded',
	template : row_template,
	baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=sensitive_page'
});

function init_row_event(id, tr) {
	tr.find('.ip').contextMenu('#ip_menu',
	function(action) {
		var callback = ct.func(action);
		callback && callback(id, tr);
	}).click(function(e){
		var t = $(this);
		setTimeout(function(){t.trigger('contextMenu',[e])},0);
	});
}
function json_loaded(json) {
	$('#pagetotal').html(json.total);
}

$(function() {
	$.validate.setConfigs({
		xmlPath:'/apps/comment/validators/'
	});
	tableApp.load();
	$('#pagesize').val(tableApp.getPageSize());
	$('#pagesize').blur(function(){
		var p = $(this).val();
		tableApp.setPageSize(p);
		tableApp.load();
	});

	$("#ip_random").click(function(){
		var rand = function(){
			$("#ip").val(Math.ceil(Math.random()*255)+'.'+Math.ceil(Math.random()*255)+'.'+(Math.ceil(Math.random()*255))+'.'+Math.ceil(Math.random()*255));}
		var si = setInterval(rand,1);
		$.get(
			'?app=comment&controller=comment&action=get_loacltion',
			'',
			function(data) {
				clearInterval(si);
				$("#comment_location").html(data.location);
				$("#ip").val(data.ip)
			},
			'json'
		);
	});
	$('.tag_list a').click(function() {
		$('.tag_list a.s_3').removeClass('s_3');
		$(this).addClass('s_3');
	});
	$('#selectAll').click(function(){
		var d = $(this);
		var checkbox = $('#content').find('input:checkbox');
		if(d.attr('checked') == true) {
			checkbox.each(function(){
				$(this).attr('checked','checked');
			});
		} else {
			checkbox.each(function(){
				$(this).attr('checked','');
			});
		}
	});
});
</script>
<?php $this->display('footer', 'system');?>