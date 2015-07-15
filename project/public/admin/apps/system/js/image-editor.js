(function($){

var Editor = {
	init:function(src){
		var bound = $('#bound');
		var area = $('#workarea');
		var areaWidth = area[0].offsetWidth,
			areaHeight = area[0].offsetHeight;
		var img = new Image(), $img = $(img);
		img.onload = function(){
			bound.css({
				width:img.width,
				height:img.height
			});
			$img.appendTo(bound);
			$img.Jcrop({
				onChange:function(){
					console.info('yes');
				}
			});
		};
		img.src = UPLOAD_URL+src;
	}
};
window.Editor = Editor;
})(jQuery);