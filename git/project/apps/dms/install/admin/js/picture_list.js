var picture_list = function(elem, option) {
	var _this = this;
	var default_pagesize	= 10;	// config default pagesize
	var nav			= typeof (option.nav) == 'undefined' ? true : false;

	if (nav) {
		var page		= parseInt(option.page)||1;
		var pagesize	= parseInt(option.pagesize)||default_pagesize;
	} else {
		var page		= 1;
		var pagesize	= 99999;
	}
	
	var baseUrl		= option.baseUrl;
	var template	= option.template;
	var where		= option.where || null;
	var statusBar	= option.statusBar;
	var total		= 0;
	var data		= '';
	var obj			= $(elem);
	var pic_list	= null;
	var pic_nav		= null;
	var elem		= elem;

	var _buildTemplate = function(json) {
		var html	= '';
		var line	= '';
		var item;
		for (var i in json) {
			item	= json[i];
			if (!item) {
				continue;
			}
			line	= template;
			for (var key in item)
			{
				line = line.replace(new RegExp('{'+key+'}',"gm"), item[key] || '');
			}
			html += line;
		}
		return html;
	};

	var	_loadPage = function(size) {
		_this.setPage(size+1);
	};

	/* get or set page */
	_this.getPage = function() {
		return page;
	};
	_this.setPage = function(size) {
		page	= parseInt(size)||page;
		_this.reload();
	};
	_this.nextPage = function() {
		if ((page + 1) * pagesize <= total) {
			page++;
			_this.reload();
		}
	};
	_this.prevPage = function() {
		if (page > 1) {
			page++;
			_this.reload();
		}
	};

	/* get or set pagesize */
	_this.setPagesize = function(size) {
		pagesize	= parseInt(size)||pagesize;
		_this.reload();
	};
	_this.getPagesize = function() {
		return pagesize;
	};

	/* get total */
	_this.getTotal = function() {
		return total;
	};

	/* search */
	_this.search = function(_where) {
		where = _where
		_this.reload();
	}

	_this.reload = function() {
		$.getJSON(baseUrl + '&' + where, {'page':page, 'pagesize':pagesize}, function(json) {
			json.total = parseInt(json.total);
			if (json.total > 0) {
				total	= json.total;
				data	= _buildTemplate(json.data);
				pic_list.empty().append(data);
			} else {
				pic_list.empty().append('<div class="no-data">暂无数据</div>');
			}
		});
	};
	var init = function() {
		pic_list	= $('<div id="' + elem + '_list"></div>');
		pic_nav		= $(statusBar);
		obj.append(pic_list);
		$.getJSON(baseUrl, {'page':page, 'pagesize':pagesize}, function(json) {
			json.total = parseInt(json.total);
			if (json.total > 0) {
				total	= json.total;
				data	= _buildTemplate(json.data);
				pic_list.empty().append(data);
			} else {
                pic_list.empty().append('<div class="no-data">暂无数据</div>');
            }
			if (nav) {
				pic_nav.pagination(_this.getTotal(), {
					'items_per_page' : pagesize,
					'callback' : _loadPage,
					'prev_text': '&#x4E0A;&#x4E00;&#x9875;',
					'next_text': '&#x4E0B;&#x4E00;&#x9875;'
				});
			}
		});
	};
	init();
}