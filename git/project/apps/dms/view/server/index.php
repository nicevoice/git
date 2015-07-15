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
<style type="text/css">
.dms_content .button_style_1 { margin-left: 0; }
.dms_content .pagination { position: static; }
</style>
<?php $this->display('sider'); ?>
<div class="dms_search" id="dms_search">
    <ul>
        <li class="t_c">
            <input type="button" value="添加存储服务器" class="btn large primary" onclick="server.add();"/>
        </li>
    </ul>
</div>
<div id="dms_content" class="dms_content">
	<div class="dms_inner">
        <div class="bk_8"></div>
        <table width="99%" id="item_list" cellpadding="0" cellspacing="0"  class="tablesorter table_list mar_l_8">
          <thead>
            <tr>
              <th width="5" class="bdr_3 t_c"><input type="checkbox" /></th>
              <th width="25" class="ajaxer"><div name="serverid">服务器ID</div></th>
              <th width="100" class="ajaxer"><div name="name">服务器标识</div></th>
              <th width="150"><div>服务器网址</div></th>
              <th width="20">操作</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
        <div class="table_foot">
            <div id="pagination" class="pagination f_r"></div>
            <p class="f_l">
                <input type="button" onclick="server.reload();" value="刷 新" class="button_style_1"/>
                <input type="button" onclick="server.del();" value="删 除" class="button_style_1"/>
            </p>
        </div>
        <!--右键菜单-->
        <ul id="right_menu" class="contextMenu">
           <li class="edit"><a href="#server.edit">修改</a></li>
           <li class="delete"><a href="#server.del">删除</a></li>
        </ul>
    </div>
</div>
<script type="text/javascript">
(function(){
var row_template = ['<tr id="row_{serverid}">',
                        '<td class="t_c">',
                            '<input type="checkbox" class="checkbox_style" value="{serverid}" />',
                        '</td>',
                        '<td class="t_c">{serverid}</td>',
                        '<td><a href="javascript:void(0);" onclick="server.edit({serverid})">{name}</a></td>',
                        '<td class="t_l">{url}</td>',
                        '<td class="t_c">',
                           '<img class="manage edit" height="16" width="16" title="修改服务器设置" alt="修改" src="images/edit.gif"/>&nbsp;&nbsp;',
                           '<img class="manage delete" height="16" width="16" title="删除服务器" alt="删除" src="images/delete.gif"/>',
                        '</td>',
                    '</tr>'].join(''),
    init_row_event = function(id, tr){
        tr.find('img.edit').click(function(){
            server.edit(id, tr);
            return false;
        });
        tr.find('img.delete').click(function(){
            server.del(id);
            return false;
        });
    },
    table,
    server = {
        init: function() {
            table = new ct.table('#item_list', {
                rowIdPrefix : 'row_',
                rightMenuId : 'right_menu',
                pageField : 'page',
                pageSize : 15,
                dblclickHandler : server.edit,
                rowCallback : init_row_event,
                template : row_template,
                baseUrl : '?app=<?=$app?>&controller=<?=$controller?>&action=page'
            });
            table.load();
        },
        load: function() {
            table.load();
        },
        reload: function() {
            table.reload();
        },
        add: function(){
            ct.formDialog({title: '添加服务器', width: 350, height: 180}, '?app=<?=$app?>&controller=<?=$controller?>&action=add', function(json) {
                if (json.state) {
                    table.addRow(json.data);
                    return true;
                } else {
                    ct.error(json && json.error || '添加失败');
                    return false;
                }
            });
        },
        edit: function(id, tr) {
            ct.formDialog({title: '修改服务器', width: 350, height: 180}, '?app=<?=$app?>&controller=<?=$controller?>&action=edit&serverid='+id, function(json) {
                if (json.state) {
                    table.updateRow(id, json.data);
                    return true;
                } else {
                    ct.error(json && json.error || '修改失败');
                    return false;
                }
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
                $.post('?app=<?=$app?>&controller=<?=$controller?>&action=delete', 'serverid=' + id, function(json) {
                    json.state
                     ? (ct.warn(json.message || '删除完毕'), table.deleteRow(id))
                     : ct.warn(json.error);
                },'json');
            });
        }
    };

window.server = server;
})();
server.init();
</script>
</body>
</html>