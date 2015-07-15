/**
 * Table application for cmstop
 * 
 * Base on: 
 *  cmstop.js &
 *  jquery.js &
 *  jquery.pagination.js
 *
 */
(function(ct,$){
	var wrapper = document.createElement("div");
	var doc = $(document);
	ct.table = function(elem, options)
	{
		elem =  $(elem);
		var tbody = elem;
		options || (options = {});
		
		var rowIdPrefix = options.rowIdPrefix || 'row_';
		//var rightMenuId = options.rightMenuId || 'right_menu';

		//var dblclickHandler = ct.func(options.dblclickHandler || 'dblclick_handler') || function(){};

		var rowCallback = ct.func(options.rowCallback || 'init_row_event') || function(){};
		
		var jsonLoaded = ct.func(options.jsonLoaded || 'json_loaded') || function(){};
		var template = $.trim(options.template||'');
		var pager = $('#'+(options.pagerId||'pagination'));
		var pageVar = options.pageVar || 'page';
		var pagesizeVar = options.pagesizeVar || 'pagesize';
		var pageSize = options.pageSize || 12;
		var _baseUrl = (options.baseUrl || '');
		var baseUrl = _baseUrl + (pager.length ? ('&'+pagesizeVar + '=' + pageSize) : '');
		
		var rowStack = {};
		var nosortNum = 0;

		var lastFocused = null;
		
		// private method to build table row prepared with events-bind
		var buildRow = function(json)
		{
			// prepare html
			var html = template;
			for (var key in json)
			{
				html = html.replace(new RegExp('{'+key+'}',"gm"), json[key]);
			}
			
			wrapper.innerHTML = html;
			
			// create a tr
			var tr = $(wrapper.firstChild);
			//alert(tr.html());
			var id = tr[0].id.substr(rowIdPrefix.length);
			
			// add tr to stack
			rowStack[id] = tr;
			
			return tr;
		}

		var _olddata = '';
		var loadPage = function(index) {
			_load(baseUrl + '&' + (pageVar+'='+(index+1)), _olddata);
		};
		// control pagination
		var pageOption = {
			callback: loadPage,
			items_per_page: pageSize,
			num_display_entries:5,
			num_edge_entries:2,
			// use unicode to avoid errors
			prev_text:'&#x4E0A;&#x4E00;&#x9875;',
			next_text:'&#x4E0B;&#x4E00;&#x9875;'
		};
		var _load = function(url, data, callback)
		{
			$.getJSON(url, data, function(json){
				// clear table rows
				rowStack = {};
				tbody.empty();
				//checkallCtrl.length && (checkallCtrl[0].checked = false);
				// fillin with new data
				for (var i=0,item;item=json.data[i++];)
				{
					var tr = buildRow(item);
					tbody.append(tr);
				}
				elem.trigger("update");
				for (var id in rowStack)
				{
					rowCallback(id, rowStack[id]);
				}
				typeof callback == 'function' && callback(json.total);
				jsonLoaded(json);
			});
		};
		// public method
		this.load = function(data)
		{
			data && (_olddata = data.jquery ? data.serialize() : data);
			_load(baseUrl, _olddata, function(totalSize) {
					pager.length && pager.pagination(totalSize, pageOption);
			});
		}
		this.firstLoad = function(total)
		{
			pager.length && pager.pagination(total, pageOption);
		}
	};
})(cmstop,jQuery);