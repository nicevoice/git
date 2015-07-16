<?php $this->display('header', 'system');?>
<!--tablesorter-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<div class="bk_10"></div>
<input type="button" class="button_style_1 mar_l_8" value="添加水印方案" onclick="wm.add();" />
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_t_10 mar_l_8">
	<thead>
		<tr>
			<th width="30" class="bdr_3"></th>
			<th class="t_c">方案名称</th>
			<th width="80" class="t_c">默认方案</th>
			<th width="40" class="t_c">状态</th>
			<th width="140" class="t_c">管理</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<script type='text/javascript'>
var defaultWatermark = <? echo empty($default_watermark)?'""':$default_watermark;?>;

var row_template = new Array(
	'<tr id="row_{watermarkid}" class="t_c">',
	'<td>{watermarkid}</td>',
	'<td>{name}</td>',
	'<td class="is_default"></td>',
	'<td class="disable">{disable}</td>',
	'<td class="setdefault">',
	'<a href="javascript:;" onclick="wm.setDefault({watermarkid});">设置默认</a>&nbsp;',
	'<a href="javascript:;" onclick="wm.edit({watermarkid});"><img class="hand edit" width="16" height="16" alt="编辑" src="images/edit.gif" /></a>&nbsp;',
	'<a href="javascript:;" onclick="wm.delete({watermarkid});"><img class="hand delete" width="16" height="16" alt="删除" src="images/delete.gif" /></a>',
	'</td>',
	'</tr>'
).join('');

var wm = {
	'add' : function() {
		ct.formDialog('添加水印方案', '?app=<?=$app?>&controller=<?=$controller?>&action=add', function(json) {
			if (json.state) {
				tableApp.reload();
				return true;
			} else {
				ct.error('添加失败');
			}
		});
	},
	'edit' : function(id) {
		ct.formDialog('编辑水印方案', '?app=<?=$app?>&controller=<?=$controller?>&action=edit&id='+id, function(json) {
			if (json.state) {
				tableApp.reload();
				return true;
			} else {
				ct.error('修改失败');
			}
		}, function() {
			window.formReady();
		});
	},
	'setDefault' : function(id) {
		$.getJSON('?app=<?=$app?>&controller=<?=$controller?>&action=set_default&id='+id, {}, function(json) {
			if (json.state) {
				defaultWatermark = id;
				tableApp.reload();
				return true;
			}
			else {
				ct.error('设置失败');
			}
		});
	},
	'unsetDefault' : function() {
		$.getJSON('?app=<?=$app?>&controller=<?=$controller?>&action=unset_default', {}, function(json) {
			if (json.state) {
				defaultWatermark = '';
				tableApp.reload();
				return true;
			}
			else {
				ct.error('设置失败');
			}
		});
	},
	'disable' : function(id) {
		$.getJSON('?app=<?=$app?>&controller=<?=$controller?>&action=disable&id='+id, {}, function(json) {
			if (json.state) {
				tableApp.reload();
				return true;
			}
			else {
				ct.error('设置失败');
			}
		});
	},
	'delete' : function(id) {
		$.getJSON('?app=<?=$app?>&controller=<?=$controller?>&action=delete&id='+id, {}, function(json) {
			if (json.state) {
				tableApp.reload();
				return true;
			}
			else {
				ct.error('删除失败');
			}
		});
	},
	'rowCallback' : function(id,e) {
		if (id == defaultWatermark) {
			e.find('.is_default').html('默认');
			$('.setdefault').find('a').eq(0).html('取消默认').bind('click', function() {
				wm.unsetDefault();
			});
		}
		var d = e.find('.disable');
		d.html(parseInt(d.html()) > 0 ? '禁用':'启用');
	},
	'thumb' : function(elm, src) {
		$('#watermark-thumb').remove();
		var thumbDiv = $('<div id="watermark-thumb" style="display:none; position: absolute; overflow: hidden; background: #CCC; z-index:8888;"></div>');
		thumbDiv.appendTo('body');
		thumbDiv.append('<img src="'+src+'" alt="" />');
		$(document).one('keydown', function(e) {
			if (e.keyCode == 27) {	// ESC
				thumbDiv.hide();
			}
		});
		elm.hover(function() {
			thumbDiv.css({
				'top'	: elm.outerHeight(true)+elm.offset().top+1,
				'left'	: elm.offset().left - thumbDiv.find('img').outerWidth() + elm.outerWidth()
			});
			thumbDiv.show();
		}, function() {
			thumbDiv.hide();
		});
	}
};

var tableApp = new ct.table('#item_list', {
	'rowIdPrefix'	: 'row_',
	'pagerId'		: 'pagination',
	'pageVar'		: 'page',
	'pageSize'		: '20',
	'pagesizeVar'	: 'pagesize',
	'template'		: row_template,
	'dblclickHandler': wm.edit,
	'rowCallback'	: wm.rowCallback,
	'baseUrl'		: '?app=<?=$app?>&controller=<?=$controller?>&action=page'
});
$(document).ready(function() {
	tableApp.load();
	$.validate.setConfigs({
		xmlPath:'<?=ADMIN_URL?>apps/system/validators/watermark/'
	});
});
</script>
<?php $this->display('footer', 'system');