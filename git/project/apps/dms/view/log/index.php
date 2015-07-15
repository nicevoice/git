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

<link rel="stylesheet" type="text/css" href="apps/dms/css/style.css"/>

<!--dropdown-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/dropdown/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.dropdown.js"></script>

<!--lightbox-->
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.lightbox.js"></script>
<link rel="stylesheet" type="text/css" href="css/imagesbox.css" media="screen" />
<style type="text/css">
#jquery-lightbox {z-index:999}
</style>


<style type="text/css">
.dms_content .button_style_1 { margin-left: 0; }
.dms_content .pagination { position: static; }
.ct_selector {
    background-color: #FBFBFB;
    border:1px solid #ddd;
    padding:3px;
    border-color:#888 #ddd #ddd #888;
    background-color:#fbfbfb;
    font-size:12px;
    font-family:Tahoma, Verdana,"宋体";
}
.ct_selector li {
    line-height: 24px;
    margin-bottom: 0;
}
#appid, #modelid, #target { width: 171px; }
</style>
<?php $this->display('sider'); ?>
<div class="dms_search" id="dms_search">
    <ul>
        <li>
            <h2>选择应用</h2>
            <div>
                <select name="appid" id="appid">
                    <option value="all">所有应用</option>
                    <option value="0">系统应用</option>
                    <?php foreach (table('dms_app') as $dms_app): ?>
                    <option value="<?=$dms_app['appid']?>"><?=$dms_app['name']?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </li>
        <li>
            <h2>选择模型</h2>
            <div><?=form_element::select(array('id' => 'modelid', 'name' => 'modelid', 'options' => $models))?></div>
        </li>
        <li>
            <h2>操作时间</h2>
            <div><input type="text" style="width: 171px;" role="datepicker" onclick="DatePicker(this, {'format':'yyyy-MM-dd HH:mm:ss'});" name="time_start" size="30" title="开始时间" placeholder="开始时间" /></div>
            ~
            <div><input type="text" style="width: 171px;" role="datepicker" onclick="DatePicker(this, {'format':'yyyy-MM-dd HH:mm:ss'});" name="time_end" size="30" title="结束时间" placeholder="结束时间" /></div>
        </li>
        <li>
            <h2>操作对象</h2>
            <div><input type="text" id="target" name="target" size="10" title="操作对象ID" placeholder="操作对象ID" /></div>
        </li>
        <li>
            <span class="button_style_4 f_l" id="search">确定</span>
        </li>
    </ul>
</div>
<div id="dms_content" class="dms_content">
	<div class="dms_inner">
        <div class="bk_8"></div>
        <table width="99%" id="item_list" cellpadding="0" cellspacing="0"  class="tablesorter table_list mar_l_8">
            <thead>
                <tr>
                    <th width="30" class="bdr_3 t_c"><input type="checkbox" /></th>
                    <th width="120"><div name="apiid">应用</div></th>
                    <th width="120"><div>操作人</div></th>
                    <th width="80"><div>操作对象</div></th>
                    <th width="80"><div>类型</div></th>
                    <th width="100" class="ajaxer"><div name="time">操作时间</div></th>
                    <th width="50">操作</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <div class="table_foot">
            <div id="pagination" class="pagination f_r"></div>
            <p class="f_l">
                <input type="button" onclick="log.reload();" value="刷 新" class="button_style_1"/>
                <input type="button" onclick="log.del();" value="删 除" class="button_style_1"/>
            </p>
        </div>
        <!--右键菜单-->
        <ul id="right_menu" class="contextMenu">
            <li class="view"><a href="#log.view">详情</a></li>
            <li class="delete"><a href="#log.del">删除</a></li>
        </ul>
    </div>
</div>
<script type="text/javascript">
(function(){
var row_template = ['<tr id="row_{logid}">',
						'<td class="t_c">',
							'<input type="checkbox" class="checkbox_style" value="{logid}" />',
						'</td>',
						'<td class="t_l">{appname}</td>',
						'<td class="t_l">{operator}</td>',
						'<td class="t_l targetname mod_{modelalias}" target="{target}">{targetname}</td>',
						'<td class="t_c">{actionintro}</td>',
						'<td class="t_c">{timename}</td>',
						'<td class="t_c">',
						   '<img class="manage view" height="16" width="16" title="详情" alt="详情" src="images/txt.gif"/>&nbsp;&nbsp;',
						   '<img class="manage delete" height="16" width="16" title="删除" alt="删除" src="images/delete.gif"/>',
						'</td>',
					'</tr>'].join(''),
	init_row_event = function(id, tr){
		tr.find('img.view').click(function(){
			log.view(id, tr);
			return false;
		});
		tr.find('img.delete').click(function(){
			log.del(id);
			return false;
		});
		var target = tr.find('.targetname');
		if (target.hasClass('mod_article')) {
			target.html('<a onclick="ct.assoc.open(\'?app=dms&controller=article&action=edit&id='+target.attr('target')+'\', \'target\'); return false;" href="#">'+target.html()+'</a>');
		}
		if (target.hasClass('mod_picture')) {
			target.html('<a onclick="picShow(this,'+target.attr('target')+');" href="#">'+target.html()+'</a>');
		}
		if (target.hasClass('mod_picture_group')) {
			target.html('<a onclick="ct.assoc.open(\'?app=dms&controller=picture_group&action=index&id='+target.attr('target')+'\', \'target\'); return false;" href="#">'+target.html()+'</a>');
		}
		if (target.hasClass('mod_attachment')) {
			target.html('<a onclick="attShow(this,'+target.attr('target')+');" href="#">'+target.html()+'</a>');
		}
	},
	table,
	log = {
		init: function() {
			table = new ct.table('#item_list', {
				rowIdPrefix : 'row_',
				rightMenuId : 'right_menu',
				pageField : 'page',
				pageSize : 15,
				dblclickHandler : log.view,
				rowCallback : init_row_event,
				template : row_template,
				baseUrl : '?app=<?=$app?>&controller=<?=$controller?>&action=page'
			});
			table.load();

			function table_load() {
				table.load($('#dms_search :input').serialize());
			}
			$('#search').click(table_load);

			$('#appid').dropdown({
				appid:{
					'onchange': function(val) {
						table_load();
					},
					'selected': 'all'
				}
			});
			$('#modelid').find('option:first').text('所有模型').end().dropdown({
				modelid:{
					'onchange': function(val) {
						table_load();
					},
					'selected': null
				}
			});
		},
		load: function() {
			table.load();
		},
		reload: function() {
			table.reload();
		},
		view: function(id, tr) {
			ct.ajaxDialog({title: '查看日志', width: 600, height: 490}, '?app=<?=$app?>&controller=<?=$controller?>&action=view&logid='+id, function() {}, function() {
				return true;
			});
		},
		del: function(id) {
			var msg;
			if (id === undefined) {
				id = table.checkedIds();
				if (!id.length) {
					ct.warn('请选中要删除项');
					return;
				}
				msg = '确定删除选中的<b style="color:red">'+id.length+'</b>条记录吗？';
			} else {
				msg = '确定删除编号为<b style="color:red">'+id+'</b>的记录吗？';
			}
			ct.confirm(msg, function() {
				$.post('?app=<?=$app?>&controller=<?=$controller?>&action=delete', 'logid=' + id, function(json) {
					json.state
					 ? (ct.warn(json.message || '删除完毕'), table.deleteRow(id))
					 : ct.warn(json.error);
				},'json');
			});
		}
	};

window.log = log;
})();
log.init();
var picShow = function(obj, target) {
	if ($(obj).attr('href') != '#') {
		return;
	}
	$.getJSON('?app=<?=$app?>&controller=picture&action=get', {'id':target}, function(json) {
		if (json) {
			$(obj).attr('href', json.url).lightBox().click();
		}
	});
}
var attShow = function(obj, target) {
	if ($(obj).attr('href') != '#') {
		return true;
	}
	$.getJSON('?app=<?=$app?>&controller=attachment&action=get', {'id':target}, function(json) {
		if (json) {
			$(obj).attr('href', json.url)[0].click();
		}
	});
}
</script>
<?php $this->display('footer', 'system');?>