/**
 * based jQuery 1.2+ javascript famework
 *
 * @author     kakalong
 * @copyright  2010 (c) cmstop.com
 * @version    $Id: cmstop.treetable.js 3773 2011-08-31 11:46:51Z liyawei $
 */
(function(ct,$){
	var wrapper = document.createElement("div");
	$.fn.getDescendants = function()
    {
    	var tr = this,
    		level = parseInt(tr.attr('level')) || 0,
    		ls = [];
    	while ((tr = tr.next()).length && parseInt(tr.attr('level'))>level)
    	{
    		ls.push(tr[0]);
    	}
    	return $(ls);
    };
    $.fn.getChildren = function()
    {
    	var tr = this,
    		level = parseInt(tr.attr('level')) || 0, cl=level+1, l,
    		ls = [];
    	while ((tr = tr.next()).length && (l = parseInt(tr.attr('level'))) > level)
    	{
    		cl == l && ls.push(tr[0]);
    	}
    	return $(ls);
    };
    var getInsertAfter = function(tr)
    {
    	
    	var after = tr, level = parseInt(tr.attr('level')) || 0;
    	while ((tr = tr.next()).length && parseInt(tr.attr('level')) > level)
    	{
    		after = tr;
    	}
    	return after;
    };
    
    var CLASSES = {
    	hover:'over',
    	focused:'row_chked',
    	collapsed:'collapsed',
    	parent:'parent',
    	hitarea:'hitarea',
    	treeTable:'treeTable'
    };
    var LANG = {
    	'loading':'<img src="'+IMG_URL+'images/loading.gif"/>&nbsp;&nbsp;&nbsp;&#x6570;&#x636E;&#x8F7D;&#x5165;&#x4E2D;&#x3002;&#x3002;&#x3002;',
    	'noData':'&#x6682;&#x65E0;&#x6570;&#x636E;'
    };
    ct.treeTable = function(table, options)
    {
    	table = $(table).addClass(CLASSES.treeTable);
    	options || (options = {});
    	
        var tbody = $(table[0].tBodies[0]),
    		template = $.trim(options.template||''),
        	baseUrl = options.baseUrl || '',
        	rowIdPrefix = options.rowIdPrefix || 'row_',
    		treeCellIndex = options.treeCellIndex || 0,
    		idField = options.idField || 'id',
    		parentField = options.parentField || 'parentid',
    		collapsed = !!options.collapsed,
    		jsonLoaded = ct.func(options.jsonLoaded) || function(){},
        	rowReady = ct.func(options.rowReady) || function(){},
        	rowsPrepared = ct.func(options.rowsPrepared) || function(){},
        	
    		lastFocused = null;
    	
    	var tfoot = $('<tfoot><tr><td class="empty" colspan="'+$(template).find('>td').length+'" align="center">'+LANG.loading+'</td></tr></tfoot>');
        tbody.after(tfoot);
    	
    	var setParent = function($tr, parent, place)
	    {
	    	var parentId = 0, parentTr = null, plevel = 0, level;
	    	if (parent && parent.jquery) {
	    		parentTr = parent;
	    		parentId = parent[0].id.substr(rowIdPrefix.length);
	    	} else {
	    	    parentId = parseInt(parent) || 0;
	    	    (parentId && (parentTr = $('#'+rowIdPrefix+parentId)).length)
	    	      ||
	    	    (parentTr = null);
	    	}
		    // 给父级加样式 
		    parentTr && parentTr.addClass(CLASSES.parent);
		    // 父级深度
		    plevel = parentTr ? parseInt(parentTr.attr('level')) : -1;
		    level = plevel + 1;
	        // 记录深度
	        $tr.attr('level',level).attr('parentid',parentId);
	        $tr.data('parentTr',parentTr);
	        $tr.data('indent').css('padding','0 '+(10*level)+'px');
	        // 父级是折叠的 则隐藏自己
	        parentTr && parentTr.hasClass(CLASSES.collapsed) && $tr.hide();
	        // 插入
    	    if (place && place.jquery) {
    	    	if (place.hasClass(CLASSES.parent))
    	    	{
    	    		$tr.addClass(CLASSES.parent);
    	    		place.hasClass(CLASSES.collapsed) && $tr.addClass(CLASSES.collapsed);
    	    		place.getChildren().data('parentTr',$tr);
    	    	}
    	    	place.hasClass(CLASSES.focused) && setTimeout(function(){$tr.click()},0);
    	    	place.replaceWith($tr);
    	    } else {
    	    	parentTr ? getInsertAfter(parentTr).after($tr) : tbody.append($tr);
    	    }
	    };
	    var checkChild = function($tr)
	    {
	    	var next = $tr.next();
    		if (!next.length || parseInt(next.attr('level')) <= parseInt($tr.attr('level')))
    		{
    			$tr.removeClass(CLASSES.parent+' '+CLASSES.collapsed);
    		}
	    };
    	// private method to build table row prepared with events-bind
    	var buildRow = function(json, place)
    	{
    	    var html = template;
    	    
    	    // prepare 模板
    	    for (var key in json)
    	    {
    	        html = html.replace(new RegExp('{'+key+'}',"gm"), json[key]);
    	    }
    	    wrapper.innerHTML = '<table><tbody>'+html+'</tbody></table>';
    	    
    	    // create a tr
    	    var tr = wrapper.firstChild.rows[0],
	    	$tr = $(tr),
	    	// 找到树单元格
	    	treeCell = $(tr.cells[treeCellIndex]),
	    	// 创建点击区域
	    	hitarea = $('<span class="'+CLASSES.hitarea+'"></span>')
    	    .click(function(){
    	    	if (!$tr.hasClass(CLASSES.parent)) return;
    	    	if ($tr.hasClass(CLASSES.collapsed)) {
    	    		$tr.removeClass(CLASSES.collapsed);
    	    		$tr.getChildren().each(function(){
    	    			$.data(this,'show')();
    	    		});
    	    	} else {
    	    		$tr.getDescendants().hide();
    	    		$tr.addClass(CLASSES.collapsed);
    	    	}
    	    }).dblclick(function(){return false}),
    	    // 创建缩进
    	    indent = $(document.createElement('q'));
    	    collapsed && $tr.addClass(CLASSES.collapsed);
    	    $tr.data('indent',indent);
    	    treeCell.prepend(hitarea).prepend(indent);
    	    setParent($tr,json[parentField],place);
    	    var id = tr.id.substr(rowIdPrefix.length);
    	    $tr.hover(function(){
            	$tr.addClass(CLASSES.hover);
            },function(){
            	$tr.removeClass(CLASSES.hover);
            }).click(function(){
            	if (lastFocused == $tr) return;
            	lastFocused && lastFocused.removeClass(CLASSES.focused);
            	lastFocused = $tr;
            	$tr.addClass(CLASSES.focused);
            })
            // 绑定show事件 显示时 按需展开后裔
            .data('show',function(){
            	$tr.show();
            	if ($tr.hasClass(CLASSES.parent) && !$tr.hasClass(CLASSES.collapsed))
            	{
            		$tr.getChildren().each(function(){
    	    			$.data(this,'show')();
    	    		});
            	}
            }).data('_delete_',function(){
            	$tr.hasClass(CLASSES.parent) && $tr.getChildren().each(function(){
            		$.data(this,'_delete_')();
            	});
            	lastFocused == $tr && (lastFocused = null);
            	$tr.remove();
            }).data('_setParent_',function(parent){
            	var children = $tr.hasClass(CLASSES.parent) ? $tr.getChildren() : null;
            	setParent($tr, parent);
            	children && children.each(function(){
    				$.data(this,'_setParent_')($tr);
    			});
            });
    	    
    	    rowReady(id, $tr, json);
    	    return $tr;
    	};
    	this.addRow = function(json)
    	{
    		return buildRow(json);
    	};
    	this.updateRow = function(id,json)
    	{
    		var $tr = $('#'+rowIdPrefix+id);
    		if (parseInt(json[parentField]) == parseInt($tr.attr('parentid')))
        	{
        		return buildRow(json, $tr);
        	}
    		var children = $tr.hasClass(CLASSES.parent) ? $tr.getChildren() : null;
    		var ntr = buildRow(json, $tr);
    		if (children)
    		{
    			$tr.hasClass(CLASSES.collapsed) && ntr.addClass(CLASSES.collapsed);
    			children.each(function(){
    				$.data(this,'_setParent_')(ntr);
    			});
    		}
    		$tr.hasClass(CLASSES.focused) && ntr.click();
    		var p = $tr.data('parentTr');
    		$tr.remove();
    		p && checkChild(p);
    		return ntr;
    	};
    	this.deleteRow = function(id)
    	{
    		var $tr = $('#'+rowIdPrefix+id);
        	var p = $tr.data('parentTr');
    		$tr.data('_delete_')();
        	p && checkChild(p);
    	};
    	var fillin = function(json) {
    		var temp;
    		if ($.isArray(json)) {
    			temp = {};
    			for (var i=0,item;item=json[i++];) {
	    			temp[item[idField]] = item;
	    		}
    		} else {
    			temp = $.extend({}, json);
    		}
    		
    		var added = {};
    		var recursiveAdd = function(k) {
    			var item = temp[k],
    				parentid = item[parentField];
    			if (k in added) {
    				return;
    			}
    			if (!(parentid in added)) {
    				(parentid in temp) && recursiveAdd(parentid);
    			}
    			buildRow(item);
    			delete temp[k];
    			added[k] = 1;
    		}
    		for (var k in temp) {
    			recursiveAdd(k);
    		}
    	};
    	this.load = function(where) {
    		var url = baseUrl;
    		where && (url += '&'+where);
    		$.getJSON(url, function(json){
    			jsonLoaded(json);
    	        tfoot.hide();
    			// fillin with new data
    			tbody.empty();
    			
    			fillin(json);
    	        rowsPrepared(tbody);
    		});
    	};
    }
})(cmstop,jQuery);