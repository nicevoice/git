function windowHeight(){
	var de = document.documentElement;
	return self.innerHeight || (de && de.clientHeight) || document.body.clientHeight;
}
function ChuteModel(){}

ChuteModel.prototype = {
	
	timer: null,
	
	distTime: null,
	
	init: function( o ){
		if(!o) return;
		this.o = o;
		this.start();
	},
	
	start: function(){this.addOnScroll(this.console);},
	
	console: function(){
		var self = this;
		if( this.distTime ) clearTimeout(this.distTime);
		if( this.timer ) clearTimeout(this.timer);
		this.distTime = setTimeout(function(){self.process(self)},500);
	},
	
	process: function( self ){
		var 	o   = this.o,
	    	final_y = this.scrolltop(o),
			 	 tv = parseInt(o.css('top'));
		if( tv != final_y )
		{
			var  dist = Math.ceil(Math.abs(tv - final_y)*0.08);
				 tv = tv > final_y ? tv - dist : tv + dist;
		} 
		else {
			clearTimeout(this.timer);
		}
		o.css('top',tv);
		this.timer = setTimeout(function(){ self.process( self )}, 10);
	},
	
	addOnScroll: function( fn ){
		var _self = this;
		var oldscroll = window.onscroll;
		if(typeof oldscroll != "function"){
			window.onscroll = function(){
				fn.call(_self);
			};
		}
		else 
		{
			window.onscroll = function(){
				oldscroll.call(_self);
				fn.call(_self);
			}
		}	
	},
	
	scrolltop: function( o ){
		var dist =  (document.documentElement && document.documentElement.scrollTop) || (document.body ? document.body.scrollTop : false);
		var sh = windowHeight();
		var oh = $(o).height()+6;
		var v = sh - oh;
		return dist + v;
	}
	
};


