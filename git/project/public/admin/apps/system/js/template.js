(function(){
var row_template = 
'<tr id="row_{path}" type="{type}" right_menu_id="right_menu_{type}">\
	<td class="t_l nbr"><a class="{type}" href="javascript:;">{file}</a></td>\
	<td class="t_l nbr" width="260">{name}</td>\
	<td class="t_c">\
	   <img class="manage edit" height="16" width="16" alt="编辑" src="images/edit.gif"/>&nbsp;\
       <img class="manage delete" height="16" width="16" alt="删除" src="images/delete.gif"/>\
	</td>\
</tr>',
init_row_event = function(id, tr) {
    if (tr.attr('type') == 'folder') {
        tr.find('>td>img.edit').remove();
        tr.find('img.delete').click(function(){
            App.del(id, tr);
        });
        tr.find('a.folder').click(function(){
            App.open(id);
            return false; 
        });
    } else {
        tr.find('a.file,img.edit').click(function(){
            App.edit(id, tr);
            return false; 
        });
        tr.find('img.delete').click(function(){
            App.del(id, tr);
        });
    }
},
dblclick_handler = function(id, tr){
    tr.attr('type') == 'folder' ? App.open(id) : App.edit(id, tr);
},
json_loaded = function(json) {
    json.dir && (
        nav.trigger('setNav', [json.dir.path, json.dir.alias]),
        (current_dir = json.dir.path)
    );
},
current_dir, addUrl, editUrl, delUrl, nav,

App = {
    table:null,
    init:function(baseUrl){
        addUrl = baseUrl + '&action=add';
        editUrl = baseUrl + '&action=edit';
        delUrl = baseUrl + '&action=delete';
        
        nav = $('#navigator').navigator({
            dirUrl:baseUrl+'&action=dir'
        }).bind('cd', function(e, path){
        	App.open(path);
        });
        
        var up = $.uploader('#uploadify', {
			script         : baseUrl+'&action=upload',
			fileDesc	: 'ZIP、HTML文件',
			fileExt		 : '*.zip;*.html',
			fileDataName   : 'upfile',
			jsonType:1,
			selectend:function(){
				up.set('scriptData', {
                    path:current_dir
                });
			},
			complete:function(json, data) {
				if (json.state) {
					for (var i=0,l=json.data.length;i<l;i++) {
                        App.table.addRow(json.data[i],true).trigger('check');
                    }
				} else {
					ct.tips('文件“'+data.file.name+'”上传失败，'+json.error, 'error');
				}
			},
			allcomplete:function(data) {
				ct.ok("所有文件上传完成");
			},
			error:function(data) {
				ct.tips(data.error.type,'error');
			}
		});
        
        App.table = new ct.table('#item_list',{
            dblclickHandler : dblclick_handler,
            rowCallback     : init_row_event,
            jsonLoaded      : json_loaded,
            template : row_template,
            baseUrl  : baseUrl+'&action=page'
        });
        App.table.load();
    },
    open:function(path){
        App.table.load('dir='+path);
    },
	edit:function(id, tr) {
	    parent.superAssoc.open(editUrl+'&path='+encodeURIComponent(id), 'newtab');
	},
    add:function(){
        parent.superAssoc.open(addUrl+'&path='+encodeURIComponent(current_dir), 'newtab');
    },
    test:function(){
    	var overlay = $('<div class="overlay"></div>').appendTo(document.body);
    	var testbox = $(
		'<div class="test-box">'+
			'<div class="close">&#x2716;</div>'+
			'<div class="progress-control">'+
				'<div class="control">开始检测</div>'+
				'<div class="progress">'+
					'<div class="bar">'+
						'<div class="percent">0%</div>'+
						'<div class="indicator"></div>'+
					'</div>'+
				'</div>'+
			'</div>'+
			'<div class="current"></div>'+
			'<div class="output"></div>'+
		'</div>').appendTo(document.body);
		var close, progressControl, progress, percent, indicator, control, state, output;
		testbox.find('div').each(function(){
			switch(this.className){
			case 'close': close = $(this); break;
			case 'progress-control': progressControl = $(this); break;
			case 'progress': progress = $(this); break;
			case 'percent': percent = $(this); break;
			case 'indicator': indicator = $(this); break;
			case 'control': control = $(this); break;
			case 'current': current = $(this); break;
			case 'output': output = $(this); break;
			}
		});
		var running = false, proceed = 0, errors = 0, ival = null, inPing = true, xhr;
		progress.hide();
		function stop(clear){
			running = false;
			progress.hide();
			progressControl.removeClass('wide');
			xhr && xhr.abort();
			control.html('重新检测');
			current.html('检测完毕 <b>共检测:'+proceed+'</b> <b style="color:red">问题数:'+errors+'</b>');
			ival && clearTimeout(ival);
			$.getJSON('?app=system&controller=template&action=stopTest&clear='+(clear||0));
			ct.endLoading();
		}
		function start(){
			if (inPing) return;
			running = true;
			proceed = 0;
			errors = 0;
			percent.html('0%');
			indicator.width('0%');
			progressControl.addClass('wide');
			testbox.removeClass('haserror');
			progress.show();
			control.html('终止检测');
			current.empty();
			output.empty();
			xhr = $.ajax({
				dataType:'json',
				url:'?app=system&controller=template&action=test'
			});
			ct.endLoading();
			ival = setTimeout(ping, 50);
		}
		function update(json){
			inPing = false;
			if (!running) return;
			if (json.state) {
				xhr.abort();
				var p = Math.floor(json.percent * 100)+'%';
				percent.html(p);
				indicator.width(p);
				proceed = json.proceed;
				current.html('正在检测:'+json.current);
				if (json.results && json.results.length) {
					errors || testbox.addClass('haserror');
					errors += json.results.length;
					$.each(json.results, function(){
						output.append('<p>'+this+'</p>');
					});
					output.scrollTop(10000);
				}
				if (json.percent == 1 || json.total == 0) {
					return stop(1);
				}
			} else if (proceed > 0) {
				return stop();
			}
			ival = setTimeout(ping, 300);
		}
		function ping(){
			inPing = true;
			$.getJSON('?app=system&controller=template&action=pingTest&proceed='+proceed, update);
			ct.endLoading();
		}
		control.click(function(){
			running ? stop(1) : start();
		});
		close.click(function(){
			running = false;
			xhr && xhr.abort();
			ival && clearTimeout(ival);
			testbox.remove();
			overlay.remove();
		});
		$.getJSON('?app=system&controller=template&action=pingTest', function(json){
			inPing = false;
			json.state && start();
		});
    },
    del:function(id, tr){
        
        var msg = '此操作不可恢复，确定删除<strong>'
            +(tr.attr('type') == 'folder' ? '目录' : '文件')
            +'</strong> "<b style="color:red">'+id+'</b>"吗？';
        var data = 'path='+id;
        ct.confirm(msg,function(){
            $.post(delUrl, data, function(json){
                json.state
                 ? (ct.ok('删除完毕'), App.table.deleteRow(id))
                 : ct.error(json.error);
            },'json');
        });
    },
    alias:function(path){
    	ct.form('修改别名', '?app=system&controller=template&action=alias&path='+path, 250, 100, function(response){
    	    if (response.state)
    	    {
    	    	App.table.load();
    	    	ct.ok('操作成功');
    	    	return true;
    	    }
    	    else
    	    {
    	    	ct.error(response.error);
    	    }
    	});
    }
};
window.App = App;
})();