/**
 * based jQuery 1.3+ javascript famework
 *
 * @author     shanhuhai
 * @copyright  2010 (c) cmstop.com
 * @version    $Id: cmstop.adjuster.js 1350 2010-10-21 02:52:18Z root $
 */
(function($){
	var OPTS = {
		isStep		: false,	
		stepConfig	: null,
		minOffset   : '0',//设置滑块的横向范围
		maxOffset   : '1',
		offset      : '0',
		hwidth      : '6px',
		onDragInit	: function(){},
		onDragStart : function(){},
		onDrag      : function(){},
		onDragEnd   : function(){},
		handleBg    : '#000000',
		cursor		: 'pointer',
		delay		: 100,
		pointWidth	: '6px',
		pointHeight	: '10px',
		pointBg     : '#FF6600',
		appro		: 0.005
	},
	slider = {
	 	handle : null,
		box : null,
		hWidth : 0,
		bWidth : 0,
		bHeight : 0,
		roundset : null,
		defaultOffset : 0,
		init : function(options){
			$.extend(OPTS,options||{});
			var t = $(this),
			tposition = t.css('position'),
			c = OPTS.stepConfig;
			slider.defaultOffset = OPTS.offset;
			slider.box = t;
			slider.hWidth = slider._getPxNum(OPTS.hwidth);
			slider.bWidth = slider._getPxNum(t.css('width'));
			slider.bHeight = slider._getPxNum(t.css('height'));
			OPTS.delay = parseInt(OPTS.delay);
			t.css('position',(tposition !='relative' && tposition != 'absolute')?'relative':tposition);
			t.append($('<div style="position:absolute;z-index:10"></div>'));//初始化滑块
			var  h =slider.handle = t.children(0),
			offset = OPTS.offset*slider.bWidth-slider.hWidth/2;
			h.css({'background':OPTS.handleBg,
			'left'     :offset+'px',
			'width'    :OPTS.hwidth,
			'height'   :slider.bHeight,
			'cursor'   :OPTS.cursor
			});
			if(c){
				pw = slider._getPxNum(OPTS.pointWidth),pLeft = 0,pobj = null,pstack = [];
				for(var i in c){
					pOffset = slider.bWidth*i-pw/2;
					pobj = $('<div style="position:absolute"></div>').css({
					'left'      :pOffset,
					'background':OPTS.pointBg,
					'width'     :OPTS.pointWidth,
					'height'	:OPTS.pointHeight,
					'cursor'	:OPTS.cursor
					});
					t.append(pobj);
					pstack.push([pobj,i,c[i]]);
				}
			}
			h.onDragInit = OPTS.onDragInit;
			h.onDragStart = OPTS.onDragStart; //为handle定义事件
			h.onDragEnd  = OPTS.onDragEnd;
			h.onDrag = OPTS.onDrag;
			var percent = (offset+slider.hWidth/2)/slider.bWidth;
			h.onDragInit(h, t, pstack, c);
			t.bind('mousedown', slider.start).bind('click', slider.clickMove);
			return this;
		},
		start : function(e){
			var h = slider.handle,
			b = slider.box,
			left = h.position().left;
			h.lastMouseX = e.pageX;
			$(document).bind('mousemove', slider.drag);
			$(document).bind('mouseup', slider.end);
			h.onDragStart(h, e, (left+slider.hWidth/2)/slider.bWidth);
			return false;
		},
		drag : function(e){
			var h = slider.handle,
			b = slider.box,
			mouseX = e.pageX,
			left = h.position().left,
			currentLeft = left+(mouseX-h.lastMouseX),
			minOffset = slider.bWidth*OPTS.minOffset-slider.hWidth/2,
			maxOffset = slider.bWidth*OPTS.maxOffset-slider.hWidth/2,
			c = OPTS.stepConfig;
			if(currentLeft<minOffset || currentLeft>maxOffset) return;
			h.css('left', currentLeft);
			h.lastMouseX = mouseX;
			var percent = (currentLeft+slider.hWidth/2)/slider.bWidth;
			h.onDrag(h, e, percent, c[percent]);
			return false;
		},
		end : function(e){
			var h = slider.handle,
			left = h.position().left,
			b = slider.box,
			c = OPTS.stepConfig;
			$(document).unbind('mousemove');
			$(document).unbind('mouseup');
			if(e.target == b[0] || e.target.parentNode ==b[0]) return;
			if(c) {
				var r = slider._fixOffset(left);
				left = r==left?left:r-slider.hWidth/2;
				h.css('left',left);
			}
		    if(OPTS.isStep) slider._stepDrop(h, e);
			else{
				var c = OPTS.stepConfig,
				percent = (left+slider.hWidth/2)/slider.bWidth;
				h.onDragEnd(h,e,percent,[percent,c[percent]]);
			}
		},
		clickMove : function(e){
			var b = slider.box,
			h = slider.handle,
			offset = e.pageX-b.offset().left;
			c = OPTS.stepConfig;
			if(c) offset = slider._fixOffset(offset);
			if(OPTS.isStep) slider._stepDrop(h, e ,offset);		
			else{
				h.animate({left:(offset-h.width()/2)},OPTS.delay,function(){
					var percent = offset/slider.bWidth,
					c = OPTS.stepConfig;
					h.onDragEnd(h, e, percent, [percent,c[percent]]);
				});
			}
		},
		_stepDrop : function(h, e, offset){
			var bWidth = slider.box.width(),
			c = OPTS.stepConfig,
			hOffset = offset?offset:h.position().left,
			min = 0,max = 0,point = 0;
			for(var i in c){
				if(hOffset>bWidth*i) min = bWidth*i;
				if(hOffset<bWidth*i) {
					max = bWidth*i;break;
				}
			}
			point = (hOffset-min)>(max-hOffset)?max:min;
			h.animate({'left':(point-slider.hWidth/2)},OPTS.delay);
			h.onDragEnd(h,e,point/bWidth,c[point/bWidth]);
		},
		_getPxNum : function(px){
			return  parseInt(px.substr(0,px.length-2));
		},
		_fixOffset : function(offset){
			var c = OPTS.stepConfig,
			b = slider.box;
			for(var i in c){
				i  = parseFloat(i);
				if(offset>=slider.bWidth*(i-OPTS.appro) && offset<=slider.bWidth*(i+OPTS.appro)){
					offset = slider.bWidth*i;break;
				} 
			}
			return offset;
		},
		setPoint : function(percent){
			var h = slider.handle,
			b = slider.box,
			offset = slider.bWidth*percent-slider.hWidth/2,
			c = OPTS.stepConfig;
			h.animate({left:offset},OPTS.delay,function(){
				h.onDragEnd(h,'set',percent,[percent,c[percent]]);
			});
		},
		reset : function(){
			this.setPoint(this.defaultOffset);
		}
	}
	$.fn.slider = slider.init;
	$.slider = slider;
})(jQuery);