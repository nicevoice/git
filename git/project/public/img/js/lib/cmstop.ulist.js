/**
 * Table application for cmstop
 *
 * Base on:
 *  cmstop.js &
 *  jquery.js &
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
		var tbody = $(elem).find("ul");
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


        var sortDisabled = {};
        var headers = [];

        var rowStack = {};
        var checkboxStack = [];
        var nosortNum = 0;

        var tfoot = $('<div class="empty"><table width="100%"><tr><td width="100%" align="center">'+LANG.loading+'</td></tr></table></div>');
        var setMsg = function(msg) {
        	tfoot.find('td').html(msg);
        	tfoot.show();
        };
        tbody.after(tfoot);
        // toggle check-all & un-check-all checkbox-control
        var checkallCtrl = $('#checkAll');
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
    	        html = html.replace(new RegExp('{'+key+'}',"gm"), json[key]);
    	    }

    	    wrapper.innerHTML = "<ul>"+html+"</ul>";

    	    // create a li
            var li = $(wrapper.firstChild.firstChild);
    	    var id = li.attr('id').substr(rowIdPrefix.length);

    	    // add li to stack
    	    rowStack[id] = li;

    	    // init hover event
    	    li.hover(function(){
    	        li.addClass('over');
    	    },function(){
    	        li.removeClass('over');
    	    });

    	    // init click event
    	    var checkbox = li.find('input:checkbox');
    	    // has checkbox? bind event to checkbox
    	    if (checkbox.length)
    	    {
        	    li.bind('check',function(){
        	        // toggle seleted
        	        (li.addClass('row_chked'), (checkbox[0].checked = true));
        	    }).bind('unCheck',function(){
        	        (li.removeClass('row_chked'), (checkbox[0].checked = false));
        	    });
        	    var togglechk = function(e){
        	        // toggle seleted
        	        e.stopPropagation();
			doc.click();
		        var flag = checkbox[0].checked;
		        e.target == checkbox[0] && (flag = !flag);
        	        li.trigger(flag ? 'unCheck' : 'check');
        	    };
        	    checkbox.click(togglechk);
        	    li.click(togglechk);

        	    // add checkbox to stack and bind function to beforeRemove
        	    checkboxStack.push(checkbox);
        	    li.bind('beforeRemove',function(){
        	        var index = checkboxStack.indexOf(checkbox);
        	        index != -1 && checkboxStack.splice(index, 1);
        	        delete rowStack[id];
        	    });
    	    }
    	    // no checkbox
    	    else
    	    {
    	        li.click(function(){
    	        	li.trigger('check');
    	        }).bind('check',function(){
    	        	if (lastFocused == li) return;
    	        	lastFocused && lastFocused.trigger('unCheck');
    	        	lastFocused = li;
    	            li.addClass('row_chked');
    	        }).bind('unCheck',function(){
    	            li.removeClass('row_chked');
    	        }).bind('beforeRemove',function(){
    	            delete rowStack[id];
    	        });
    	    }
    	    // init dblclick event
    	    li.dblclick(function(){
				dblclickHandler(id, li, json);
    	    });
    	    if ($.fn.contextMenu) {
	    	    li.bind('contextMenu',function(){
	    	        for (var id in rowStack)
	    	        {
	    	            rowStack[id].trigger('unCheck');
	    	        }
	    	        li.trigger('check');
	    	    });
	    	    // init right menu
	    	    li.contextMenu('#'+(li.attr('right_menu_id') || rightMenuId),
	    		function(action) {
	    			var callback = ct.func(action);
	    			callback && callback(id, li, json);
	    		});
    	    }

    	    return li;
    	}
    	// public method
    	this.addRow = function(json, prepend)
    	{
    		$('div.empty').remove();
    	    var li = buildRow(json);
    	    prepend ? tbody.prepend(li) : tbody.append(li);
    	    rowCallback(li.attr('id').substr(rowIdPrefix.length), li);
    	    elem.trigger("update");
    	    tbody.find('>li').length && tfoot.hide();
    	    return li;
    	};
    	// public method
    	this.updateRow = function(id,json)
    	{
    	    var li = rowStack[id];
    	    li.trigger('beforeRemove');
    	    var nli = buildRow(json);
    	    li.replaceWith(nli);
    	    nli.trigger('check');
    	    rowCallback(id, nli);
    	    elem.trigger("update");
    	    return nli;
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
	            var li = rowStack[id[i]];
        	    li && (
        	        li.trigger('beforeRemove'),
        	        li.remove()
        	    );
	        }
	        tbody.find('>li').length || setMsg(LANG.noData);
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
			$.ajax({
				url:url,
				data:data,
				dataType:'json',
				type:_lastType,
				success:function(json){
		    	    rowStack = {};
		            checkboxStack = [];
		    	    tbody.empty();
		    	    tbody.empty();
		    	    checkallCtrl.length && (checkallCtrl[0].checked = false);
					jsonLoaded(json);
					// fillin with new data
					for (var i=0,item;item=json.data[i++];)
					{
						var li = buildRow(item);
						tbody.append(li);
					}
					elem.trigger("update");
					for (var id in rowStack)
					{
						rowCallback(id, rowStack[id]);
					}
					tbody.find('>li').length ? tfoot.hide() : setMsg(LANG.noData);

					typeof callback == 'function' && callback(json.total);
				}
			});
    	};

    	// public method
    	this.load = function(data,type) {
    	    data && (_olddata = data.jquery ? data.serialize() : data);
    	    _load(baseUrl, _olddata, function(totalSize){
    	        pager.length && pager.pagination(totalSize, pageOption);
    	    },type);
    	}

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