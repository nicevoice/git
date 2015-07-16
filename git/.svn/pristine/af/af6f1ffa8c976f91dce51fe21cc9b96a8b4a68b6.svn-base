<?php $this->display('header');?>
<script src="apps/dms/js/picture_group.js"></script>
<script src="<?=IMG_URL?>js/lib/cmstop.datepicker.js"></script>

<link href="apps/dms/css/style.css" rel="stylesheet" type="text/css" />
<!--pagination-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.pagination.js"></script>
<div class="dms_search">
	<ul>
        <li>
            <h2>搜索：</h2>
            <div>
                <input type="text" style="width: 171px;" name="tag" size="30" id="searchTag" />
            </div>
        </li>
		<li>
            <span class="button_style_4 f_l" id="search">确定</span>
        </li>
	</ul>
</div>
<?php $this->display('sider');?>
<div class="dms_content">
	<div class="dms_inner">
		
<div class="bk_8"></div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list" style="margin-left:6px;">
  <thead>
    <tr>
      <th width="80" class="bdr_3 ajaxer"><div name="tagid">ID</div></th>
      <th width="500">名称</th>
      <th width="150" class="ajaxer"><div name="total">统计</div></th>
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

<script type="text/javascript">
var row_template = '<tr id="row_{tagid}">\
	                 	<td class="t_c">{tagid}</td>\
	                	<td class="t_l"><a href="javascript:ct.assoc.open(\'?app=dms&controller=article&action=index&tag={name}\', \'target\');">{name}</a></td>\
	                	<td class="t_c">{total}</td>\
	                </tr>';
var tableApp = new ct.table('#item_list', {
    rowIdPrefix : 'row_',
    pageField : 'page',
    pageSize : 15,
    template : row_template,
    baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page'
});

function init_row_event(id, tr)
{
	//  
}

$(document).ready(function() {
	tableApp.load();
	$('#search').bind('click', function() {
		var text = $('#searchTag').val();
		tableApp.load('kw='+text);
	});
	$('#searchTag').bind('keydown',function(e) {
		if (e.keyCode == 13) {
			var text = $('#searchTag').val();
			tableApp.load('kw='+text);
		}
	});
});
</script>
<?php $this->display('footer');?>
	</div>
</div>