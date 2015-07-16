(function($){
function innerWidth(){
	return document.documentElement.clientWidth;
}
function innerHeight(){
	return document.documentElement.clientHeight;
}
var box, leftBox, rightBox, centerBox,
	leftList, centerList, rightList,
	
	BOX_MARGIN_TOP, BOX_MARGIN_BOTTOM, H3_HEIGHT, BTNAREA_HEIGHT,
	MARGIN_X_WIDTH, BOX_BORDER_WIDTH, BOX_BORDER_HEIGHT, 
	LEFTBOX_SOLID_WIDTH, RIGHTBOX_DIFF_WIDTH,
	
	ulChannel,
	
	ulNew, ulViewed, ulPicked, task,
	
	TAB_VIEW_NEW = '0', TAB_VIEW_VIEWED = '1', TAB_VIEW_PICKED = '2',
	currentTabView = TAB_VIEW_NEW;
var adapt = function(){
	var clientHeight = innerHeight();
	if (clientHeight > 250) {
		var boxHeight = clientHeight - BOX_MARGIN_TOP - BOX_MARGIN_BOTTOM;
		var inHeight = boxHeight - BOX_BORDER_HEIGHT;
		box.height($.boxModel ? inHeight : boxHeight);
		leftList.height(inHeight - H3_HEIGHT);
		var listareaHeight = inHeight - H3_HEIGHT - BTNAREA_HEIGHT;
		centerList.height(listareaHeight);
		rightList.height(listareaHeight);
	}
	var clientWidth = innerWidth();
	if (clientWidth > 700)
	{
		var boxWidth = clientWidth - MARGIN_X_WIDTH;
		var inWidth = boxWidth - BOX_BORDER_WIDTH;
		box.width($.boxModel ? inWidth : boxWidth);
		var remainWidth = inWidth - LEFTBOX_SOLID_WIDTH;
		centerBox.width(parseInt(remainWidth * .6));
		var remainWidth = remainWidth - centerBox[0].offsetWidth;
		rightBox.width(remainWidth - RIGHTBOX_DIFF_WIDTH);
	}
};
var focusedNav = null;
var _loading = function(){
	var layer = $('<div class="loading" style="width:120px;display:none;position:fixed;"><sub></sub> 载入中……</div>');
	layer.appendTo(document.body);
    layer.ajaxStart(function(){
    	layer.css({
    		left:(innerWidth()/2-130),
    		top:(innerHeight()/2-50)
    	}).show();
    }).ajaxStop(function(){
		layer.hide();
    });
};
var buildNav = function(json)
{
	var taskid = json.taskid;
	var li = $('<li id="task_'+taskid+'"><a href="javascript:;" title="'+json.title+'">'+json.title+'</a></li>').hover(function(){
		li.addClass('hover');
	},function(){
		li.removeClass('hover');
	}).click(function(){
		if (focusedNav == li) return;
		focusedNav && focusedNav.removeClass('focus');
		focusedNav = li;
		li.addClass('focus');
		ulNew.load('taskid='+taskid,function(){
			ulViewed.load('taskid='+taskid,function(){
				ulPicked.load('taskid='+taskid);
			});
		});
	}).contextMenu('#right_menu',function(action){
		App[action](taskid, li, json);
	});
	return li;
};
var buildTempNav = function()
{
	var li = $('<li id="task_temp"><a href="javascript:;">临时采集任务</a></li>').hover(function(){
		li.addClass('hover');
	},function(){
		li.removeClass('hover');
	}).click(function(){
		focusedNav && focusedNav.removeClass('focus');
		focusedNav = li;
		li.addClass('focus');
		ulNew.load('taskid=temp',function(){
			ulViewed.load('taskid=temp',function(){
				ulPicked.load('taskid=temp');
			});
		});
	});
	return li;
};
var viewedOne = function(id, li)
{
	if (!(li.hasClass('viewed') || li.hasClass('spiden')))
	{
		var json = li.data('json');
		$.post('?app=spider&controller=spider&action=viewedone&spiderid='+id,
		function(){
			json.status = 'viewed';
			li.addClass('viewed');
			ulNew.deleteRow(id);
			ulViewed.addRow(json);
		});
	}
	else
	{
		ulNew.deleteRow(id);
	}
};
var _add_task_ready = function(form, dialog)
{
	$(form[0].url).keyup(function(){
		if (!this.value) return;
		$.getJSON('?app=spider&controller=manager&action=matchRule&url='+encodeURIComponent(this.value),
		function(json){
			if (json.state) {
				form[0].ruleid.value = json.ruleid;
			}
		});
	}).keyup();
};
var taskPaused = 0, taskRow = null, goon = null;
function spiderItem(taskRow, callback)
{
	taskRow.addClass('row_chked');
	if (taskRow.data('picked'))
	{
		return callback();
	}
	// spiderid status
	var id = taskRow[0].id.substr(5);
	var status = rightBox.find('>div.btn_area>select').val();
	var data = 'spiderid='+id+'&catid='+taskRow.find('input').val()+'&status='+status;
	var img = taskRow.find('img.del').hide(), loading;
	if ((loading = img.next('img')).length)
	{
		loading.attr('src', "images/loading.gif");
	} else {
		loading = $('<img width="16" height="16" src="images/loading.gif" />');
		img.after(loading);
	}
	$.ajax({
		url:"?app=spider&controller=spider&action=spider",
		data:data,
		type:'post',
		dataType:'json',
		success:function(json){
			if (json.state)
			{
				taskRow.data('picked', 1);
				// img replacewith ok
				loading.attr('src', "images/sh.gif");
				ulNew.deleteRow(id);
				ulViewed.deleteRow(id);
				ulPicked.addRow(json.data);
				// settimeout
				setTimeout(function(){
					taskRow.fadeOut(function(){
						task.remove(id);
					});
				}, 5000);
			}
			else
			{
				loading.attr('src', "images/warn.png");
				img.show();
			}
			callback();
		},error:function(json){
			loading.attr('src', "images/warn.png");
			img.show();
			callback();
		}
	});
}
var defaultCat = null;
function taskAdd(json) {
	var j = $.extend({},json);
	if (defaultCat) {
		j = $.extend(j, defaultCat);
	}
	task.add(j);
};
var App = {
	init:function(cat){
		defaultCat = cat;
		box = $('#box');
		leftBox = $('#leftBox');
		centerBox = $('#centerBox');
		rightBox = $('#rightBox');
		leftList = $('#leftList');
		centerList = $('#centerList');
		rightList = $('#rightList');
		
		var firstH3 = leftBox.find('h3'),
			firstBtnArea = centerBox.find('div.btn_area'),
			boxOffset = box.offset();
		
		BOX_MARGIN_TOP = boxOffset.top;
		BOX_MARGIN_BOTTOM = 5;
		H3_HEIGHT = firstH3[0].offsetHeight;
		BTNAREA_HEIGHT = firstBtnArea[0].offsetHeight;
		MARGIN_X_WIDTH = parseFloat(box.css('marginLeft')) * 2;
		BOX_BORDER_WIDTH = parseFloat(box.css('borderLeftWidth')) * 2;
		BOX_BORDER_HEIGHT = parseFloat(box.css('borderTopWidth')) * 2;
		LEFTBOX_SOLID_WIDTH = leftBox[0].offsetWidth;
		RIGHTBOX_DIFF_WIDTH = rightBox[0].offsetWidth - rightBox.width();
		
		
		$(window).bind('resize', adapt);
		adapt();
		
		
		var ul = centerList.find('ul').hide();
		
		var tabSwitched = function(index, span)
		{
			currentTabView = index;
			firstBtnArea.find('button:last').css('visibility',
				currentTabView == TAB_VIEW_NEW ? 'visible' : 'hidden');
		};
		var tabs = centerBox.find('h3>span').each(function(){
			var t = $(this).click(function(){
				ul.hide();
				tabs.removeClass('active');
				t.addClass('active');
				var index = t.attr('index');
				tabSwitched(index, t);
				ul.eq(t.attr('index')).show();
			});
		});
		tabs.eq(0).click();
		
		var detailUrl = '?app=spider&controller=spider&action=loaddetail';
		ulNew = new scrollFeed(ul[0], {
			template:'\
		<li id="item_{spiderid}" param="spiderid={spiderid}" class="{status}">\
			<h3 class="item-header">\
				<span class="header-right">\
					<img src="images/txt.gif" title="标记已读" class="markread" height="16" width="16">\
					<img class="marktask" width="16" height="16" title="添加到采集任务" src="images/add_1.gif" />\
				</span>\
				<span class="header-left">{title}</span>\
				<a target="_blank" href="{url}"><img width="16" height="16" alt="访问原始地址" title="访问原始地址" src="images/view.gif" /></a>\
			</h3>\
		</li>',
			baseUrl:'?app=spider&controller=spider&action=loadlist&status=new',
			rowReady:function(li, json){
				li.find('img.marktask').click(function(e){
					e.stopPropagation();
					taskAdd(json);
				});
				li.find('img.markread').click(function(e){
					e.stopPropagation();
					viewedOne(json.spiderid, li);
				});
				li.find('a').click(function(e){e.stopPropagation();});
			},
			rowFirstClick:function(div, li, json) {
				div.html('<img width="16" height="16" src="images/loading.gif" />');
				div.load(detailUrl, li.attr('param'));
				// extend after coming
				// add edit button and refresh button
				json.status = 'viewed';
				li.addClass('viewed');
				ulViewed.addRow(json);
			},
			countRow:function(count){
				$('em',tabs[0]).text(count);
			},
			pageCtrl:centerList
		});
		ulViewed = new scrollFeed(ul[1], {
			template:'\
		<li id="item_{spiderid}" param="spiderid={spiderid}" class="{status}">\
			<h3 class="item-header">\
				<span class="header-right">\
					<img class="marktask" width="16" height="16" title="标记采集" src="images/add_1.gif" />\
				</span>\
				<span class="header-left">{title}</span>\
				<a target="_blank" href="{url}"><img width="16" height="16" alt="访问原始地址" title="访问原始地址" src="images/view.gif" /></a>\
			</h3>\
		</li>',
			baseUrl:'?app=spider&controller=spider&action=loadlist&status=viewed',
			rowReady:function(li, json){
				li.find('img.marktask').click(function(e){
					e.stopPropagation();
					taskAdd(json);
				});
				li.find('a').click(function(e){e.stopPropagation();});
			},
			rowFirstClick:function(div, li, json) {
				div.html('<img width="16" height="16" src="images/loading.gif" />');
				div.load(detailUrl, li.attr('param'));
				// extend after coming
				// add edit button and refresh button
			},
			countRow:function(count){
				$('em',tabs[1]).text(count);
			},
			pageCtrl:centerList
		});
		
		ulPicked = new scrollFeed(ul[2], {
			template:'\
		<li id="item_{spiderid}" param="spiderid={spiderid}" class="{status}">\
			<h3 class="item-header">\
				<span class="header-right">\
					<img class="marktask" width="16" height="16" title="标记采集" src="images/add_1.gif" />\
				</span>\
				<span class="header-left">{title}</span>\
				<a target="_blank" href="{url}"><img width="16" height="16" alt="访问原始地址" title="访问原始地址" src="images/view.gif" /></a>\
			</h3>\
		</li>',
			baseUrl:'?app=spider&controller=spider&action=loadlist&status=spiden',
			rowReady:function(li, json){
				li.find('img.marktask').click(function(e){
					e.stopPropagation();
					taskAdd(json);
				});
				li.find('a').click(function(e){e.stopPropagation();});
			},
			rowFirstClick:function(div, li, json) {
				div.html('<img width="16" height="16" src="images/loading.gif" />');
				div.load(detailUrl, li.attr('param'));
				// extend after coming
				// add edit button and refresh button
			},
			countRow:function(count){
				$('em',tabs[2]).text(count);
			},
			pageCtrl:centerList
		});
		
		task = new taskList(rightList.find('table'),{
			template:'\
		<tr id="task_{spiderid}">\
			<td>\
				<a target="_blank" href="{url}">{title}</a>\
			</td>\
			<td width="100" class="t_c"><span title="点击修改" class="catname"><input type="hidden" value="{catid}" />[<b>{catname}</b>]</span></td>\
			<td width="50" class="t_c">\
				<img width="16" height="16" class="hand del" src="images/del.gif" />\
			</td>\
		</tr>',
			rowReady:function(id, tr, json){
				tr.find('img.del').click(function(e){
					e.stopPropagation();
					task.remove(id);
				});
				var span = tr.find('span.catname').click(function(e){
					e.stopPropagation();
					var $select = span.next('select'), select = $select[0];
					var input = span.find('input');
					var b = span.find('b');
					if ($select.length) {
						span.hide();
						$select.show().focus();
					} else {
						$.get('?app=spider&controller=spider&action=getcat',
						function(html){
							$select = $(html).change(function(){
								input.val(this.value);
							});
							select = $select[0];
							select.value = input.val();
							span.hide().after($select);
							select.focus();
						});
					}
				});
			},
			countRow:function(count)
			{
				rightBox.find('>h3:first em').text(count);
			}
		});
		
		ulChannel = leftList.find('ul');
		
		$.getJSON('?app=spider&controller=spider&action=tasklist',function(json){
			for (var i=0,l;l=json[i++];ulChannel.append(buildNav(l))){}
			
			ulChannel.find('li:first').click();
		});
		
		$("#keyword").autocomplete({
			itemSelected:function(a, item, input){
				App.tempGrap();
			},itemFormat:function(item, k){
				return k ? (item.title.replace(new RegExp(k, 'ig'), function(k){
					return '<strong>' + k + '</strong>';
		    	})+'<br/>'+item.url.replace(new RegExp(k, 'ig'), function(k){
					return '<strong>' + k + '</strong>';
		    	})) : (item.title+'<br/>'+item.url);
			},itemPrepared:function(a, item){
				a.height(50);
			}
		});
		
		$('#status').change(function(){
			$.post('?app=spider&controller=spider&action=memstatus','status='+this.value);
		});
		
		_loading();
	},
	clearTask:function(){
		task.clear();
	},
	viewedAll:function(){
		var list = ulNew.allRow();
		for (var id in list)
		{
			viewedOne(id, list[id]);
		}
	},
	addAlltask:function(){
		var ul = currentTabView == TAB_VIEW_NEW ? ulNew :
				 currentTabView == TAB_VIEW_VIEWED ? ulViewed : ulPicked;
		var list = ul.allRow();
		for (var id in list)
		{
			taskAdd(list[id].data('json'));
		}
	},
	addTask:function(){
		ct.form('添加监控任务','?app=spider&controller=manager&action=addTask',
		400,240,function(json){
			if (json.state) {
				ct.tips('添加采集任务成功','success');
				ulChannel.append(buildNav(json.data));
				return true;
			}
		}, _add_task_ready);
	},
	editTask:function(id, li, json){
		ct.form('编辑监控任务','?app=spider&controller=manager&action=editTask&taskid='+id,
		400,240,function(json){
			if (json.state) {
				ct.tips('编辑采集任务成功','success');
				var hasFocus = focusedNav == li;
				var nli = buildNav(json.data);
				li.replaceWith(nli);
				hasFocus && nli.click();
				return true;
			}
		});
	},
	delTask:function(id, li){
		var msg = '此操作不可恢复，确定要删除监控任务“'+li.text()+'”吗？';
		ct.confirm(msg, function(){
			$.getJSON('?app=spider&controller=manager&action=delTask&taskid='+id,
			function(json){
				if (json.state)
				{
					if (focusedNav == li)
					{
						focusedNav = null;
					}
					li.remove();
					ct.tips('删除成功','success');
				}
				else
				{
					ct.tips('删除失败','error');
				}
			});
		});
	},
	tempGrap:function(){
		var url = $('#keyword').val();
		if (!url) return;
		$.post('?app=spider&controller=spider&action=tempGrap',
		'url='+url,function(json){
			if (json.state) {
				if (json.taskid) {
					$('#task_'+json.taskid).click();
				} else {
					var li = $('#task_temp');
					if (li.length) {
						li.click();
						return;
					}
					li = buildTempNav();
					ulChannel.append(li);
					li.click();
					$('#saveTempBtn').show();
				}
			}
			else
			{
				$('#saveTempBtn').hide();
				var msg = '<div class="bk_5"></div><p class="ct_msg"><sup></sup>当前网址无法采集，系统规则库里没有对应规则，你可以添加或者选择已有规则。</p>';
		    	var buttons = {
		    		"选择规则": function() {
		    			App.selectRule(url);
		    			dialog.dialog("close");
		    		},
		    		"添加规则": function() {
		    			dialog.dialog("close");
		    			ct.assoc.open('?app=spider&controller=manager&action=addrule&url='+encodeURIComponent(url), 'newtab');
		    		},
		    		"取消":function() {
		    			dialog.dialog("close");
		    		}
		    	};
		        var dialog = $("<div></div>").html(msg);
		    	dialog.dialog({
		    		bgiframe: true,
		    		closeOnEscape:false,
		    		height:'auto',
		    		width:360,
		    		resizable:false,
		    		modal: true,
		    		close: function(){
		    			dialog.dialog('destroy').remove();
		    		},
		    		title:'添加或选择采集规则',
		    		buttons: buttons
		    	});
			}
		},'json');
	},
	selectRule:function(url){
		ct.form('选择对应的规则','?app=spider&controller=manager&action=selectRule&url='+encodeURIComponent(url),
		400, 240, function(json){
			if (json.state) {
				if (json.data) {
					var li = buildNav(json.data);
					ulChannel.append(li);
					li.click();
				} else {
					var li = $('#task_temp');
					if (li.length) {
						li.click();
						return;
					}
					li = buildTempNav();
					ulChannel.append(li);
					li.click();
					$('#saveTempBtn').show();
				}
			}
		});
	},
	tempSave:function(){
		ct.form('保存临时采集任务','?app=spider&controller=manager&action=saveTempTask',
		400, 240, function(json){
			if (json.state) {
				$('#saveTempBtn').hide();
				var tempNav = $('#task_temp');
				var hasFocus = tempNav.hasClass('focus');
				// add new nav
				var li = buildNav(json.data);
				tempNav.replaceWith(li);
				// has focus  
				hasFocus && li.click();
				return true;
			}
		}, _add_task_ready);
	},
	refreshNav:function(){
		var focustaskid = null;
		if (focusedNav)
		{
			focustaskid = focusedNav.attr('id');
		}
		ulChannel.empty();
		$.getJSON('?app=spider&controller=spider&action=tasklist',function(json){
			for (var i=0,l;l=json[i++];ulChannel.append(buildNav(l))){}
			
			if (focustaskid) {
				ulChannel.find('#'+focustaskid).click();
			}
		});
	},
	refreshItem:function(){
		if (!focusedNav)
		{
			return;
		}
		var id = focusedNav[0].id.substr(5);
		var param = 'taskid='+id;
		if (currentTabView == TAB_VIEW_NEW)
		{
			ulNew.load(param);
		} else if (currentTabView == TAB_VIEW_VIEWED) {
			ulViewed.load(param);
		} else {
			ulPicked.load(param);
		}
	},
	run:function(){
		taskRow = task.first();
		var ctrls = rightBox.find('>div.btn_area>*');
		var buttonsGrpbeforeRun = ctrls.filter(':lt(3)');
		var buttonsRunning = ctrls.eq(3).show();
		buttonsGrpbeforeRun.css('visibility','hidden');
		var complete = function(){
			taskRow = null;
			buttonsGrpbeforeRun.css('visibility','visible');
			buttonsRunning.hide();
		};
		if (!taskRow.length) return complete();
		var _callback = function(){
			taskRow.removeClass('row_chked');
			taskRow = taskRow.next();
			if (taskRow.length) {
				if (taskPaused) {
					goon = function(){
						spiderItem(taskRow, _callback);
					};
					return;
				} else {
					spiderItem(taskRow, _callback);
				}
			} else {
				complete();
			}
		};
		spiderItem(taskRow, _callback);
	},
	pause:function(btn){
		if (taskPaused) {
			btn.innerHTML = '暂停';
			taskPaused = 0;
			if (goon) {
				goon();
				goon = null;
			}
		} else {
			btn.innerHTML = '继续';
			taskPaused = 1;
		}
	}
};
window.App = App;
})(jQuery);
