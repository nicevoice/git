(function(){
var dialog = null;
var editEl = null;
var editmode = 0;
var noconfirm = 0;
var editid = null;
var ivallock = null;
var fragment = null;
var preview_w = null;
var IN_PREVIEW = false;
var clearIlock = function(){
    ivallock && clearInterval(ivallock);
};
var setEditing = function(id){
    editmode = 1;
    clearIlock();
    editid = id;
    ivallock = setInterval(function(){
    	if (!editmode) {
            clearIlock();
            return;
        }
        ct.stopListenOnce();
        $.post('?app=page&controller=section&action=lock','sectionid='+id,
        function(json){
        	if (!json.state)
        	{
        		clearIlock();
        		ct.warn(json.error+'，请退出编辑');
        	}
        },'json');
    }, 10000);
};
var exitEditing = function(){
    editmode = 0;
    clearIlock();
    editid && $.post('?app=page&controller=section&action=unsave','sectionid='+editid);
    editid = null;
    if (preview_w && preview_w.data('previewing')) {
    	var m = preview_w.data('masker');
    	_moveNodes(fragment, preview_w);
    	m.attr('title', preview_w.data('title'));
    	m[0].className = preview_w.data('className');
    	preview_w.data('previewing', 0);
    	preview_w = null;
    	_resetPosition();
    }
};
var minZindex = 0;
var sectionaction = {
    edit:function(w, masker, sectionid){
        masker.trigger('click');
    },
    publish:function(w, masker, sectionid){
        var url = '?app=page&controller=section&action=publish';
        $.post(url,'sectionid='+sectionid, function(json){
            if (json.state) {
                var title = w.attr('title');
                masker.removeClass('red').addClass('green');
                masker.attr('title', title+' 已生成到最新');
            } else {
                ct.error(json.error);
            }
        },'json');
    },
    property:function(w, masker, sectionid){
    	var url = '?app=page&controller=section&action=property&sectionid='+sectionid;
    	ct.form('设置区块“'+w.attr('title')+'”属性', url, 460, 380,
        function(json){
        	if (json.state) {
        		masker.removeClass('green').addClass('red');
        		var title = json.name;
	        	masker.attr('title', title+' 设置了属性');
	        	w.attr('title', title);
        		return true;
        	}
        },function(form){
        	form[0].data && $(form[0].data).editplus({
        		buttons:'fullscreen,wrap',
        		height:150
        	});
        });
    },
    grap:function(w, masker, sectionid){
        var url = '?app=page&controller=section&action=grap';
        $.post(url,'sectionid='+sectionid, function(json){
            if (json.state) {
                var title = w.attr('title');
                masker.removeClass('red').addClass('green');
                masker.attr('title', title+' 刚刚抓取成功');
                w.html(json.html||'');
                _resetPosition();
            } else {
                ct.error(json.error);
            }
        },'json');
    },
    moveDown:function(w, masker) {
    	masker.css('z-index',parseInt(masker.css('z-index'))-1);
    	w.css('z-index',parseInt(w.css('z-index'))-1);
    }
};
var dragSortInited = 0;
var _readyrow = function(row,sectionid){
    var cells = row[0].cells;
    var c1 = $(cells[1]);
    c1.find('li').each(function(){
        var li = $(this).contextMenu('#section_item_menu',
		function(action, el, pos) {
		    handaction[action](li,sectionid);
		});
		li.find('a').click(function(e){
			li.triggerHandler('contextMenu',[e]);
			return false;
		}).attrTips('tips',null,350);
    });
    c1.contextMenu('#section_cell_menu',
	function(action, el, pos) {
		handaction[action](c1,row,sectionid);
	});
    $('>img',cells[2]).click(function(){
        handaction[this.getAttribute('action')](c1,row,sectionid);
    });

    var tbody = $('tbody#sortable');
    if (dragSortInited) {
        tbody.sortable('refresh');
    } else {
        tbody.sortable({
            'axis': 'y',
            'handle': 'td:first',
            'items': 'tr',
            'helper': 'clone',
            'placeholder': 'tr-placeholder',
            'opacity': 0.8,
            create: function() {
                dragSortInited = 1;
            },
            start: function(ev, ui) {
                // TODO FIXME!
                $('<td colspan="3">&nbsp;</td>').appendTo(ui.placeholder);
                ui.helper.find('td:eq(1)').width(ui.item.width() - (ct.IE ? 145 : ($.browser.safari ? 130 : 136)));
                ui.helper.find('>td').css('border-bottom', 'none');
                ui.helper.css('background-color', '#FFF');
                ct.IE && ui.helper.css('margin-left', ct.IE7 ? '-1px' : '0px');
            },
            stop: function(ev, ui) {
                var oldIndex = parseInt(ui.item.find('td:first').html()),
                    newIndex = tbody.find('>tr').index(ui.item.get(0)) + 1,
                    diff = oldIndex - newIndex;
                if (diff) {
                    $.post("?app=page&controller=section&action=" + (diff > 0 ? 'uprow' : 'downrow'), {
                        row: oldIndex - 1,
                        sectionid: sectionid,
                        num: Math.abs(diff)
                    });
                }
                tbody.find('>tr').each(function(index, tr) {
                    $(tr).find('td:first').text(index + 1);
                });
            },
            change: function(ev, ui) {

            }
        });
    }
};
var _scantable = function(table,sectionid){
    var rows = $(table[0].rows).filter(':gt(0)');
    rows.each(function(){
        _readyrow($(this),sectionid);
    });
};
var _bind_item_form_event = function(form,dialog)
{
	var title = form.find('input[name=title]').maxLength();
    var colorPicker = title.nextAll('img');
    colorPicker.titleColorPicker(title, colorPicker.next('input'));
    dialog.find('form input[name="thumb"]').imageInput();
};
var searchOption = {
    template : '\
		<tr id="search_{contentid}">\
	        <td class="t_c"><input name="hand_new_item" class="radio_style" type="radio" value="{contentid}"></td>\
	    	<td class="t_l"><a href="{url}" thumb="{thumb}" target="_blank">{title}</a></td>\
	    	<td class="t_c">{username}</td>\
	    	<td class="t_r">{created}</td>\
		</tr>',
    baseUrl  : '?app=page&controller=content&action=search'
};
var recommendOption = {
	template : '\
		<tr id="recommend_{recommendid}">\
	        <td class="t_c"><input name="hand_new_item" class="radio_style" type="radio" value="{recommendid}" /></td>\
	        <td><a href="{url}" thumb="{thumb}" target="_blank">{title}</a></td>\
	        <td class="t_c">{username}</td>\
	        <td class="t_r">{recommended}</td>\
	        <td class="t_c"><img width="16" height="16" class="hand del" alt="删除" src="images/del.gif"></td>\
	    </tr>',
    baseUrl  : '?app=page&controller=content&action=recommend'
};
var _prepareItem = function(form, dialog){
    var div = dialog.find('div.part');

    new scrollTable($('>table',div[0]),$.extend({
    	rowChecked:function(tr, json){
			form[0].title.value = json.title;
            $(form[0].title).keyup();
			form[0].contentid.value = json.contentid;
            form[0].url.value = json.url;
            if (json.thumb && json.thumb != 'null') {
            	form[0].thumb.value = json.thumb;
            }
            form[0].description.value = json.description;
            form[0].time.value = json.created;
        },
        rowReady:function(tr,json){
        	tr.find('img.del').click(function(){
        		$.post('?app=page&controller=content&action=delrecommend',
        			'recommendid='+json.recommendid,
        		function(json){
        			if (json.state) {
        				tr.remove();
        			} else {
        				ct.tips(json.error,'error');
        			}
        		},'json');
        		return false;
        	});
			var a = tr.find('a:first');
        	a.attr('tips', json.tips);
			a.attrTips('tips', null, 380);
        	if (json.thumb && json.thumb != 'null') {
        		var img = $('<img thumb="'+json.thumb+'" style="margin-right:3px;vertical-align:middle;" src="images/thumb.gif"/>');
        		img.floatImg({url:UPLOAD_URL,height:200});
        		a.before(img);
        	}
        },
        pageCtrl:dialog
    }, recommendOption)).load('sectionid='+editid);

    var searchTable = new scrollTable($('>table', div[1]),$.extend({
        rowChecked:function(tr, json){
        	form[0].title.value = json.title;
            $(form[0].title).keyup();
            form[0].url.value = json.url;
            if (json.thumb && json.thumb != 'null') {
            	form[0].thumb.value = json.thumb;
            }
            form[0].description.value = json.description;
            form[0].time.value = json.created;
        },
        rowReady:function(tr,json){
        	var a = tr.find('a:first');
        	a.attr('tips', json.tips);
			a.attrTips('tips', null, 350);
        	if (json.thumb && json.thumb != 'null') {
        		var img = $('<img thumb="'+json.thumb+'" style="margin-right:3px;vertical-align:middle;" src="images/thumb.gif"/>');
        		img.floatImg({url:UPLOAD_URL,height:200});
        		a.before(img);
        	}
        },
        pageCtrl:dialog
    }, searchOption));

    var searchElements = dialog.find('div.search_icon :input');
    searchElements.filter('button:last').click(function(){
    	var where = [
            'thumb='+(searchElements.filter('[name=thumb]').is(':checked') ? 1 : 0),
    		'keywords='+encodeURIComponent(searchElements.filter('[name=keywords]').val()),
    		'modelid='+searchElements.filter('[name=modelid]').val(),
    		'catid='+searchElements.filter('[name=catid]').val()
    	];
        searchTable.load(where.join('&'));
    }).click();

    _bind_item_form_event(form,dialog);

    dialog.find('ul.tag_list').tabnav({
		dataType:null,
		forceFocus:true,
		focused:function(li){
			div.hide();
			var t = div[li.attr('index')];
			t && (t.style.display = 'block');
		}
	});
};
var _newitem = function(item,sectionid)
{
	var li = $('<li url="'+item.url+'" col="'+item.col+'"><a href="'+item.url+'" target="_blank">'+item.title+'</a></li>');
	var a = li.find('a');
	item.color && a.css('color', item.color);
	a.attr('tips',item.tips);
	a.attrTips('tips',null,350);
	li.contextMenu('#section_item_menu',
	function(action, el, pos) {
	    handaction[action](li,sectionid);
	});
	return li;
};
var _itemattr = function(li,a,item)
{
	// edit a title,text,url
    a.text(item.title||'')
     .attr('href', item.url||'')
     .attr('tips', item.tips);
    item.color && a.css('color', item.color);
    // edit li url
    li.attr('url', item.url);
};
var handaction = {
    edititem:function(li,sectionid){
        var row = li.parents('td').attr('row');
        var col = li.attr('col');
        var url = '?app=page&controller=section&action=edititem&sectionid='+sectionid+'&row='+row+'&col='+col;
        var a = li.find('>a');
        ct.form('编辑条目：'+a.text(),url,530,380,function(json){
            if (json.state) {
            	_itemattr(li,a,json.data);
                return true;
            }
        },_bind_item_form_event);
    },
    delitem:function(li,sectionid){
        ct.confirm('确定删除项“<b style="color:red">'+li.text()+'</b>”？',
        function(){
            var row = li.parents('td').attr('row');
            var col = li.attr('col');
            var url = '?app=page&controller=section&action=delitem';
            var data = 'sectionid='+sectionid+'&row='+row+'&col='+col;
            $.post(url, data, function(json){
                if (json.state) {
                    // change next-all-li col value
                    var c = parseInt(col);
                    li.nextAll('li').each(function(){
                        this.setAttribute('col',c++);
                    });
                    // remove this li
                    li.remove();
                } else {
                    ct.error(json.error);
                }
            }, 'json');
        });
    },
    replaceitem:function(li,sectionid){
        var row = li.parents('td').attr('row');
        var col = li.attr('col');
        var url = '?app=page&controller=section&action=replaceitem&sectionid='+sectionid+'&row='+row+'&col='+col;
        var a = li.find('>a');
        ct.form('替换条目：'+a.text(),url,530,400,function(json){
            if (json.state) {
            	_itemattr(li,a,json.data);
                return true;
            }
        },_prepareItem);
    },
    moveitemleft:function(li,sectionid){
        var prevli = li.prev('li');
        if (!prevli.length) return;
        var row = li.parents('td').attr('row');
        var col = li.attr('col');
        var url = '?app=page&controller=section&action=leftitem';
        var data = 'sectionid='+sectionid+'&row='+row+'&col='+col;
        $.post(url, data, function(json){
			if (json.state) {
				li.attr('col', prevli.attr('col'));
				prevli.attr('col', col);
				li.after(prevli);
			}
		},'json');
    },
    moveitemright:function(li,sectionid){
        var nextli = li.next('li');
        if (!nextli.length) return;
        var row = li.parents('td').attr('row');
        var col = li.attr('col');
        var url = '?app=page&controller=section&action=rightitem';
        var data = 'sectionid='+sectionid+'&row='+row+'&col='+col;
        $.post(url, data, function(json){
			if (json.state) {
				li.attr('col', nextli.attr('col'));
				nextli.attr('col', col);
				li.before(nextli);
			}
		},'json');
    },
    viewitem:function(li,sectionid){
        window.open(li.attr('url'),'_blank');
    },
    additem:function(cell,row,sectionid){
        var url = '?app=page&controller=section&action=additem&sectionid='+sectionid+'&row='+cell.attr('row');
        ct.form('添加项', url, 530, 400, function(json){
            if (json.state)
            {
                cell.find('>ul').append(_newitem(json.data,sectionid));
        		return true;
            }
        },_prepareItem);
    },
    delrow:function(cell,row,sectionid){
        ct.confirm('此操作不可恢复，确认删除此行吗？',function(){
            var url = '?app=page&controller=section&action=delrow';
            var rowid = cell.attr('row');
            var data = 'sectionid='+sectionid+'&row='+rowid;
            $.post(url,data,function(json){
                if (json.state) {
                    var r = parseInt(rowid);
                    row.nextAll('tr').each(function(){
                        var cells = this.cells;
                        cells[1].setAttribute('row',r++);
                        cells[0].innerHTML = r;
                    });
                    row.remove();
                }
            }, 'json');
        });
    },
    downrow:function(cell,row,sectionid){
        var nexttr = row.next('tr');
        if (!nexttr.length) return;
        var url = '?app=page&controller=section&action=downrow';
        var rowid = cell.attr('row');
        var data = 'sectionid='+sectionid+'&row='+rowid;
        $.post(url, data, function(json){
            if (json.state) {
                // switch ul
                var ncell = $(nexttr[0].cells[1]);
                var ul = cell.find('>ul');
                cell.prepend(ncell.find('>ul'));
                ul.prependTo(ncell);
            }
        }, 'json');
    },
    uprow:function(cell,row,sectionid){
        var prevtr = row.prev('tr');
        if (!prevtr.length) return;
        var url = '?app=page&controller=section&action=uprow';
        var rowid = cell.attr('row');
        var data = 'sectionid='+sectionid+'&row='+rowid;
        $.post(url, data, function(json){
            if (json.state) {
                // switch ul
                var pcell = $(prevtr[0].cells[1]);
                var ul = cell.find('>ul');
                cell.prepend(pcell.find('>ul'));
                ul.prependTo(pcell);
            }
        }, 'json');
    },
    addrowafter:function(cell,row,sectionid){
    	handaction.addrow(
    		row.parents('table:first'),
    		parseInt(row.find('>td:eq(1)').attr('row'))+1, sectionid);
    },
    addrow:function(table, n, sectionid){
    	var tbody = table[0].tBodies[0];
        var l = tbody.rows.length;
        var url = '?app=page&controller=section&action=addrow';
        if (!n) {
        	n = 0;
        } else if (n == 'last') {
        	n = l;
        }
        var data = 'sectionid='+sectionid+'&pos='+n;
        $.post(url, data, function(json){
        	if (!json.state) {
        		ct.tips('添加行失败','error');
        		return;
        	}
        	n < l && $(tbody).find(n > 0 ? ('>tr:gt('+(n-1)+')') : '>tr').each(function(i){
    			$('td:eq(0)', this).html(n+i+2);
    			$('td:eq(1)', this).attr('row', n+i+1);
    		});
        	var tr = $('<tr>\
            	<td class="t_c">'+(n+1)+'</td>\
            	<td row="'+n+'">\
            		<ul class="inline w_120"></ul>\
            	</td>\
            	<td class="t_c">\
	                <img alt="增加" width="16" height="16" class="hand" action="additem" src="images/add_1.gif" />\
	                <img alt="上移" width="16" height="16" class="hand" action="uprow" src="images/up.gif" />\
	                <img alt="下移" width="16" height="16" class="hand" action="downrow" src="images/down.gif" />\
	                <img alt="删除" width="16" height="16" class="hand" action="delrow" src="images/del.gif" />\
            	</td>\
            </tr>');
        	n == 0 ? tr.prependTo(tbody) :
        		(n == l ? tr.appendTo(tbody) : $(tbody).find('>tr:eq('+(n-1)+')').after(tr));
        	_readyrow(tr, sectionid);
        },'json');
    }
};
var _visibleChilds = function(div)
{
	var el = div.firstChild;
	if (!el) return [];
	var elems = [];
	do {
		if ((el.nodeType == 3 && $.trim(el.nodeValue).length) ||
			 (el.nodeType == 1 && (el.offsetHeight || el.offsetWidth)))
		{
			elems.push(el);
		}
	} while (el = el.nextSibling);
	return $(elems);
};
var _moveNodes = function(src, target)
{
	var el = src[0].firstChild, temp;
	target = target.empty()[0];
	while (el) {
		temp = el;
		el = el.nextSibling;
		target.appendChild(temp);
	}
};
var _compareDim = function(el, dim)
{
	var offset = $(el).offset();
	if (offset.left <  dim.minL) {
		dim.minL = offset.left;
	}
	if (offset.top < dim.minT) {
		dim.minT = offset.top;
	}
	var r = offset.left + el.offsetWidth;
	var b = offset.top + el.offsetHeight;
	if (r > dim.maxR) {
		dim.maxR = r;
	}
	if (b > dim.maxB) {
		dim.maxB = b;
	}
};
var _descendants = function(elem, elems)
{
	elems.push(elem);
	if ($.css(elem, 'overflow') == 'visible')
	{
		$('>*:visible', elem).each(function(){
			_descendants(this, elems);
		});
	}
};
var _descendantDimensions = function(elem, dim)
{
	var elems = [];
	_descendants(elem, elems);
	$.each(elems,function(){
		_compareDim(this, dim);
	});
};
var _dimensions = function(w)
{
    var childs = _visibleChilds(w[0]);
	var H, W, dim = {
		minT : 99999,
		minL : 99999,
		maxR : 0,
		maxB : 0
	};
	if (childs.length) {
		childs.each(function(){
	    	if (this.nodeType==3) {
	    		// wrap textNode to comulate dimensions
	    		var p = document.createElement('xx');
	    		var o = this.parentNode;
	    		o.insertBefore(p, this);
	    		p.appendChild(this);
	    		_compareDim(p, dim);
	    		o.insertBefore(this, p);
	    		o.removeChild(p);
	    	} else {
	    		_descendantDimensions(this, dim);
	    	}
	    });
	    H = dim.maxB - dim.minT;
	    W = dim.maxR - dim.minL;
	} else {
		W = w.width();
		H = w.height();
		var offset = w.offset();
		dim.minL = offset.left;
		dim.minT = offset.top;
	}
    if (H < 20) {
    	H = 20;
    }
    if (W < 20) {
    	W = 20;
    }
    return {width:W,height:H,left:dim.minL,top:dim.minT};
}
var _resetOnePos = function() {
	var w = $(this), masker = w.data('masker'), dim = _dimensions(w);
	masker.css({
		width:dim.width,
	    height:dim.height,
	    left:dim.left,
	    top:dim.top
	});
};
var _resetPosition = function(){
	$('span.section').each(_resetOnePos);
};
var _prepareSection = function(){
    var w = $(this);
    var dim = _dimensions(w);
	var section_id = w.attr('id');
	var masker = $('<div class="section_marsker"></div>')
	.css({
	    width:dim.width,
	    height:dim.height,
	    position:'absolute',
	    left:dim.left,
	    top:dim.top,
	    opacity:.3
	}).hover(function(){
	    masker.addClass('hover');
	},function(){
	    masker.removeClass('hover');
	});
	w.data('masker', masker);
	masker.appendTo(document.body);
	var title = w.attr('title');
	w.hasClass('updated')
	    ? masker.addClass('red').attr('title',title+'  未生成到最新')
	    : masker.addClass('green').attr('title',title+'  已生成到最新');
	var type = w.attr('type').toLowerCase();
	if (type == 'feed' || type == 'rpc' || type == 'json')
    {
        masker.contextMenu('#section_menu_grap',
    	function(action, el, pos) {
    		sectionaction[action](w, masker, section_id);
    	});
    	masker.click(function(e){
    	    masker.trigger('contextMenu',[e]);
    	});
    }
    else
    {
        masker.contextMenu('#section_menu_html',
    	function(action, el, pos) {
    		sectionaction[action](w, masker, section_id);
    	});
    	var next_click = function(){
		    var url = '?app=page&controller=section&action=visual&sectionid='+section_id;
		    $.getJSON(url,function(json){
    	        masker.removeClass('hover');
			    if (!json.state)
			    {
			        ct.error(json.error);
			        return;
			    }
			    var t = '编辑区块:'+w.attr('title');
                var previewShow = function(callback) {
                    IN_PREVIEW = true;
                    var widget = dialog.parent(),
                        origin = {
                            width: widget.width(),
                            height: widget.height(),
                            left: widget.offset().left,
                            top: widget.offset().top
                        },
                        animate = {
                            width: 155,
                            height: 60,
                            left: origin.left + origin.width - 155,
                            top: origin.top + origin.height - 60
                        };
                    dialog.css('visibility', 'hidden');
                    widget.data('origin', origin).animate(animate, 'fast', function() {
                        dialog.hide();
                        widget.find('.btn_area button:eq(1)').text('返回');
                        widget.find('.pop_title').css('max-width', 110);
                        (ct.func(callback) || function() {})();
                    });
                };
                var previewRestore = function(options) {
                    options = options || {};
                    IN_PREVIEW = false;
                    var widget = dialog.parent(), origin = widget.data('origin'), win,
                        callback = function() {
                            dialog.css({visibility: 'visible'});
                            widget.find('.btn_area button:eq(1)').text('预览');
                            widget.find('.pop_title').css('max-width', 'none');
                        };
                    if (options.center) {
                        win = $(window);
                        origin.left = Math.floor((win.width() - origin.width) / 2);
                        origin.top = $(document).scrollTop() + Math.floor((win.height() - origin.height) / 2);
                    }
                    widget.removeData('origin');
                    dialog.show();
                    if (options.unsave) {
                        exitEditing();
                    }
                    if (options.close) {
                        widget.hide().css(origin);
                        callback();
                    } else {
                        widget.show().animate(origin, 'fast', function() {
                            callback();
                        });
                    }
                };
			    if (!dialog)
    			{
    			    dialog = $('<div/>');
    			    dialog.dialog({
                        autoOpen:true,
            			bgiframe: true,
            			width : 600,
            			height: 420,
            			modal : false,
            			title : t,
            			buttons : {'确定':function(){}},
            			beforeclose: function(){
            			    if (noconfirm)
            			    {
            			        noconfirm = 0;
            			        return true;
            			    }
            			    return window.confirm('退出当前编辑吗？');
            			},
            			close: function(){
            			    noconfirm = 0;
                            IN_PREVIEW && previewRestore({center: true, close: true});
                            exitEditing();
            			},open:function(){

            			}
            		});
    			}
    			else
    			{
                    if (IN_PREVIEW) {
                        previewRestore({center: true, unsave: editid !== section_id});
                    } else {
                        dialog.dialog('option', 'position', 'center');
                    }
    				dialog.prev().find('>:first').text(t);
    			}
                setEditing(section_id);
    			dialog.html(json.html||'');
			    var buttons;
			    var overlay = $('<div style="background:#44DAF4;z-index:2;position:absolute;display:none;width:100%;height:100%"/>')
                              .css('opacity',.4);
                dialog.prepend(overlay);
                var save = function(data, after, preview) {
                    overlay.show();
			        dialog.nextAll('div.btn_area').children('button').attr('disabled','disabled');
			        $.ajax({
			            url:url,
			            data:data,
			            dataType:'json',
			            type:'POST',
			            success:function(res){
			                if (res.state)
			                {
			                    if (preview && !w.data('previewing'))
			                    {
			                    	_moveNodes(w, fragment);
			                    	preview_w = w;
			                    	w.data('previewing', 1);
			                    	w.data('className', masker[0].className);
			                    	w.data('title', masker.attr('title'));
			                    }
			                    w.html(res.html||'');
			                    _resetPosition();
			                    after(dialog);
			                    if (! preview)
			                    {
			                    	fragment.empty();
			                    	w.data('previewing', 0);
			                    	if (preview_w == w) {
			                    		preview_w = null;
			                    	}
			                    	noconfirm = 1;
			                    	dialog.dialog('close');
			                    }
			                }
			                else
			                {
			                    var info = $('<div class="error"><sub></sub>'+res.error+'</div>').prependTo(dialog);
			                    setTimeout(function(){info && info.hide()},2000);
			                }
			            },complete:function(){
			                overlay.hide();
			                dialog.nextAll('div.btn_area').children('button').removeAttr('disabled');
			            }
			        });
                };
			    if (type == 'html' || type == 'auto') {
			        editEl = dialog.find('textarea#data');
        		    buttons = {
        			    '保存':function(){
        			    	var data = 'do=save&data='+encodeURIComponent(editEl.val());
        			    	var time_publish = dialog.find('input[name=commit_publish]')[0].checked;
        			    	if (time_publish)
        			    	{
        			    		data += '&nextupdate='
        			    			+encodeURIComponent(dialog.find('input[name=nextupdate]').val());
        			    	}
        			        save(data,function(){
        			        	if (time_publish)
        			        	{
	        			            masker.removeClass('green').addClass('red');
	        			            masker.attr('title',w.attr('title')+' 未生成到最新');
        			        	}
        			        	else
        			        	{
        			        		masker.removeClass('red').addClass('green');
        			            	masker.attr('title',w.attr('title')+' 已生成到最新');
        			        	}
                                IN_PREVIEW && previewRestore({center: true, close: true});
        			        });
        			    },
        		    	'预览':function(){
                            if (IN_PREVIEW) {
                                previewRestore();
                            } else {
                                previewShow(function() {
                                    save('do=preview&data='+encodeURIComponent(editEl.val()),function(){
                                        masker.removeClass('green').addClass('red');
                                        masker.attr('title',w.attr('title')+' 预览中');
                                    },1);
                                });
                            }
        		    	},
        			    '取消':function(){
        			        noconfirm = 1;
                            // IN_PREVIEW && previewRestore({center: true, close: true});
        			        dialog.dialog('close');
        			    }
        			};
			    } else if (json.type == 'hand') {
			    	editEl = null;
			        var table = dialog.find('table.table_info');
    			    _scantable(table,section_id);
    			    dialog.find('input[name=addrow]').click(function(){
    			        handaction.addrow(table, this.getAttribute('pos'), section_id);
    			    });
    			    buttons = {
        			    '保存':function(){
        			    	var data = 'do=save';
        			    	var time_publish = dialog.find('input[name=commit_publish]')[0].checked;
        			    	if (time_publish)
        			    	{
        			    		data += '&nextupdate='
        			    			+encodeURIComponent(dialog.find('input[name=nextupdate]').val());
        			    	}
        			        save(data,function(){
        			        	if (time_publish)
        			        	{
	        			            masker.removeClass('green').addClass('red');
	        			            masker.attr('title',w.attr('title')+' 未生成到最新');
        			        	}
        			        	else
        			        	{
        			        		masker.removeClass('red').addClass('green');
        			            	masker.attr('title',w.attr('title')+' 已生成到最新');
        			        	}
        			        });
                            IN_PREVIEW && previewRestore({center: true, close: true});
        			    },
    			    	'预览':function(){
                            if (IN_PREVIEW) {
                                previewRestore();
                            } else {
                                previewShow(function() {
                                    save('do=preview',function(){
                                        masker.removeClass('green').addClass('red');
                                        masker.attr('title',w.attr('title')+' 预览中');
                                    },1);
                                });
                            }
    			    	},
        			    '取消':function(){
        			        noconfirm = 1;
                            // IN_PREVIEW && previewRestore({center: true, close: true});
        			        dialog.dialog('close');
        			    }
        			};
			    } else { editEl = null;}
    			dialog.dialog('option','buttons',buttons).dialog('open');
    			if (editEl && editEl.is(':visible')) {
    				editEl.editplus({
    					buttons: (type=='auto' ? 'fullscreen,wrap,|,db,content,discuz,phpwind,shopex,|,loop,ifelse,elseif,|,preview' : 'fullscreen,wrap,visual'),
    					width:560,
    					height:300
    				});
    			}
			});
		};
    	masker.click(function(){
    	    (!editmode || window.confirm('退出当前编辑吗？')) && next_click();
    	});
    }
};
window.init = function(){
    $('span.section').each(_prepareSection);
    window.onresize = _resetPosition;
    fragment = $('<div/>');
};
})();