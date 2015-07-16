<?php $this->display('header', 'system');?>
<!--tablesorter-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<!--contextmenu-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<!--pagination-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.pagination.js"></script>

<script type="text/javascript" src="apps/system/js/app.js"></script>
<div class="bk_10"></div>
<div class="tag_1 mar_l_8">
	<ul class="tag_list">
		<li><a href="?app=system&controller=app&action=index">已安装</a></li>
		<li><a class="s_3" href="?app=system&controller=app&action=noneInstall">未安装</a></li>
	</ul>
</div>
<div class="bk_8"></div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list" style="margin-left:6px;">
  <thead>
    <tr>
    	<th class="bdr_3">英文名</th>
    	<th>应用</th>
		<th width="15%">作者</th>
		<th width="100">管理操作</th>
    </tr>
  </thead>
  <tbody id="list_body">
  </tbody>
</table>
<!--右键菜单-->
<script type="text/javascript">
var row_template = '<tr id="row_{app}">\
                	<td class="t_l"><a class="app" href="javascript:;" tips="{tips}">{app}</a></td>\
                	<td class="t_l">{name}</td>\
                	<td class="t_c"><a class="author" href="{author_url}" target="_blank"/>{author}</a></td>\
                	<td class="t_c">\
	                	<img src="images/install.gif" title="安装" class="hand install"/>\
	                	<img src="images/delete.gif" title="彻底删除App" class="delete hand"/>\
                	</td>\
                </tr>';

var tableApp = new ct.table('#item_list', {
    rowIdPrefix : 'row_',
    rightMenuId : 'right_menu',
    pageField : 'page',
    pageSize : '<?=$size?>',
    dblclickHandler : 'app.save',
    rowCallback     : 'init_row_event',
    template : row_template,
    baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page&type=none'
});
$(function (){
	tableApp.load();
});

function init_row_event(id, tr)
{
	tr.find('.delete').click(delApp);
	tr.find('.install').click(install);
	tr.find('a.app[tips]').attrTips('tips');
	tr.find('a.author').each(function (i, e){
		if(e.href == 'javascript:;') {
			e.target = '_self';
		}
	});
}

function install()
{
	if(!this.tagName)
	{
		var appname = ($('#list_body>tr.row_chked>td>input').val());
	}else{
		var appname = $(this).parents('tr').find('a.app').text();
	}
	$.getJSON('?app=<?=$app?>&controller=<?=$controller?>&action=install&appname='+appname, function (data){
		if(data.state) {
			ct.ok('恭喜你，' + appname + '模块安装成功');
			tableApp.load();
		}else{
			ct.warn(data.error);
		}
	});
}

function delApp()
{
	var appname = $(this).parents('tr').find('a.app').text();
	ct.confirm("确定要删除"+appname+"模块吗？此操作不可恢复。", function (){
		$.getJSON('?app=<?=$app?>&controller=<?=$controller?>&action=delete&appname='+appname, function (data){
			if(data.state) {
				tableApp.load();
				ct.ok(appname + '模块已经删除');
			}else{
				ct.warn(data.error);
			}
		});
	});
}
</script>
<style>
#cmstopAttrHiddenDivtips_orange label{
	float: left;
	width: 60px;
}
</style>
<?php $this->display('footer', 'system');