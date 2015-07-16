var page = function(){
//-----------------------------------------------
var viewBox = null;
var sectionList = null, sectionBox = null;
var pageid = null;
var cur_view_id;
var editmode = 0;
var VIEW_PAGEPROPERTY = 1;
var VIEW_SECTION = 2;
var viewWhat = null;
var editUrl = '?app=page&controller=section&action=edit';
var editEl = null;
var ivalstate = null;
var clearIstate = function(){
	ivalstate && clearTimeout(ivalstate);
};
var ivallock = null;
var clearIlock = function(){
    ivallock && clearInterval(ivallock);
};
function innerWidth(){
	return document.documentElement.clientWidth;
}
function innerHeight(){
	return document.documentElement.clientHeight;
}
function floatBox(html, pos, afterHtml) {
	$('div.floatbox').remove();
	var div = $('<div class="floatbox" style="position:fixed;visibility:hidden"></div>')
		.appendTo(document.body);
	var img = $('<img src="images/close.gif" />').css({
		position:'absolute',
		top:1,
		right:2,
		cursor:'pointer'
	}).click(function(){
		div.remove();
	});
	
	div.html(img).append(html);
	typeof afterHtml == 'function' && afterHtml(div);
	var doc = $(document);
	var style = cmstop.pos(pos, div.outerWidth(true), div.outerHeight(true));
	style.visibility = 'visible';
	div.css(style);
	return div;
}
var setEditing = function(){
    editmode = 1;
    clearIlock();
    ivallock = setInterval(function(){
        if (!editmode) {
            clearIlock();
            return;
        }
        var id = cur_view_id;
        ct.stopListenOnce();
        $.post('?app=page&controller=section&action=lock','sectionid='+id,
        function(json){
        	if (!json.state) {
        		clearIlock();
        		ct.confirm(json.error+'，留在此页吗？',function(){

        		},function(){
        			s.unsave(id);
        		});
        	}
        },'json');
    }, 15000);
};
var exitEditing = function(){
    editmode = 0;
    clearIlock();
};
var section_state_lock = false;
var section_state = function(callback){
	if (section_state_lock) return;
	var origLiset = sectionList.find('>li');
	ct.stopListenOnce();
	section_state_lock = true;
	clearIstate();
    $.ajax({
    	url:'?app=page&controller=page&action=sections&pageid='+pageid,
    	dataType:'json',
    	success:function(json){
    		for (var i=0,l;l=json[i++];) {
				_build_li_section(l,true);
			}
			if (cur_view_id == undefined) {
				id	= sectionList.find('li').eq(0).attr('id');
				if (id != undefined) {
					id	= id.split('_')[1];
					page.viewSection(id);
				}
			}
			origLiset.remove();
			sectionList.find('>li').show();
			if (cur_view_id) {
				var cLi = $('#section_'+cur_view_id);
				if (!cLi.length)
				{
					//避免跨pageid搜索无法获得cLi
					$.getJSON('?app=page&controller=section&action=view&sectionid='+cur_view_id, null, function(json) {
						if (!json.state) {
							cur_view_id = null;
							ct.confirm('您当前查看的区块已被删除，继续留在此页？',null,goFirstSection);
							return;
						} else {
							location.href	= "?app=page&controller=page&action=view&pageid="+json.pageid+"&sectionid="+cur_view_id;
						}
					});
				} else {
					cLi.addClass('active');
				}
			}
			section_state_lock = false;
    		typeof callback=='function' && callback();
    		ivalstate = setTimeout(section_state,60000);
    	},error:function(){
    		section_state_lock = false;
    		typeof callback=='function' && callback();
    		ivalstate = setTimeout(section_state,60000);
    	}
    });
};
var _build_li_section = function(item,hidden){
    var li = $('<li id="section_'+item.sectionid
        +'" class="section-item '+item.type
        +'" title="'+item.name
        +'"><div title="操作"></div><a href="">'+item.name+'</a></li>');
    var a = li.find('a').click(function(){
    	s.clickSection(item.sectionid);
    	return false;
    });
    li.find('div').click(function(e){
    	li.trigger('contextMenu',[e]);
    	return false;
    });
    li.click(function(){
    	s.clickSection(item.sectionid);
    }).hover(function(){
    	li.addClass('hover');
    },function(){
    	li.removeClass('hover');
    });
    item.locked && (a.addClass('locked'), a.attr('title',item.lockedby+' 编辑中'));
    ct.IE7 && a.focus(function(){this.blur();});
    li.contextMenu('#section_menu', function(action) {
	    sectionaction[action](item.sectionid,li);
	});
	hidden && li.hide();
    sectionList.append(li);
};
var _add_form_ready = function(form,dialog) {
	$(form[0].data).editplus({
		buttons:'fullscreen,wrap',
        height:150
	});
	dialog.find('input[name=type]:not(:checked)').click(function(){
		var f = form[0], n = f.name.value, w = f.width.value, c = f.description.value, t = this.value;

		dialog.load('?app=page&controller=section&action=add&pageid='+pageid+'&type='+t,
        function(){
            dialog.trigger('ajaxload');
            var frm = dialog.find('form:eq(0)'), _f = frm[0];
            _f.name.value = n;
            _f.width.value = w;
            _f.description.value = c;
        });
	});
};

var _readyrow = function(row){
    var cells = row[0].cells;
    var c1 = $(cells[1]);
    c1.find('li').each(function(){
        var li = $(this).contextMenu('#section_item_menu',
		function(action) {
		    handaction[action](li,c1);
		});
		li.find('a').click(function(e){
			li.triggerHandler('contextMenu', [e]);
			return false;
		}).attrTips('tips',null,350);
    });
    c1.contextMenu('#section_cell_menu',
	function(action, el, pos) {
		handaction[action](c1,row);
	});
    $('>img',cells[2]).click(function(){
        handaction[this.getAttribute('action')](c1,row);
    });
	// 通过拖动的方法排序
	$('tbody#sortable').sortable({
		"handle":"td:first-child",
		"start" : function(e) {
			var dragObj = $(e.originalTarget || e.srcElement);	// 解决不同浏览器e对象不同的问题
			dragObj = dragObj.is('tr') ? dragObj : dragObj.parent('tr');
			if (ct.IE) {
				dragObj.css('margin-left', '-1px').css('background', '#D0E6EC');
			}
			dragObj.css('cursor', 'move').find('td').css('border-top', '1px solid #D0E6EC').eq(1).width($(e.target).width() - 183);
			$('.ui-sortable-placeholder').next('tr').find('td').css('border-top', '1px solid #D0E6EC');
		},
		"stop" : function(e,u) {
			var rid	= u.item.children().eq(0).html();
			var dragObj = $(e.originalTarget).is('tr') ? $(e.originalTarget) : $(e.originalTarget).parent('tr');
			dragObj.find('td').css('border-top', 'none').eq(1).attr('width','');
			dragObj.next('tr').css('border-top', 'none');
			$.each($(this).find("tr"), function(i, tr) {
				if ($(tr).children().eq(0).html() == rid) {
					var c	= rid - i - 1;
					if (c > 0) {
						$.post("?app=page&controller=section&action=uprow", {"row" : rid-1,"sectionid" : cur_view_id, "num":c});
					} else if (c < 0) {
						$.post("?app=page&controller=section&action=downrow", {"row" : rid-1,"sectionid" : cur_view_id, "num":-c});
					}
				}
				$(tr).children().eq(0).html(i+1);
				$(tr).children().eq(1).attr("row", i);
				$(e.target).find('tr').css('cursor', 'auto').not('.ui-sortable-helper').find('td').css('border-top', 'none');
			});
		},
		"change" : function(e,u) {
			var dragObj = $(e.originalTarget).is('tr') ? $(e.originalTarget) : $(e.originalTarget).parent('tr');
			$(e.target).find('tr').not('.ui-sortable-helper').find('td').css('border-top', 'none');
			$('.ui-sortable-placeholder').next('tr').find('td').css('border-top', '1px solid #D0E6EC');
		},
		"axis" : 'y'
	});
	$('tbody').disableSelection();
};
var _scantable = function(table){
    var rows = $(table[0].rows).filter(':gt(0)');
    rows.each(function(){
        _readyrow($(this));
    });
};
var goFirstSection = function(){
	var a = sectionList.find('>li:first>a:first');
	a.length ? a.click() : (viewBox.empty(),(viewWhat=VIEW_SECTION));
};
var resetSectionList = function(){
	sectionList.find('>li').each(function(){
		$.className.remove(this, 'active');
	});
	cur_view_id = null;
};
var s = {
	grapSection:function(id) {
        id || (id = cur_view_id);
        $.post('?app=page&controller=section&action=grap','sectionid='+id,
        function(json){
            if (json.state)
            {
                ct.ok(json.info);
                s.viewSection(id);
            }
            else
            {
                ct.error(json.error);
            }
        },'json');
    }, editSection:function(id) {
        id || (id = cur_view_id);
        var url = editUrl+'&sectionid='+id;
        $.getJSON(url, function(json){
    		if(json.state){
    			setEditing();
    			viewBox.html(json.html);
                var frm = viewBox.find('form');
    			if (json.type == 'html' || json.type=='auto') {
        			editEl = $('#data');
        			if (editEl.length) {
        				editEl.editplus({
        					buttons: (json.type=='auto' ? 'fullscreen,wrap,|,db,content,discuz,phpwind,shopex,|,loop,ifelse,elseif,|,preview' : 'fullscreen,wrap,visual'),
        					width:(editEl.parent().innerWidth() - 22) || 750,
        					height:300
        				});
        			}
    			} else if (json.type == 'hand') {
    			    var table = viewBox.find('table.table_info:first');
    			    _scantable(table);
    			    viewBox.find('input[name=addrow]').click(function(){
    			        handaction.addrow(table, this.getAttribute('pos'));
    			    });
    			}
				var dp = new DatePanel({'place':viewBox.find('div.calendar')[0],'format':'yyyy-MM-dd'});
				dp.bind('DATE_CLICKED', function() {
					viewBox.find('div.logtable').load('?app=page&controller=section&action=logpack&sectionid='+id+'&d='+encodeURIComponent(dp.format()));
				});
    			frm.ajaxForm(function(json){
                	if (json.state) {
                		ct.confirm(json.info+'，继续留在此页？',null,
                		function(){
                			s.unsave(id);
                		});
                	} else {
                		ct.error(json.error);
                	}
                });
                section_state();
    		} else {
    			ct.warn(json.error);
    		}
    	});
    }, previewSection:function(form, presave) {
    	var id = form.sectionid.value || cur_view_id;
    	var url = '?app=page&controller=section&action=preview&pageid='+pageid+'&sectionid='+id+'&gen='+Math.random();
    	if (presave) {
    		$.post(url,'data='+encodeURIComponent(form.data.value),function(json){
    			if (json.state)
    			{
    				window.open(url+'#'+id, 'previewsection_'+id);
    			}
    			else
    			{
    				ct.warn('无预览');
    			}
    		},'json');
    	} else {
    		window.open(url+'#'+id, 'previewsection_'+id);
    	}
    }, templateSelect:function() {
        ct.ajax('选择模板', '?app=system&controller=template&action=select');
    }, visualEdit:function(id) {
		id == undefined && (id = pageid);
		window.open('?app=page&controller=page&action=visualedit&pageid='+id, 'view_edit');
	}, publish:function(id) {
        $.post('?app=page&controller=section&action=publish','sectionid='+id,
        function(json){
            if (json.state) {
                ct.tips(json.info,'success');
                if (id == cur_view_id && !editmode) {
        			s.viewSection(id);
        		}
            } else {
                ct.error(json.error);
            }
        },'json');
    }, publishPage:function(id) {
    	id == undefined && (id=pageid);
    	$.post('?app=page&controller=page&action=publish','pageid='+id,
		function(json) {
			if (json.state) {
				ct.ok(json.info);
			} else {
				ct.error(json.error);
			}
		},'json');
    }, pageSetting:function(id) {
    	id == undefined && (id=pageid);
        ct.form('编辑页面属性','?app=page&controller=page&action=edit&pageid='+id,
    	   380, 290,
    	function(json) {
    	    if (json.state) {
    	        ct.tips(json.info, 'success');
    	        s.loadPage(id);
    	        return true;
    	    }
    	});
    }, templateEdit:function() {
    	ct.assoc.open('?app=system&controller=template&action=edit&pageid='+pageid,'newtab');
    }, addPage:function() {
    	ct.form('添加页面', '?app=page&controller=page&action=add&parentid='+pageid, 400, 230,
        function(json){
    	    if (json.state)
    	    {
        		ct.assoc.call('refresh', json.path.split(','), true);
        	    return true;
        	}
    	});
    }, addRootPage:function() {
    	ct.form('添加页面', '?app=page&controller=page&action=add&parentid=0', 400, 230,
        function(json) {
    	    if (json.state) {
        		ct.assoc.call('refresh', json.path.split(','), true);
        	    return true;
        	}
    	});
    }, delPage:function(id){
    	id == undefined && (id=pageid);
        ct.confirm('此操作不可恢复，确认删除此页面吗？',function(){
    	    $.post('?app=page&controller=page&action=delete', 'pageid='+id,
    		function(json){
    			if(json.state){
    				ct.assoc.call('refresh', json.path.split(','), true);
    				ct.ok('页面已删除');
    			}else{
    				ct.error(json.error);
    			}
    		},'json');
    	});
    }, pageProperty:function(){
        var next = function(){
			$.get('?app=page&controller=page&action=property&pageid='+pageid, function(html) {
                if (ct.detectLoadError(html)) return false;
				viewBox.empty().append(html);
                resetSectionList();
                var ctrla = viewBox.find('.days>a');
                ctrla.click(function(){
                	var log_from = $(this).attr('from');
                	var cur_a = $(this);
                	$.getJSON('?app=page&controller=page&action=sectionlog', 'from='+log_from+'&pageid='+pageid,
                	function(json){
                		if(json.state){
                			ctrla.removeClass('s_5');
                			cur_a.addClass('s_5');

                			$('#logs').html(json.html);
                		}
                	});
                	return false;
                });
            });
            viewWhat = VIEW_PAGEPROPERTY;
        };
        if (editmode) {
            ct.confirm('退出当前编辑？',function(){
                exitEditing();
                $.post('?app=page&controller=section&action=unsave', 'sectionid='+cur_view_id,
                function(){
                    section_state();
                });
                next();
            });
        } else {
            next();
        }
    }, viewSection:function(id){
        cur_view_id = id;
        exitEditing();
        var url = '?app=page&controller=section&action=view&sectionid='+id;
		$.get(url, null, function(json) {
			if (json.state) {
				viewBox.empty().append(json.data);
				sectionList.find('>li.active').removeClass('active');
				var li = $('#section_'+id);
				li.addClass('active');
				var offset = 0;
				li.prevAll('li').each(function(){
					offset += $(this).outerHeight(true);
				});
				if ((offset + 5 + li.outerHeight(true) - sectionBox.scrollTop()) > sectionBox.innerHeight())
				{
					sectionBox.scrollTop(offset);
				} else if(offset < sectionBox.scrollTop()) {
					sectionBox.scrollTop(offset);
				}
				$('#html_content').load('?app=page&controller=section&action=loadViewHtml&sectionid='+id);
				$('#pagetab>li:eq(1)').click();
			} else {
				viewBox.empty().append(json.error);
			}
    	}, 'json');
		section_state();
    	viewWhat = VIEW_SECTION;
    }, unlock:function(id,btn){
    	ct.confirm('解锁会导致他人正在编辑的区块无法保存，确实解锁？',function(){
	        $.post('?app=page&controller=section&action=unlock','sectionid='+id,
	        function(json){
	            if (json.state)
	            {
	                $(btn).remove();
	                section_state();
	            }
	        },'json');
    	});
    }, addSection:function() {
        ct.form('添加区块','?app=page&controller=section&action=add&pageid='+pageid,510,430,
        function(json){
            if (json.state) {
                if (!section_state_lock) {
                	_build_li_section(json.data);
                	s.clickSection(json.data.sectionid);
                }
                return true;
            }
        }, _add_form_ready);
    }, clickSection:function(id) {
    	if (viewWhat == VIEW_SECTION) {
    		if (cur_view_id == id) {
    			return;
    		}

	        if (editmode) {
	            ct.confirm('退出当前编辑？',function(){
	                $.post('?app=page&controller=section&action=unsave', 'sectionid='+cur_view_id,
	                function(){
	                    section_state();
	                });
	                s.viewSection(id);
	            });
	        } else {
	            s.viewSection(id);
	        }
    	} else {
    		s.viewSection(id);
    	}
    }, unsave:function(id) {
        id || (id = cur_view_id);
        $.post('?app=page&controller=section&action=unsave', 'sectionid='+id, section_state);
    	s.viewSection(id);
	}, setProperty:function(id) {
        id || (id = cur_view_id);
        var li = $('#section_'+id);
        var h = (li.hasClass('auto') || li.hasClass('html')) ? 230 : 400;
        var url = '?app=page&controller=section&action=property&sectionid='+id;
        ct.form('设置区块属性', url, 450, h, function(json){
        	if (json.state) {
        		if (id == cur_view_id && !editmode) {
        			s.viewSection(id);
        		}
        		section_state();
        		return true;
        	}
        },function(form){
        	form[0].data && $(form[0].data).editplus({
        		buttons:'fullscreen,wrap',
        		height:150
        	});
        });
    },
    viewLog:function(id) {
        $("#viewholder").load('?app=page&controller=section&action=viewlog&logid='+id);
    },
    clearLog:function() {
    	var sid = cur_view_id;
    	sid && ct.confirm('此操作不可恢复，确定要清空吗？',function(){
            $.getJSON('?app=page&controller=section&action=clearlog&sectionid='+sid,
            function(json) {
                if (json.state) {
                    ct.ok("清空完毕");
                    viewBox.find('div.logtable').load('?app=page&controller=section&action=logpack&sectionid='+sid);
                } else {
                    ct.error("清空失败");
                }
            },'json');
        });
    },
    restoreLog:function(id) {
        var sid = cur_view_id;
        var data = id=='orig' ? ('logid=orig&sectionid='+sid) : ('logid='+id);
        ct.confirm('确定恢复吗？',function() {
            $.post('?app=page&controller=section&action=restorelog',data,
            function(json) {
                if (json.state) {
                    ct.ok(json.info);
                    s.editSection(sid);
                } else {
                    ct.error(json.error);
                }
            },'json');
        });
    },
    getLog:function(id) {
        $.getJSON('?app=page&controller=section&action=getlog&logid='+id,
        function(json) {
            if (json.state) {
                editEl.val(json.data);
            }
        });
    }, init:function(id) {
    	var m = /&sectionid=(\d+)/.exec(location.search);
    	var sectionid = m && m[1];
    	var editTemplate = /&editTemplate=1/.test(location.search);
        viewBox = $("#viewBox");
        sectionList = $("#sectionList");
        var sectionPanel = $('#sectionPanel');
        sectionBox = $('#sectionBox');
        var bodyContainer = $('#bodyContainer');
        var panelWidth = sectionPanel.outerWidth(true) + 8;
        var adapt = function(){
        	viewBox.css('width', innerWidth() - panelWidth);
        	var h = innerHeight() - bodyContainer.offset().top - 1;
        	bodyContainer.css('height', h);
            sectionPanel.css('height', h);
        	sectionBox.css('height', sectionPanel.innerHeight() - 24);
        };
        adapt();
        window.onresize = adapt;
        $('#pagetab').tabnav({
			dataType:null,
			forceFocus:true,
			focused:function(li){
				pagetabfunc[li.attr('func')](li);
			}
		});
		id && s.loadPage(id, sectionid, editTemplate);
        $(window).bind('unload',function(){
            clearIstate();
            clearIlock();
        });
    }, loadPage:function(id, sectionid, editTemplate){
    	var next = function(){
	        clearIstate();
	        // init var
	        pageid = id;
	        cur_view_id = null;
			editmode = 0;
			viewWhat = null;
			editEl = null;
			section_state(function(){
				$('#pagetab>li:last').click();
				if (editTemplate) {
					sectionList.find('#section_'+sectionid).hasClass('hand')
						? page.setProperty(sectionid)
						: page.editSection(sectionid);
				} else {
					page.viewSection(sectionid);
				}
			});
    	};
    	if (editmode) {
            ct.confirm('退出当前编辑？',function(){
                exitEditing();
                $.post('?app=page&controller=section&action=unsave', 'sectionid='+cur_view_id);
                next();
            });
        } else {
            next();
        }
    }, searchSection:function(o) {
    	var div = floatBox('\
    	<input type="text" class="bdr_6" size="30"\
    		url="?app=page&controller=section&action=searchall&pageid='+pageid+'&keyword=%s"\
    	/>', o,
    	function(box){
    		box.find('input').autocomplete({
    			itemSelected:function(a, item){
    				s.clickSection(item.sectionid);
    				div.remove();
    			}, itemPrepared:function(a, item){
    				a.addClass('section-item '+item.type).css('padding-left', 16);
    			}
    		});
    	});
    }
};
var pagetabfunc = {
	property:function(){
		pageid && viewWhat != VIEW_PAGEPROPERTY && s.pageProperty();
	},
	section:function(){
		viewWhat != VIEW_SECTION && goFirstSection();
	}
};

var searchOption = {
    template : '\
		<tr id="search_{contentid}">\
	        <td class="t_c"><input name="hand_new_item" class="radio_style" type="radio" value="{contentid}"></td>\
	    	<td class="t_l"><a href="{url}" thumb="{thumb}" target="_blank">{title}</a></td>\
	    	<td class="t_c"><a href="javascript:url.member({createdby});">{username}</a></td>\
	    	<td class="t_r">{created}</td>\
		</tr>',
    baseUrl  : '?app=page&controller=content&action=search'
};
var recommendOption = {
	template : '\
		<tr id="recommend_{recommendid}">\
	        <td class="t_c"><input name="hand_new_item" class="radio_style" type="radio" value="{recommendid}" /></td>\
	        <td><a href="{url}" thumb="{thumb}" target="_blank">{title}</a></td>\
	        <td class="t_c"><a href="javascript:url.member({createdby});">{username}</a></td>\
	        <td class="t_r">{recommended}</td>\
	        <td class="t_c"><img width="16" height="16" class="hand del" alt="删除" src="images/del.gif"></td>\
	    </tr>',
    baseUrl  : '?app=page&controller=content&action=recommend'
};
var _bind_item_form_event = function(form, dialog) {
	var title = form.find('input[name=title]').maxLength();
    var colorPicker = title.nextAll('img');
    colorPicker.titleColorPicker(title, colorPicker.next('input'));

    dialog.find('form input[name="thumb"]').imageInput();
};
var _prepareItem = function(form, dialog) {
    var div = dialog.find('div.part');

    new scrollTable($('>table',div[0]),$.extend({
    	rowChecked:function(tr, json){
			form[0].title.value = json.title;
            $(form[0].title).keyup();
            form[0].url.value = json.url;
            if (json.thumb && json.thumb != 'null') {
            	form[0].thumb.value = json.thumb;
            }
            var ck = $(form[0].title).nextAll('input:checkbox');
            if (json.subtitle) {
            	ck[0].checked || ck.click();
            	form[0].subtitle.value = json.subtitle;
            } else {
            	ck[0].checked && ck.click();
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
    }, recommendOption)).load('sectionid='+cur_view_id);

    var searchTable = new scrollTable($('>table', div[1]),$.extend({
        rowChecked:function(tr, json){
        	form[0].title.value = json.title;
            $(form[0].title).keyup();
            form[0].url.value = json.url;
			form[0].contentid.value = json.contentid;
            if (json.thumb && json.thumb != 'null') {
            	form[0].thumb.value = json.thumb;
            }
            var ck = $(form[0].title).nextAll('input:checkbox');
            if (json.subtitle) {
            	ck[0].checked || ck.click();
            	form[0].subtitle.value = json.subtitle;
            } else {
            	ck[0].checked && ck.click();
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

    _bind_item_form_event(form, dialog);

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
function _newitem(item) {
	var li = $('<li url="'+item.url+'" col="'+item.col+'"><a href="" target="_blank">'+item.title+'</a></li>');
	var a = li.find('a').click(function(e){
		li.trigger('contextMenu',[e]);
		return false;
	});
	item.color && a.css('color', item.color);
	a.attr('tips',item.tips);
	a.attrTips('tips',null,350);
	li.contextMenu('#section_item_menu',
	function(action, el, pos) {
	    handaction[action](li);
	});
	return li;
}
function _itemattr(li,a,item) {
	// edit a title,text,url
    a.text(item.title||'')
     .attr('tips', item.tips);
    item.color && a.css('color', item.color);
    // edit li url
    li.attr('url', item.url);
}
var itemMoveLock = false;
var handaction = {
    edititem:function(li){
        var row = li.parents('td').attr('row');
        var col = li.attr('col');
        var url = '?app=page&controller=section&action=edititem&sectionid='+cur_view_id+'&row='+row+'&col='+col;
        var a = li.find('a');
        ct.form('编辑条目：'+a.text(),url,530,380,function(json){
            if (json.state) {
            	_itemattr(li,a,json.data);
                return true;
            }
        },_bind_item_form_event);
    },
    delitem:function(li){
        ct.confirm('确定删除项“<b style="color:red">'+li.text()+'</b>”？',
        function(){
            var row = li.parents('td').attr('row');
            var col = li.attr('col');
            var url = '?app=page&controller=section&action=delitem';
            var data = 'sectionid='+cur_view_id+'&row='+row+'&col='+col;
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
    replaceitem:function(li){
        var row = li.parents('td').attr('row');
        var col = li.attr('col');
        var url = '?app=page&controller=section&action=replaceitem&sectionid='+cur_view_id+'&row='+row+'&col='+col;
        var a = li.find('a');
        ct.form('替换条目：'+a.text(),url,530,400,function(json){
            if (json.state) {
            	_itemattr(li,a,json.data);
                return true;
            }
        },_prepareItem);
    },
    moveitemleft:function(li){
        if (itemMoveLock) return;
        var prevli = li.prev('li');
        if (!prevli.length) return;
        var row = li.parents('td').attr('row');
        var col = li.attr('col');
        var url = '?app=page&controller=section&action=leftitem';
        var data = 'sectionid='+cur_view_id+'&row='+row+'&col='+col;
        itemMoveLock = true;
        $.post(url, data, function(json){
			if (json.state) {
				li.attr('col', prevli.attr('col'));
				prevli.attr('col', col);
				li.after(prevli);
			}
            itemMoveLock = false;
		},'json');
    },
    moveitemright:function(li){
        if (itemMoveLock) return;
        var nextli = li.next('li');
        if (!nextli.length) return;
        var row = li.parents('td').attr('row');
        var col = li.attr('col');
        var url = '?app=page&controller=section&action=rightitem';
        var data = 'sectionid='+cur_view_id+'&row='+row+'&col='+col;
        itemMoveLock = true;
        $.post(url, data, function(json){
			if (json.state) {
				li.attr('col', nextli.attr('col'));
				nextli.attr('col', col);
				li.before(nextli);
			}
            itemMoveLock = false;
		},'json');
    },
    viewitem:function(li){
        window.open(li.attr('url'),'_blank');
    },
    additem:function(cell){
        var url = '?app=page&controller=section&action=additem&sectionid='+cur_view_id+'&row='+cell.attr('row');
        ct.form('添加项', url, 560, 400, function(json){
            if (json.state) {
            	cell.find('>ul').append(_newitem(json.data));
        		return true;
            }
        }, _prepareItem);
    },
    delrow:function(cell,row){
        ct.confirm('此操作不可恢复，确认删除此行吗？',function(){
            var url = '?app=page&controller=section&action=delrow';
            var rowid = cell.attr('row');
            var data = 'sectionid='+cur_view_id+'&row='+rowid;
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
    downrow:function(cell,row){
        if (itemMoveLock) return;
        var nexttr = row.next('tr');
        if (!nexttr.length) return;
        var url = '?app=page&controller=section&action=downrow';
        var rowid = cell.attr('row');
        var data = 'sectionid='+cur_view_id+'&row='+rowid;
        itemMoveLock = true;
        $.post(url, data, function(json){
            if (json.state) {
                // switch ul
                var ncell = $(nexttr[0].cells[1]);
                var ul = cell.find('>ul');
                cell.prepend(ncell.find('>ul'));
                ul.prependTo(ncell);
            }
            itemMoveLock = false;
        }, 'json');
    },
    uprow:function(cell,row){
        if (itemMoveLock) return;
        var prevtr = row.prev('tr');
        if (!prevtr.length) return;
        var url = '?app=page&controller=section&action=uprow';
        var rowid = cell.attr('row');
        var data = 'sectionid='+cur_view_id+'&row='+rowid;
        itemMoveLock = true;
        $.post(url, data, function(json){
            if (json.state) {
                // switch ul
                var pcell = $(prevtr[0].cells[1]);
                var ul = cell.find('>ul');
                cell.prepend(pcell.find('>ul'));
                ul.prependTo(pcell);
            }
            itemMoveLock = false;
        }, 'json');
    },
    addrowafter:function(cell,row){
    	handaction.addrow(
    		row.parents('table:first'),
    		parseInt(row.find('>td:eq(1)').attr('row'))+1);
    },
    addrow:function(table, n){
        var tbody = table[0].tBodies[0];
        var l = tbody.rows.length;
        var url = '?app=page&controller=section&action=addrow';
        if (!n) {
        	n = 0;
        } else if (n == 'last') {
        	n = l;
        }
        var data = 'sectionid='+cur_view_id+'&pos='+n;
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
        	_readyrow(tr);
        	handaction.additem($(tr[0].cells[1]));
        },'json');
    }
};

var sectionaction = {
    del:function(id,li){
        ct.confirm('确定删除区块"<b style="color:red">'+li.text()+'</b>"吗？删除后模板中对应的调用代码请手工删除',function(){
            $.post('?app=page&controller=section&action=delete','sectionid='+id,
            function(json){
                if (json.state)
                {
                    li.remove();
                    viewWhat == VIEW_SECTION && goFirstSection();
                }
                else
                {
                    ct.error(json.error);
                }
            },'json');
        });
    },
    property:function(id){
        s.setProperty(id);
    },
    move:function(id,li){
    	ct.form('移动区块','?app=page&controller=section&action=move&sectionid='+id,
    	250,300,function(json){
    		if (json.state)
            {
                li.remove();
                viewWhat == VIEW_SECTION && goFirstSection();
                ct.tips(json.info,'success');
            }
            else
            {
                ct.tips(json.error,'error');
            }
            return true;
    	});
    }
};
//-----------------------------------------------
return s;
}();