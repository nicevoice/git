/**
 * scrollTable application
 * 
 * Base on: 
 *  cmstop.js &
 *  jquery.js &
 *
 */
(function($){
var wrapper = document.createElement("div");
window.scrollTable = function(elem, options){
	elem =  $(elem);
    var tbody = $(elem[0].tBodies[0]);
    options || (options = {});
    
    var template = $.trim(options.template||'');
    var pageSize = options.pageSize || 20;
    var _baseUrl = (options.baseUrl || '');
    var rowReady = ct.func(options.rowReady) || function(){};
    var rowChecked = ct.func(options.rowChecked) || function(){};
    var baseUrl = _baseUrl + '&pagesize=' + pageSize;
    var pageCtrl = options.pageCtrl;
    
    var checkedRow = null;
    // private method to build table row prepared with events-bind
	var buildRow = function(json)
	{
	    // prepare html
	    var html = template;
	    for (var key in json)
	    {
	        html = html.replace(new RegExp('{'+key+'}',"gm"), json[key]);
	    }
	    wrapper.innerHTML = '<table><tbody>'+html+'</tbody></table>';
	    // create a tr
        var tr = $(wrapper.firstChild.rows[0]);
	    
	    // init hover event
	    tr.hover(function(){
	        tr.addClass('over');
	    },function(){
	        tr.removeClass('over');
	    });
	    
	    // init click event
	    var radio = tr.find('input:radio');
	    // has radio? bind event to radio
	    if (radio.length)
	    {
    	    tr.bind('check',function(){
	        	checkedRow && checkedRow.trigger('unCheck');
    	    	tr.addClass('row_chked');
	        	radio[0].checked = true;
	        	rowChecked(tr,json);
	        	checkedRow = tr;
    	    }).bind('unCheck',function(){
    	        tr.removeClass('row_chked');
    	        radio[0].checked = false;
    	    });
    	    var chk = function(e){
    	        e.stopPropagation();
    	        tr.trigger('check');
    	    };
    	    radio.click(chk);
    	    tr.click(chk);
	    }
	    // no checkbox
	    else
	    {
	        tr.click(function(){
    	        tr.trigger('check');
	        }).bind('check',function(){
	        	if (checkedRow == tr) return;
	        	rowChecked(tr,json);
	        	tr.addClass('row_chked');
	        	checkedRow && checkedRow.trigger('unCheck');
	        	checkedRow = tr;
	        }).bind('unCheck',function(){
	            tr.removeClass('row_chked');
	        });
	    }
	    rowReady(tr,json);
	    return tr;
	};
    var _oldwhere = '';
	var count = 0;
	var page = 1;
	var total = 0;
	var show_more_lock = false;
	this.load = function(where)
	{
		where.nodeType && (where = $(where));
		where && (_oldwhere = where.jquery ? where.serialize() : where);
		$.post(baseUrl, _oldwhere, function(json){
			page = 1;
			checkedRow = null;
			if (json && json.data) {
				tbody.empty();
				total = json.total;
				count = json.data.length;
				for (var i=0;i<count;i++)
				{
					var tr = buildRow(json.data[i]);
					tbody.append(tr);
				}
			}
		}, 'json');
	};
    pageCtrl.scroll(function(){
		if (!show_more_lock && count < total 
			&& pageCtrl.scrollTop() + pageCtrl.height() > pageCtrl[0].scrollHeight - 90)
		{
			show_more_lock = true;
			$.post(baseUrl+'&page='+(++page), _oldwhere, function(json){
				if (json && json.data)
				{
					var l = json.data.length;
					count += l;
					for (var i=0;i<l;i++)
					{
						var tr = buildRow(json.data[i]);
						tbody.append(tr);
					}
				}
				show_more_lock = false;
			}, 'json');
		}
	});
};
})(jQuery);