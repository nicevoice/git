(function($){
function insertText(o, textValue, sel, offset, length){
    o.focus();
	var selection = document.selection;
	if (offset == undefined) {
		offset = 0;
	}
	if (selection && selection.createRange) {
		if (!sel) {
			sel = selection.createRange();
		}
		sel.text = textValue;
		var l = textValue.replace(/\r\n/g, '\n').length;
		sel.moveStart('character', offset-l);
		if (length != undefined) {
			sel.moveEnd('character', length-(l-offset));
		}
		sel.select();
	}
	else {
		var st = o.scrollTop, sl = o.scrollLeft;
		if (length == undefined) {
			length = textValue.replace(/\r\n/g, '\n').length - offset;
		}
		if (typeof o.selectionStart != 'undefined') {
			var opn = o.selectionStart + 0;
			o.value = o.value.substring(0, o.selectionStart)+textValue+o.value.substr(o.selectionEnd);
			o.selectionStart = opn + offset;
			o.selectionEnd = o.selectionStart + length;
		} else {
			o.value += textValue;
			o.focus();
			o.selectionStart = offset;
			o.selectionEnd = o.selectionStart + length;
		}
		o.scrollTop = st;
		o.scrollLeft = sl;
	}
	return this;
}
var editEl,ep,
_clipD = null,
clipContainer = null,sectionContainer = null,pageDropdown = null,
pageid,selectedPageid = null,
_add_form_ready = function(form, dialog) {
    var id = selectedPageid || pageid;
	var f = form[0];
	$(f.data).editplus({
		buttons:'fullscreen,wrap',
		height:150
	});
	dialog.find('input[name=type]:not(:checked)').click(function(){
		var n = f.name.value, w = f.width.value, c = f.description.value, d = f.data.value;
		dialog.load('?app=page&controller=section&action=add&pageid='+id+'&type='+this.value,
        function(){
            dialog.trigger('ajaxload');
            var frm = dialog.find('form:eq(0)'), _f = frm[0];
            if (frm.length) {
	            _f.name.value = n;
	            _f.width.value = w;
	            _f.description.value = c;
	            d && (_f.data.value = d);
            }
        });
	});
},
editor = {
    init:function(id) {
    	pageid = id;
        var txt_main = document.getElementById('editor');
        editEl = $(txt_main);
        $.editplus.setPlugin('clip',function(a,sel){
        	var dialog = $('\
				<table class="mar_l_8 table_form" cellspacing="0" cellpadding="0" width="98%" border="0">\
					<tr>\
						<th width="60"><span class="redstar">*</span>名称：</th>\
						<td><input name="clipname" style="width:250px;" /></td>\
					</tr>\
					<tr>\
						<th>代码：</th>\
						<td>\
							<textarea name="code" style="width:350px;height:100px;">'+sel.text+'</textarea>\
						</td>\
					</tr>\
				</table>\
			').dialog({
				title:'添加模板剪辑',
				width:450,
				height:220,
				modal:true,
				resizable:false,
				buttons:{
					'确定':function(){
						var data = 'name='+encodeURIComponent(dialog.find('input').val())
							+'&code='+encodeURIComponent(dialog.find('textarea').val());
						$.ajax({
							dataType:'json',
							type:'post',
							data:data,
							url:'?app=system&controller=template&action=addclip',
							success:function(json){
								editor.addOneClip(json);
							}
						});
						dialog.dialog('destroy').remove();
					},
					'取消':function(){dialog.dialog('destroy').remove();}
				}
			});
	    },{
	    	text:'转化为剪辑',
	    	desc:'转化为剪辑'
	    }).setPlugin('template',function(a, sel){
			var dialog = $('\
				<table class="mar_t_10 table_form" cellspacing="0" cellpadding="0" width="90%" border="0">\
					<tr>\
						<th width="80"><span class="redstar">*</span>文件名：</th>\
						<td><input style="width:180px;" /></td>\
					</tr>\
				</table>\
			').dialog({
				title:'插入子模板标签',
				width:300,
				height:100,
				modal:true,
				resizable:false,
				buttons:{
					'确定':function(){
						insertText(editEl[0], "{template '"+dialog.find('input:eq(0)').val()+"'}", sel.selection);
						dialog.dialog('destroy').remove();
					},
					'取消':function(){dialog.dialog('destroy').remove();}
				}
			});
	    },{
	    	desc:'子模板'
	    }).setPlugin('section',function(a, sel){
	    	var id = selectedPageid || pageid;
	    	if (!id) {
	    		ct.warn('请选择所属页面');
	    		return;
	    	}
	    	var title = '添加/转化区块到页面：'+pageDropdown.find('option[value='+id+']').text();
	    	ct.form(title,'?app=page&controller=section&action=add&pageid='+id,510,430,
	        function(json){
	            if (json.state) {
	                id == selectedPageid && editor.addOneSection(json.data);
	                insertText(editEl[0], '<!--#include virtual="/section/'+json.data.sectionid+'.html"-->', sel.selection);
	                return true;
	            }
	        },function(form, dialog){
				sel.text && (form[0].data.value = sel.text);
	        	_add_form_ready(form, dialog);
	        });
	    },{
	    	text:'添加/转化为区块',
	    	desc:'添加/转化为区块'
	    });
		ep = editEl.editplus({
			buttons:'fullscreen,wrap,|,db,content,discuz,phpwind,shopex,|,loop,ifelse,elseif,template,|,preview,section,clip',
			width:430,
			height:430
		});

	    var adapt = function(){
		    var tw = document.documentElement.clientWidth;
		    ep.setDim(tw - 220);
		};
	    window.onresize = adapt;
	    adapt();

        clipContainer = $('#clipContainer');
        editor.loadClip();

   		sectionContainer = $('#sectionContainer');
        pageDropdown = $('#page_dropdown').change(function(){
        	selectedPageid = this.value;
        	editor.loadSection(this.value);
        });
        if (pageid) {
        	editor.loadSection(pageid);
        	pageDropdown.val(pageid);
        	selectedPageid = pageid;
        } else {
        	pageDropdown.change();
        }

        var toolDiv = $('#toolbox>div.toolarea');
        $('#tooltab').tabnav({
        	dataType:null,
        	initFocus:1,
			forceFocus:true,
			focused:function(li){
				toolDiv.hide();
				var t = li.attr('index');
				toolDiv[t].style.display='block';
				if (t == 0) {
					editor.add = ep.clip;
				} else {
					editor.add = ep.section;
				}
			}
        }).prev('a').click(function(){
        	editor.add($(this), ep.getSelection());
        	return false;
        });
        $(txt_main.form).ajaxForm(function(json){
            if (json.state) {
                ct.confirm('保存成功，继续待在此页？', null, function(){ct.assoc.close()});
            } else {
                ct.error(json.error);
            }
        });
    }, addOneClip:function(item,origli) {
        var li = $('<li><a href="">'+item.name+'</a></li>');
        var a = li.find('a');
        a.click(function(){
        	var sel = ep.getSelection();
            var text = (item.code||'').replace(/\^\!/g, sel.text);
        	insertText(editEl[0], text, sel.selection);
        	return false;
        }).contextMenu('#clip_menu',
		function(action) {
		    editor[action](a, item.clipid);
		});
        origli ? origli.replaceWith(li) : clipContainer.append(li);
        return li;
    }, addOneSection:function(item){
    	var li = $('<li><a class="'+item.type+'" href="">'+item.name+'</a></li>');
        var a = li.find('a');
    	a.click(function(){
        	insertText(editEl[0], '<!--#include virtual="/section/'+item.sectionid+'.html"--><!--'+item.name+'-->');
        	return false;
        }).contextMenu('#section_menu',
		function(action) {
			editor[action](a, item.sectionid);
		});
		sectionContainer.append(li);
        return li;
    }, delClip:function(a, clipid){
    	ct.confirm('确定要删除剪辑"'+a.text()+'"吗？',function(){
            $.post('?app=system&controller=template&action=delclip',
            'clipid='+clipid,
            function (json) {
                if (json.state) {
                    a.parent().remove();
                }
            },'json');
        });
    }, editClip:function(a, clipid){
    	ct.form('编辑剪辑:'+a.html(),
    	'?app=system&controller=template&action=editclip&clipid='+clipid,
    	450,220,function (json) {
    		if (json.state) {
    			editor.addOneClip(json.data,a.parent());
    			return true;
    		}
    	});
    }, loadClip:function() {
    	clipContainer.empty();
        $.getJSON('?app=system&controller=template&action=loadclip',
        function(json){
            for (var i=0,item;item = json[i++];editor.addOneClip(item)){}
        });
    }, loadSection:function(id) {
    	sectionContainer.empty();
    	$.getJSON('?app=page&controller=page&action=sections&pageid='+id,
		function(json){
			for (var i=0,item;item = json[i++];editor.addOneSection(item)){}
		});
    }, viewSection:function(a, id){
    	ct.ajax(
    	'查看区块：'+a.html(),
    	'?app=page&controller=section&action=view&foreditor=1&sectionid='+id);
    }, setProperty:function(a, id){
    	var url = '?app=page&controller=section&action=property&sectionid='+id;
    	ct.form('设置区块“'+a.html()+'”属性', url, 460, 360,
        function (json) {
        	if (json.state) {
        		a.html(json.name);
        		return true;
        	}
        });
    }, previewPage : function(){
    	var url = '?app=page&controller=page&action=preview&pageid='+pageid;
    	$.post(url,'data='+encodeURIComponent(editEl.val()),function(json){
			if (json.state) {
				window.open(url, 'previewpage_'+pageid);
			} else {
				ct.tips('无预览','error');
			}
		},'json');
    }, exportTpl:function() {
    	window.open('?app=page&controller=page&action=exportTemplate&pageid='+pageid, '_blank');
    }, insertClip:function(a) {
    	a.click();
    }, insertSection:function(a) {
    	a.click();
    },
    add:null
};
window.editplus = editor;
})(jQuery);