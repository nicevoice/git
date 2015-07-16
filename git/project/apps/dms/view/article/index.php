<?php $this->display('header');?>
<script type="text/javascript" src="apps/dms/js/search.js"></script>
<link href="apps/dms/css/style.css" rel="stylesheet" type="text/css" />
<?php $this->display('sider');?>
<div class="dms_search">
	<ul>
		<li>
			<div class="dms-search-input-wrap">
                <div class="dms-search-input-text">
                    <input type="text" name="keyword" value="" placeholder="模糊查询" size="30"/>
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
			<input type="button" name="search" class="button_style_4 f_l" onclick="article.search(); return false;" value="查询" />
		</li>
	</ul>
</div>
<div class="dms_content">
	<div class="dms_inner">
        <div class="bk_10"></div>
		<table width="99%" cellpadding="0" cellspacing="0" id="article_list" class="tablesorter table_list" style="margin-left:6px;">
			<thead>
				<tr>
					<th class="bdr_3 title" width="">标题</th>
					<th class="author" width="80">作者</th>
                    <th class="source" width="80">来源</th>
					<th class="createtime" width="140">创建时间</th>
					<th class="controller" width="50">操作</th>
				</tr>
			</thead>
			<tbody id="list_body">
			</tbody>
		</table>
		<div style="clear:both"></div>
		<div class="statusbar">
			<input type="button" class="button_style_1" name="refresh" value="刷新" onclick="article.reload();" />
			<div id="pagination" class="pagination"></div>
		</div>
	</div>
</div>
<script type="text/javascript">
(function($) {
var templateRow	= '<tr id="row_{articleid}">\
    <td class="article-title"><a onclick="ct.assoc.open(\'?app=dms&controller=article&action=edit&id={articleid}\', \'target\'); return false;" href="#" title="{title}">{shorttitle}</a></td>\
    <td class="t_l author">{author}</td>\
    <td class="t_l source">{source}</td>\
    <td class="t_c createtime">{createtime}</td>\
    <td class="t_l controller"><a class="article-edit" style="display: block;width:16px; height:16px; background: url(apps/dms/css/image/pic-action.png) no-repeat -32px 0; margin-left:6px; float: left;" onclick="ct.assoc.open(\'?app=dms&controller=article&action=edit&id={articleid}\', \'target\'); return false;" href="#" title="修改"></a><a class="article-delete" style="display: block;width:16px; height:16px; background: url(apps/dms/css/image/pic-action.png) no-repeat -60px 0; margin-left:6px; float:left;" href="javascript:void(0);" title="删除"></a></td>\
</tr>';
var init_row_event = function(id, tr) {
    tr.find('.article-delete').click(function() {
		ct.confirm('确定要删除么', function() {
			article.remove(id);
		});
        return false;
    });
	tr.find('a.title_list').attrTips('tips');
};
var tableApp = new ct.table('#article_list', {
    rowIdPrefix : 'row_',
    pageField : 'page',
    pageSize : 15,
    template : templateRow,
    rowCallback : init_row_event,
    baseUrl  : '?app=dms&controller=article&action=page'
});

var article = {
    remove: function(id) {
        $.getJSON('?app=dms&controller=article&action=del&id='+id, null, function(json) {
            if (json.state) {
                ct.ok('删除成功');
                tableApp.reload();
            } else {
                ct.error('删除失败');
            }
        });
    },
    search: function() {
        tableApp.load($('.dms_search :input').serialize());
    },
    reload: function() {
        tableApp.reload();
    },
	quoteInfo: function(id) {
		ct.ajaxDialog({'title':'引用追踪', 'width':400},'?app=dms&controller=article&action=quote&id='+id, undefined,function() {
			return true;
		});
	}
};

var search = new ct.dms.search(article.search);
$(function() {
    tableApp.load();
    search.init();

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
            article.search();
            return false;
        });
    });
});
window.article = article;
var tagSearch = "<?php echo $_GET['tag']?>";
if (tagSearch != "") {
	tableApp.load('keyword='+tagSearch+'&type=tags');
}
})(jQuery);
</script>
<?php $this->display('footer');