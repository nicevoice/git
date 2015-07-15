$.fn.isnot = function(filter, is, not){
	return this.each(function(i){
		(typeof filter == 'function'
			? filter.call(this)
			: ($.multiFilter(filter, [this]).length > 0)
		) ? (is && is.apply(this, [i]))
		  : (not && not.apply(this, [i]));
	});
};
(function(){
var _channel,_model,_date,_size,_page,_auto,
	channels, models, condDate,
	maxTimer = 180,
	auto, autoOn, autoOff, timer, autoIval, least,
	pager, pagination, dataContainer,
_where = function(){
	var where = [];
	_channel && where.push('channel='+_channel);
	_model && where.push('model='+_model);
	_date && where.push('date='+encodeURIComponent(_date));
	_size && where.push('size='+_size);
	_page && where.push('page='+_page);
	
	return where.join('&');
},
_fillData = function(data){
	var html = [];
	var k = 0;
	html[k] = ['<ul>'];
	var h = html[k];
	for(var i=0,l=data.length;i<l;i++)
	{
		var d = data[i];
		h.push('<li><span class="date">'+d.date+'</span><span class="tabs-name"><a target="_blank" href="'+d.caturl+'">['+d.catname+']</a></span><span class="cont-list02"><a target="_blank" href="'+d.url+'">'+d.title+'</a></span></li>');
		if ((i+1)%5 == 0) {
			h.push('</ul>');
			html[++k] = ['<ul>'];
			h = html[k];
		}
	}
	h.push('</ul>');
	var shtml = '';
	for(var j=0;j<=k;j++) {
		if (html[j].length>2) {
			shtml += html[j].join('')+'<div class="hr-dotted hr-h12 mar-tb-6 clear"></div>';
		}
	}
	dataContainer.html(shtml);
};
var APP = {
	init:function(){
		_channel = parseInt((/[#&]channel=(\d*)/i.exec(location.hash)||{1:0})[1]);
		_model = parseInt((/[#&]model=(\d*)/i.exec(location.hash)||{1:0})[1]);
		_date = (/[#&]date=([\d\-]*)/i.exec(location.hash)||{1:''})[1];
		_size = parseInt((/[#&]size=(\d*)/i.exec(location.hash)||{1:30})[1]);
		_page = parseInt((/[#&]page=(\d*)/i.exec(location.hash)||{1:1})[1]);
		
		channels = $('#cond-channel>li').each(function(){
			var a = $('a', this);
			var ch = a[0].getAttribute('href', 2);
			_channel == ch && $.className.add(this, 'on');
			a.click(function(e){
				e.preventDefault();
				APP._channel != ch && APP.channel(ch);
			});
		});
		
		condDate = $('#cond-date');
		var d;
		if (_date) {
			d = _date.split('-');
			condDate.text(d[0]+'年'+d[1]+'月'+d[2]+'日');
		} else {
			d = /(\d+)年(\d+)月(\d+)日/.exec(condDate.text());
			d.shift();
			_date = d.join('-');
		}
		$('#datepicker').click(function(e){
			var dp = DatePicker(this, {'format':'yyyy-MM-dd', 'value':function(v) {
				if (typeof (v) != 'undefined') {
					APP.date(v);
				}
			}});
			e.preventDefault();
		});
		
		models = $('#cond-model>li').each(function(){
			var a = $('a', this);
			var md = a[0].getAttribute('href', 2);
			_model == md && a.addClass('on');
			a.click(function(e){
				e.preventDefault();
				APP._model != md && APP.model(md);
			});
		});
		
		$('#hand').click(function(){
			APP.query();
		});
		
		auto = $('#auto').click(function(){
			APP.auto(this.checked);
		});
		autoOn = $('#auto-on');
		least = maxTimer;
		timer = autoOn.find('b');
		autoOff = $('#auto-off');
		this.auto(true);
		
		pageOption = {
        	callback: function(index){
        		APP.page(index+1);
        	},
        	current_page:_page,
        	items_per_page:_size,
        	num_display_entries:10,
        	num_edge_entries:2,
        	prev_text:'上一页',
        	next_text:'下一页'
        };
        pager = $('#pagination');
        
        dataContainer = $('#data-container');
        
        this.query();
	},
	query:function(){
		least = maxTimer;
		var where = _where();
		location.hash = where;
		
		$.getJSON(APP_URL+'roll.php?do=query&callback=?',where,
		function(json){
			pageOption.current_page = json.page-1;
			pageOption.items_per_page = json.size;
			_page = json.page;
			_size = json.size;
			location.hash = _where();
			json.total > json.size
				? pager.pagination(json.total, pageOption)
				: pager.empty();
			_fillData(json.data);
		});
	},
	channel:function(channel){
		channels.isnot(function(){
			return $('a', this)[0].getAttribute('href', 2) == channel;
		},function(){
			$.className.add(this, 'on');
		},function(){
			$.className.remove(this, 'on');
		});
		_channel = channel;
		this.query();
	},
	model:function(model){
		models.isnot(function(){
			return $('a', this)[0].getAttribute('href', 2) == model;
		},function(){
			$('a', this).addClass('on');
		},function(){
			$('a', this).removeClass('on');
		});
		_model = model;
		this.query();
	},
	date:function(date){
		var d = date.split('-');
		condDate.text(d[0]+'年'+d[1]+'月'+d[2]+'日');
		_date = date;
		this.query();
	},
	page:function(page){
		_page = page;
		this.query();
	},
	auto:function(flag){
		if (!flag == !autoIval) return;
		if (flag) {
			autoOn.show();
			timer.text(least);
			autoOff.hide();
			auto[0].checked = true;
			autoIval = setInterval(function(){
				timer.text(--least);
				if (least < 1) {
					APP.query();
				}
			}, 1000);
		} else {
			autoOn.hide();
			autoOff.show();
			auto[0].checked = false;
			clearInterval(autoIval);
			least = maxTimer;
			autoIval = null;
		}
	}
};
window.APP = APP;
})();