/**
 * Table application for cmstop
 *
 * Base on:
 *  cmstop.js &
 *  jquery.js &
 *  jquery.tablesorter.js &
 *  cmstop.contextMenu.js &
 *  jquery.pagination.js
 *
 */
(function(ct,$){
    var LANG = {
    	'loading':'<img src="'+IMG_URL+'images/loading.gif"/>&nbsp;&nbsp;&nbsp;&#x6570;&#x636E;&#x8F7D;&#x5165;&#x4E2D;&#x3002;&#x3002;&#x3002;',
    	'noData':'&#x6682;&#x65E0;&#x6570;&#x636E;',
    	'prevPage':'&#x4E0A;&#x4E00;&#x9875;',
        'nextPage':'&#x4E0B;&#x4E00;&#x9875;'
    };
    var wrapper = document.createElement("div");
    var doc = $(document);
    ct.table = function(elem, options)
    {
        elem =  $(elem);
        var tbody = $(elem[0].tBodies[0]);
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
        var numDisplayEntries = options.num_display_entries || 5;
        var numEdgeEntries = options.num_edge_entries || 2;
        var _baseUrl = (options.baseUrl || '');
        var baseUrl = _baseUrl + (pager.length ? ('&'+pagesizeVar + '=' + pageSize) : '');
		var lastUrl	= '';


        var sortDisabled = {};
        var headers = [];

        var rowStack = {};
        var checkboxStack = [];
        var nosortNum = 0;
        // fetch all head cells
        var cells = $(elem[0].rows[0].cells).each(function(i){
            var th = $(this);
            // no sorter className than disabled sortable
            if (!th.hasClass('sorter'))
            {
                sortDisabled[i] = {sorter:false};
                nosortNum++;
                // store th which need to sort by small btn & hidden-div
                th.hasClass('ajaxer') && (
                    th.removeClass('sorter').addClass('header'),
                    headers.push(th)
                );
            }
            else
            {
                th.removeClass('sorter');
            }
        });

        // init tablesorter
    	nosortNum < cells.length && elem.tablesorter({
    		headers: sortDisabled
    	});
        var tfoot = $('<tfoot><tr><td class="empty" colspan="'+cells.length+'" align="center">'+LANG.loading+'</td></tr></tfoot>');
        var setMsg = function(msg) {
        	tfoot.find('td').html(msg);
        	tfoot.show();
        };
        tbody.after(tfoot);
        // toggle check-all & un-check-all checkbox-control
        var checkallCtrl = cells.find('input:checkbox');
        checkallCtrl.click(function(){
            var clause = checkallCtrl[0].checked ? 'check' : 'unCheck';
            for (var i=0,c;c = checkboxStack[i++];)
            {
                c.trigger(clause);
            }
        });
        var lastFocused = null;

        // private method to build table row prepared with events-bind
    	var buildRow = function(json)
    	{
    	    // prepare html
    	    var html = template;
    	    for (var key in json)
    	    {
    	        html = html.replace(new RegExp('{'+key+'}',"gm"), json[key]||'');
    	    }

    	    wrapper.innerHTML = '<table><tbody>'+html+'</tbody></table>';

    	    // create a tr
            var tr = $(wrapper.firstChild.rows[0]);
    	    var id = tr[0].id.substr(rowIdPrefix.length);

    	    // add tr to stack
    	    rowStack[id] = tr;

    	    // init hover event
    	    tr.hover(function(){
    	        tr.addClass('over');
    	    },function(){
    	        tr.removeClass('over');
    	    });

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
			doc.click();
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
    		$('tr.empty').remove();
    	    var tr = buildRow(json);
    	    prepend ? tbody.prepend(tr) : tbody.append(tr);
    	    rowCallback(tr[0].id.substr(rowIdPrefix.length), tr);
    	    elem.trigger("update");
    	    tbody.find('>tr').length && tfoot.hide();
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
	        tbody.find('>tr').length || setMsg(LANG.noData);
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
        	num_display_entries: numDisplayEntries,
        	num_edge_entries: numEdgeEntries,
        	// use unicode to avoid errors
        	prev_text:LANG.prevPage,
        	next_text:LANG.nextPage
        };
        var _lastType = 'GET';
    	var _load = function(url, data, callback, type) {
    	    // clear table rows
    		// setMsg(LANG.loading);
    		type && (_lastType = type);
            _lastType == 'GET' ?
                (lastUrl.indexOf(data) == -1 && (data += lastUrl)) :
                (lastUrl.indexOf(url) == -1 && (url += lastUrl));
			$.ajax({
				url:url,
				data:data,
				dataType:'json',
				type:_lastType,
				success:function(json){
		    	    rowStack = {};
		            checkboxStack = [];
		    	    tbody.empty();
		    	    checkallCtrl.length && (checkallCtrl[0].checked = false);
					jsonLoaded(json);
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
					tbody.find('>tr').length ? tfoot.hide() : setMsg(LANG.noData);

					typeof callback == 'function' && callback(json.total);
				}
			});
    	};

    	// public method
    	this.load = function(data,type) {
    	    data && (_olddata = data.jquery ? data.serialize() : data);
			if (_olddata) {
				lastUrl	= '&' + _olddata;
			}
    	    _load(baseUrl, _olddata, function(totalSize){
    	        pager.length && pager.pagination(totalSize, pageOption);
    	    },type);
    	};
        this.reload = function(page) {
            if (!page && pager.length) {
                page = parseInt(pager.find('[class=current]').html());
            }
            loadPage(page - 1);
        };

        // control small btn
        $.each(headers,function(){
            // bind click event to em
            var visable = false;
            var icallout = null;
            var clearIcallout = function(){
                icallout && (clearTimeout(icallout), (icallout = null));
            };
            var ivalhide = null;
            var clearIvalhide = function(){
                ivalhide && (clearTimeout(ivalhide) , (ivalhide = null));
            };
            var th = this;
            var ihide = function(){
        		clearIcallout();
                visable && ivalhide || (ivalhide = setTimeout(hideDiv, 5000));
            };
            var ishow = function(){
                clearIvalhide();
                visable || icallout || (icallout = setTimeout(function(){
        			em.click();
        		},800));
            };
            var em = th.find('>em').click(function(e){
                visable ? hideDiv() : showDiv();
            }).hover(ishow,ihide);

            var name = em.attr('name');
            var hidDiv = $('#'+name).hover(clearIvalhide, ihide);
            var blurDiv = function(e){
                hidDiv[0] == e.target || hidDiv.find(e.target.nodeName).index(e.target) != -1 || hideDiv();
                return true;
            };

            var hideDiv = function(){
                clearIvalhide();
                clearIcallout();
                doc.unbind('click',blurDiv);
                em.removeClass('more_pop_open');
                visable = false;
                hidDiv.hide();
            };
            var showDiv = function(){
                clearIvalhide();
                clearIcallout();

        	    em.addClass('more_pop_open');
                var offset = em.offset();

                var css = {
                    top:(offset.top + parseInt(em.outerHeight())),
                    left: 0,
                    display:'block'
                };
                // calculator pop width
                if (hidDiv.css('width') == 'auto') {
                	css.width = th.width() - 14;
                } else {
                	css.width = parseInt(hidDiv.css('width'));
                }

                // get left, align left
                css.left = th.offset().left - css.width + th.width() - 14;
                hidDiv.css(css);
                visable = true;
                // hack avoid close in open
                setTimeout(function(){
                    doc.bind('click',blurDiv);
                },0);
            };

            var div = th.find('>div');
            var name = div.attr('name');
            if (!name) {
                return;
            }
            div.click(function(e){
                var direct = th.hasClass('headerSortDown') ? 'desc' : 'asc';
                _olddata = 'orderby='+name+'%7C'+direct;
                _load(baseUrl, _olddata, function(totalSize){
        	        pager.length && pager.pagination(totalSize, pageOption);
        	        cells.removeClass('headerSortDown headerSortUp');
                	th.addClass(direct == 'asc' ? 'headerSortDown' : 'headerSortUp');
        	    });
            });
        });
    };
})(cmstop,jQuery);
