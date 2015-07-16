(function($){

function use(url, ok, error) {
    var img = new Image();
    img.onload = function(){ok.call(img)};
    img.onerror = error;
    img.src = url;
}
function slider(){
	if ($.data(this, 'slider-inited')) {
		return;
	}
	$.data(this, 'slider-inited', 1);
	var elem = $(this),
		box = elem.parent().addClass('slider-player'),
		items = elem.children('li'), nums = items.length,
		link = $('<div class="slider-link"></div>').appendTo(box),
		links = items.find('.title').appendTo(link).hide(),
		nav = $('<div class="slider-nav"></div>').appendTo(box),
		moon = $('<div class="slider-moon"></div>').appendTo(box),
		imgWidth  = elem.width(),
		imgHeight = elem.height(),
		navs={}, loaded={}, ival, actived = -1;
	
	function switchTo(i){
		if (i != actived) {
			items.eq(i).stop().css({zIndex:'0',opacity:''}).show();
			links.eq(i).show();
			if (actived != -1) {
				links.eq(actived).hide();
				items.eq(actived).css('zIndex','1').fadeOut();
				navs[actived].removeClass('active');
			}
			actived = i;
			navs[i].addClass('active');
		}
		ival = setTimeout(next, 4000);
	}
	function createA(i){
		var a = $('<a>'+(i+1)+'</a>').click(function(){
			clearTimeout(ival);
			if (i in loaded) {
				switchTo(i);
				return;
			}
			loaded[i] = 0;
			moon.show();
			var img = $('a.pic', items[i]).find('img')[0];
			use(img.src, function(){
				var t = this;
				t.height/imgHeight < t.width/imgWidth
				? (img.height = imgHeight)
				: (img.width = imgWidth);
				switchTo(i);
				moon.hide();
			},function(){
				switchTo(i);
				moon.hide();
			});
		}).appendTo(nav);
		navs[i] = a;
	}
	function next(){
		navs[(actived + 1 >= nums) ? 0 : (actived + 1)].click();
	}
	for (var i=0;i<nums;i++){createA(i)}
	nav.css('top', elem.outerHeight(true)-nav.outerHeight(true));
	nums && navs[0].click();
}
$.fn.slider = function(){
	return this.each(slider);
};

$(function(){
	$('ul.slider-content').slider();
});
})(jQuery);