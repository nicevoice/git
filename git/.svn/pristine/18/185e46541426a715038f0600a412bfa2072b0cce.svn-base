<?php $this->display('header', 'system');?>
<!--tablesorter-->
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>

<script type="text/javascript" src="apps/system/js/app.js"></script>
<div class="bk_10"></div>
<div class="tag_1 mar_l_8">
	<ul class="tag_list">
		<li><a class="s_3" href="?app=system&controller=app&action=index">已安装</a></li>
		<li><a href="?app=system&controller=app&action=noneInstall">未安装</a></li>
	</ul>
</div>
<div class="bk_8"></div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list" style="margin-left:6px;">
  <thead>
    <tr>
    	<th class="bdr_3">英文名</th>
    	<th>应用</th>
		<th width="15%">作者</th>
		<th width="7%">状态</th>
		<th width="100">管理操作</th>
    </tr>
  </thead>
  <tbody id="list_body">
  </tbody>
</table>
<script type="text/javascript">
var row_template = '<tr id="row_{app}">\
                	<td class="t_l app" tips="{tips}"> <a class="app" href="javascript:;">{app}</a> </td>\
                	<td class="t_l">{name}</td>\
                	<td class="t_c"><a class="author" href="{author_url}" target="_blank"/>{author}</a></td>\
                	<td class="t_c"> <a class="disabled" href="javascript:;">{disabled}</a></td>\
                	<td class="t_c">\
                		<img href="{setting}" title="设置" class="hand setting" src="images/setting.gif"/>\
	                	<img src="images/uninstall.gif" title="卸载" config="{config}" class="uninstall hand"/>\
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
    baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page'
});

$(function (){
	tableApp.load();
});

function init_row_event(id, tr)
{
	tr.find('img.setting').each(function (i, e){
		if($(e).attr('href') != 'javascript:;')
		{
			$(e).click(function (){
				ct.assoc.open($(this).attr('href'), 'newtab');
			});	
		}else{
			$(e).attr('src', 'images/setting_gray.gif').removeClass('hand');
		}
	});
	tr.find('img.uninstall').each(function (i, e){
		if($(e).attr('config'))
		{
			tr.find('.uninstall').click(uninstall);
		}else{
			$(e).attr('src', 'images/uninstall_gray.gif').removeClass('hand');
		}
	});
	
	tr.find('a.disabled').each(function (i, e){
		if(e.innerHTML == '启用') {
			e.title = '点击禁用';
		}else{
			e.title = '点击启用';
			e.style.color = '#d00';
		}
	}).click(function (){
		var state = this.innerHTML == '启用' ? 1 : 0;
		var e = this;
		$.get('?app=system&controller=app&action=state&appname='+id+'&state=' + state, function (data){
			if(data == 1) {
				e.style.color = state ? '#d00' : '#077AC7';
				e.title = state ? '点击启用' : '点击禁用';
				e.innerHTML = state ? '禁用' : '启用';
				ct.ok('状态修改成功');
			}else{
				ct.warn('状态修改出错，请联系cmstop');
			}
		});
	});
	tr.find('td.app[tips]').attrTips('tips');
	tr.find('a.author').each(function (i, e){
		if(e.href == 'javascript:;') {
			e.target = '_self';
		}
	});
}

function uninstall()
{
	if(!this.tagName)
	{
		var appname = ($('#list_body>tr.row_chked>td>input').val());
	}else{
		var appname = $(this).parents('tr').find('a.app').text();
	}
	ct.confirm("确定要卸载"+appname+"模块吗？卸载后可以再安装，但是数据会丢失。", function (){
		$.getJSON('?app=<?=$app?>&controller=<?=$controller?>&action=uninstall&appname='+appname, function (data){
			if(data.state) {
				tableApp.load();
				ct.ok(appname + '模块已经卸载');
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