<?php $this->display('header', 'system');?>
<link rel="stylesheet" type="text/css" href="<?= IMG_URL?>js/lib/pagination/style.css"/>
<link rel="stylesheet" type="text/css" href="<?= IMG_URL?>js/lib/contextMenu/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>apps/comment/js/comment.table.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.pagination.js"></script>
<script type="text/javascript" src="apps/comment/js/comment.js"></script>
<!--2011/04/18 排序方式下拉列表 start-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/list/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.list.js"></script>
<!--End-->

<!-- 时间选择器 -->
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.datepicker.js"></script>
<link href="<?=IMG_URL?>js/lib/datepicker/style.css" rel="stylesheet" type="text/css" />

<div class="bk_10"></div>
<div class="tag_1 mar_l_8">
	<ul class="tag_list">
		<li>
			<a href="?app=comment&controller=comment&action=index&status=2" <?= $status == 2 ? 'class="s_3"' : ''?> >已审</a>
		</li>
		<li>
			<a href="?app=comment&controller=comment&action=index&status=1" <?= $status == 1 ? 'class="s_3"' : ''?> >待审</a>
		</li>
	</ul>
</div>
<table class="table_form mar_5 mar_l_8" cellpadding="0" cellspacing="0" width="760px">
	<tr>
		<th width="11%">快捷通道</th>
		<td width="89%">
			<input name="rwkeyword" id="rwkeyword" type="text" size="20" value="<?=empty($_GET['rwkeyword'])?'请输入话题URL或者话题ID':$_GET['rwkeyword']?>" onfocus="this.value == '请输入话题URL或者话题ID' && (this.value = '')" onblur=" this.value || (this.value = '请输入话题URL或者话题ID')" style="width:300px"/>
			<input type="button" id="rw" value="列出" class="button_style_1"/>
		</td>
	</tr>
	<tr>
		<th>排序方式</th>
		<td>
			<select id="modelset"style="width:100px;float:left;margin-right:5px;">
			<?php foreach($orderlist as $oid => $oder):?>
				<option value="<?=$oid?>" <?= $_GET['oid'] == $oid ? 'selected' : ''?>><?=$oder?></option>
			<?php endforeach;?>
			</select>
		</td>
	</tr>
	<tr>
		<th>检索</th>
		<td>
		<form  name="search_f" id="search_f" action="?app=comment&controller=comment&action=page" method="GET" onsubmit="tableApp.load($('#search_f'));return false;">
			<input type="text" name="published" id="published" class="input_calendar" value="" size="20"/>
				至 
			<input type="text" name="unpublished" id="unpublished" class="input_calendar" value="" size="20"/>
			<select name="type" id="type">
			  <option value="1">关键词</option>
			  <option value="2">IP</option>
			  <option value="3">用户名</option>
			</select>
			<input name="keywords" id="keywords" type="text" size="20" value="<?= $keyword;?>"/> 
			<input type="submit" value="搜索" class="button_style_1"/>
			<input type="button" value="删除" onclick="comment.searchDel();" class="button_style_1"/>
		</form>
		</td>
	</tr>
</table>
<div class="bk_10"></div>
<div class="tag_list_1 pad_8 layout mar_l_8" id="bytime_list">
	<a href="javascript:tableApp.load('published=');" id="all" class="s_5">全部</a>
	<a href="javascript: tableApp.load('published=<?= date('Y-m-d', TIME)?>');">今天</a>
	<a href="javascript: tableApp.load('published=<?= date('Y-m-d', strtotime('yesterday'))?>&unpublished=<?= date('Y-m-d', strtotime('yesterday'))?>');">昨天</a>
	<a href="javascript: tableApp.load('published=<?= date('Y-m-d', strtotime('last monday'))?>');">本周</a>
	<a href="javascript: tableApp.load('published=<?= date('Y-m-d', strtotime('last month'))?>');">本月</a>
	<div class="clear"></div>
</div>

<div id="content" class="mar_l_8"></div>
<div class="bk_10"></div>
<div class="clear"></div>
<div class="table_foot mar_l_8" style="width:80%">
	<span class="f_l">
		<input type="checkbox" id="selectAll"/>&nbsp;&nbsp;
		<?php if($status) {?><input type="button" id="check" onclick="comment.multiCheck();" value="通过" class="button_style_1"/><?php } ?>
		<input type="button" id="delete"  onclick="comment.multiDel();"  value="删除" class="button_style_1"/>
	</span>
	<span id="pagination" class="pagination f_r"></span>
	<span class="f_r"><label id="status"></label>共有<span id="pagetotal">0</span>条记录&nbsp;&nbsp;&nbsp;每页<input id="pagesize" size="2">条</span>
</div>
<ul id="ip_menu" class="contextMenu">
	<li class="delete"><a href="#comment.ipDeleteAll">删除所有</a></li>
	<li class="new"><a href="#comment.ipDisallow">锁定ip</a></li>
	<li class="edit"><a href="#comment.ipEdit">修改ip</a></li>
</ul>
<script type="text/javascript">
var ipTime = <?= $SETTING['iptime'];?>;
var row_template = '\
<div class="comment_list mar_5" id="div_{commentid}">\
<ul class="f_l">\
	<li>\
		<span class="f_r">\
			<a href="javascript:void(0);" class="u" onclick="comment.url({createdby})">{username}</a> \
			<a href="javascript:void(0);" class="u ip"  value= "{commentid}" id="ip_{commentid}" title="点击弹出菜单">{ip}</a>\
			<span class="c_gray">({location})</span> <span class="c_gray"> 顶 ({supports})</span>\
			<span class="date">{created}</span>\
		</span>\
		<input type="checkbox" name="commentid[{commentid}]" id="checkbox3" class="mar_3 checkbox_style" value="{commentid}"/>\
		<span>{warn}</span>\
		<a href="{url}" target="_blank"class="c_blue b">{title}</a>\
		<a href="javascript:void(0);" onclick="comment\.get_by_contentid({topicid})" title="全部评论">\
		<img src="images/dialog.gif" style="vertical-align:bottom"></a>\
	</li>\
	<li>\
		<div ondblclick="comment.setTextArea(this,{commentid})" id="content{commentid}" style="line-height:20px;padding:3px;overflow:hidden;">{content}</div> \
	</li>\
</ul>\
<ol class="f_l"  style="margin-left:10px">\
	<?php if($status == 2) {?>\
		<li style="margin-top:-15px;">\
			<a href="javascript:comment.top({commentid})" class="check" value="{commentid}">\
				<img src="images/up.gif" title="文章页置顶" height="16" width="16" class="hand" name="文章页置顶"/>\
			</a>\
			<a href="javascript:comment.canceltop({commentid})" class="check" value="{commentid}">\
				<img src="images/down.gif" title="取消置顶" height="16" width="16" class="hand" name="取消置顶"/>\
			</a>\
		</li>\
	<?php } elseif($status == 1) {?>\
		<li>\
			<a href="javascript:comment.check({commentid})" class="check" value="{commentid}">\
				<img src="images/sh.gif" title="通过审核" height="16" width="16" class="hand" name="通过审核"/>\
			</a>\
		</li>\
	<?php } ?>\
		<li>\
		<a href="javascript:comment.del({commentid})" class="del" value="{commentid}">\
			<img src="images/del.gif" title="删除" height="16" width="16" class="hand" name="删除"/>\
		</a>\
		</li>\
		<li>\
			<a href="javascript:comment.edit({commentid})" class="edit">\
				<img src="images/edit.gif" title="双击修改" height="16" width="16" class="hand" name="修改"/>\
			</a>\
		</li>\
</ol>\
<div class="clear"></div>\
</div>';

var tableApp = new ct.table('#content', {
	rowIdPrefix : 'div_',
	pageSize : <?= $pagesize?>,
	rowCallback: 'init_row_event',
	jsonLoaded : 'json_loaded',
	template : row_template,
	baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page&status=<?=$status?>'
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
	tableApp.load(<?php if(!empty($_GET['rwkeyword'])):?>'rwkeyword=<?=$_GET['rwkeyword']?>'<?php endif;?>);
	$('#pagesize').val(tableApp.getPageSize());
	$('#pagesize').blur(function(){
		var p = $(this).val();
		tableApp.setPageSize(p);
		tableApp.load();
	});

	$('input.input_calendar').DatePicker({'format':'yyyy-MM-dd HH:mm:ss'});

	$('#rw').click(function(){
		var value = $('#rwkeyword').val();
		tableApp.load('rwkeyword='+value);
	});
	$('#bytime_list > a').click(function(){
		$('#bytime_list > a.s_5').removeClass('s_5');
		$(this).addClass('s_5');
	}).focus(function(){
		this.blur();
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

// 排序方式下拉列表
$('#modelset').modelset().bind('changed',function(e, t){
	tableApp.load('oid='+t.checked);
});
</script>
<?php $this->display('footer', 'system');?>