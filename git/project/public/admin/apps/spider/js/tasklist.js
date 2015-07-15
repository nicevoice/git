/**
 * taskList application
 * 
 * Base on: 
 *  cmstop.js &
 *  jquery.js &
 *
 */
(function($){
var wrapper = document.createElement("div");
window.taskList = function(elem, options){
	elem =  $(elem);
    var tbody = $(elem[0].tBodies[0]);
    options || (options = {});
    
    var template = $.trim(options.template||''),
	    idPrefix = (options.idPrefix || 'task_'),
    	rowReady = ct.func(options.rowReady) || function(){},
    	countRow = ct.func(options.countRow) || function(){},
    	list = {}, count = 0;
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
	    var id = tr[0].id.substr(idPrefix.length);
	    if (id in list) {
	    	return;
	    }
	    
	    // init hover event
	    tr.hover(function(){
	        tr.addClass('over');
	    },function(){
	        tr.removeClass('over');
	    });
	    
	    rowReady(id, tr, json);
	    tr.data('json',json);
	    list[id] = tr;
	    tbody.append(tr);
	    countRow(++count);
	    return tr;
	};
	
	this.add = function(json)
	{
		buildRow(json);
	};
	this.remove = function(id)
	{
		var tr = list[id];
		if (tr) {
			tr.remove();
			delete list[id];
			countRow(--count);
		}
	};
	this.clear = function(){
		for (var id in list) {
			list[id].remove();
			delete list[id];
			countRow(--count);
		}
	};
	this.first = function()
	{
		return tbody.find('tr:first');
	};
};
})(jQuery);