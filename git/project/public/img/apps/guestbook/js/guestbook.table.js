/**
 * Table application for cmstop
 * 
 * Base on: 
 *  cmstop.js &
 *  jquery.js &
 *  jquery.contextMenu.js &
 *  jquery.pagination.js
 *
 */
(function(ct,$){
	var wrapper = document.createElement('div');
	var doc = $(document);
	ct.table = function(elem, options)
	{
		elem =  $(elem);
		var tbody = elem;
		options || (options = {});
		
		var rowIdPrefix = options.rowIdPrefix || 'row_';
		var rightMenuId = options.rightMenuId || 'right_menu';

		var dblclickHandler = ct.func(options.dblclickHandler || 'dblclick_handler') || function(){};

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
		var checkboxStack = [];
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
			
			// init click event
			var checkbox = tr.find('input:checkbox');
			// has checkbox? bind event to checkbox
			if (checkbox.length)
			{
				tr.bind('check',function(){
					// toggle seleted
					(tr.addClass('row_chked'), (checkbox[0].checked = true));
				}).bind('unCheck',function(){
					(tr.removeClass('row_chked'), (checkbox[0].checked = false));
				});
				var togglechk = function(e){
					// toggle seleted
					e.stopPropagation();
					var flag = checkbox[0].checked;
					e.target == checkbox[0] && (flag = !flag);
					tr.trigger(flag ? 'unCheck' : 'check');
				};
				checkbox.click(togglechk);
				tr.click(togglechk);
				
				// add checkbox to stack and bind function to beforeRemove
				checkboxStack.push(checkbox);
				tr.bind('beforeRemove',function(){
					var index = checkboxStack.indexOf(checkbox);
					index != -1 && checkboxStack.splice(index, 1);
					delete rowStack[id];
				});
			}
			// no checkbox
			else
			{
				tr.click(function(){
					tr.trigger('check');
				}).bind('check',function(){
					if (lastFocused == tr) return;
					lastFocused && lastFocused.trigger('unCheck');
					lastFocused = tr;
					tr.addClass('row_chked');
				}).bind('unCheck',function(){
					tr.removeClass('row_chked');
				}).bind('beforeRemove',function(){
					delete rowStack[id];
				});
			}
			// init dblclick event
			tr.dblclick(function(){
				dblclickHandler(id, tr, json);
			});
			if ($.fn.contextMenu) {
				tr.bind('contextMenu',function(){
					for (var id in rowStack)
					{
						rowStack[id].trigger('unCheck');
					}
					tr.trigger('check');
				});
				// init right menu
				tr.contextMenu('#'+(tr.attr('right_menu_id') || rightMenuId),
				function(action) {
					var callback = ct.func(action);
					callback && callback(id, tr, json);
				});
			}
			
			return tr;
		}
		// public method
		this.addRow = function(json, prepend)
		{
			var tr = buildRow(json);
			prepend ? tbody.prepend(tr) : tbody.append(tr);
			rowCallback(tr[0].id.substr(rowIdPrefix.length), tr);
			elem.trigger("update");
			return tr;
		};
		// public method
		this.updateRow = function(id,json)
		{
			var tr = rowStack[id];
			tr.trigger('beforeRemove');
			var ntr = buildRow(json);
			tr.replaceWith(ntr);
			ntr.trigger('check');
			rowCallback(id, ntr);
			elem.trigger("update");
			return ntr;
		};
		// public method
		this.deleteRow = function(id)
		{
			(id == undefined)
			  ? (id = this.checkedIds())
			  : (typeof id == 'number')
				? (id = [id])
				: !$.isArray(id) && (id = (id+'').split(','));
			for (var i=0,l=id.length;i<l;i++)
			{
				var tr = rowStack[id[i]];
				tr && (
					tr.trigger('beforeRemove'),
					tr.remove()
				);
			}
		};
		// public method
		this.checkedIds = function(){
			var ids = [];
			for (var i=0,c;c=checkboxStack[i++];)
			{
				c[0].checked && ids.push(c.val());
			}
			return ids;
		};
		this.checkedRow = function(){
			return lastFocused;
		};
		this.setPageSize = function(size){
			pageSize = parseInt(size) || 12;
			pageOption.items_per_page = pageSize;
			baseUrl = _baseUrl + (pager.length ? ('&'+pagesizeVar + '=' + pageSize) : '');
		};
		this.getPageSize = function() {
			return pageSize;
		};
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
				checkboxStack = [];
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
			_load(baseUrl, _olddata, function(totalSize){
					pager.length && pager.pagination(totalSize, pageOption);
			});
		}
	};
})(cmstop,jQuery);