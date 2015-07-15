/**
 * @author samirtohope@gmail.com
 */
$.extend( $.GC, {
		LoadingText: '数据加载中...',
		DefaultThumb : $.GC.BaseUrl+'data/image/no_thumb.png',
		Component: {
			R: '#R', // Home页CardID Recommend
			D: '#D', // 单文章内容详情CardID Detail
			V: '#V', // 视频 Video
			P: '#P', // 组图 Photo List
			C: '#C', // 评论 Comment list
			PS: '#PS', // 组图详情页面 photo show
			CCL: '#CCL', // 栏目列表页
			A : '#A',	// 关于页面
			CS : '#CS', // 评论详情页
			L : '#L', // 登陆页
			tab: '#main-tab', // 底部切换Tab的 id
			logo: '#logo', // LOGO Img
			recatgory: 'recommend-category', // 首页栏目列表
			bodyID : 'desktop',
			fullscreenId : '#x-fullscreen', // 最外层fullscreen
			homeRecommendId:'#x-scroll-card-recommend', // 首页首页推荐列表外层元素
			homeRecommendListId: '#recommend-list', // 首页推荐列表
			detailId: 'x-detail-panel', // 内容区Panel id
			ccList: 'cat-cont-list-wrap', // 栏目列表页列表区id
			commentshowid : 'x-comment-show', // 评论内容区id
			commentlistid : 'comment_list_container_wrap', // 评论列表id
		},
		References: {
			/**
			 * fullscreen r:$.GC.References.fullscreen
			 * homeRecommend 推荐页banner 和 List的父ID
			 * x-card-D
			 * x-detail-panel
			 * tab 底部切换tab
			 * R 首页card
			 * D 详情页card
			 * V 视频列表
			 * PS 组图详情页
			 * CCL 栏目内容列表页
			 * CS 评论详情页
			 * CI 输入评论页
			 * CIW 评论输入框 comment-input-widget
			 * dropWrap 图片详情页图片区
			 * cat-cont-list-wrap  栏目列表页列表区
			 * Ab 关于页面
			 * comment_list_container_wrap 评论列表区
			 * cate_topbar  栏目页面顶部条
			 */
		},
		Ev: {
			tap : $.os['ios'] || $.os['android'] ? 'tap' : 'click'
		}
		
	});
 /**
  * define $.Viewport
  * 
  * properties:[
  * 	name,
  * 	width,
  * 	height
  * ]
  * 
  * Methods: [
  * 	init,
  * 	WH,
  * 	getViewportWidthHeight,
  * 	set,
  * 	get,
  * 	fit
  * ]
  * TODO: 定义视口对象$.Viewport
  */
 ( function ( $ ) {
 	
	$.isFunction( $.Viewport ) || ($.Viewport = function () {});

	$.extend( $.Viewport.prototype, {
		
		init: function () {
			this.WH();
			this.fit();
			this.placeLogo();
			return this;
		},
		
		WH: function() {
			this.getViewportWidthHeight();
		},
		
		getViewportWidthHeight: function () {
			var wiewWH = {
				w : $.WIDTH,
				h : $.HEIGHT
			}
			
			if(isNaN($.Viewport.width)) 
				$.Viewport.width = wiewWH.w; 
			if(isNaN($.Viewport.height)) 
				$.Viewport.height = wiewWH.h;
				
			this.set('width',wiewWH.w);
			this.set('height',wiewWH.h);
			return wiewWH;
		},
		
		set: function ( k, value ) {
			this[k] = value;
		},
		
		get: function ( k ) {
			return this[k];
		},
		
		fit: function () {
			$.GC.References.fullscreen.css({
				height:  $.Viewport.height
			});
		},
		
		placeLogo : function( ) {
			$($.GC.Component.logo).attr('src',GlobalData[0].logo);
		}
		
	});
	
	/**
	 *  TODO: 模拟滚动条，采用iScroll插件
	 *  @loadiScroll 加载滚动条
	 *  @iScroll 加载和刷新滚动条，当内容改变时，刷新滚动条
	 */
	$.extend( $.Viewport , {
		iscrolls : {},
		
		loadiScroll : function( id ) {
			var o = {};
			o[id] = new iScroll( id , {
				bounce: true, // 是否反弹
				hideScrollbar: 'isIDevice', //隐藏滚动条
				/**
				 * TODO: 修复Input,Ttextarea无法输入Bug
				 */
				onBeforeScrollStart : function ( e ) { 
					var target = e.target;
					while (target.nodeType != 1) target = target.parentNode;
		
					if (target.tagName != 'SELECT' && target.tagName != 'INPUT' && target.tagName != 'TEXTAREA')
						e.preventDefault();
				}
			});
			$.extend( $.Viewport.iscrolls , o );
			o = null;
		},
		
		iScroll: function( id ) {
			if( $.Viewport.iscrolls[ id ] ) {
				$.Viewport.iscrolls[ id ].refresh();
			} else {
				$.Viewport.loadiScroll( id );
			}
		}
		
	});
 })( Zepto );
 
 function getHost(url) { 
  	var re = /[\w-]+\.(com|dev|loc|dev|net|org|gov|cc|biz|info|cn)(\.(cn|hk))*/;
	return re.exec(url)[0];
 }

 function comment_toggle( bak, active ) {
 	if(bak.attr('id') == 'CI' && $.GC.References['CIW'][0] ) {
		$.GC.References['CIW'].hide();
	}
	if(active.attr('id') == 'CI' && $.GC.References['CIW'][0] ) {
		$.GC.References['CIW'].show();
		$.Action.Login.index();
	}
 }
 
 function add_calss_animation( elem , cls ) {
 	// IOS平台下的动画全部采用CSS3，因为该平台能够较好的支持CSS3动画，其他平台采用CSS变换
 	if ($.os.ios){
		elem.toggleClass(cls);
		setTimeout(function() {
			elem.toggleClass(cls);
		} , 350 );
	}
 }
 /**
  * TODO: History
  * @param {Object} $
  */
 ( function( $ ) {
 	
 	$.History || ($.History = {});
	
	$.extend( $.History , {
		
		activeCls: 'x-card-active',
		
		init: function ( defobj ) {
			this.navigate.push( defobj );
		},
		
		navigate : [],
		
		add: function ( active ) {
			var bak = this.navigate[this.navigate.length - 1];
			this.switchover( bak , active );
			this.navigate.push( active );
			this.menu( active );
			comment_toggle( bak , active );
		},
		
		back: function () {
			var bak = this.navigate[this.navigate.length - 1], active = this.navigate[this.navigate.length - 2];
			this.switchover(  bak , active );
			this.navigate.pop();
			this.menu( active );
			comment_toggle( bak , active );
		},
		
		switchover: function ( back , active ) {
			var cls = this.activeCls,Attr = $.Lang.data , duration = 300 , easing = 'ease-in-out';
			if( Attr(back , 'left') === null ) 
				Attr( back , {left: -200});
			
			if( Attr(active , 'left') === null ) 
				Attr( active , {left: -100});
			
			var v = parseInt( Attr(back , 'left')) > parseInt( Attr(active , 'left')) ? '0%' : '-200%';
			
			back.animate({translateX : v } , duration , easing , function() {
				Attr( back , {left: v});
				back.removeClass( cls )
			});
			
			active.animate({translateX: '-100%'} , duration , easing , function() {
				Attr( active , {left: -100});
				active.addClass(cls);
			});
		},
		
		menu : function ( active )  {
			var that = this, tab = $.GC.References['tab'], show = false;
			if( !this.refs ) {
				this.refs = [];
				tab.find('.x-tab-button').each( function () {
					var r = $.Lang.data( $(this) ,'panel');
					that.refs.push( r );
				});
			} 
			
			$.each( this.refs, function( i, item ) {
				if( item  == active.attr('id') ) show = true;
			});
			
			//show == true ? this.anmi(1) : this.anmi(0);
			show === true ? tab.show() : tab.hide();
		},
		
		anmi: function( v ) {
			var tab = $.GC.References['tab'];
			tab.anim({ opacity: v}, 1.5, 'ease-out');
		}
		
	});
	
 })( Zepto );
 
 /**
  * define $.Sheet 
  * TODO: Sheet层，用于数据加载状态的提示
  * @param {Object} $
  */
 ( function( $ ) {
 	$.isFunction( $.Message ) || ( $.Message = function( options ) {
		this.name = '$.Sheet';
		this.options = options;
		this.init();
	});
	$.extend( $.Message.prototype, {
		
		init: function() {
			this.add();
		},
		
		add: function() {
			this.xsheet = $('<div class="x-sheet"></div>');
			this.xsheet.css({
				'width': '100%',
				'height': $.Viewport.height,
				'zIndex': 1000
			});
			var xsheet_box = $('<div class="x-sheet-box"><img class="x-img-loading" src="'+$.GC.BaseUrl+'ui/css/ui/load.gif" alt="" /><span class="x-sheet-text">'+$.GC.LoadingText+'</span></div>');
			this.xsheet.append(xsheet_box);
			$.GC.References['fullscreen'].append(this.xsheet);
		},
		
		clear: function() {
			this.xsheet.remove();
		}
		
	});
	$.extend( $.Message , {
		msg : function ( msg ,rel ) {
			var Ref = $.GC.References,
				tip = $('<div class="x-msg">'+msg+'</div>').appendTo( Ref['fullscreen'] );
			if( rel ) {
				var t = rel.offset().top , h = rel.height();
				tip.css({
					top : t + h +'px'
				});
			}
			tip.css({
				left : (($.Viewport.width - tip.width()) / 2 ) + 'px'
			});
			window.setTimeout( function() {tip.remove();} , 2000 );
		}
	});
 })( Zepto );
 
 
 ( function( $ ) {
 	$.Lang || ( $.Lang = {} );
	
	$.extend( $.Lang, {
		
		data: function( el, s, fields ) {
			if( typeof s == 'string') {
				return el.attr('data-' + s);
			} else {
				for( var key in s ) {
					if( $.isArray(fields) ) {
						for( var i = 0, len = fields.length ; i < len; i++ ) {
							if( fields[i] == key ) {
								el.attr('data-'+key, s[key]);
							}
						}
					} else {
						if( s[key] !='' ) el.attr('data-'+key, s[key]);
					}
				}
			}
		},
		
		cookie : {
			set: function (name, value, expires, path, domain, secure) {
		        var cookieText = encodeURIComponent(name) + "=" + encodeURIComponent(value);
		    
		        if (expires instanceof Date) {
		            cookieText += "; expires=" + expires.toGMTString();
		        }
		    
		        if (path) {
		            cookieText += "; path=" + path;
		        }
		    
		        if (domain) {
		            cookieText += "; domain=" + domain;
		        }
		    
		        if (secure) {
		            cookieText += "; secure";
		        }
		
		        document.cookie = cookieText;
		    },
			get : function ( name ) {
				var cookieName = encodeURIComponent(name) + "=",
		            cookieStart = document.cookie.indexOf(cookieName),
		            cookieValue = null;
		            
		        if (cookieStart > -1){
		            var cookieEnd = document.cookie.indexOf(";", cookieStart)
		            if (cookieEnd == -1){
		                cookieEnd = document.cookie.length;
		            }
		            cookieValue = decodeURIComponent(document.cookie.substring(cookieStart + cookieName.length, cookieEnd));
		        } 
		        return cookieValue;
			},
			unset: function (name, path, domain, secure){
		        this.set(name, "", new Date(0), path, domain, secure);
		    }
		},
		
		Ev : {
			one : function( elem , type, fn ){
				if( !elem.data( type ) ) {
					elem.bind( type , fn );
					elem.data( type , type );
				}
			}
		}
		
	});
 })( Zepto );
 
 
 ( function( $ ) {
 	( $.Data && $.isObject( $.Data ) ) || ( $.Data = {} );
	$.extend( $.Data , {
		Store: function( opts ) {
			if(opts.proxy.type == 'ajax') 
			{
				var conf = {};
				for( var key in opts.proxy ) 
				{
					if(key == 'type') continue;
					else conf[key] = opts.proxy[key];
				}
				if( opts.model == 'category' || opts.model == 'article' || opts.model == 'picture' ) {
					var k = opts.model + (opts.proxy.params.catid || opts.proxy.params.contentid);
					this.process( conf , k);
				} else {
					this.process( conf , opts.model );
				}
			}
		},
		process : function( conf , key ) {
			var is_history;
			if($.jStorage.get(key)) 
			{
				var data = $.jStorage.get(key);
				conf['success']( data );
				is_history = false;
			}
			this.Ajax( conf , key , is_history );
		},
		save: function( key ,data) {
			$.jStorage.set( key , data );
		},
		Ajax: function( options , key , _history ) {
			var panel_sheet = null , is_sheet = 0;
			$.ajax({
				type: options.type || 'GET',
				url: $.GC.DataUrl,
				data: options.params,
				dataType: 'json',
				timeout: 10000,
				beforeSend: function() {
					if(_history !== false ) {
						is_sheet = setTimeout(function() {
							panel_sheet = new $.Message();
						} , 50 );
					}
				},
				error : function() {
					if( panel_sheet ) panel_sheet.clear();
					$.Message.msg('远程数据加载失败!');
				},
				complete : function(){
					if( is_sheet ) clearTimeout( is_sheet );
				},
				success: function( root ) {
					if( panel_sheet ) panel_sheet.clear();
					options.success( root , _history );
					if( key ) $.Data.save( key , root );
				}
			});
		}
	});
 })( Zepto );
 
 /**
  * TODO: 负责界面的渲染
  * @param {Object} $
  */
 ( function( $ ) {
 	$.View || ( $.View = {} );
	
	$.extend( $.View, {
		
		create : function( key, data ) {
			this[key]( data , arguments[2] || null );
		},
		
		about : function () {
			var Ref = $.GC.References, Cmp = $.GC.Component['A'];
			if( ! Ref[Cmp] ) {
				var A = $.View.panel({
					id : Cmp.split('#')[1],
					parent: Ref.fullscreen
				});
				Ref[Cmp] = A;
				$.View.toolbar( 'about' , {
					parent: Ref[Cmp],
					id: 'about-toolbar'
				});
			} else {
				var data = arguments[0];
				var wrap = $('<div class="x-about-content" id="about-content">').appendTo( Ref[Cmp] ),
					inner = $('<div class="x-inner">').appendTo( wrap );
					inner.html( data );
					
				var height = $.Viewport.height - wrap.prev().height();
					wrap.css('height',height);
					
				$.Viewport.iScroll( 'about-content' );
			} 
		},
		
		tab : function() {
			
			var Ref = $.GC.References,
				wrap = Ref.tab,
				data = GlobalData[0].model,
				view = ['R','V','P','C'];
			
			$.map( data, function( o, index ) {
				/**
				 * TODO: 创建 Tab 菜单
				 */
				var div = $('<div class="x-tab-button x-ui-blue-dark">').appendTo( wrap ),
					label = $('<span class="x-button-label">').html( o['title']).appendTo( div );
				if( index == 0 ) div.addClass('x-tab-focus');
					o['panel'] = view[index];
					$.Lang.data( div, o );
				/**
				 * TODO: 创建Tab对应的Panel
				 */
				if( !Ref[div.attr('data-panel')] && div.attr('data-panel') != 'R' ) 
				{
					var _div = $('<div>')
						.addClass('x-card')
						.attr('id', div.attr('data-panel') )
						.appendTo( Ref.fullscreen );
						
						Ref[div.attr('data-panel')] = _div;
				}
				/**
				 * TODO: 添加动作
				 */
				$.Action.tab( div );
			});
		},
		
		category : function() {
			
			var wrap = $('#'+$.GC.Component.recatgory);
			var data = window.GlobalData[0]['category'], 
				clsA = 'x-menu-button',
				clsB = 'x-menu-button-label';
				max = 4, 
				k = 0, 
				baseLeft = 4;
			/**
			 * TODO: 存放未显示在首页上的栏目，避免重复筛选
			 */
			GlobalData.CateMore = [];
			
			$.map( data, function( o, index ) {
				
				if( ( o['parentid'] == '' || o['parentid'] == 0 || o['parentid'] == null ) && k < max) {
					
					var div = $('<div>').addClass(clsA),
						label = $('<span>').addClass(clsB).html( o['name']).appendTo( div );
						div.appendTo( wrap );
						
					if( k == 0 ) div.css('left', baseLeft );
					else div.css('left', baseLeft + div.prev().width() + parseInt(div.prev().css('left')) );
					k++;
					
					$.Lang.data( div, o , ['catid']);
					
					$.Action.category( div );
					
				} else {
					GlobalData.CateMore.push( o );
				}
			});
			var cate_ = $('#select_cate_home');
			this.set_center( cate_ );
			$.Action.toggle_cate( cate_ ); 
		},
		
		set_center : function( elem ) {
			elem.css({
				'left' : ( $.WIDTH -  elem.width() ) / 2 + 'px'
			});
		},
		
		list : function( view , root ) {
			var data = root[0]['list'];
			/**
			 * TODO: 创建视频列表并绑定事件
			 * 	1、绘制界面和刷新界面
			 */
			switch( view ) {
				
				case 'video' : 
					var is_more = arguments[2];
					
					var V = $.GC.References['V'], id = 'x-video-list-scrollbar', cls = 'x-video-list-panel', vid = 'x-list-container-video';
					
					if( $.Lang.data( V, 'created') != '1' ) {
						
						var container 	= $('<div>').addClass('x-panel x-layout-fit x-no-scroll-xy' +' '+ cls).attr('id',id),
							inner		= $('<div>').addClass('inner').appendTo( container ),
							wrap  		= $('<div>').addClass('x-list-container').attr('id', vid ).appendTo( inner ),
							more = $('<div class="load-more"><input type="button" value="更多..." /></div>').appendTo( inner );
						
						$.Lang.data( more , {view: 'video'} );
						$.Action.More.bind(more);
						
						container.appendTo( V );
					}
					else if(!is_more)
						$('#'+vid).empty();
						
					$.Action.More.button( more || V.find('.load-more') , parseInt(root[0]['more']) );
					
					$.each( data , function( i, r ) {
						var r_thumb = ( r['thumb'] != false ) ? r['thumb'] : $.GC.DefaultThumb;
						var tpl =  '';
							tpl +=  '<div class="x-list-item" data-modelid="4" >';
							tpl += '<a href="'+r['url']+'">';
						if(r['thumb'] != false )	{
							tpl +=  '	<img src="'+r['thumb']+'" alt="" class="post-thumb" />';
							tpl +=  '	<div class="play-ico"></div>';
						}
							tpl +=  '	<div class="x-list-item-news">';
							tpl +=  '		<h3 class="post-title">'+r['title']+'</h3>';
							tpl +=  '		<p class="post-summary">时长：'+format_time(r['playtime'])+'</p>';
							tpl +=  '	</div>';
							tpl += '</a>';
							tpl +=  '</div>';
							
						var li = $( tpl ).appendTo( $('#'+vid) );
							$.Lang.data( li , r , ['contentid'] );
							$.Action.list( 'video', li );
					});
					
					setTimeout( function() {
						$.Viewport.iScroll( id );
					}, 500);
					
				break;
				
				
				case 'picture' :
					
					var is_more = arguments[2];
					
					var P = $.GC.References['P'], id = 'x-picture-list-scrollbar', cls = 'x-picture-list-panel', pid = 'x-list-container-picture';
					
					if( $.Lang.data( P, 'created') != '1' ) {
						
						var container 	= $('<div>').addClass('x-panel x-layout-fit x-no-scroll-xy' +' '+ cls).attr('id',id);
						var inner		= $('<div>').addClass('inner').appendTo( container );
						var wrap  		= $('<div>').addClass('x-list-container').attr('id', pid ).appendTo( inner );
						
						var more = $('<div class="load-more"><input type="button" value="更多..." /></div>').appendTo( inner );
						$.Lang.data( more , {view: 'picture'} );
						$.Action.More.bind(more);	
						
						container.appendTo( P );
					}
					else if(!is_more)
						$('#'+pid).empty();
						
					$.Action.More.button( more || P.find('.load-more') , parseInt(root[0]['more']) );
						
					var row = null, total = data.length;
					
					$.each( data , function( i, r ) {
						var r_thumb = ( r['thumb'] != false || r['thumb' != ''] ) ? r['thumb'] : $.GC.DefaultThumb;
						var tpl =  '';
							tpl +=  '<div class="x-list-item" data-modelid="2" >';
							tpl +=  '	<img src="'+ r_thumb+'" alt="" class="post-thumb" />';
							tpl +=  '	<div class="x-list-item-news">';
							tpl +=  '		<h3 class="post-title">'+r['title']+'</h3>';
							tpl +=  '	</div>';
							tpl +=  '</div>';
							
						if( i % 2 == 0 ) {
							row = $('<div class="x-list-picture-item-row"></div>');
							row.appendTo( $('#' + pid) );
						}
						
						var li = $( tpl ).appendTo( row );
							$.Lang.data( li , r , ['contentid'] );
							$.Action.list( 'picture', li );
						
						if( total%2 != 0 && total-1 == i ) {
							$('<div class="x-list-item">').appendTo(row);
						}
					});
					
					
					setTimeout( function() {
						$.Viewport.iScroll( id );
					}, 500 );
					
				break;
				
				case 'comment' :
					var is_more = arguments[2];
					
					var Ref = $.GC.References,  C = Ref['C'], id = 'x-comment-list-scrollbar', cls = 'x-comment-list-panel', vid = 'x-list-container-comment';
					
					if( $.Lang.data( C, 'created') != '1' ) {
						
						var container 	= $('<div>').addClass('x-panel x-layout-fit x-no-scroll-xy' +' '+ cls).attr('id',id);
						var inner		= $('<div>').addClass('inner').appendTo( container );
						var wrap  		= $('<div>').addClass('x-list-container').attr('id', vid ).appendTo( inner );
						var more = $('<div class="load-more"><input type="button" value="更多..." /></div>').appendTo( inner );	
						$.Lang.data( more , {view: 'comment'} );
						$.Action.More.bind(more);
						container.appendTo( C );
					}
					else if(!is_more)
						$('#'+vid).empty();
						
					$.Action.More.button( more || C.find('.load-more') , parseInt(root[0]['more']) );
						
					$.each( data , function( i, r ) {
						var tpl = '';
							tpl += '<div class="x-list-item">';
							tpl += '	<div class="comments">'+r['comments']+'</div>';
							tpl += '	<div class="title">'+r['title']+'</div>';
							tpl +=  '</div>';
							
						var li = $( tpl ).appendTo( $('#'+vid) );
							$.Lang.data( li , r , ['topicid','contentid'] );
							$.Action.list( 'comment', li );
					});
					
					setTimeout( function() {
						$.Viewport.iScroll( id );
					}, 500);
					
				break;
				
				case 'multi' :
					var is_more = arguments[3], parent = arguments[2] , container = null;
					
					if( !is_more ) {
						parent.find('.x-list-container').remove(),
						parent.find('.load-more').remove();
						container = $('<div>').addClass('x-list-container').appendTo( parent );
						
						if( parent.parent().parent().attr('id') != 'R') {
							var more = $('<div class="load-more"><input type="button" value="更多..." /></div>').appendTo( parent );	
								$.Lang.data( more ,{ view: 'category'} );
								$.Action.More.bind(more);
						}
					} else 
						container = parent.find('.x-list-container');
						
					$.Action.More.button( more || parent.find('.load-more') , parseInt(root[0]['more']) );
					
					
					$.each( data , function ( i , r ) {
						//var r_thumb = ( r['thumb'] != false ) ? r['thumb'] : $.GC.DefaultThumb;
						
						var tpl =  '';
							tpl +=  '<div class="x-list-item">';
						if( r['modelid'] == 4)	
							tpl += '<a href="'+r['url']+'">';
						
						if(r['thumb'] != false)	{
							tpl +=  '<img src="'+r['thumb']+'" alt="" class="post-thumb" />';
						}
						if( r['modelid'] == 4)	
						if(r['thumb'] != false)	tpl +=  '	<div class="play-ico"></div>';
							tpl +=  '	<div class="x-list-item-news">';
							tpl +=  '		<h3 class="post-title">'+r['title']+'</h3>';
						if( r['description'] != null )	
							tpl += '		<p class="post-summary">'+r['description']+'</p>';
							tpl += '		<span class="x-layout-fit post-time">'+r['published']+'</span>';
							tpl +=  '	</div>';
						if( r['modelid'] == 4 )
							tpl += '</a>'
							tpl +=  '</div>';
						var li = $( tpl ).appendTo( container  );	
							$.Lang.data( li , r , ['contentid','modelid'] );
						
						if( r['modelid'] == 1 ) 
							$.Action.list( 'article', li );	
						if( r['modelid'] == 2 ) 
							$.Action.list( 'picture', li );
						if( r['modelid'] == 4 ) 
							$.Action.list( 'video', li );
					});
					
					setTimeout( function() {
						$.Viewport.iScroll( parent.parent().attr('id') );
					}, 500);
					
				break;
				
			}
		},
		
		banner : function( ) {
			var _banner = '';
			if( $('#recommend-banner')[0] ) {
				_banner = $('#recommend-banner');
				_banner.empty();
			} else {
				_banner = $('<div class="x-banner-wrap" id="recommend-banner"></div>');
				_banner.appendTo( $.GC.References['homeRecommend'].find('.x-inner') );
			}
			
			var r = arguments[0],
				tpl = '';
			if(r['modelid'] == 4 ) 
				tpl += '<a href="'+r['url']+'" title="">';
				tpl += '	<img style="max-width:'+ $.Viewport.width+'px" src="'+r['thumb']+'" class="x-banner-image" />';
				tpl += '	<div class="x-banner-title x-layout-fit">';
				tpl += '		<h2 class="h2">'+r['title']+'</h2>';
				tpl += '	</div>';
			if(r['modelid'] == 4 ) 
				tpl += '</a>';	
			var banner_inner = $(tpl).appendTo(_banner);
			
			$.Lang.data( _banner ,r, ['modelid','contentid']);
			
			if( r['modelid'] == 1 ) 
				$.Action.list( 'article', _banner );	
			if( r['modelid'] == 2 ) 
				$.Action.list( 'picture', _banner );
			if( r['modelid'] == 4 ) 
				$.Action.list( 'video', _banner );
		},
		
		
		
		video : function() { 
			var V = $.GC.References['V'];
			/**
			 * TODO: 添加顶部toolbar
			 */
			if( $.Lang.data( V, 'created' ) != '1') {
				$.View.toolbar( 'video' , {
					parent: V,
					id: 'video-toolbar',
					title: '视频',
					back: false,
					refresh: true
				});
			}
			/**
			 * TODO: 添加视频列表
			 */
			$.View.list( 'video', arguments[0] , arguments[1] || null );
			/**
			 *  TODO: 标记创建成功
			 */
			$.Lang.data( V, {created: 1});
		},
		
		
		picture : function () {
			var P = $.GC.References['P'];
			
			if( $.Lang.data( P, 'created' ) != '1') {
				$.View.toolbar( 'picture' , {
					parent: P,
					id: 'picture-toolbar',
					title: '组图',
					back: false,
					refresh: true
				});
			}
			
			$.View.list( 'picture', arguments[0] , arguments[1] || null);
			$.Lang.data( P, {created: 1});
		},
		
		comment : function () {
			var Ref = $.GC.References, C = Ref['C'] , data = arguments[0];
			if( $.Lang.data( C, 'created' ) != '1') {
				$.View.toolbar( 'comment' , {
					parent: C,
					id: 'comment-toolbar',
					title: '评论排行',
					back: false,
					refresh: true
				});
			}
			$.View.list( 'comment', data , arguments[1] || null);
			$.Lang.data( C, {created: 1});
		},
		
		
		pictureshow : function () {
			var Ref = $.GC.References , PS = Ref['PS'], data = arguments[0], id = 'brushwrap', idw = 'dropWrap', cls = 'brush',itemcls = 'brush-item', drop = null;
			
			if( $.Lang.data( PS, 'created' ) != '1' ) {
				
				$.View.toolbar( 'pictureshow' , {
					parent: PS,
					id: 'picture-show-toolbar',
					title: 'Number',
					back: true,
					refresh: false
				});
				
				var wrap = $('<div class="brush-slider" id='+idw+'>').appendTo( Ref['PS'] );
					Ref['dropWrap'] = wrap;
					$.Lang.data( PS, {created: 1} );
			} 
			
			var Cbtn =  PS.find('.x-comment .x-button-label');
			var old_text = '评论'
				Cbtn.html( '<b class="comment_count">'+(data['comments'] || '' )+'</b>' + old_text);
				
			if($('#'+id).length != 0) $('#'+id).remove();
			
			drop = $('<ul>').addClass( cls ).attr( 'id' , id ).appendTo( Ref['dropWrap'] );
			
			Ref[id] = $('#'+id);
			
			/**
			 * TODO: 为评论按钮添加contentid和topicid，作为评论页面显示依据
			 */
			var btn_c = PS.find('.x-comment');
				$.Lang.data( btn_c , data , ['contentid','catid','topicid'] );
				$.Action.Comment.add( btn_c );
			
			$.each( data['images'] , function ( index , imgItem ) {
				var src = '';
				if( index == 0 ) {
					src += 'src=' + imgItem[0]; 
				} else {
					src += 'data-src=' + imgItem[0];
				}
				var li_str = '<li class='+itemcls+'><img class="brush-thumb" '+src+' alt="" style="max-width:'+($.Viewport.width  )+'px;"  /><div class="note">'+imgItem[1]+'</div></li>';
				$(li_str).appendTo( drop );
			});
				
				
			$.UI.Brush.init({
				id: '#'+id,
				item: '.'+itemcls,
				viewwh: [$.Viewport.width,$.Viewport.height - 40],
				pagination: ['.current-number','.count-number'],
				note: '.note',
				description: data['description']
			});
		},
		
		toolbar : function ( view , conf ) {
			
			var tpl = '';
				tpl += '<div id="'+conf['id']+'" class="x-topbar x-docked-top x-ui-blue-topbar">';
				
			if( typeof conf['title'] == 'string' & conf['title'] != 'Number' ) 
				tpl += '	<h2 class="x-toolbar-title x-layout-fit x-centered">'+conf['title']+'</h2>';
			else if( conf['title'] == 'Number' )
				tpl += '	<div class="x-toolbar-title x-layout-fit x-centered"><span class="current-number">0</span>\/<span class="count-number">0</span></div>';
			
			if( conf['back'] == true ) {
				tpl += '	<div class="x-button-back x-layout-fit">';
				tpl += '		<span class="x-button-label">返回</span>';
				tpl += '	</div>';
			}
			
			if( conf['refresh'] == true ) {
				tpl += '<div class="x-refresh"></div>';
			} else {
				if( conf['origintext'] == true) {
					tpl += '	<div class="x-button x-button-blue x-docked-right x-button-round x-origintext">';
					tpl += '		<span class="x-button-label">原文</span>';
					tpl += '	</div>';
				} else {
					tpl += '	<div class="x-button x-button-blue x-docked-right x-button-round x-comment">';
					tpl += '		<span class="x-button-label">评论</span>';
					tpl += '	</div>';
				}
			}
			tpl += '</div>';
				
			switch( view ) {
				
				case 'video' :
					var tv = $(tpl);
						tv.appendTo( conf['parent'] );
						$.Action.refresh( 'video' , tv.find('.x-refresh') );
				break;
				
				case 'picture' :
					var tp = $(tpl);
						tp.appendTo( conf['parent'] );
						$.Action.refresh( 'picture' , tp.find('.x-refresh') );
				break;
				
				case 'pictureshow' : 
					var tps = $(tpl);
						tps.appendTo( conf['parent'] );
						$.Action.back(  tps.find('.x-button-back') ,'pictureshow' );
				break;
				
				case 'articleshow' : 
					var s = $(tpl);
						s.appendTo( conf['parent'] );
						$.Action.back( s.find('.x-button-back') );
				break;
				
				case 'cat_content_list' :
					var ccl = $(tpl);
						ccl.appendTo( conf['parent'] );
						$.Action.back(  ccl.find('.x-button-back') );
						$.Action.refresh('cat_content_list', ccl.find('.x-refresh'));
				break;
				
				case 'comment' :
					var c = $(tpl);
						c.appendTo( conf['parent'] );
						$.Action.back(  c.find('.x-button-back') );
						$.Action.refresh('comment', c.find('.x-refresh'));
				break;
				
				case 'about' : 
					var ab_tpl = '';
						ab_tpl += '<div id="'+conf['id']+'" class="x-topbar x-docked-top x-ui-blue-topbar">';
						ab_tpl += 	'<div class="x-logo x-docked-left">';
						ab_tpl += '		<img src="'+GlobalData[0]['logo']+'" alt="">';
						ab_tpl += 	'</div>';
						ab_tpl += '		<div class="x-button x-button-blue x-docked-right x-button-round">';
						ab_tpl += '			<span class="x-button-label">返回</span>';
						ab_tpl += '		</div>';
						ab_tpl += '</div>';
						
					var atpl = $(ab_tpl).appendTo( conf['parent'] );
					$.Action.back(  atpl.find('.x-button') );
				break;
				
				case 'commentshow' : 
					var c = $(tpl);
						c.appendTo( conf['parent'] );
						$.Action.back(  c.find('.x-button-back') );
				break;
				
				case 'category_index' : 
					var c = $(tpl);
						c.appendTo( conf['parent'] );
						$.Action.back(  c.find('.x-button-back') );
				break;
			}
			
		},
		
		
		/**
		 * TODO: 创建面板
		 * 参数 ： parent 添加到的父元素对象  id 新创建panel的id
		 */
		panel: function () {
			var id = arguments[0]['id'] , Ref = $.GC.References;
			if( !Ref[id] ) {
				var panel_elem = $('<div>').addClass('x-card'+' ' + (arguments[0]['cls'] || '')).attr('id', id).appendTo(arguments[0]['parent']);
				Ref[id] = panel_elem;
			}
			return Ref[id];
		},
		
		
		recommend : function() { 
			var Ref = $.GC.References , Cmp = $.GC.Component, cate_more = $('#'+Cmp['recatgory'] ).find('.x-menu-more');
			$.View.create('category');
			$.Action.More.bind( cate_more );
			$.Action.recommend( Ref['homeRecommend'].find('.x-inner') );
			$.Action.refresh('recommend' , $('.recommend-refresh') );
		},
		
		articleshow : function () {
			var Ref = $.GC.References, Cmp = $.GC.Component, 
				D = Ref['D'], data = arguments[0], 
				tit_id = 'detail-title', spid = 'detail-post-time',c_id = 'detail-post-content';
			
			if( $.Lang.data( D, 'created' ) != '1' ) {
				var fit_height = $.Viewport.height - 40 + 'px';
				
				$.View.toolbar( 'articleshow' , {
					parent: D,
					id: 'article-show-toolbar',
					title: '',
					back: true,
					refresh: false
				});
				
				var article_container = $('<div>').addClass('x-panel-detail-text').attr('id', Cmp.detailId ).css('height',fit_height).appendTo( $.GC.References['D']);
					$('<div class="x-inner"><section><header class="detail-head-title"><h1 class="article-title x-centered" id="'+tit_id+'"></h1><div class="x-centered post-time" id="'+spid+'"></div></header></section></div>').appendTo( article_container );
					$('<div class="detail-panel" id="'+c_id+'"></div>').appendTo(article_container.find('section'));
				
				Ref[Cmp.detailId] = article_container;
				$.Lang.data( D , {created : 1});
				
			}
			
			/**
			 * TODO: 更新评论数
			 */
			var Cbtn =  D.find('.x-comment .x-button-label');
			var old_text = '评论'
				Cbtn.html( '<b class="comment_count">'+(data['comments'] || '' )+'</b>' + old_text);
				
			/**
			 * TODO: 为评论按钮添加contentid和topicid，作为评论页面显示依据
			 */
			var btn_c = D.find('.x-comment');
				$.Lang.data( btn_c , data , ['contentid','catid','topicid'] );
				$.Action.Comment.add( btn_c );
			/**
			 * TODO: 更新文章内容
			 */
			$('#'+tit_id).text( data['title']);
			$('#'+spid).text(data['published'] + (data['source'] ? ' 来源：'+data['source'] : ''));
			$('#'+c_id).html(data['content']);
			/**
			 * TODO: 文章内容里的图片放大效果
			 */
			$.UI.Imagemagnifier.init({
				container: $.GC.References['x-detail-panel']
			});
			/**
			 * TODO: 滚动处理
			 */
			$.Viewport.iScroll( $.GC.Component.detailId );
			$.Viewport.iscrolls[$.GC.Component.detailId].scrollTo( 0 , 0 );
		},
		/**
		 * TODO: 评论详情
		 */
		commentshow : function () {
			var Ref = $.GC.References, Cmp = $.GC.Component, CS = Ref['CS'], data = arguments[0], cid = Cmp['commentlistid'] , type = $.GC.Ev.tap;
			
			if( $.Lang.data( CS, 'created' ) != '1' ) {
				var fit_height = $.Viewport.height - 40 + 'px';
				
				$.View.toolbar( 'commentshow' , {
					parent: CS,
					id: 'comment-show-toolbar',
					title: '评论',
					back: true,
					refresh: false,
					origintext: true
				});
				
				var comment_container 	= $('<div class="x-panel-comment-show x-layout-fit" id="'+Cmp['commentshowid']+'" style="height: '+fit_height+';">').appendTo( CS ),
					inner 				= $('<div class="x-inner">').appendTo( comment_container );
					
				var tpl = '';
					tpl += '<div class="comment-list-container" id="comment_list_container">';
					tpl += 		'<div class="comment-list-container-wrap" id="'+cid+'"></div>';
					tpl += '</div>';
				$( tpl ).appendTo( inner );
				
							  Ref[cid] 	= $('#'+cid);
				Ref[Cmp.commentshowid] 	= comment_container;
				
				var	more = $('<div class="load-more"><input type="button" value="更多..." /></div>').appendTo( inner );
				$.Lang.data( more , {view: 'commentshow',topicid : data['topicid'] , contentid: data['contentid']} );
				$.Action.More.bind(more);
				
				$.Lang.data( CS , {created : 1});
			}
			
			/**
			 * TODO: <原文>按钮操作，
			 * 	1、如果modelid = 4,则不显示该按钮
			 * 	2、如果是从具体的内容页过来，则采用<返回>按钮动作
			 * 	3、如果从<评论排行>页过来，则需要重新取数据并展示相应页面
			 */
			var OBtn = CS.find('.x-origintext'), Parent = $('#comment-show-toolbar');
				OBtn.remove();
		
			if( parseInt(data['modelid']) == 4 ) $.Lang.data( Ref['CS'] , data, ['contentid', 'modelid', 'topicid'] );
				
			$.Action.comment_data = data;
			if(CS.find('.x-to-comment').length == 0) {
				var c_btn = '<div class="x-button x-button-blue x-docked-right x-button-round x-to-comment"><span class="x-button-label">写评论</span></div>';
				var CBtn = $(c_btn).appendTo( Parent );
				$.Lang.Ev.one(CBtn , $.GC.Ev.tap , function() {
					$.Action.tapFocus( CBtn , 'x-to-comment-focus' , function() {
						$.View.comment_panel( $.Action.comment_data );
					});
				});
			}
			
			var root = [data];
			$.View.comment_show_list( root );
		},
		
		comment_panel : function() {
			var Ref = $.GC.References, Cmp = $.GC.Component, CI = Ref['CI'], CS = Ref['CS'], data = arguments[0];
			var Panel = this.panel({
				parent : $.GC.References['fullscreen'],
				id : 'CI'
			});
			if( $.Lang.data( Ref['CI'], 'created' ) != '1' ) {
				var fit_height = $.Viewport.height - 40 + 'px';
				$.View.toolbar( 'commentshow' , {
					parent: Panel,
					id: 'comment-input-toolbar',
					title: '评论',
					back: true,
					refresh: false,
					origintext: true
				});
			$('#comment-input-toolbar').find('.x-origintext').hide();
			var comment_container = $('<div class="x-docked-top comment-form-container" style="height: '+fit_height+'">').appendTo(Ref['CI']);
				$.View.comment_input( comment_container );
				$.Lang.data(Ref['CI'] , {created: 1});
			}
			
			var OBtn = $('#comment-input-toolbar').find('.x-origintext'),Parent = $('#comment-input-toolbar');
				OBtn.remove();
			if( parseInt(data['modelid']) != 4 ) {
				$.View.origin( Parent , data );
			}
			$.History.add( Panel );
		},
		
		origin : function( parent ,data ) {
			var Ref = $.GC.References, Cmp = $.GC.Component;
			if( parseInt(data['modelid']) != 4 ) {
				
				var Str = '<div class="x-button x-button-blue x-docked-right x-button-round x-origintext"><span class="x-button-label">原文</span></div>',
					NBtn = $(Str).appendTo( parent );
					
				$.Lang.data( NBtn , data, ['contentid', 'modelid', 'topicid'] );
				
				var His = $.History.navigate;
				if( parseInt(data['modelid']) == 1 ) $.Action.list( 'article' , NBtn );
				if( parseInt(data['modelid']) == 2 ) $.Action.list( 'picture' , NBtn );
			}
		},
		/**
		 * TODO: 栏目内容列表
		 */
		catgory_content_list: function () {
			var Ref = $.GC.References, Cmp = $.GC.Component, CCL = Ref['CCL'], data = arguments[0];
			
			if( $.Lang.data( CCL, 'created' ) != '1' ) {
				var fit_height = $.Viewport.height - 40 + 'px';
				$.View.toolbar( 'cat_content_list' , {
					parent: CCL,
					id: 'catecontlist-show-toolbar',
					title: '',
					back: true,
					refresh: true
				});
				var cate_list_conatiner = $('<div>').addClass('x-cate-cont-list').attr('id', Cmp['ccList'] ).css('height',fit_height).appendTo( CCL );
				var inner = $('<div class="x-inner">').appendTo( cate_list_conatiner );
				
				/**
				 * TODO: 创建顶部栏目选择条
				 */
				var cate_topbar = $('<div class="x-cate-ui category_toggle" id="select_cate_category">首页<span class="arrow">▼</span></div>').appendTo($('#catecontlist-show-toolbar'));
				var sub_cate = $('#recommend-category')[0].cloneNode(true);
				var $sub_cate = $(sub_cate);
				$sub_cate.attr({
					'id' : '',
					'style': ''
				});
				cate_topbar.parent().after($sub_cate);
				$.Action.toggle_cate( $('#select_cate_category') );
				$sub_cate.find('.x-menu-button').each(function(i,elem){
					$.Action.category($(elem));
				});
				$.Action.More.bind( $sub_cate.find('.x-menu-more') );
				
				Ref['cate_topbar'] = cate_topbar;
				Ref[Cmp['ccList']] = cate_list_conatiner;
				
				$.Lang.data( CCL , {created : 1});
			}
			
			/**
			 * TODO: 更新标题
			 */
			Ref['cate_topbar'].html(data[1]+'<span class="arrow">▼</span>');
			/**
			 * TODO: 设置居中
			 */
			$.View.set_center( Ref['cate_topbar'] );
			
			$.Lang.data( CCL , {catid: data[2]});
			
			/**
			 * TODO: 更新list
			 */
			$.View.list('multi', data, Ref[Cmp['ccList']].find('.x-inner'));
			/**
			 * TODO: 更新滚动条
			 */
			$.Viewport.iScroll( Cmp['ccList'] );
			$.Viewport.iscrolls[Cmp['ccList']].scrollTo( 0 , 0 );
		},
		/**
		 * TODO: 评论框
		 */
		comment_input: function ( parent ) {
			var w = $.Viewport.width - 28 , textarea_h, btn_top;
			textarea_h = $.os.ipad ? 100 : $.os.iphone ? 60 : 40;
				btn_top  = $.os.ipad ? 126 : $.os.iphone ? 86 : 66;
			var tpl = '';
				tpl += '<div class="comment-input-container" style="width: '+(w+28)+'px;position:absolute;left: 0;top: 40px;">';
				tpl += '	<form action="" name="commentunit">';
				tpl += 			'<div>';
				tpl += 				'<textarea name="" class="textarea default-text" id="inputext" style="height: '+textarea_h+'px;width: '+ w +'px;">请输入评论的内容</textarea>';
				tpl += 			'</div>';
				tpl += 			'<div>';
				tpl += 				'<input style="top:'+btn_top+'px;" class="go-comment" name="postcomment" id="postcomment" type="submit" value="提交" />';
				tpl += 			'</div>';
				tpl += '	</form>';
				tpl += '</div>';
			$.GC.References['CIW'] = $(tpl).appendTo( document.body );	
			$.Action.textarea( $('#inputext') );
			$.Action.Comment.bind();
		},
		/**
		 * TODO: 评论内容列表
		 */
		comment_show_list: function ( root , action ) {
			var data 		= root[0]['data'],
				Ref 		= $.GC.References, 
				Cmp 		= $.GC.Component, 
				id  		= Cmp['commentlistid'], 
				wid 		= Cmp['commentshowid'], 
				wrap 		= Ref[id] , 
				btn_more 	= Ref['CS'].find('.load-more');
				
			if( !action ) {
				wrap.empty();
				$.Lang.data( btn_more, {page: 2});
			}
				
			if(data.length == 0 ) 
				$.Message.msg('评论内容为空!');
			
			/**
			 * TODO: 刷新加载更多按钮上的数据
			 */
			if (root[0]['more'] !== undefined ) {
				$.Lang.data( btn_more , {contentid: root[0]['contentid'], topicid: root[0]['topicid']} );
				$.Action.More.button(btn_more, parseInt(root[0]['more']));
			}
			/**
			 * TODO: 每次进入评论页的初始值为2
			 */
			$.each( data , function ( i , r ) {
				var tpl = '';
					tpl += '<div class="comment-list-item">';
					tpl += 		'<div class="comment-list-info"><div class="createby">'+(r['createdby'] == null ? '思拓网友' : r['nickname'])+'</div><div class="date">'+r['date']+'</div></div>';	
					tpl += 		'<div class="comment-content">'+r['content']+'</div>';
					tpl += '</div>';
				if( action == 'add' ) {
					var target = wrap.find('.comment-list-item').eq(0);
					if(target[0])
						target.before($(tpl));
					else 
						$(tpl).appendTo( wrap );
				}
				else 
					$( tpl ).appendTo( wrap );
			});
			/**
			 * TODO: 刷新滚动条
			 */
			$.Viewport.iScroll( wid );
			if( action == 'add' ) $.Viewport.iscrolls[wid].scrollTo( 0, 0 );
		},
		
		login : function() {
			
			var Ref = $.GC.References, Cmp = $.GC.Component, L = Cmp['L'].split('#')[1];
			
			if (Ref[L]) {
				Ref[L].addClass('x-card-login-active').show();
				add_calss_animation( $('#loginFrom') , 'animate-bounceIn' );
				return;
			}
			
			var w = $.Viewport.width, h = $.Viewport.height;
			
			var LS = $.View.panel({
					id: Cmp['L'].split('#')[1],
					parent: Ref['fullscreen']
				})
				.addClass('x-card-login x-card-login-active')
				.css({
					'height' : h + 'px',
					'width' : w + 'px'
				});
				
			$('<div class="login-sheet">').css({
				'height' : h + 'px',
				'width' : w + 'px'
			} ).appendTo( LS );
			
			
			var tpl = '';
				tpl += '<div class="login-ui" id="loginFrom" name="login" style="left: '+ (( w - 245 ) / 2) +'px">';
				tpl += 		'<form>';
				tpl += 			'<div>'
				tpl += 				'<label class="title">用户名</label>';
				tpl += 				'<input type="text" class="login-input login-name" name="username" id="username" />';
				tpl += 			'</div>';
				tpl += 			'<div>'
				tpl += 				'<label class="title">密码</label>';
				tpl += 				'<input type="password" class="login-input login-pw" name="password" id="password" />';
				tpl += 			'</div>';
				tpl += 			'<div style="width: 100%;height: 60px;">'
				tpl += 				'<label class="title">验证码</label>';
				tpl += 				'<input type="text" class="login-input login-seccode" id="seccodetxt" name="seccodetxt" /> <img class="seccode" src='+$.GC.DataUrl+'?controller=member&action=seccode'+' alt="" /> <a class="changeSeccode" title="">换一张</a>';
				tpl += 			'</div>';
				tpl += 			'<div>'
				tpl += 				'<input type="submit" value="登录" class="login-btn" id="submit-btn" /><input type="button" value="取消" class="login-btn login-cancel" id="login_cancel" />';
				tpl += 			'</div>'
				tpl += 		'<form>';
				tpl += '</div>';
				
			$(tpl).appendTo( LS );
			
			Ref['loginform'] = $('#loginFrom').find('form').eq(0);
			
			add_calss_animation( $('#loginFrom') , 'animate-bounceIn' );
			
			$.Action.Login.seccode( $('.changeSeccode') );
			$.Action.Login.bind();
			
			Ref[L] = LS;
		},
		/**
		 * TODO: 栏目导航页
		 */
		category_index : function() {
			var Ref = $.GC.References, Cmp = $.GC.Component, topbarid = 'cate_nav_toolbar' , c_cls = 'x-category-nav-container' , iid = "category_iscroll";
			var Panel = $.View.panel({
				parent: Ref['fullscreen'],
				id: 'category_panel_id'
			});
			if( $.Lang.data( $('#'+topbarid) , 'created') != '1') {
				var fit_height = $.Viewport.height - 40 + 'px';
				$.View.toolbar( 'category_index' , {
					parent: Panel,
					id: topbarid,
					title: '栏目导航',
					back: true,
					refresh: false
				});
				Panel.find('.x-comment').hide();
				
				$('<div id="'+iid+'" class="x-layout-fit '+c_cls+'">').appendTo( Panel ).height(fit_height).append( $('<div class="x-inner">') );
				$.Lang.data( $('#'+topbarid) , {created : 1} );
				$.View.category_list( Panel.find('.x-inner') );
			}
			$.Action.Category.reset_iscroll( $('#cate0') );
			$.History.add( Panel );
		},
		
		category_list : function( parent ) {
			var data = window.GlobalData[0]['category'];
				$.View.sub_category( parent , data );
			parent.find('ul').each( function( i , ul ){
				if( $(ul).attr('data-parentid') != '0' ) $(ul).addClass('hidden');
			});
		},
		/**
		 * TODO : 多级菜单处理，如果为顶级栏目，则默认parentid = '0'
		 * @param {Object} parent
		 * @param {Object} data
		 */
		sub_category : function(  parent , data , super_name ) {
			var ul = $('<ul>');
			if(super_name) 
				$.Lang.data( ul, {'parentname' : super_name});
				
			$.each( data , function( i , c ) {
				var pid = c['parentid'] == null ? '0' : c['parentid'];
				
				if (i == data.length - 1) 
					ul.attr({
						'data-parentid' : pid ,
						'id': 'cate'+pid
					}).addClass('category-nav-ul');
					
				var tpl = '';
					tpl += '<li class="category-nav-item" data-catid="'+c['catid']+'" data-parentid="'+pid+'" data-childids="'+c['childids']+'">';
					tpl += c['name'];
				if (c['childids'] != null) 
					tpl += '<span class="arrow"></span>';
					tpl += '</li>';
				var item = $(tpl).appendTo( ul );
				
				$.Action.Category.bind( item );
				
				if( c['children'].length > 0 ) 
					$.View.sub_category( parent , c['children'] , c['name']);
				
			});
			
			ul.appendTo( parent );
			$.Lang.data( ul , {'height' : ul.height()});
		}
	});
	
 })( Zepto );
 
/**
 * TODO: 负责动作的绑定 
 * @param {Object} $
 */ 
 ( function( $) {
 	
 	$.Action || ( $.Action = {});
	 
	$.extend( $.Action, {
		
		tabCls : function( cElem ) {
			$.GC.References.tab.find('.x-tab-button').removeClass('x-tab-focus');
			cElem.addClass('x-tab-focus');
		},
		
		/**
		 * TODO: 底部切换菜单
		 * @param {Object} elem
		 */
		tab: function( elem ) {
			var Ref = $.GC.References;
			elem.bind( $.GC.Ev.tap, function () {
				var me = $(this) , dp = elem.attr('data-panel') , view = {'V' : 'video' , 'P' : 'picture' , 'C' : 'comment'}[dp];
				if( $.Lang.data(Ref[dp],'created') != '1') {
					$.Data.Store({
						model: view,
						proxy: {
							type: 'ajax',
							params:{controller: view, action: 'index'},
							success : function( root , _history ) {
								$.Action.tabCls( me );
								if(_history !== false ) $.History.add( Ref[me.attr('data-panel')] );
								$.View.create( view, root );
							}
						}
					});
				} else {
					$.Action.tabCls( me );
					$.History.add( Ref[me.attr('data-panel')] ); 
				}
			});
		},
		
		
		list : function( view , elem ) {
			
			var Ref = $.GC.References, Cmp = $.GC.Component , one = $.Lang.Ev.one ;
			
			var show = function ( view , elem ) {
					var that = elem , id , LiOK = true , tapCls = 'x-list-item-focus';
					
					if( view == 'picture' ) 
							id = Cmp['PS'].split('#')[1];
					if( view == 'article' ) 
							id = Cmp['D'].split('#')[1];
					if( view == 'commentshow' ) 
							id = Cmp['CS'].split('#')[1];
						
					$.View.create('panel', {
						id: id,
						parent: Ref['fullscreen']
					});
					
					if( elem.hasClass('x-comment') ) {
						LiOK = false;
						tapCls = 'x-comment-focus';
					} 
					else if ( elem.hasClass('x-origintext') ) {
						LiOK = false;
						tapCls = 'x-origintext-focus';
					} 
					
					$.Action.tapFocus( elem , tapCls, function () {
						
						switch( view ) {
							case 'article' :
								$.Data.Store({
									model: 'article',
									proxy: {
										type: 'ajax',
										params: {controller: 'article', action: 'show', contentid: $.Lang.data( that,'contentid') },
										success: function( root ,_history ) {
											if(_history !== false ) $.History.add( Ref[id] );
											$.View.create( 'articleshow' , root[0] );
										}
									}
								});
							break;
							
							case 'picture' :
								$.Data.Store({
									model: 'picture',
									proxy: {
										type: 'ajax',
										params: {controller: 'picture', action: 'show', contentid: $.Lang.data( that, 'contentid')},
										success: function( root ,_history ) {
											if(_history !== false ) $.History.add( Ref[id] );
											$.View.create( 'pictureshow' , root[0] );
										}
									}
								});
							break;
							
							case 'commentshow' : 
								$.Data.Ajax({
									params: {controller: 'comment', action: 'show', topicid: that.data('topicid') , contentid: that.data('contentid') },
									success: function( root ) {
										$.History.add( Ref[id] );
										$.View.create( 'commentshow' , root[0] );
									}
								});
							break;
						}
						if(LiOK) that.addClass('x-list-item-visited');
					});
			};
			switch( view ) {
				case 'video' : 
					one( elem , $.GC.Ev.tap , function() {
						var that = $(this);
						$.Action.tapFocus( that ,'x-list-item-focus', function () {
							that.addClass('x-list-item-visited');
						});
					});
				break;
				case 'picture' : 
					one( elem , $.GC.Ev.tap , function() {
						show( view , $(this) );
					});
				break;
				case 'article' :
					one( elem , $.GC.Ev.tap , function() {
						show( view , $(this) );
					});
				break;
				case 'comment' : 
					one( elem , $.GC.Ev.tap , function() {
						show( 'commentshow' , $(this) );
					});
				break;
			};
		},
		
		refresh : function( view , button ) {
			if( view == 'cat_content_list' ) {
				var Cmp = $.GC.Component, Ref = $.GC.References;
				button.bind( $.GC.Ev.tap , function() {
					var _btn = $(this);
					$.Action.tapFocus( _btn, 'x-refresh-tap' , function () {
						$.Action.More.active(_btn.parent().parent().find('.load-more'));
						$.Data.Ajax({
							params: { controller: 'category', action: 'ls', catid: $.Lang.data( Ref['CCL'] , 'catid') },
							success: function( root ) {
								$.View.list( 'multi', root , Ref[Cmp['ccList']].find('.x-inner') );
							}
						});
					});
				});
			} 
			else if( view == 'recommend') {
				button.bind($.GC.Ev.tap , function() {
					$.Action.tapFocus( $(this) , 'recommend-refresh-focus' , function() {
						var parent = $.GC.References['homeRecommend'].find('.x-inner');
							parent.empty();
						$.Action.recommend(parent);
					});
				});
			} else {
				button.bind( $.GC.Ev.tap , function() {
					var _btn = $(this);
					$.Action.tapFocus( _btn, 'x-refresh-tap' , function () {
						$.Action.More.active(_btn.parent().parent().find('.load-more'));
						$.Data.Ajax({
							params: { controller: view, action: 'index' },
							success: function( root ) {
								$.View.create( view, root );
							}
						});
					});
				});
			}
		},
		
		back: function( button , view ) {
			var one = $.Lang.Ev.one, type = $.GC.Ev.tap, Ref = $.GC.References , cls = 'x-button-back-focus';
			
			if( view == 'pictureshow' ) 
				one( button , type , function () {
					BACK( $(this) , function(){
						Ref['brushwrap'].remove();
					});
				});
			else 
				one( button , type , function() {
					BACK( $(this) );
				});
			
			var BACK = function( elem, fn ) {
				$.Action.tapFocus( elem , cls, function () {
					/**
					 * TODO: 栏目内容页面的<返回>总返回首页
					 */
					var PID = elem.parent().parent().attr('id');
					if(PID == 'CCL') $.History.navigate = [Ref['R'],Ref['CCL']];
					
					/**
					 * TODO: 文章或组图不再返回评论面板，而直接返回到首页或者列表页
					 */
					if(PID == 'D' || PID == 'PS') {
						var panels = [];
						for( var i = 0 , len = $.History.navigate.length ; i < len ; i++ ) {
							if( $.History.navigate[i].attr('id') == PID ) {
								panels.push($.History.navigate[i]);
								break;
							} else {
								panels.push($.History.navigate[i]);
							}
						}
						$.History.navigate = panels;
					}
					$.History.back();
					if( fn ) fn();
				});
			}
		},
		
		recommend: function () {
			var parent = arguments[0];
			var success = function( root , back) 
			{
				$.View.banner( root[0]['banner'] );
				$.View.list( 'multi', root, parent);
				/**
				 * 加载滚动条，这是特殊处理（banner的高度由浏览器处理，这样整个区域的高度不固定）
				 * 考虑到banner图片的高度不固定，如果界面刚渲染完加载滚动条，会出现误差，
				 * 所以采用先加载，后刷新的方案，这样可以确保准确
				 * 另外如果只延迟加载滚动条，会出现闪屏现象，体验很差
				 */
				var sid = $.GC.Component.homeRecommendId.split('#')[1];
					$.Viewport.iScroll( sid );
					$.Action.tapFocus( $(document.body), '', function () {
						$.Viewport.iscrolls[sid].refresh();
					});
					$.Lang.data($.GC.References['R'] , {created : 1});
			};
			
			$.Data.Store({
				model: 'recommend',
				proxy: {
					type: 'ajax',
					params:{controller: 'index', action: 'recommend'},
					success : success
				}
			});
			
		},
		
		toggle_cate : function( elem ) { 
			var menu = elem.parent().next(), duration = 0.3 , type = 'ease-out';
				menu.attr('data-show','false');
			$.Lang.Ev.one( elem , $.GC.Ev.tap , function() {
				if( menu.attr('data-show') == 'false' ) {
					menu.anim({translateY: '40px'} , duration , type , function(){
						menu.attr('data-show', 'true');
					});
					$(this).find('.arrow').anim({rotate: '180deg'} , duration , type);
				} else {
					menu.anim({translateY: '0'} , duration , type , function() {
						menu.attr('data-show', 'false');
					});
					$(this).find('.arrow').anim({rotate: '0deg'} , duration , type);
				}
			});
		},
		
		category : function ( cate ) {
			cate.bind( $.GC.Ev.tap , function () {
				var me = $(this)
				var catid = $.Lang.data( me ,'catid' ), Ref = $.GC.References, Cmp = $.GC.Component, id = Cmp['CCL'].split('#')[1];
				if( !Ref[id] ) {
					var cl = $('<div>').addClass('x-card').attr('id', id ).appendTo(Ref['fullscreen']);
					Ref[id] = cl;
				}
				
				$.Action.tapFocus( me , 'x-menu-button-focus' , function () {
					$.Data.Store({
						model: 'category',
						proxy: {
							type: 'ajax',
							params: {controller: 'category',action: 'ls' , catid: catid },
							success : function( root , _history ) {
								root[1] = me.text();
								root[2] = catid;
								$.View.create('catgory_content_list', root);
								if (_history !== false && me.parent().parent().attr('id') != 'CCL') $.History.add( Ref[id] );
								$('.x-menu-button-focus').removeClass('x-menu-button-focus');
							}
						}
					});
				});
			});
		},
		
		about: function () {
			var Ref = $.GC.References, Cmp = $.GC.Component, type = $.GC.Ev.tap ,  Tapfocus = $.Action.tapFocus , btn = $('#about');
			
			$.View.create( 'about' );
			
			var A = Ref[Cmp['A']];
			
			btn.bind( type , function() { 
				/**
				 * TODO: 只请求一次数据，根据data-created来进行判断
				 */
				if( $.Lang.data(A,'created') != '1' ) {
					Tapfocus( $(this) , 'x-refresh-tap', function () {
						$.Data.Ajax({
							params: {controller: 'index',action: 'about'},
							success: function( root ) {
								$.View.create( 'about' ,root[0]['content'] );
								$.Lang.data( A, {created: 1});
								$.History.add( A );
							}
						});
					});
				} else {
					Tapfocus( $(this) , 'x-refresh-tap', function() {
						$.Viewport.iscrolls['about-content'].scrollTo( 0 , 0);
						$.History.add( A );
					});
				}
			});
		},
		
		textarea : function( elem ) {
			elem.bind( 'focus' , function ( e ) {
				$.Action.Login.index();
				$.Action.toggleTextareaFocus( e.type , elem );
			});
		},
		
		toggleTextareaFocus : function( type, elem ) {
			if( type == 'focus' ) {
				if( elem.val() == elem[0].defaultValue ) {
					elem.val(' ');
					elem.toggleClass('default-text');
				}
			};
			if( type = 'blur' ) {
				if( elem.val() == '' ) {
					elem.val( elem[0].defaultValue );
					elem.toggleClass('default-text');
				}
			}
		},
		
		Login : {
			
			index: function(){
				if( parseInt(window.GlobalData[0]['anonymous'] , 10) == 1 ) {
					if( $.Lang.cookie.get('cmstop_username') == null ) {
						setTimeout(function(){
							$.View.create('login');
							$('#username').focus();
						} , 250 );
					}
				}
			},
			
			bind : function () {
				var form = $.GC.References['loginform'], submit_btn = $('#submit-btn'), cancel_btn = $('#login_cancel');
				form.bind( 'submit' , function () {
					return false;
				});
				submit_btn.bind( $.GC.Ev.tap, function() {
					$.Action.Login.post();
				});
				cancel_btn.bind( $.GC.Ev.tap , function () {
					$.Action.Login.cancel();
				});
			},
			
			post: function() {
				var form = $.GC.References['loginform'], username = $('#username'), pw = $('#password'), seccodetxt = $('#seccodetxt') ,submit_btn = $('#submit-btn'), status;
				if( $.Action.Login.valid() == true ) {
					$.ajax({
						type : 'POST',
						url : $.GC.DataUrl+'?controller=member&action=login',
						data:  {
							'username' : username.val(),
							'password' : pw.val(),
							'seccode'  : seccodetxt.val()
						},
						dataType : 'json',
						success : function ( data ) {
							data[0]['state'] == true ? $.Action.Login.success( data[0] ) : $.Action.Login.error( data[0] );
						}
					});
				}
			},
			
			valid : function() {
				var Ref = $.GC.References, Cmp = $.GC.Component, 
					form = Ref['loginform'], username = $('#username'), pw = $('#password'), seccodetxt = $('#seccodetxt') ,submit_btn = $('#submit-btn'), 
					status;
				if(username.val() == '' ) {
					$.Message.msg('对不起，用户名不能为空!' , username );
					username.addClass('login-error');
					status = false;
				} else {
					username.removeClass('login-error');
					status = true;
				}
				if(pw.val() == '' ) {
					$.Message.msg('对不起，密码不能为空!' , pw );
					pw.addClass('login-error');
					status = false;
				} else {
					pw.removeClass('login-error');
					status = true;
				}
				if(seccodetxt.val() == '' ) {
					$.Message.msg('对不起，验证码不能为空!' , seccodetxt);
					seccodetxt.addClass('login-error');
					status = false;
				} else {
					seccodetxt.removeClass('login-error');
					status = true;
				}
				return status;
			},
			
			cancel : function () {
				var Cmp = $.GC.Component;
				$(Cmp['L']).removeClass('x-card-login-active').hide();
			},
			
			seccode : function ( elem ) {
				elem.bind( 'click' , function () {
					$('.seccode').attr( 'src', $.GC.DataUrl+'?controller=member&action=seccode&s='+(+new Date()) );
					return false;
				});
			},
			
			success : function( r ) {
				$.Message.msg('恭喜您，登陆成功，快来发表评论吧!');
				this.cancel();
				/**
				 * TODO: 设置cookie过期时间为30天
				 */ 
				var exp = new Date(); 
       				exp.setTime (exp.getTime()+ 60 * 60 * 24 * 30);
				$.Lang.cookie.set('cmstop_username',$.Lang.cookie.get('cmstop_username'), exp, location.pathname, getHost(document.location.href));
			},
			
			error : function( r ) {
				$.Message.msg( r['error'] );
			}
		},
		
		Comment : {
			
			bind : function () {
				var form = $(document.forms['commentunit']) , post_btn = $('#postcomment'), commentinput = $('#inputext') , status;
				form.bind('submit' , function () {
					return false;
				});
				post_btn.bind( $.GC.Ev.tap , function() {
					$.Action.Comment.post();
				});
			},
			
			add : function( elem ) {
				$.Action.list( 'comment' , elem );
			},
			
			post : function () {
				var form = $(document.forms['commentunit']) , post_btn = $('#postcomment'), commentinput = $('#inputext') , status , Ref = $.GC.References;
				
				if( $.Action.Comment.valid() == true ) {
					var params = {
						// TODO: anonymous == 0 开启匿名评论时，不提交username字段
						//'username' : $.Lang.cookie.get('cmstop_username'), 
						'topicid' :  Ref['CI'].find('.x-origintext')[0] ? Ref['CI'].find('.x-origintext').data('topicid') : Ref['CI'].data('topicid'),
						'content' : $('#inputext').val()
					};
					if( parseInt(window.GlobalData[0]['anonymous'] , 10) == 1 ) 
						params['username'] = $.Lang.cookie.get('cmstop_username');
				
					$.ajax({
						type : 'POST',
						url : $.GC.DataUrl+'?controller=comment&action=comment',
						data: params,
						dataType : 'json',
						success : function ( data ) {
							if( data[0]['state'] == true ) 
								$.Action.Comment.success( data );
							else 
								$.Action.Comment.error( data[0]['error'] );
						}
					});
				}
			},
			
			success : function ( data ) {
				var form = $(document.forms['commentunit']) , post_btn = $('#postcomment'), commentinput = $('#inputext') , status;
				$.Message.msg('评论发表成功!');
				/**
				 * TODO: 添加用户评论内容到评论列表中
				 */
				data[0]['data'][0]['content'] = commentinput.val();
				$.View.comment_show_list( data , 'add' );
				/**
				 * TODO: 恢复评论框样式
				 */
				commentinput.val( commentinput[0].defaultValue );
				commentinput.addClass('default-text');
				
				setTimeout( function(){
					$.History.back();
				}, 250 ); 
			},
			
			error : function( msg ) {
				$.Message.msg(msg);
			},
			
			valid : function() {
				var form = $(document.forms['commentunit']) , post_btn = $('#postcomment'), commentinput = $('#inputext') , status;
				if(commentinput.val() == '' || commentinput.val() == commentinput[0].defaultValue ) {
					$.Message.msg('对不起，评论内容不能为空!');
					status = false;
				} else if(commentinput.val().length < 3 ) {
					$.Message.msg('评论内容不能少于10个字符!');
					status = false;
				} else 
					status = true;
				return status;
			}
		},
		
		More : {
			bind : function ( elem ) {
				var _ = this;
				$.Lang.Ev.one( elem , $.GC.Ev.tap , function() {
					if( !elem.hasClass('x-menu-more') ) 
						_.load( elem );
					else {
						$.Action.tapFocus( elem , 'x-menu-more-focus' , function() {
							$.View.category_index();
						});
					}
				});
			},
			
			load : function ( elem ) {
				var  _ = this, params = null;
				if( !$.Lang.data( elem , 'page') ) 
					$.Lang.data( elem , {page: 2});
					
				var view = $.Lang.data( elem ,'view'),
					page = $.Lang.data( elem , 'page');
				
				if( view == 'category' ) 
					params = { controller: view, action: 'ls' , catid : $.Lang.data( $.GC.References['CCL'] , 'catid' ),  page : page };
				else if( view == 'commentshow' )
					params = { controller: 'comment', action: 'show' , topicid:  elem.data('topicid'), contentid: elem.data('contentid'), page : page };
				 else 
					params = { controller: view, action: 'index' , page : page };
				
				$.Action.tapFocus( elem, 'x-more-tap' , function () {
					$.Data.Ajax({
						params: params,
						success: function( root ) {
							if( (root[0]['list'] && root[0]['list'].length == 0 ) || (root[0]['data'] && root[0]['data'].length == 0)) {
								$.Action.More.useless( elem );
								return;
							}
							_.update( view , root );
							$.Lang.data( elem , {page: parseInt(page) + 1});
						}
					});
				});
			},
			
			update : function( view , root ) {
				if( view == 'category' )
					$.View.list( 'multi', root , $.GC.References[$.GC.Component['ccList']].find('.x-inner') , 'more' );
				else if( view == 'commentshow' )
					$.View.comment_show_list( root , 'more' );
				else
					$.View.create( view, root , 'more' );
			},
			
			active: function( elem ) {
				elem.css('opacity', 1).find('input').val('更多...');
				$.Lang.data( elem , {page: 2});
				$.Action.More.bind( elem );
			},
			
			useless : function( elem ) {
				elem.css('opacity', .5).unbind().find('input').val('没有更多内容可加载');
				$.Message.msg('没有更多内容可加载');
			},
			button : function( btn , more ) {
				more == 0 ? btn.hide() : btn.show();
			}
		},
		
		Category: {
			bind : function( elem ) {
				var _self = this;
				elem.bind( $.GC.Ev.tap , function() {
					var item = $(this);
					if($.Lang.data( item , 'childids') == 'null' ) 
						_self.content_list( item );
					else 
						_self.sub_category_list( item );
				});
			},
			
			sub_category_list : function ( item ) {
				var self = this;
				var parent = item.parent() , catid = $.Lang.data( item , 'catid') , id = '#cate'+catid , C = $(id);
				this.update_parent_data( parent , {
					'subid' : catid,
					'text' : item.text()
				});
				$.Action.tapFocus( item , 'category-nav-item-tap' , function() {
					self.tab( item , C );
				});
			},
			
			tab: function( last , current ) {
				var parent = last.parent();
				if($.os.android) {
					parent.addClass('android-category-ul-parent');
					current.addClass('android-category-ul-current now').removeClass('hidden');
				} else {
					parent.addClass('category-ul-parent');
					current.addClass('category-ul-current now').removeClass('hidden');
				}
				this.tab_sub_callback( last , current );
			},
			
			tab_sub_callback : function( last , current ) {
				var self = this;
				this.update_title( last.text() );
				this.update_back( true );
				this.update_level_back();
				setTimeout( function() {
					if ($.os.android) 
						last.parent().addClass('hidden').removeClass('android-category-ul-current now android-category-ul-parent');
					else 
						last.parent().addClass('hidden').removeClass('category-ul-current now category-ul-parent');
					self.update_title( '' , 'remove' );
					$.Action.Category.reset_iscroll( current );
				} , 250 );
			},
			
			tab_back_callback : function( last , current ) {
				var self = this;
				last.addClass('hidden category-ul-parent-back').removeClass('category-ul-current now');
				current.removeClass('hidden').addClass('now category-ul-current-back');
				setTimeout( function() {
					current.removeClass('category-ul-current-back');
					last.removeClass('category-ul-parent-back');
					
					var parentname = $.Lang.data(current , 'parentname');
					self.update_title( parentname || '栏目列表' );
					
					if(parentname == null) 
						self.update_back( false );
					$.Action.Category.reset_iscroll( current );
				}, 250 );
			},
			
			update_title : function( name ,action ) {
				var h2 = $('#cate_nav_toolbar h2');
				if( action == 'remove') 
					h2.removeClass('category-title');
				else 
					h2.addClass('category-title').text(name);
			},
			
			update_back : function(b) {
				var toolbar = $('#cate_nav_toolbar');
				toolbar.find('.x-button-back')[b == true ? 'addClass' : 'removeClass']('back-hide');
				if (b == false) {
					toolbar.find('.x-level-button').remove();
				}
			},
			
			update_level_back : function() {
				var self = this , parent = null, toolbar = $('#cate_nav_toolbar'), level_btn = toolbar.find('.x-level-button');
				
				if( level_btn.length == 0 ) {
					var tpl = "";
						tpl += '<div class="x-level-button x-layout-fit">';
						tpl += 		'<span class="x-button-label">上一级</span>';
						tpl += '</div>';
					var level = $(tpl).addClass('level-show').appendTo( toolbar );
					level.bind( $.GC.Ev.tap , function() {
						var wrap = $(this).parent().next().find('.x-inner') , curr = wrap.find('.now') , id = curr.attr('data-parentid');
						wrap.find('.category-nav-ul').each( function( i , elem ) {
							if( $(elem).attr('data-subid') ==  id ) {
								parent = $(elem);
								return;
							}
						});
						$.Action.tapFocus($(this) , 'x-level-button-focus' , function() {
							self.tab_back_callback( curr , parent );
						});
					});
				} 
			},
			
			update_parent_data : function( parent , conf ) {
				$.Lang.data( parent , conf );
			},
			
			reset_iscroll : function( elem ) {
				var inner = elem.parent() , id = inner.parent().attr('id') , height = parseInt($.Lang.data( elem , 'height' ));
				inner.css('height',height);
				$.Viewport.iScroll(id);
			},
			
			content_list : function( cate ) {
				$.Action.tapFocus( cate , 'category-nav-item-tap' , function() {
					var catid = $.Lang.data( cate ,'catid' ), Ref = $.GC.References, Cmp = $.GC.Component, id = Cmp['CCL'].split('#')[1];
					if( !Ref[id] ) {
						var cl = $('<div>').addClass('x-card').attr('id', id ).appendTo(Ref['fullscreen']);
						Ref[id] = cl;
					}
					$.Data.Ajax({
						params: {controller: 'category',action: 'ls' , catid: catid },
						success: function( root ) {
							root[1] = cate.text();
							root[2] = catid;
							$.View.create('catgory_content_list', root);
							$.History.add( Ref[id] );
						}
					});
				});
			}
		},
		
		tapFocus : function( elem , cls , fn ) {
			elem.toggleClass( cls );
			setTimeout( function() {
				if(fn) fn();
				elem.toggleClass( cls );
			}, 200 );
		}
		
	});
 })( Zepto );
 
 
 ( function( $ ) {
 	
 	$.UI || ( $.UI = {});
	/**
	* TODO: 文章内容里图片放大镜 插件
	 */
	$.UI.Imagemagnifier = im = {};
	
	$.extend( im, {
		
		elem_sheet: null,
		elem_img: null,
		
		collect: [],
		
		init: function( options ) {
			this.options = options;
			this.collectImage();
		},
		
		collectImage: function( ) {
			this.collect = this.options.container.find('img[data-origin]');
			this.wrap();
		},
		
		wrap: function() {
			$.each( this.collect, function( index, o ) {
				var div = $('<div>').css({
					textAlign: 'center',
					position: 'relative',
					margin: '0 auto',
					marginBottom: 20
				});
					$(o).after( div );
					div.append( o );
					div.parent().css('text-indent','0');
				var hander = $('<div>').addClass('hander-ico').appendTo( div );
					
					hander.bind( $.GC.Ev.tap , function() {
						im.sheet();
						im.show( $(this).prev() );
					});
					$(this).bind( $.GC.Ev.tap , function() {
						im.sheet();
						im.show( hander.prev() );
					});
			});
		},
		
		sheet: function() {
			var sheet_div = $('<div>').css({height:  $.Viewport.height}).addClass('im-sheet').appendTo( $.GC.References['D'] );
			sheet_div.bind( $.GC.Ev.tap , im.remove);
			im.elem_sheet = sheet_div;
		},
		
		remove: function( ) {
			im.elem_sheet.remove();
			im.elem_img.remove();
			im.elem_sheet = null;
			im.elem_img = null;
		},
		
		show : function( img ) {
			
			var w = img.attr('data-width'), h = img.attr('data-height'), bdw = 1;
			
			var Img = $('<img />').attr({
				src: img.attr('data-origin'),
				width: w,
				height: h
			}).appendTo( $.GC.References['D'] ).addClass('sheet-img');
			
			$(Img).anim({ scale: '1,1',opacity: 1}, .7, 'ease-out');
			
			var l = ($.Viewport.width - w - bdw ) / 2,
				t = ($.Viewport.height - h - bdw ) / 2;
				
				Img.css({
					left: l,
					top: t,
					zIndex: 1200,
					position: 'absolute',
					border: bdw+'px solid #000'
				});
			
			im.elem_img = Img;
			
			Img.bind( $.GC.Ev.tap , function() {
				im.remove();
			});
		}
		
	});
/**
 * TODO: 组图展示效果Brush
 */
//	Brush.init({
//		id: '', 
//		item: '', 
//		pagination: ['.c','.all'], // 页码
//		viewwh: 视口宽高
//	});
	$.UI.Brush = Brush = {};
	$.extend( Brush , {
		
		init: function ( settings ) {
			var id = settings.id, 
				item = settings.item;
				
			this.wrap = $(id);
			this.items = this.wrap.find(item);
			this.note = settings.note;
			this.description = settings.description;
			
			this.pageCurrent = $(settings.pagination[0]);
			this.pageCount = $(settings.pagination[1]);
			this.pageCount.text(0);
			
			this.w = settings.viewwh[0];
			this.h = settings.viewwh[1];
			
			this.size();
			this.viewport();
			this.setPage(1);
		},
		
		size: function () { 
			this.len = this.items.size();
			return this.len;
		},
		
		setPage: function ( n ) {
			this.pageCurrent.text( n );
			if(this.pageCount.text() == '0') 
				this.pageCount.text( this.len );
		},
		
		viewport: function () {
			this.wrap.parent().css({
				width: this.w,
				height: this.h
			});
			this.wrap.css('width',this.len * this.w );
			this.items.css({
				width: this.w,
				height: this.h
			});
			this.action();
		},
		
		action: function () {
			$.each( this.items, function( i , o ) {
				var li = $(o);
				if (i == 0) 
					li.attr('data-x', 0);
				else 
					li.attr('data-x', -i * Brush.w + 'px' );
				li.attr('data-i', i + 1);
				
				if( li.find(Brush.note).text().length < 3 ) {
					li.find(Brush.note).text(Brush.description);
				}
				
				Brush.swipeLeft( li );
				Brush.swipeRight( li );
			});
		},
		
		swipeLeft: function( li ) {
			li.bind( 'swipeLeft', function ( ) {
				var that = $(this),next = that.next();
				if(next.attr('data-x')) {
					var img = next.find('img');
					img.attr('src',img.attr('data-src'));
					Brush.anmi( Brush.wrap, next.attr('data-x'), function( ) {
						Brush.setPage( next.attr('data-i') );
					}, next );
				}
				else 
					$.Message.msg('已经是最后一张了');
			});
			li.bind('touchmove' , function( event ) {
				event.preventDefault(); 
			});
		},
		
		swipeRight: function ( li ) {
			li.bind('swipeRight', function ( e ) {
				var that = $(this),prev = that.prev();
				if(prev.attr('data-x')) 
					Brush.anmi( Brush.wrap, prev.attr('data-x') , function() {
						Brush.setPage( prev.attr('data-i') );
					});
				else 
					$.Message.msg('这已经是第一张了');
			});
			li.bind('touchmove' , function( event ) {
				event.preventDefault(); 
			});
		},
		
		anmi: function( tar , x , fn ) {
			tar.anim({ translateX: x}, 0.7, 'ease-out', function(){
				fn();
			});
		}
			
	});
 })( Zepto );
 function format_time( t ) {
	var T = (t/60).toFixed(2) , M = T.split('.')[0], S = T.split('.')[1];
	if( parseInt(M) < 10 ) M = '0'+M;
	return M+':'+S;
 }
 function addScreen( detect ) {
	setTimeout(function() {
		if (!$.Lang.cookie.get('addscreen')) 
		{
			var exp = new Date(); 
	   			exp.setTime (exp.getTime()+ 60 * 60 * 24 * 365);
			$.Lang.cookie.set('addscreen', 'addscreened', exp , location.pathname);
			var screen_ico = $('<div class="add-ico-screen"><img width="248" height="99" src="'+$.GC.AddScreenIco+'" alt="" /></div>').appendTo(document.body);
			var close =	$('<div class="addscreen-close">x</div>').appendTo( screen_ico );
				screen_ico.bind('tap',function() {
					setTimeout(function(){
						screen_ico.remove();
					}, 10 );
				});
			setTimeout(function(){
				screen_ico.remove();
			} , 10000 );
		}
	}, 5000);
 }
 /**
  * define Application
  * properties: [name]
  * Methods: [launch]
  */
 ( function ( $ ) {
	
	$.isFunction( $.Application ) || ($.Application = function ( name ) {});
	
	$.Application.NAME = 'CTMobile';
	
	$.extend( $.Application.prototype, {
		
		launch: function () {
			/**
			 *  TODO: 设置引用 $.GC.References，这个引用可以避免每次重新获取元素
			 */
			var C = $.GC.Component, Ref = $.GC.References;
			
			$.extend( Ref, {
				fullscreen : $( C.fullscreenId ),
				homeRecommend:  $( C.homeRecommendId ),
				tab: $( C.tab ),
				R: $( C.R )
			});
			
			/**
			 * TODO: 获取并设置视口元素
			 */
			new $.Viewport().init();
			$.History.init( Ref['R'] );
			
			$.Action.about();
			$.View.create('tab');
			$.View.create('recommend'); 
			/**
			 * TODO: 添加到主屏幕
			 */
			if($.os.iphone && window.location && ($(window).height() == 356) ) addScreen('iphone');
		}
		
	});
 })( Zepto );
  
 /**
  * Init
  */
 $(function() {
	new $.Application( 'CTMobile' ).launch();
 });
 
	
	
