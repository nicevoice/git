
function nothing(e)
{
}

/* 载入工作区大图  */
function load_img(src)
{
	$('#img').remove();
	var img = document.createElement('img');
	img.src = src+'?'+Math.random(5);
	img.id = 'img';
	//判断加载图片完成
	window.load_interval_timer = setInterval(function()
	{
		if (img.width > 0 && img.height > 0)
		{
			try { clearInterval(window.load_interval_timer); }catch(e) {}
		}
		else
		{
			return;
		}
		
		$(img).prependTo('#imgarea');
		var p = $('#imgarea').width()/img.width;
		var p1 = $('#imgarea').height()/img.height;
		if (p1<p) p= p1;
		if (p > 1) p = 1;
		$(img).data('width',img.width).data('height',img.height).data('minp',p);
		zoom_to(p,200);
		$('#imgarea').get(0).scrollTop = 0;
		$('#imgarea').get(0).scrollLeft = 0;
		//图片拖动效果
		$(img).bind('mousedown.move',function(event)
		{
			if (window.croping) return;
			var imgarea = $('#imgarea').get(0);
			var start_x = parseInt(event.clientX);
			var start_left = parseInt(imgarea.scrollLeft);
			var start_y = parseInt(event.clientY);
			var start_top = parseInt(imgarea.scrollTop);
			var img = $(this);
			$(document.body).bind('mousemove.img',function(event)
			{
				var end_x = parseInt(event.clientX);
				var end_y = parseInt(event.clientY);
				imgarea.scrollTop = start_top - end_y + start_y;
				imgarea.scrollLeft = start_left - end_x + start_x;
				event.preventDefault();
				return false;
			}).bind('mouseup.img',function(event)
			{
				$(document.body).unbind('.img');
			});
			event.preventDefault();
			return false;
		});
	},100);	
}

/* 裁剪图片时，当拖动选框时触发 */
function crop_tracker_change()
{
	var holder = $('.jcrop-holder');
	if (holder.length <= 0) return;
	holder = holder.get(0);
	var img = $(holder.firstChild.firstChild.firstChild);
	var tracker = $(holder.firstChild);
	var oimg = $('#img');
	var imgarea = $('#imgarea').get(0);
	img.attr('src',oimg.attr('src'));
	img.css(
	{
		left:-parseInt(imgarea.scrollLeft)-parseInt(tracker.css('left')),
		top:-parseInt(imgarea.scrollTop)-parseInt(tracker.css('top')),
		width:oimg.width(),
		height:oimg.height()
	});
	
	//更新提示信息
	var p = $('#img').data('p');
	//计算真实坐标
	var area = 
	{
		x: Math.round( (parseInt(imgarea.scrollLeft) + parseInt(tracker.css('left')))/p),
		y: Math.round( (parseInt(imgarea.scrollTop) + parseInt(tracker.css('top')))/p),
		w: Math.round( parseInt(tracker.width())/p),
		h: Math.round( parseInt(tracker.height())/p),
		src:$('#img').attr('src').replace(/\?[0-9\.]*$/ig,'')
	};
	
	//减去白边
	if (area.x + area.w > oimg.data('width')) area.w = oimg.data('width') - area.x;
	if (area.y + area.h > oimg.data('height')) area.h = oimg.data('height') - area.y;
	
	if (area.w <= 0) area.w = 0;
	if (area.h <= 0) area.h = 0;
		
	$('#croped_width').val(area.w);
	$('#croped_height').val(area.h);

	return;
}

/* 设定选框大小 */
function update_crop()
{
	var tracker = $('.jcrop-tracker');
	if (tracker.length > 0 ) 
	{
		var w = parseInt($('#croped_width').val());
		var h = parseInt($('#croped_height').val());
		var p = $('#img').data('p');
		w = w*p;
		h = h*p;
		if (w > 0 && h > 0)
			$('#transgif').data('Jcrop').setSelect([0,0,w,h]);
	}
}

/* 执行裁剪 */
function do_crop(event)
{
	if ($('.pop_box:visible').length > 0) return;
	window.croping = true;
	var img = $('#img');
	var tracker = $('.jcrop-tracker');
	if (tracker.length > 0 ) //执行
	{
		
		var imgarea = $('#imgarea').get(0);
		var tracker = tracker.parent().parent();
		if (tracker.width() <= 0)
		{
			return;
		}
		var p = $('#img').data('p');
		//计算真实坐标
		var area = 
		{
			x: Math.round( (parseInt(imgarea.scrollLeft) + parseInt(tracker.css('left')))/p),
			y: Math.round( (parseInt(imgarea.scrollTop) + parseInt(tracker.css('top')))/p),
			w: Math.round( parseInt(tracker.width())/p),
			h: Math.round( parseInt(tracker.height())/p),
			src:$('#img').attr('src').replace(/\?[0-9\.]*$/ig,'')
		};
		//减去白边
		if (area.x + area.w > $('#img').data('width')) area.w = $('#img').data('width') - area.x;
		if (area.y + area.h > $('#img').data('height')) area.h = $('#img').data('height') - area.y;
		
		area.new_w = area.w;
		area.new_h = area.h;
		
		if (area.w <= 0) return;
		if (area.h <= 0) return;

		$.post('?app=system&controller=attachment&action=crop_image',area,crop_callback,'json');
		$(document.body).trigger('mousedown.crop').unbind('.crop');
		return;
	}
	else
	{
		$('<div id="transgifwrapper"><img id="transgif" src="images/trans.png" /></div>').appendTo($('#imgarea').parent());
		window.croping = true;
		$('#crop_bar').fadeIn(200);
		var offset = $('#imgarea').offset();
		$('#transgifwrapper').css(
		{
			height:$('#imgarea').height(),
			width:$('#imgarea').width(),
			top:offset.top,
			left:offset.left
		}).find('img').css(
		{
			height:$('#imgarea').height(),
			width:$('#imgarea').width()
		}).Jcrop(
		{
			onChange:crop_tracker_change
		});
	}
	
	setTimeout(function()
	{
		
		$(document.body).unbind('.crop').bind('mousedown.crop',function(event)
		{
			if ($(event.target).attr('rel') == 'crop') return;
			
			reset_topbt();
			$('#transgifwrapper').remove();
			event.preventDefault();
			event.stopPropagation();
			update_zoom();
			$(document.body).unbind('.crop');
			$('#crop_bar').hide();
			window.just_canceled_croping = true;
			setTimeout("window.just_canceled_croping = false;",300);
			return false;
		});
		$(document.body).bind('keydown.crop',function(event)
		{
			if (event.keyCode == 13) do_crop(event);
		});
	},300);
}

/* 裁剪完成，增加历史记录 */
function crop_callback(data)
{
	reset_topbt();
	if (data.state)
	{
		$(document.body).trigger('mousedown.crop');
		load_img_and_add_history(data.src);
		
	}
	else
	{
		ct_alert(data.error,'error',false);
	}
}

/* rt */
function load_img_and_add_history(src)
{
	img_current++;
	img_history[img_current-1] = src;
	while(img_history.length > img_current) img_history.pop();
	load_img(src);
	temporary_img_list.push(src);
	$('#prev_history').fadeTo(300,1);
	$('#next_history').fadeTo(300,0);
}

/* 拖动左边调整显示比例 */
function update_zoom()
{
	if (window.croping)
	{
		window.croping = false;
	}
	
	var top = parseInt($('#pointer').css('top'));
	
	if (top <= 0)
	{
		top = 0;
		$('#pointer').css('top','0px');
	}
	if (top >= 290)
	{
		top = 290;
		$('#pointer').css('top','290px');
	}
	top = 290 - top;
	var p = top/290;
	zoom_to(p,0);
}

/* 调整显示比例 */
function zoom_to(p,duration)
{
	var img = $('#img');
	if (p>1) p = 1;
	if (p < img.data('minp')) p = img.data('minp');
	duration = parseInt(duration);
	//新的width和height
	var width = img.data('width')*p;
	var height = img.data('height')*p;
	
	
	/*添加文字的图片*/
	if(window.adding_words)
	{
		var dleft = (width - img.width());
		var dtop = (height - img.height());
		$('.word_img').each(function()
		{
			wimg = $(this);
			/*计算定点移动距离*/
			var _dleft = dleft * wimg.data('px');
			var _dtop = dtop * wimg.data('py');
			wimg.data('left',wimg.data('left')+_dleft);
			wimg.data('top',wimg.data('top')+_dtop);
			
			wimg.animate(
			{
				width:wimg.data('width')*p,
				height:wimg.data('height')*p,
				top:wimg.data('top'),
				left:wimg.data('left')
			},duration);
		});
	}

	img.animate({width: width,height:height},duration);
	$('#stick').height(height);
	$('#pointer').animate({top:290-p*290},duration).html(parseInt(p*100)+'%');
	crop_tracker_change();
	img.data('p',p);
}

/* 调整图片大小 */
function do_scale()
{
	if ($('.pop_box:visible').length > 0) return;
	dialog('choose', 
	{
		'title': '调整图片大小', 
		'dialog_id': 'rescale_dialog', 
		width:230, 
		height:150, 
		modal:false,
		'type':'html', 
		'content':'<div style="margin:10px 20px;">'
		+'宽：<input  class="dialog_input" size="4" type="text" rel="scale" id="new_w" onkeyup="check_wh(this)"'
		+' value="'+$('#img').data('width')+'" />px&nbsp;&nbsp;'
		+'高：<input  class="dialog_input" size="4" type="text" rel="scale" id="new_h" onkeyup="check_wh(this)"'
		+' value="'+$('#img').data('height')+'" />px<br />'
		+'<input style="border:0;" type="checkbox" id="baochibili" checked="checked" />保持比例'
		+'</div>'
	}, 'scale_submit', reset_topbt);
	$('input[rel=scale]').keydown(function(event)
	{
		if (event.keyCode == 13) scale_submit();
	});
}

/* 点击保持比例的时候，自动同步宽高*/
function check_wh(o)
{
	if ($('#baochibili').is(':checked'))
	{
		var img = $('#img');
		var w = img.data('width');
		var h = img.data('height');
		if ($(o).attr('id') == 'new_w')
		{
			$('#new_h').val( Math.round($(o).val()*h/w) );
		}
		else
		{
			$('#new_w').val( Math.round($(o).val()*w/h) );
		}
	}
}

/* 提交调整后的尺寸 */
function scale_submit()
{
	var img = $('#img');
	var w = img.data('width');
	var h = img.data('height');
	var new_w = parseInt($('#new_w').val());
	var new_h = parseInt($('#new_h').val());
	reset_topbt();
	if (!isNaN(new_w) && !isNaN(new_h) && new_w > 0 && new_h > 0)
	{
		
		$.post('?app=system&controller=attachment&action=crop_image',
		{
			x:0,
			y:0,
			w:w,
			h:h,
			new_w:new_w,
			new_h:new_h,
			src:$('#img').attr('src').replace(/\?[0-9\.]*$/ig,'')
		},crop_callback,'json');
		$('#rescale_dialog').dialog('close');
	}
	else
	{
		ct_alert('输入错误','error',false);
	}
}


/* 图片加字后，载入生成的文字图片 */
function load_word_img(src,vars)
{
	var img = document.createElement('img');
	img.src = src;
	img.className = 'word_img';
	if (vars.left) img.style.left = vars.left;
	if (vars.top) img.style.top = vars.top;
	if (vars.zIndex) img.style.zIndex = vars.zIndex;
	temporary_word_imgs.push(src);
	//判断加载图片完成
	window.load_word_img_interval_timer = setInterval(function()
	{
		if (img.width > 0 && img.height > 0)
			try { clearInterval(window.load_word_img_interval_timer); }catch(e) {}
		else return;
		
		$(img).appendTo('#imgarea');
		$(img).data('width',img.width).data('height',img.height).data('vars',vars);
		var p = $('#img').data('p');
		$('.selected').removeClass('selected');
		$(img).height($(img).data('height')*p).width($(img).data('width')*p).addClass('selected');
		
		addword_imgs_indexes();
		//图片拖动效果
		$(img).bind('mousedown.move',function(event)
		{
			//设置为选中
			$('.selected').removeClass('selected');
			$(img).addClass('selected');
			$('#add_word_bar').fadeIn(100);
			$(document.body).bind('click.add_word_select',function(event)
			{
				if ($(event.target).attr('rel') == 'addword') return;
				$('img.selected').removeClass('selected');
				$(document.body).unbind('.add_word_select');
				$('#add_word_bar').fadeOut(100);
			});
			
			var imgarea = $('#imgarea').get(0);
			var start_x = parseInt(event.clientX);
			var start_left = parseInt($(img).css('left'));
			var start_y = parseInt(event.clientY);
			var start_top = parseInt($(img).css('top'));
			$(document.body).bind('mousemove.wordimg',function(event)
			{
				var end_x = parseInt(event.clientX);
				var end_y = parseInt(event.clientY);
				var top = start_top + end_y - start_y;
				var left = start_left + end_x - start_x;
				$(img).css('top', top);
				$(img).css('left', left);
				
				
				event.preventDefault();
				return false;
			}).bind('mouseup.wordimg',function(event)
			{
				$(document.body).unbind('.wordimg');
				/*缓存位置数据*/
				var oimg = $('#img');
				var px = parseInt($(img).css('left'))/oimg.width();
				var py = parseInt($(img).css('top'))/oimg.height();
				$(img).data('px',px)
					.data('py',py)
					.data('left',parseInt($(img).css('left')))
					.data('top',parseInt($(img).css('top')));
				
			});
			event.preventDefault();
			return false;
		}).bind('dblclick',function(event)
		{
			add_word(this);
		}).mouseover(function()
		{
			$(img).addClass('hover');
		}).mouseout(function()
		{
			$(img).removeClass('hover');
		}).click(function(event)
		{
			event.preventDefault();
			return false;
		});
		
		
		
	},100);	
}

//取消加字操作
function cancel_add_word()
{
	$('.word_img').remove();
	window.adding_words = false;
	$('#add_word_bar').fadeOut(300);
}

//往后一层
function addword_put_backward()
{
	var img  = $('img.selected');
	if (img.length == 1)
	{
		addword_imgs_indexes();
		var this_index = parseInt(img.css('z-index'));
		if (this_index == 1) return;
		this_index--;
		var the_other_img = null;
		$('img.word_img').each(function()
		{
			if (this.style.zIndex == this_index) the_other_img = this;
		});
		if (the_other_img)
		{
			the_other_img.style.zIndex = img.css('z-index');
			img.css('z-index',this_index);
		}
	}
}

//往前一层
function addword_put_forward()
{
	var img  = $('img.selected');
	if (img.length == 1)
	{
		addword_imgs_indexes();
		var this_index = parseInt(img.css('z-index'));
		if (this_index == $('.word_img').length) return;
		this_index++;
		var the_other_img = null;
		$('img.word_img').each(function()
		{
			if (this.style.zIndex == this_index) the_other_img = this;
		});
		if (the_other_img)
		{
			the_other_img.style.zIndex = img.css('z-index');
			img.css('z-index',this_index);
		}
	}
}

//整理文字图层
function addword_imgs_indexes()
{
	var imgs = [];
	$('.word_img').each(function()
	{
		imgs.push({index:this.style.zIndex,img:this});
	});
	imgs.sort(function(img1,img2)
	{
		return (img1.index - img2.index);
	});
	for(var i=0;i<imgs.length;i++)
	{
		imgs[i].img.style.zIndex = (i+1);
	}
}

//编辑文字图层
function edit_addword()
{
	var img  = $('img.selected');
	if (img.length == 1)
	{
		img.trigger('dblclick');
	}
}
//删除图层
function delete_addword()
{
	var img  = $('img.selected');
	if (img.length == 1)
	{
		img.remove();
		addword_imgs_indexes();
	}
}
//合并所有可见图层
function add_word_complete(callback)
{
	var word_imgs = [];
	var p = $('#img').data('p');
	$('.word_img').each(function()
	{
		var data = 
		{
			x:Math.round(parseInt($(this).css('left'))/p)+1,
			y:Math.round(parseInt($(this).css('top'))/p)+1,
			index:parseInt($(this).css('z-index')),
			src:$(this).attr('src')
		};
		word_imgs.push(data);
	});
	if (word_imgs.length <= 0) return;
	word_imgs.sort(function(img1,img2)
	{
		return (img1.index - img2.index);
	});
	var vars = 
	{
		bg:$('#img').attr('src').replace(/\?[0-9\.]*$/ig,'')
	};
	for(var i=0;i<temporary_word_imgs.length;i++)
	{
		vars['temporary_word_imgs[]'] = temporary_word_imgs[i];
	}
	
	for(var i =0;i<word_imgs.length;i++)
	{
		vars['word_img['+i+'][x]'] = word_imgs[i].x;
		vars['word_img['+i+'][y]'] = word_imgs[i].y;
		vars['word_img['+i+'][src]'] = word_imgs[i].src;
	}
	
	$.post('?app=system&controller=attachment&action=add_word_do',vars,function(data)
	{
		if (data.state)
		{
			load_img_and_add_history(data.src);
			cancel_add_word();
			if (callback) callback(data.src);
		}
		else
		{
			ct_alert(data.error,'error',false);
		}
	},'json');
}

//显示加字对话框
function add_word(img)
{
	if ($('.pop_box:visible').length > 0) return;
	if (img == undefined)
	{
		var edit = false;
		var title = "添加文字";
		var vars = 
		{
			content:'',
			size:30,
			font:false,
			color:'#ffffff'
		};
		
	}
	else
	{
		img = $(img);
		var edit = true;
		var title = "编辑文字";
		var vars = img.data('vars');
	}

	$('#add_word_dialog').remove();
	dialog('choose', 
	{
		'title': title, 
		'dialog_id': 'add_word_dialog', 
		width:350, 
		height:200, 
		modal:false,
		'type':'html', 
		'content':'<div style="margin:10px 20px;line-height:30px;">'
		+'文字：<input style="width:220px;" class="dialog_input" type="text" rel="addword" id="addword_content"  value="'+vars.content+'" /><br />'
		+'大小：<input size="3"  class="dialog_input"  type="text" rel="addword" id="addword_size" value="'+vars.size+'" />pt&nbsp;&nbsp;'
		+'颜色：<input type="hidden" id="addword_color" value="'+vars.color+'" />'
		+'<img src="images/color.gif" alt="色板" height="16" width="16" id="choose_color" '
		+'style="background-color:'+vars.color+'" />&nbsp;'
		+'字体：<select rel="addword"  id="addword_font">'
		+font_options
		+'</select>'
		+'</div>'
	}, add_word_submit = function()
	{
		var vars = 
		{
			content:$('#addword_content').val(),
			size:parseInt($('#addword_size').val()),
			font:$('#addword_font').val(),
			color:$('#addword_color').val(),
			zIndex:false
		};
		
		
		if (vars.content == '')
		{
			$('#addword_content').css('border-color','red');
			return;
		}
		
		if (vars.size <= 0 || isNaN(vars.size))
		{
			ct_alert('文字大小应为一个正整数','error',false);
			return;
		}
		//记录编辑前的位置
		if (edit && img)
		{
			vars.left = img.css('left');
			vars.top = img.css('top');
			vars.zIndex = img.css('z-index');
		}
		
		reset_topbt();
		$.post('?app=system&controller=attachment&action=create_words_image',vars,function(data)
		{
			if (data.state)
			{
				if (!vars.zIndex)
				{
					var max_index = 1;
					$('.word_img').each(function()
					{
						if (parseInt(this.style.zIndex) > max_index) max_index = parseInt(this.style.zIndex);
					});
					vars.zIndex = parseInt(max_index)+1;
				}
				load_word_img(data.src,vars);
				$('#add_word_dialog').dialog('close');
				window.adding_words = true;
				$('#add_word_bar').fadeIn(300);
				if (edit && img)  img.remove();
			}
			else
			{
				ct_alert(data.error,'error',false);
			}
			
		},'json');
	}, reset_topbt);
	//选颜色
	$('#choose_color').click(function()
	{
		$(this).colorPicker(
		{ 
			setColor:'#choose_color',
			setValue:"#addword_color"
		});
		$('#fy_ColorPicker').css('z-index','2000');
	});
	//检查文字内容
	$('#addword_content').change(function()
	{
		$(this).css('border-color',(this.value == '')?'red':'');
	});
	
	
	if (edit)
	{
		//重新选择select
		$('#addword_font').find('option').each(function()
		{
			if ( this.value == vars.font) this.selected = true;
		});
	}
	//支持回车提交
	$('input[rel=addword]').keydown(function(event)
	{
		if (event.keyCode == 13) add_word_submit();
	});
}

//点击最下面的提交按钮。结束当前所有操作，保存图片，删除缓存，更新列表等
function all_submit(imgsrc)
{
	if (window.croping)
	{
		ct_alert('请先完成图片裁剪操作','error',false);
		$('[role=dialog]').find('.btn_area').find('button').attr('rel','crop');
		return;
	}
	if (window.adding_words)
	{
		add_word_complete(all_submit);
		return;
	}
	var orig_img = orig_img_src;
	var now_img = imgsrc?imgsrc:$('#img').attr('src').replace(/\?[0-9\.]*$/ig,'');
	var vars = 
	{
		orig_img:orig_img,
		now_img:now_img
	};
	
	
	for(var i=0;i<temporary_img_list.length;i++)
	{
		vars['temporary_img_list[]'] = temporary_img_list[i];
	}
	
	$.post('?app=system&controller=attachment&action=edit_image_submit',vars,function(s)
	{
		if (s) ct_alert(s,'error',false);
		else
		{
			if (parent)
			{
				if (parent.edit_image_done){
					parent.edit_image_done.call();
				}
				else if (window.dialogCallback && dialogCallback.ok)
				{
					dialogCallback.ok(vars);
				}
				else
				{
					window.getDialog && getDialog().dialog('close');
				}
			}
		}
	});
}

function close_window()
{
	if (parent)
	{
		if (parent.edit_image_cancel)
		{
			parent.edit_image_cancel.call();
		}
		else if (window.dialogCallback && dialogCallback.cancel)
		{
			dialogCallback.cancel();
		}
		else
		{
			window.getDialog && getDialog().dialog('close');
		}
	}
}

//执行一个操作的时候，禁止开始其他操作
function disable_topbt(o)
{
	$('[rel=topbt]').attr('disabled','disabled').addClass('button_noaction_style');
	$(o).attr('disabled','').removeClass('button_noaction_style');
}
//恢复所有操作可点
function reset_topbt()
{
	$('[rel=topbt]').attr('disabled','').removeClass('button_noaction_style');
}