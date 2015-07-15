/**
 * scrollFeed application
 * 
 * Base on: 
 *  cmstop.js &
 *  jquery.js &
 *
 */
(function($){
window.scrollFeed = function(ul, options){
	ul =  $(ul);
    options || (options = {});
    
    var template = $.trim(options.template||''),
	    pageSize = options.pageSize || 20,
	    baseUrl = (options.baseUrl || '') + '&pagesize=' + pageSize,
	    idPrefix = (options.idPrefix || 'item_'),
	    rowReady = ct.func(options.rowReady) || function(){},
	    rowChecked = ct.func(options.rowChecked) || function(){},
	    rowFirstClick = ct.func(options.rowFirstClick) || function(){},
	    jsonLoaded = ct.func(options.jsonLoaded) || function(){},
	    countRow = ct.func(options.countRow) || function(){},
	    pageCtrl = options.pageCtrl,
	    list = {},
	    checkedRow = null,
	    _count = 0,_total = 0;
    // private method to build table row prepared with events-bind
	var buildRow = function(json)
	{
	    // prepare html
	    var li = template;
	    for (var key in json)
	    {
	        li = li.replace(new RegExp('{'+key+'}',"gm"), json[key]);
	    }
	    li = $(li);
        
	    var id = li[0].id.substr(idPrefix.length);
	    if (id in list) return;
	    list[id] = li;
	    // init hover event
	    li.hover(function(){
	        li.addClass('hover');
	    },function(){
	        li.removeClass('hover');
	    });
	    
	    var h3 = li.find('>h3');
	    
        h3.click(function(){
	        li.trigger('check');
        });
        li.bind('check',function(){
        	if (li.hasClass('expanded')) {
        		li.removeClass('expanded');
        	} else if (li.find('>div').length) {
        		li.addClass('expanded');
        	} else {
        		var div = $('<div class="item-desc"></div>')
        		.appendTo(li.addClass('expanded'));
        		rowFirstClick(div, li, json);
        	}
        	if (checkedRow == li) return;
        	rowChecked(li, json);
        	li.addClass('focus');
        	checkedRow && checkedRow.trigger('unCheck');
        	checkedRow = li;
        }).bind('unCheck',function(){
            li.removeClass('focus expanded');
        });
	    
	    li.data('json', json);
	    rowReady(li, json);
	    return li;
	};
    var _oldwhere = '';
	var count = 0;
	var page = 1;
	var total = 0;
	var show_more_lock = false;
	this.load = function(where, callback)
	{
		where.nodeType && (where = $(where));
		where && (_oldwhere = where.jquery ? where.serialize() : where);
		$.post(baseUrl, _oldwhere, function(json){
			total = json.total;
			page = 1;
			count = json.data.length;
			ul.empty();
			list = {};
			checkedRow = null;
			for (var i=0;i<count;i++)
			{
				var li = buildRow(json.data[i]);
				li && ul.append(li);
			}
			jsonLoaded(json);
			callback && callback();
			_total = total;
			_count = count;
			countRow(_count+'/'+_total);
            if (pageCtrl[0].clientHeight > ul[0].scrollHeight) {
                pageCtrl.trigger('scroll');
            }
		}, 'json');
	};
	this.addRow = function(json) {
		var li = buildRow(json);
		li && (ul.prepend(li), countRow((++_count)+'/'+(++_total)));
	};
	this.deleteRow = function(id) {
		var li = list[id];
		if (!li) return;
		li.remove();
		delete list[id];
		countRow((--_count)+'/'+(--_total));
		if (ul.find('>li').length < pageSize)
		{
			pageCtrl.scroll();
		}
	};
	this.allRow = function()
	{
		return list;
	};
    pageCtrl.scroll(function(){
		if (!show_more_lock && count < total && ul.is(':visible')
			&& pageCtrl.scrollTop() + pageCtrl.height() > pageCtrl[0].scrollHeight - 90)
		{
			show_more_lock = true;
			$.post(baseUrl+'&page='+(++page), _oldwhere, function(json){
				if (json.total) {
					_total = total = json.total;
				}
				var l = json.data.length;
				count += l;
				_count += l;
				countRow(_count+'/'+_total);
				for (var i=0;i<l;i++)
				{
					var li = buildRow(json.data[i]);
					li && ul.append(li);
				}
				show_more_lock = false;
			}, 'json');
		}
	});
};
})(jQuery);