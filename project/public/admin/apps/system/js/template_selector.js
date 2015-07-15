(function(){
var row_template = 
'<tr id="row_{path}" type="{type}" code="{code}" right_menu_id="right_menu_{type}">\
	<td class="t_l nbr"><a class="{type}" href="javascript:;">{file}</a></td>\
	<td class="t_l nbr">{name}</td>\
	<td class="t_l">{size}</td>\
	<td class="t_c">{mtime}</td>\
</tr>',
dblclick_handler = function(id, tr, json){
    tr.attr('type') == 'folder' ? App.open(id) : App.check(json.code);
},
json_loaded = function(json) {
    json.dir && (
        nav.trigger('setNav', [json.dir.path, json.dir.alias]),
        (current_dir = json.dir.path)
    );
    if(App.dir == json.dir.path)
    {
    	setTimeout(function (){
    		$('#item_list a.file' ).each(function (i, e){
    			if($(e).text() == App.file)
    			{
    				$(e).parents('tr').click();
    			}
    		});
    	}, 10)
    }
},
current_dir, nav, table,
App = {
	dir: '',
	file: '',
    init:function(baseUrl){
        nav = $('#navigator').navigator({
            dirUrl:baseUrl+'&action=dir'
        }).bind('cd',function(e,path){
        	App.open(path);
        });
        
        table = new ct.table('#item_list',{
            dblclickHandler : dblclick_handler,
            jsonLoaded      : json_loaded,
            rowCallback : function(id, tr){
            	tr.attr('type') == 'folder' && tr.find('a').click(function(){
            		App.open(id);
            		return false;
            	});
            },
            template : row_template,
            baseUrl  : baseUrl+'&action=spage'
        });
        table.load('dir=' + this.dir + '&file=' + this.file);
    },
    open:function(path){
        table.load('dir='+path);
    },
    check:function(code)
    {
		if (code == undefined) {
			var tr = table.checkedRow();
			if (! tr || tr.attr('type') != 'file') {
				ct.warn('请选择一个模板');
				return;
			}
			code = tr.attr('code');
		}
		if (parent)
		{
			if (window.dialogCallback && dialogCallback.ok)
			{
				dialogCallback.ok(code);
			}
			else
			{
				window.getDialog && getDialog().dialog('close');
			}
		}
	},
    rcheck:function(id, tr, json)
    {
    	App.check(json.code);
    },
    cancel:function(){
    	if (parent)
		{
			window.getDialog && getDialog().dialog('close');
		}
    }
};
window.App = App;
})();