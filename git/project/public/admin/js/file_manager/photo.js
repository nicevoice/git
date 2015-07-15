/*关闭预览，删除所有相关dom */
lz_close_preview = function(event)
{
	window.lz_preview_cancled = true;
	$(window).unbind('scroll.preview').unbind('resize.preview');
	if (document.getElementById('preview_bottom'))
	{
		$('#preview_bottom').animate(
		{
			opacity:0,
			top:'-='+$('#preview_bottom').height()+'px'
		},200,function()
		{
			$('#preview_bottom').remove();
			$('[preview]').fadeOut('fast',function()
			{
				$(this).remove();
				$('[preview]').remove();
			});
		});
	}
	else
	{
		$('#preview_bg').fadeOut('fast',function()
		{ 
			try {this.parentNode.removeChild(this);} catch(e) { };
			$('[preview]').each(function(){ try {this.parentNode.removeChild(this);} catch(e) { }; });
		});
	}
	event && event.stopPropagation();
}
/* 获得当前页面的最大宽高，做背景用 */
lz_get_page_size = function()
{
	var d = document, w = window;
	var re = {};
	if (d.documentElement && d.documentElement.clientHeight)
	{
		var doc = d.documentElement;
		re.width = (doc.clientWidth>doc.scrollWidth)?doc.clientWidth:doc.scrollWidth;
		re.height = (doc.clientHeight>doc.scrollHeight)?doc.clientHeight:doc.scrollHeight;
	}
	else
	{
		var doc = d.body;
		re.width = (w.innerWidth>doc.scrollWidth)?w.innerWidth:doc.scrollWidth;
		re.height = (w.innerHeight>doc.scrollHeight)?w.innerHeight:doc.scrollHeight;
	}
	return re;
}
/* 检查图片文件是否加载，onload的替换方法，解决ie缓存bug */
lz_preview_img_load_check = function()
{
	if (!lz_preview_worked && lz_imgobj.width > 0 && lz_imgobj.height >0)
		lz_preview_img_load.call(o);
	else
		setTimeout(lz_preview_img_load_check,100);
}

/* loading图片的地址 */
var lz_preview_loading_image_url = 'images/loading.gif';
var animating = false;
var preview_small_width = 99;
var preview_small_height = 99;
var preview_small_border = 3;
/* 预览大图窗口最大宽高 */
var preview_big_width = $(window).width()-300;
var preview_big_height = $(window).height()-150;

/* 调用函数，传入带有src属性的dom对象，不一定是img元素 */
function preview(o)
{
	if(o.attr('rel') == 'no') return false; 
	var d = document,w = window;
	window.animating = true;
	window.lz_preview_obj = o;
	window.lz_preview_cancled = false;
	//preload the loading image
	var _loading_img = document.createElement('img');
	_loading_img.src = lz_preview_loading_image_url;


	//global background
	var bg = d.createElement('div');
	var page = lz_get_page_size();
	$(bg).css(
	{
		height: page.height,
		width: page.width,
		zIndex: '1000',
		display:'none',
		position:'absolute',
		top:'0px',
		left:'0px',
		padding:'0px',
		margin:'0px',
		backgroundColor:'#000'
	})
	.fadeTo(1,0,function(){ this.style.display=''; })
	.attr('id','preview_bg')
	.attr('preview','yes')
	.bind('click',lz_close_preview)
	.fadeTo(500,0.8);
	
	//append bg to document.body
	$(d.body).append(bg);

	//image container
	var div = d.createElement('div');
	//remember last image's width and height
	if (!window.lz_preview_last_width || !window.lz_preview_last_height)
	{
		window.lz_preview_last_width = 100;
		window.lz_preview_last_height = 100;
	}
	$(div)
	.css('display','none')
	.css('opacity','0')
	.css(
	{
		height: window.lz_preview_last_height+'px',
		width: window.lz_preview_last_width+'px',
		zIndex: '1005',
		backgroundColor: '#fff',
		position:'absolute',
		padding:'0px',
		margin:'0px',
		top: $(d).scrollTop()+50, 
		left: $(d).scrollLeft() + ($(window).width() / 2) - (window.lz_preview_last_width/2)
	})
	.css('background','#fff url('+lz_preview_loading_image_url+') no-repeat 50% 50%')
	.attr('preview','yes')
	.attr('id','preview_img_div')
	.fadeTo(200,1);

	//append the container to body
	$(d.body).append(div);

	window.lz_preview_worked = false;
	window.lz_imgobj = document.createElement('img');

	
	//load the image
	window.lz_imgobj.onload = lz_preview_img_load;
	$(window.lz_imgobj).attr('id','preview_hidden_img').bind('load',lz_preview_img_load);
	//run!
	window.lz_imgobj.src = o.attr('src');
	
	
	$(window).bind('scroll.preview',function()
	{
		var size = lz_get_page_size();
		$('#preview_bg').width(size.width).height(size.height);
	}).bind('resize.preview',function()
	{
		var size = lz_get_page_size();
		$('#preview_bg').width(size.width).height(size.height);
	});
	
}

lz_preview_img_load = function()
{
	if (window.lz_preview_cancled) return;
	var d=document,w=window;
	if (lz_preview_worked) return;
	lz_preview_worked = true;
	$('#preview_hidden_img').remove();
	o = window.lz_preview_obj;
	
	var p = window.preview_big_width/lz_imgobj.width;
	var p1 = window.preview_big_height/lz_imgobj.height;
	if (p1 < p ) p = p1; //取最小的
	if (p > 1) p = 1;
	var div_width = parseInt(lz_imgobj.width * p);
	var div_height = parseInt(lz_imgobj.height * p);
	
	div_width += 20;
	div_height += 20;
	
	var speed_x = Math.abs(window.lz_preview_last_width - div_width)*2.5;
	var speed_y = Math.abs(window.lz_preview_last_height - div_height)*2.5;
	if (speed_x>500) speed_x = 500;
	if (speed_y>500) speed_y = 500;
	if (speed_x<100) speed_x = 100;
	if (speed_y<100) speed_y = 100;
	
	window.lz_preview_last_width = div_width;
	window.lz_preview_last_height = div_height;
	
	$('#preview_bottom').remove();
	$('[preview_small_img]').remove();

	$('#preview_img_div')
		.animate({ height: div_height },speed_y)
		.animate({ width: div_width , left: $(d).scrollLeft() + ($(w).width() - div_width ) / 2 },speed_x,function()
		{ 
			if (window.lz_preview_cancled) return;
			var img = document.createElement('img');
			img.style.margin = "10px";
			
			img.src = o.attr('src');
			$(img)
				.attr('preview','yes')
				.attr('id','preview_big_img')
				.width(div_width-20)
				.height(div_height-20);
			$(img).fadeTo(1,0.5,function()
			{
				$('#preview_img_div').empty().append(this).find('img').fadeTo(500,1, function()
				{
					if (window.lz_preview_cancled) return;
					var img_div = $('#preview_img_div');
				
					var div = document.createElement('div');
					$(div)
						.attr('preview','yes')
						.attr('id','preview_bottom')
						.css(
						{
							position:'absolute',
							background:'#fff',
							margin:'0px',
							padding:'0px',
							opacity:0,
							zIndex:1001,
							paddingBottom:'5px'
						});
					$(d.body).append(div);
					var rel = $(o).attr('rel');
					var total = 0;
					var ii = 0;
					$('[rel=preview]').each(function()
					{
						total++;
						if ($(this).attr('src') == o.attr('src') ) ii = total;
					});
					window.prev_img = $('[rel=preview]').eq( (ii-2)<0?total-1:ii-2 );
					window.next_img = $('[rel=preview]').eq( (ii>=total)?0:ii );
					if (prev_img.attr('thumb'))
					{
						prev_div = preview_create_small(prev_img,img_div);
						$(prev_div).css({left: $(window).width()/2 - img_div.width()/2 - window.preview_small_border});
						document.body.appendChild(prev_div);
						$(prev_div)
						.attr('id','preview_small_prev')
						.attr('preview_small_img','yes')
						.animate(
						{
							left: img_div.offset().left - window.preview_small_width -window.preview_small_border - 30,
							opacity:0.6
						},300,function(){
							window.animating = false;
						});
					}
					if (next_img.attr('thumb'))
					{
						next_div = preview_create_small(next_img,img_div);
						$(next_div).css({left: $(window).width()/2 + img_div.width()/2 - window.preview_small_width -window.preview_small_border});
						document.body.appendChild(next_div);
						$(next_div)
						.attr('id','preview_small_next')
						.attr('preview_small_img','yes')
						.animate(
						{
							left: img_div.offset().left + img_div.width() + 30,
							opacity:0.6
						},300,function(){
							window.animating = false;
						});
					}
					   
					var description = document.createElement('div');
					var description_text = o.find('.filename').html();
					if (description_text) description_text+= '&nbsp;';
					description_text += '['+ii+'/'+total+']';
					$(description).css(
					{
						float:'left',
						marginLeft:'10px',
						display:'inline'
					})
					.attr('preview','yes')
					.html(description_text);
										
					var closebt = document.createElement('div');
					$(closebt)
					.attr('preview',"yes")
					.css(
					{
						float:'right',
						marginRight:'10px',
						cursor:'pointer',
						width:'50px',
						fontWeight:'bold',
						display:'inline',
						'text-align':'center'
					})
					.bind('click',lz_close_preview)
					.html('\u5173\u95ed')
					.hover(function(){
						$(this).css({color:'#FFFFFF','background-color':'#333333'});
					},function(){
						$(this).css({color:'#000000','background-color':'#FFFFFF'})
					});
					
					var clear_div = document.createElement('div');
					$(clear_div).css({width:0,height:0,overflow:'hidden',clear:'both'});
					$(div).append(description).append(closebt).append(clear_div)
					.css( 
					{
						opacity:0,
						width:parseInt(img_div.width())+'px',
						top: parseInt(img_div.css('top')) + parseInt(img_div.height())- parseInt($(div).height()),
						left: img_div.css('left')
					})
					.animate(
					{
						opacity:1,
						top: parseInt(img_div.css('top')) + parseInt(img_div.height()) +'px'
					},300);
					
				});
			});
		});
}

/* 两边的小图 */
function preview_create_small(prev_img,img_div)
{
	var prev_div = document.createElement('div');
	$(prev_div).css(
	{
		height:window.preview_small_height+'px',
		width:window.preview_small_width+'px',
		cursor:'pointer',
		border:window.preview_small_border+'px solid #fff',
		opacity:0,
		background:'#fff url('+prev_img.attr('thumb')+') no-repeat 50% 50%',
		overflow:'hidden',
		position:'absolute',
		zIndex:1002,
		top: img_div.offset().top + (img_div.height()-window.preview_small_height-window.preview_small_border)/2,
		left: $(window).width()/2
	})
	.attr('preview','yes')
	.bind('mouseover',function()
	{
		$(this).fadeTo(200,1);
	})
	.bind('mouseout',function()
	{
		$(this).fadeTo(200,0.6);
	})
	.bind('click',function(event)
	{
		window.animating = true;
		preview_sibling(prev_img);
		event.stopPropagation();
	});
	return prev_div;
}

/* 显示两边的小图 */
function preview_sibling(o)
{
	var d = document,w = window;
	window.lz_preview_obj = o;
	window.lz_preview_cancled = false;

	window.lz_preview_worked = false;
	window.lz_imgobj = document.createElement('img');
	$('#preview_bottom').animate(
	{
		opacity:0,
		top:'-='+$('#preview_bottom').height()+'px'
	},200,function()
	{
		$('#preview_bottom').remove();
		$('#preview_img_div').empty();
	});
	//load the image
	window.lz_imgobj.onload = lz_preview_img_load;
	$(window.lz_imgobj).attr('id','preview_hidden_img').bind('load',lz_preview_img_load);
	//run!
	window.lz_imgobj.src = o.attr('src');
}
/******/
$(self.document).bind('keydown',function(e){
	if(e.keyCode == 39 && !window.animating) $('#preview_small_next').trigger('click');
	if(e.keyCode == 37 && !window.animating) $('#preview_small_prev').trigger('click');
	e.keyCode == 27 && lz_close_preview(e);
});