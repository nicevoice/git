$(function(){

	//$(document).bind("contextmenu",function(e){   
		//return false;
	//});

	var wh=$(window).height();
	$("a.open_about").click(function(){
		$(".about").css({"height":wh}).show();
		$(document).scrollTop($(document).scrollTop());
		$('html, body').animate({scrollTop:$(".main").height()},200,function(){
			$(".main").hide();
			$("html").css({"overflow-y":"auto"});
			$(".about").css({"overflow-y":"scroll"});
		});
		return false;
	});
	$("a.close_about").click(function(){
		$(".about").css({"overflow-y":"auto"});
		$("html").css({"overflow-y":"scroll"});
		$(".main").show();
		$(document).scrollTop($(".main").height());
		$('html, body').animate({scrollTop:$(".main").height()-$(".about").height()},200,function(){
			$(".about").hide();
		});
		return false;
	});
	$(".pic_b").hover(function(){
		$(this).find(".load_ok").css({"background":"#000"});
		$(this).find("h3,dl,ins,span").show();
		//$(this).find("ins").height($(this).find("span").height());
		$(this).find(".load_ok img").stop().animate({"opacity":"0.4"},200);
		$(this).find("a").addClass("hover");
	},function(){
		_this=$(this).find(".load_ok");
		$(this).find("h3,dl,ins,span").hide();
		$(this).find(".load_ok img").stop().animate({"opacity":"1"},200,function(){
			_this.css({"background":"#E8E8E8"});
		});
		$(this).find("a").removeClass("hover");
	});
	$(".pic_b h3,.pic_b dt,.pic_b dl").hover(function(){
		$(this).addClass("hover");
	},function(){
		$(this).removeClass("hover");
	});
	$(".pic_b dd.sina").hover(function(){
		$(this).addClass("sinahover");
	},function(){
		$(this).removeClass("sinahover");
	});
	$(".pic_b dd.qq").hover(function(){
		$(this).addClass("qqhover");
	},function(){
		$(this).removeClass("qqhover");
	});
	
	if(parseInt($(".bbox img").width())>214)
	{
		$(".bbox img").width(214);
	}
	
	eachpic();
	$(window).scroll(function(){
		eachpic();
	});
	
	if($(".main").height()<$(window).height())
	{
		$(".main").height($(window).height());
	}
	
	
	$(".pic_a").click(function(){
		//location.href=$(this).attr("href");
	});
	
	
});

function eachpic(){
	
	$(".pic_div:not('.load_ok')").each(function(){
		if( parseInt($(this).offset().top) < parseInt($(document).scrollTop())+parseInt($(window).height()) )
		{
			var _this=$(this);
			$(this).html('<img src="'+_this.attr("src")+'" width="304" style="filter:alpha(opacity=0);opacity:0;" />');
			$(this).find("img").load(function(){	
				_this.addClass("load_ok");
				_this.find("img").animate({"opacity":1},300);
				_this.parent().find("em").css({"top":(_this.height()/2)-(_this.parent().find("em").height()/2)-10});
			});
		}
	});
}