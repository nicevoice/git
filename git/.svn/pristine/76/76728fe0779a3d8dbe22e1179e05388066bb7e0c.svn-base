/**
 * based on jQuery 1.3+
 *
 * @author     shanhuhai
 * @copyright  2010 (c) cmstop.com
 * @version    $Id: cmstop.score.js 1354 2010-12-20 14:32:47Z root $
 */
(function($){
	var OPTS = {
		maxValue : 5,
		value : 0,
		spacing : '5px',//每个星星之间的间隔
		width : '20px',
		height : '20px',
		image : 'images/star-big.gif',
		selectedOffset : '-2px',
		unSelectedOffset : '-23px',
		callback : function(){}
	}
	var score = {
		box : null,
		init : function(settings){
			if(typeof settings == 'function') OPTS.callback = settings;
			else $.extend(OPTS, settings || {});
			score.box = $(this);
			var divs = [],value = OPTS.value;	
			for(var i=0;i<OPTS.maxValue;i++){
				divs.push('<div style="position:absolute;left:'+(numeric(OPTS.width)*i+numeric(OPTS.spacing)*(i+1))+'px;width:'+OPTS.width+';height:'+OPTS.height+';background:url(images/star-big.gif) 0px '+(value-->0?OPTS.selectedOffset:OPTS.unSelectedOffset)+';cursor:pointer"></div>');
			}
			score.box.append(divs.join('')).children('div').mouseover(function(){
				score.render($(this).prevAll().andSelf(), true);
				score.render($(this).nextAll(), false);
			}).mouseout(function(){
				score.revert();
			}).click(function(){
				// 双击最右边的星星可取消选择
				newValue	= ($(this).position().left-numeric(OPTS.spacing))/(numeric(OPTS.width)+numeric(OPTS.spacing))+1;
				OPTS.value	= (OPTS.value != newValue ) ? newValue : 0;
				score.revert();
				OPTS.callback(OPTS.value); 
			});
		},
		revert : function(){
			var divs = this.box.children();
			this.render(divs, false);
			for(var i=0;i<OPTS.value;i++){
				this.render(divs.eq(i), true);
			}
		},
		render : function(obj, selected){
			 obj.css('background-position','0px '+(selected ? OPTS.selectedOffset : OPTS.unSelectedOffset));
		}
	}
	function numeric(str){
		return parseInt(str.slice(0,str.length-2));
	}
	$.fn.score = score.init;
})(jQuery);