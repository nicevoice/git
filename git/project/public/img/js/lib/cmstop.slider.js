/*
 * jQuery Slider Plugin For CmsTop
 * 
 * version: 1.11 (23:32 2010-1-11)
 * @base jQuery v1.3.2 or later
 * @author xuxu
 * @email xuqihua@cmstop.com
 * @company CmsTop (http://www.cmstop.com/)
 *
*/
(function($){
	$.fn.extend({
		imgSlide: function(options) {
		
			if(options.auto  == undefined)  	options.auto  = true;			//默认为自动播放
			if(options.type  == undefined)  	options.type  = 'click';		//默认为点击切换
			if(options.speed == undefined)  	options.speed = 3000;			//默认为3000毫秒
			var div = this;
			var imgDiv = this.children('.image');
			var navDiv = this.children('.number');
			var titDiv = this.children('.title');
			
			var items = $('#'+options.data+' > li > a');
			var itemsLength = items.length;
			if(itemsLength == 0) return this;
			
			var length = itemsLength/2;
			var timeoutReturn;
			var curItemIndex = 0; //当前的索引
			var navDivHtml = '';
			//var turn = false;
			for(var i =1;i<length+1;i++) {
				navDivHtml +='<span class="">'+i+'</span>';
			}
			navDiv.html(navDivHtml);
			
			var process = function(turn) {
				imgDiv.html(items.eq(curItemIndex*2));
				titDiv.html(items.eq(curItemIndex*2+1));
				navDiv.children('span.this').removeClass('this');
				navDiv.children('span').eq(curItemIndex).addClass('this');
				if(options.auto != false) {
					curItemIndex++;
				}
				if(curItemIndex == length) curItemIndex = 0;
				if(turn != true) {
					timeoutReturn = setTimeout(process,options.speed);
				}
			}
			
			navDiv.children('span').each(function(i) {
				$(this).bind(options.type,function() {
					clearTimeout(timeoutReturn);
					curItemIndex = i;
					process(true);
					return false;
				});
			});
			
			this.mouseover(function(e){
				clearTimeout(timeoutReturn);
				div.mouseout(function(evt){
					div.unbind('mouseout');
					var all = div.find('*');
					var t = evt.relatedTarget;
					if(all.index(t) != -1 && t != div[0]) return ;
					timeoutReturn = setTimeout(process,options.speed);
				});
			});
			process();
			return this;
		}
	});
})(jQuery);