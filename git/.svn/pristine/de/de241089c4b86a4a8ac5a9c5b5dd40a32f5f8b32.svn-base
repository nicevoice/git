 function callback(fn,scope){
 	var args = Array.prototype.slice.call(arguments).slice(2);
	if( typeof fn == 'function'){
		return fn.apply( scope || this, args);
	}
 }
 function galleryObject( options ){
 		this.name= 'Gallery Object';
		
		this.windowEle = options.windowEle;
		this.slideEle = this.windowEle.find('ul:first');
		this.li = this.slideEle.find('li');
		
 		this.btn_l = options.button_l;
		this.btn_r = options.button_r;
		
		this.groups = options.groups || 1;
		
		this.showNum = options.showNum || 1;
 }
 galleryObject.prototype = {
 	
 		cv: 0,
		
		origin: 0,
		//终点值，初始化的时候会自动赋值
		end: 0,
		
		//判断是否越过起点
		isOrigin: function( v ){
			return v > this.origin ? true : false;
		},
		
		//是否越过终点
		isEnd: function( v ){
			return v < this.end ? true : false;
		},
		
 		init: function(){
			var _self = this;
			this.inShowArea();
			this.setUlWidth();
			
			//赋值终点值，即终点临界点
			this.end = -this.allWidth() + this.liWidth();
			
			this.btn_l.click(function(){
				
					callback(function(){this.scroll('l'); },_self);
					return false;
			});
			this.btn_r.click(function(){
				
					callback(function(){this.scroll('r'); },_self);
					return false;
			});
			
		},
		liWidth: function(){
			return this.li.first().width();
		},
		liLen: function(){
			return this.li.size();
		},
		allWidth: function(){
			return this.liWidth() * this.liLen();
		},
		setUlWidth: function(){
			this.slideEle.css('width',this.allWidth());
		},
		distance: function(){
			return this.liWidth() * this.groups;
		},
		durPx: function( lr ){
			var v = 0;
			if( lr == 'l'){
				return this.cv + this.distance();
			} else {
				return this.cv - this.distance();
			}
		},
		scroll: function( lr,v ){
			var c = this.durPx(lr);
			var _self = this;
			//已经是起点了
			if(this.isOrigin( c ) == true ){
				c = 0;
			}
			if(this.isEnd( c ) == true ){
				c = this.end;
			} 
			this.slideEle.animate({marginLeft: v || c},1000,function(){
				_self.cv = parseInt($(this).css('marginLeft'));
			});
		},
		//查找大图所对应的缩略图
		findCurr: function(){
			return this.li.filter('.nowstyle');
		},
		//获得当前大图所对应的缩略图在ul中的序号
		getcurrEq: function(){
			return this.li.index(this.findCurr());
		},
		//设置大图对应的缩略图显示出来
		inShowArea: function(){
			var eq = this.getcurrEq();
			//获得大图对应缩略图的距离左边的值
			var eqv = -eq * this.liWidth();
			//获得当前的ul距离左边值
			var cv = parseInt(this.slideEle.css('marginLeft'));
			//可显示区域的末尾值，这个值是对应着cv的
			var inshow = cv - (this.showNum * this.liWidth());
			if(eqv <= inshow || eqv > cv){this.scroll('r',eqv);}
			
		}
 }

 var galler = new galleryObject({
 			
			windowEle: $("#thumbs-wrap"),
			//左右按钮
			button_l: $("#" + "button-l"),
			button_r: $("#" + "button-r" ),
			
			//一组滑动个数
			groups: 1,
			
			//显示区域显示的个数
			showNum: 5
			
 });
 	galler.init();
