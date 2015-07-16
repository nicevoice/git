<?php $this->display('header', 'system');?>
<!--tablesorter-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<!--contextmenu-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<div class="bk_10"></div>
<div class="table_head">
  <input type="button" value="添加" class="button_style_2 f_l" onclick="App.add();"/>
</div>
<div class="bk_8"></div>
<table width="98%" id="item_list" cellpadding="0" cellspacing="0"  class="tablesorter table_list mar_l_8">
  <thead>
    <tr>
      <th width="30" class="bdr_3 t_c"><input type="checkbox" /></th>
      <th class="sorter"><div>网站名称</div></th>
      <th width="150" class="sorter"><div>规则数</div></th>
      <th width="150" class="sorter"><div>任务数</div></th>
      <th width="100">操作</th>
    </tr>
  </thead>
  <tbody>
  </tbody>
</table>
<div class="table_foot">
  <p class="f_l">
    <input type="button" onclick="App.del();" value="删 除" class="button_style_1"/>
  </p>
</div>
<!--右键菜单-->
<ul id="right_menu" class="contextMenu">
   <li class="edit"><a href="#App.edit">编辑</a></li>
   <li class="delete"><a href="#App.del">删除</a></li>
</ul>
<script type="text/javascript">
(function(){
	var row_template = '\
<tr id="row_{siteid}">\
	<td class="t_c">\
	   <input type="checkbox" class="checkbox_style" value="{siteid}" />\
	</td>\
	<td><a href="javascript:ct.assoc.open(\'?app=spider&controller=manager&action=index&siteid={siteid}\',\'newtab\');">{name}</a></td>\
	<td class="t_r">{rulenum}</td>\
	<td class="t_r">{tasknum}</td>\
	<td class="t_c">\
	   <img class="manage edit" height="16" width="16" alt="编辑" src="images/edit.gif"/>\
       <img class="manage delete" height="16" width="16" alt="删除" src="images/delete.gif"/>\
	</td>\
</tr>',
init_row_event = function(id, tr){
    tr.find('img.edit').click(function(){
    	App.edit(id, tr);
    	return false;
    });
    tr.find('img.delete').click(function(){
    	App.del(id);
    	return false;
    });
},
table,
App = {
    init:function() {
        table = new ct.table('#item_list',{
            dblclickHandler : App.edit,
            rowCallback : init_row_event,
            template : row_template,
            baseUrl : '?app=spider&controller=manager&action=lsSite'
        });
        table.load();
    },
    edit:function(id, tr){
		ct.form('编辑网站','?app=spider&controller=manager&action=editSite&siteid='+id,
		280,100,
		function(json){
			if (json.state)
			{
				table.updateRow(id, json.data);
				return true;
			}
		});
    },
    add:function(){
        ct.form('添加网站','?app=spider&controller=manager&action=addSite',
		280,100,
		function(json){
			if (json.state)
			{
				table.addRow(json.data);
				return true;
			}
		});
    },
    del:function(id){
        var msg;
        if (id === undefined)
        {
            id = table.checkedIds();
            if (!id.length)
            {
                ct.warn('请选中要删除项');
                return;
            }
            msg = '确定删除选中的<b style="color:red">'+id.length+'</b>条记录吗？';
        }
        else
        {
            msg = '确定删除编号为<b style="color:red">'+id+'</b>的记录吗？';
        }
        ct.confirm(msg,function(){
            $.post('?app=spider&controller=manager&action=delSite','id='+id,
            function(json){
                json.state
                 ? (ct.warn('删除完毕'), table.deleteRow(id))
                 : ct.warn(json.error);
            },'json');
        });
    }
};
window.App = App;
})();
App.init();
</script>
</body>
</html>