(function(){
var row_template = 
'<tr id="row_{dsnid}">\
	<td class="t_l">{name}</td>\
	<td class="t_l">{driver}</td>\
	<td class="t_l">{host}</td>\
	<td class="t_l">{dbname}</td>\
	<td class="t_l">{charset}</td>\
	<td class="t_l">{created}</td>\
	<td class="t_c">\
	   <img class="manage edit" height="16" width="16" alt="编辑" src="images/edit.gif"/>\
       <img class="manage delete" height="16" width="16" alt="删除" src="images/delete.gif"/>\
	</td>\
</tr>',
init_row_event = function(id, tr)
{
    tr.find('>td>img.edit').click(function(){
        App.edit(id, tr);
    });
    tr.find('>td>img.delete').click(function(){
        App.del(id, tr);
    });
},
editUrl,delUrl,addUrl,testUrl,
App = {
    table:null,
    init:function(baseUrl, dataAction){
        editUrl = baseUrl+'&action=edit';
        addUrl = baseUrl+'&action=add';
        delUrl = baseUrl+'&action=delete';
        testUrl = baseUrl+'&action=test';
        App.table = new ct.table('#item_list',{
            dblclickHandler : App.edit,
            rowCallback     : init_row_event,
            template : row_template,
            baseUrl  : baseUrl+'&action='+(dataAction||'page')
        });
        App.table.load();
    },
    edit:function(id, tr){
        ct.form('编辑数据源',editUrl+'&dsnid='+id,370,390,function(json){
            if (json.state)
            {
                App.table.updateRow(id, json.data).trigger('check');
                return true;
            }
        });
    },
    add:function(){
        ct.form('添加数据源', addUrl, 370,390, function(json){
            if (json.state)
            {
                App.table.addRow(json.data).trigger('check');
                return true;
            }
        });
    },
    del:function(id, tr){
        var msg;
        if (id === undefined)
        {
            id = App.table.checkedIds();
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
        	var data = 'id='+id;
            $.post(delUrl,data,function(json){
                json.state
                 ? (ct.ok('删除完毕'), App.table.deleteRow(id))
                 : ct.error(json.error);
            },'json');
        });
    },
    testlink:function(frm)
    {
        frm = $(frm);
        $.post(testUrl,frm.serialize(),function(json){
            var info = json.state
                ? $('<div class="success"><sub></sub>资源正常</div>')
                : $('<div class="error"><sub></sub>'+json.error+'</div>');
            frm.before(info);
            setTimeout(function(){info && info.hide()}, 3000);
        },'json');
    }
};
window.App = App;
})();