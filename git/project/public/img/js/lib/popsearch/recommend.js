/*
@cmstop 来自搜索引擎的搜索推荐
使用方法
	@依赖jquery.js config.js
	引入CSS文件及此文件
	须使用config.js中定义的APP_URL
*/

(function($){
	function Popmenu( wrap,se, c){
		this.wrap = $(wrap);
		//用户开启和关闭的开关element
		this.c = $(se);
		this.co = $(c);
		//PopMenu的状态：1、显示状态     2、隐藏状态
		this.status = false;
		//前10秒钟，监听用户是否对popmenu进行了操作
		this.ctrl = false;
		//滑出的最大值和最小值
		this.maxH = 0;
		this.minH = 0;
	}
	Popmenu.prototype = {
			init: function(){
				var self = this;
				this.inner = this.wrap.find('.inner');
				this.maxH = this.inner.height();
				this.minH = this.wrap.find('.hd').height();
				this.wrap.css({
						position: 'fixed',
						left: 0,
						bottom:0
				});
				this.inner.css('height',this.minH);
				
				this.co.click(function(){
						self.wrap.css('display','none');
						return false;
				});
				
				this.c.click(function(){
					self.ctrl = true;
					if(self.status == true){
						self.hide();
					}else{
						self.show();
					}
					return false;
				});

				this.show();
				// 如果30秒钟内，用户还未对popmenu进行操作，则自动隐藏
				window.setTimeout(function(){
						if(self.status == true && self.ctrl == false ){
							self.hide();
						}
				},30000);
				
				if ($.browser.msie) {
					if ($.browser.version == "6.0"){
						self.wrap.css({
							'position':'absolute',
							'top':'150px',
							'bottom':'auto'
						})
						new ChuteModel().init(self.wrap);
					}
				}
			},
			hide: function(){
				var self = this;
				this.inner.animate({
						height: self.minH
				},1000,function(){
						self.c.attr('title','展开');
						self.c.removeClass('close').addClass('open');
						self.status = false;
				});
			},
			show: function(){
				var self = this;
				this.inner.animate({
						height: self.maxH
				},1000,function(){
						self.c.attr('title','隐藏');
						self.c.removeClass('open').addClass('close');
						self.status = true;
				});
			}
	}
var recommend = {
	referrer : document.referrer,
	url : '',
	map : {'google':'q','baidu':'wd','soso':'w','sogou':'query','youdao':'q','bing':'q','yahoo':'p'},
	charset : {'google':'utf-8','baidu':'gbk','soso':'gbk','sogou':'gbk','youdao':'utf-8','bing':'utf-8','yahoo':'utf-8'},
	init : function(app_url) {
		this.url = app_url+'?app=search&controller=index&action=tag&&jsoncallback=?';
		if(this.referrer != '') {
			var search = this.getSeachProvider(this.referrer);
			var charset = this.getChaset(this.referrer,search);
			if(search != '') {
				var wd = this.parseGet(this.referrer,this.map[search]);
				if(wd != '') {
					this.get(wd,charset);
				}
			}
		}
	},
	get : function(wd,charset) {
		var _this = this;
		$.getJSON(
			_this.url,
			{'wd':wd,'charset':charset,'pagesize':10},
			function(json){
				if(json.data) {
					_this.show(json.wd,json.data,json.url);
				}
			}
		);
	},
	getChaset : function(url,search) {
		var c = this.parseGet(url,'ie');
		if(!c) {
			c = this.charset[search];
		}
		return c;
	},
	show : function(wd,data,url) {
		var tpl = '<div class="pop-menu" id="popMenu"><div class="inner">\
					<div class="hd"><h3 class="title">您可能感兴趣的内容</h3><a href="#" class="close" title="隐藏" id="switchE"></a><a href="#" class="closes" title="关闭" id="close"></a></div>\
					<div class="bd"><ul class="list"></ul></div>\
					<div class="fd"><a href="#" class="search-more">更多<span></span>的相关内容 &gt&gt </a></div>\
				</div></div>';
		var div = $(tpl);
		var ul = div.find('ul.list');
		var more = div.find('a.search-more');
		var html = '';
		for(var i in data) {
			html += '<li><a href="'+data[i].url+'">'+data[i].title+'</a></li>';
		}
		ul.append(html);
		more.find('span').html(wd.substring(0,12));
		more.attr('href',url);
		div.appendTo(document.body);
		new Popmenu('#popMenu','#switchE','#close').init();
	},
	getSeachProvider : function(url) {
		if(url.indexOf('www.google.com')>0) return 'google';
		if(url.indexOf('www.baidu.com')>0) return 'baidu';
		if(url.indexOf('www.soso.com')>0) return 'soso';
		if(url.indexOf('www.sogou.com')>0) return 'sogou';
		if(url.indexOf('www.youdao.com')>0) return 'youdao';
		if(url.indexOf('cn.bing.com')>0) return 'bing';
		if(url.indexOf('search.cn.yahoo.com')>0) return 'yahoo';
		return '';
	},
	//传递第二个key则返回一个值，否则返回整个parse的GET数组
	parseGet : function(url,key) {
		var url = url.split("?");
		var gets = new Array();
		if(!url[1]) return '';
		var argv = url[1].split("&");
		var find = true;
		for(var i=0;i<argv.length;i++) {
			var get = argv[i].split("=");
			if(key) {
				if(get[0] == key){
					return get[1];
				} else {
					find = false;
				}
			} else {
				var t = {};
				t[get[0]] = get[1];
				gets.push(t);
			}
		}
		if(find == false) return '';
		return gets;
	}
}
	$(document).ready(function(){
		recommend.init(APP_URL);
	});
})(jQuery);
