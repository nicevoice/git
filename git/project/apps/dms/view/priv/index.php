<?php $this->display('header');?>
<script src="apps/dms/js/picture_group.js"></script>
<script src="<?=IMG_URL?>js/lib/cmstop.datepicker.js"></script>

<link href="apps/dms/css/style.css" rel="stylesheet" type="text/css" />

<!--pagination-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.pagination.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/list/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.list.js"></script>

<div class="dms_search">
</div>
<?php $this->display('sider');?>
<div class="dms_search" id="dms_search">
	<div class="intro" style="margin-top: 20px; width: 100px; margin-left: 20px; line-height:16px;"> 当前应用在其他应用中的可读/可编写/可删除权限设置 </div>
	<div class="bk_8"></div>
    <div class="nav_tag">
    </div>
</div>
<div class="dms_content">
	<div class="dms_inner">
		<div class="bk_8"></div>
		<div>
			当前应用：
			<select name="applist" id="applist">
			<?php foreach($app_data as $item):?>
				<option value="<?=$item['appid']?>"<?php if($item['appid'] == $app_data[0]['appid']):?> selected="selected"<?php endif;?>><?=$item['name']?></option>
			<?php endforeach;?>
			</select>
		</div>
		<div class="bk_8"></div>
		<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list" style="margin-left:6px;">
		  <thead>
			<tr>
				<th width="10%" class="bdr_3">ID</th>
				<th width="50%">应用名</th>
				<th width="10%">可读</th>
				<th width="10%">可编辑</th>
				<th width="10%">可删除</th>
			</tr>
		  </thead>
		  <tbody id="list_body">
		  </tbody>
		</table>
	</div>
</div>
<script type="text/javascript">
var sourceid = <?=$app_data[0]['appid']?>;
$('#'+sourceid).addClass('cur');
var row_template = new Array('<tr id="row_{appid}">',
								'<td class="t_c">{appid}</td>',
								'<td class="t_c">{name}</td>',
								'<td class="t_c priv_read">{r}</td>',
								'<td class="t_c priv_edit">{e}</td>',
								'<td class="t_c priv_delete">{d}</td>',
							'</tr>').join('\r\n');
var tableApp = new ct.table('#item_list', {
	rowIdPrefix : 'row_',
	pageField : 'page',
	pageSize : 30,
	template : row_template,
	rowCallback : function(i,o) {
		var parsePriv = function(name, priv) {
			if (o.find('.priv_'+name).html() == '1') {
				o.find('.priv_'+name).html('<div align="center"><a href="javascript:set_disable('+i+', '+priv+');" title="禁用" class="tick"></a></div>');
			} else {
				o.find('.priv_'+name).html('<div align="center"><a href="javascript:set_enable('+i+', '+priv+');" title="启用" class="cross"></a></div>');
			}
		};
		parsePriv('read', 1);
		parsePriv('edit', 2);
		parsePriv('delete', 4);
	},
	baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page'
});

var set_disable = function(targetid, priv) {
	$.getJSON('?app=<?=$app?>&controller=<?=$controller?>&action=set_disable', {'source':sourceid, 'target':targetid, 'priv':priv}, function(json) {
		if (json.state) {
			tableApp.load('id='+sourceid);
		} else {
			ct.error('操作失败');
			tableApp.load('id='+sourceid);
		}
	});
};

var set_enable = function(targetid, priv) {
	$.getJSON('?app=<?=$app?>&controller=<?=$controller?>&action=set_enable', {'source':sourceid, 'target':targetid, 'priv':priv}, function(json) {
		if (json.state) {
			tableApp.load('id='+sourceid);
		} else {
			ct.error('操作失败');
			tableApp.load('id='+sourceid);
		}
	});
};

var changeApp = function(appid) {
	tableApp.load('id='+appid);
};

$(document).ready(function() {
	tableApp.load('id='+sourceid);
	$('#applist').selectlist().bind('changed', function(e,t) {
		changeApp(t.checked[0]);
	});
});
</script>