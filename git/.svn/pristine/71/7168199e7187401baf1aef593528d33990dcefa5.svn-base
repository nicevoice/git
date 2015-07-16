<?php $this->display('header');?>
<script src="apps/dms/js/picture_group.js"></script>
<script src="<?=IMG_URL?>js/lib/cmstop.datepicker.js"></script>

<link href="apps/dms/css/style.css" rel="stylesheet" type="text/css" />

<!--pagination-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.pagination.js"></script>
<!--treetable-->
<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/treetable/style.css" />
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.treetable.js"></script>

<?php $this->display('sider');?>
<div class="dms_search" id="dms_search">
    <ul>
        <li class="t_c">
            <input type="button" name="add" id="add" value="添加应用" class="btn large primary" onclick="dms.add_app();"/>
        </li>
    </ul>
</div>
<div class="dms_content">
	<div class="dms_inner">
		<div class="bk_8"></div>
		<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list" style="margin-left:6px;">
		  <thead>
			<tr>
			  <th width="80" class="bdr_3">ID</th>
			  <th width="200">名称</th>
			  <th width="300">域</th>
			  <th width="150">操作</th>
			</tr>
		  </thead>
		  <tbody id="list_body">
		  </tbody>
		</table>

		<script type="text/javascript">
		var row_template = '<tr id="row_{appid}">\
								<td class="t_c">{appid}</td>\
								<td class="t_l">{name}</td>\
								<td class="t_l">{domain}</td>\
								<td class="t_c"><img src="images/edit.gif" alt="编辑" width="16" height="16" class="hand edit"/> &nbsp;<img src="images/delete.gif" alt="删除" width="16" height="16" class="hand delete" /></td>\
							</tr>';

		var tableApp = new ct.table('#item_list', {
			rowIdPrefix : 'row_',
			pageField : 'page',
			pageSize : 15,
			template : row_template,
			dblclickHandler : dms.edit_app,
			baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page'
		});

		function init_row_event(id, tr)
		{
			tr.find('img.edit').click(function(){
				dms.edit_app(id);
			});
			tr.find('img.delete').click(function(){
				dms.del_app(id);
			});      
		}

		$(document).ready(function() {
			tableApp.load();
		});
		</script>

	</div>
</div>

<script type="text/javascript">
// 添加删除时权限选择树形菜单
var row_template	= new Array('<tr id="priv_row_{id}">',
	'<td class="t_l">',
		'<label><input type="checkbox" class="checkbox_style" name="priv[]" value="{api}" />{name}</label>',
	'</td>',
'</tr>').join('\r\n');
var treeTableOptions = {
	treeCellIndex: 0,
	rowIdPrefix: 'priv_row_',
	template: row_template,
	baseUrl: '?app=dms&controller=app&action=get_api_list',
	parentField: 'parentid',
	collapsed: true,
	rowReady: function(id, tr, json) {
		if (json.checked) {
			tr.find(':checkbox').attr('checked', 'checked');
		}
	},
	rowsPrepared: function(tbody) {
		var tr = tbody.find('tr');
		tr.filter('[parentid="0"]').find('input:checkbox').bind('change', function() {
			var id = $(this).parents('tr').attr('id').substring(9);
			if (this.checked) {
				tr.filter('[parentid="'+id+'"]').find('input:checkbox').attr('checked', 'checked');
			} else {
				tr.filter('[parentid="'+id+'"]').find('input:checkbox').attr('checked', '');
			}
		});
		tr.not('[parentid="0"]').find('input:checkbox').bind('change', function() {
			var parentid = $(this).parents('tr').attr('parentid');
			if (this.checked) {
			} else {
				tr.filter('#priv_row_'+parentid).find('input:checkbox').attr('checked', '');
			}
		});
	}
};
</script>