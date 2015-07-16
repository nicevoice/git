<?php $this->display('header');?>
<script src="apps/dms/js/picture_group.js"></script>
<script src="<?=IMG_URL?>js/lib/cmstop.datepicker.js"></script>

<link href="apps/dms/css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.ulist.js"></script>

<script type="text/javascript" src="apps/dms/js/search.js"></script>
<!--pagination-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.pagination.js"></script>
<!--lightbox-->
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.lightbox.js"></script>
<link rel="stylesheet" type="text/css" href="css/imagesbox.css" media="screen" />
<style type="text/css">
#jquery-lightbox {z-index:999}
</style>
<div class="dms_search">
	<ul>
		<li>
			<div class="dms-search-input-wrap">
                <div class="dms-search-input-text">
                    <input type="text" name="keyword" value="" placeholder="模糊查询" size="30" onKeyUp="showTypeChoose(event);"/>
                    <input type="hidden" name="type" value="" />
                    <span class="dms-search-input-subject-icon"></span>
                </div>
                <div class="dms-search-input-dropdown hide">
                    <a class="dms-search-input-dropdown-fulltext" role="" label="模糊查询" hideFocus="true" href="javascript:void(0);">模糊查询</a>
                    <a class="dms-search-input-dropdown-subject" role="title" label="标题" hideFocus="true" href="javascript:void(0);">按标题查询</a>
                    <a class="dms-search-input-dropdown-description" role="description" label="描述" hideFocus="true" href="javascript:void(0);">按描述查询</a>
                    <a class="dms-search-input-dropdown-tags" role="tags" label="关键词" hideFocus="true" href="javascript:void(0);">按关键词查询</a>
                    <a class="dms-search-input-dropdown-source" role="source" label="来源" hideFocus="true" href="javascript:void(0);">按来源查询</a>
                    <a class="dms-search-input-dropdown-author" role="author" label="作者" hideFocus="true" href="javascript:void(0);">按作者查询</a>
                </div>
            </div>
		</li>
		<li>
            <h2>添加时间</h2>
			<div class="dms-search-btnflow" role="datepicker">
                <input type="hidden" name="createtime" value="" />
                <a role="" class="current" hideFocus="true" href="javascript:void(0);">不限</a>
                <a role="today" hideFocus="true" href="javascript:void(0);">今天</a>
                <a role="yesterday" hideFocus="true" href="javascript:void(0);">昨天</a>
                <a role="week" hideFocus="true" href="javascript:void(0);">本周</a>
                <a role="month" hideFocus="true" href="javascript:void(0);">本月</a>
            </div>
            <div><input type="text" style="width: 171px;" role="datepicker" onclick="DatePicker(this,{'format':'yyyy-MM-dd HH:mm:ss'});" name="createtime_start" size="30" title="开始时间" placeholder="开始时间" /></div>
            ~
            <div><input type="text" style="width: 171px;" role="datepicker" onclick="DatePicker(this,{'format':'yyyy-MM-dd HH:mm:ss'});" name="createtime_end" size="30" title="结束时间" placeholder="结束时间" /></div>
		</li>
		<li>
			<input type="button" name="search" class="button_style_4 f_l" onclick="picture_group.search(); return false;" value="查询" />
		</li>
	</ul>
</div>
<?php $this->display('sider');?>
<div class="dms_content">
	<div class="dms_inner">
		<div class="bk_8"></div>
		<div id="dms_pic_group_list" class="bottom"></div>
		<div id="dms_picture_list" class="bottom" style="display:none;"></div>
		<div id="dms_pic_show" class="bottom" style="display:none;"></div>
		<div style="clear:both"></div>
		<div class="statusbar">
			<input type="button" class="button_style_1" name="refresh" value="刷新" onclick="$pg.reload();" />
			<div id="dms_statusbar" class="pagination"></div>
		</div>
	</div>
</div>
<script type="text/javascript">
var dms_keyword = $('#dms_keyword')
var dms_keyword_type_choose	= $('#keyword_type_choose');

var showTypeChoose = function(event) {
	if (event.keyCode == '13') {
		picture_group.search();
	}
	var text = dms_keyword.val();
	if (text == '') {
		dms_keyword_type_choose.hide()
	} else {
		dms_keyword_type_choose.show();
		dms_keyword_type_choose.find('strong').text(text);
	}
};

var picClick = function(obj) {
	$(obj).parent().lightBox();
};

var templatePG = '<div class="pic-list" id="groupid_{groupid}">\
		<div class="pic-list-item">\
			<a class="pic-list-thumb" href="javascript:void(0);" onclick="picture_group.view({groupid});">\
				<img src="{cover}" alt="" />\
			</a>\
			<span class="pic-list-action">\
				<span class="pic-list-action-wrap">\
					<span title="{title}" style="float:left;width:96px;text-align:center;">{shorttitle}</span>\
					<a class="pic-list-action-delete" style="float:right;" href="javascript:void(0);" title="删除" onclick="picture_group.delete({groupid});">删除</a>\
				</span>\
			</span>\
		</div>\
	</div>';
var templateP = '<div class="pic-list">\
		<div class="pic-list-item" style="overflow:hidden;">\
			<a class="pic-list-thumb" href="{url}">\
				<img src="{url}" onload="picClick(this);" />\
			</a>\
		</div>\
	</div>';

 $('.dms-search-btnflow').each(function() {
	var btnflow = $(this),
		btns = btnflow.find('a'),
		input = btnflow.find('input'),
		role = btnflow.attr('role');
	btns.click(function() {
		var btn = $(this);
		btns.removeClass('current');
		btn.addClass('current');
		if (role == 'keyword') {
			search.updateKeywords(btn.attr('role'));
		} else if (role == 'datepicker') {
			btnflow.parent().find('[role=datepicker]').val('');
			input.val(btn.attr('role'));
		}
		picture_group.search();
		return false;
	});
});

$(document).ready(function() {
	dms_keyword_type_choose.find('a').bind('click', function(obj) {
		picture_group.search();
		return false;
	});
	$('#dms_search').bind('click', function() {
		picture_group.search();
		return false;
	});

	$pg = new picture_list('div#dms_pic_group_list', {
		'baseUrl'	: '?app=dms&controller=picture_group&action=page',
		'template'	: templatePG,
		'pagesize'	: 10,
		'statusBar'	: '#dms_statusbar'
	});
	$('body').bind('click', function(obj) {
		obj.target.id == 'dms_keyword' || dms_keyword_type_choose.hide();
	});
	var viewPage = "<?php echo $_GET['id']?>";
	if (viewPage != '') {
		picture_group.view(viewPage);
	}
});

var search = new ct.dms.search(function() {
	picture_group.search();
});
search.init();
</script>
<?php $this->display('footer', 'system');?>