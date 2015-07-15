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
            <input type="button" value="添加模型" class="btn large primary" onclick="model.add();"/>
        </li>
    </ul>
</div>
<div id="dms_content" class="dms_content">
	<div class="dms_inner">
        <div class="bk_8"></div>
        <table width="99%" id="item_list" cellpadding="0" cellspacing="0"  class="tablesorter table_list mar_l_8">
          <thead>
            <tr>
              <th width="10" class="bdr_3 t_c"><input type="checkbox" /></th>
              <th width="20" class="ajaxer"><div name="modelid">ID</div></th>
              <th width="75" class="ajaxer"><div name="name">模型名称</div></th>
              <th width="75"><div>模型别名</div></th>
              <th width="75"><div>主索引</div></th>
              <th width="75"><div>增量索引</div></th>
              <th width="30">操作</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
        <div class="table_foot">
            <div id="pagination" class="pagination f_r"></div>
            <p class="f_l">
                <input type="button" onclick="model.reload();" value="刷 新" class="button_style_1"/>
                <input type="button" onclick="model.del();" value="删 除" class="button_style_1"/>
            </p>
        </div>
        <!--右键菜单-->
        <ul id="right_menu" class="contextMenu">
           <li class="edit"><a href="#model.edit">修改</a></li>
           <li class="delete"><a href="#model.del">删除</a></li>
        </ul>
    </div>
</div>
<script type="text/javascript">
(function(){
var row_template = ['<tr id="row_{modelid}">',
                        '<td class="t_c">',
                            '<input type="checkbox" class="checkbox_style" value="{modelid}" />',
                        '</td>',
                        '<td class="t_c">{modelid}</td>',
                        '<td><a href="javascript:void(0);" onclick="model.edit({modelid})">{name}</a></td>',
                        '<td class="t_l">{alias}</td>',
                        '<td class="t_l">{mainindex}</td>',
                        '<td class="t_l">{deltaindex}</td>',
                        '<td class="t_c">',
                           '<img class="manage edit" height="16" width="16" title="修改模型" alt="修改" src="images/edit.gif"/>&nbsp;&nbsp;',
                           '<img class="manage delete" height="16" width="16" title="删除模型" alt="删除" src="images/delete.gif"/>',
                        '</td>',
                    '</tr>'].join(''),
    init_row_event = function(id, tr){
        tr.find('img.edit').click(function(){
            model.edit(id, tr);
            return false;
        });
        tr.find('img.delete').click(function(){
            model.del(id);
            return false;
        });
    },
    table,
    model = {
        init: function() {
            table = new ct.table('#item_list', {
                rowIdPrefix : 'row_',
                rightMenuId : 'right_menu',
                pageField : 'page',
                pageSize : 15,
                dblclickHandler : model.edit,
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
            ct.formDialog({title: '添加模型', width: 360, height: 230}, '?app=<?=$app?>&controller=<?=$controller?>&action=add', function(json) {
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
            ct.formDialog({title: '修改模型', width: 360, height: 230}, '?app=<?=$app?>&controller=<?=$controller?>&action=edit&modelid='+id, function(json) {
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
                $.post('?app=<?=$app?>&controller=<?=$controller?>&action=delete', 'modelid=' + id, function(json) {
                    json.state
                     ? (ct.warn('删除完毕'), table.deleteRow(id))
                     : ct.warn(json.error);
                },'json');
            });
        }
    };

window.model = model;
})();
model.init();
</script>
</body>
</html>