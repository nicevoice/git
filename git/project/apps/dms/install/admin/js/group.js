var i = 0;

var group =
{
	init: function ()
	{
		$(".imagebox>img").attrTips();
		$('.imagebox').lightBox();
		$('#pictures>div').find("ul>li").show();
		$('#pictures>div:first-child').find("ul>li:nth-child(2)").hide();
		$('#pictures>div:last-child').find("ul>li:nth-child(3)").hide();
	},
	
	add: function (aid, image, note, sort, pictureid)
	{
		i++;
		if (typeof note === 'undefined') note = '';
		if (typeof sort === 'undefined') sort = i;
		if (typeof pictureid === 'undefined') pictureid = 0;

		var picture = '<div class="pic-list-item">\
			<a class="pic-list-thumb" href="javascript:void(0);">\
			<img src="'+UPLOAD_URL+image+'" alt="">\
			</a>\
			<span class="pic-list-action">\
			<span class="pic-list-action-wrap">\
			<a class="pic-list-action-view" onclick="group.view(this);" title="查看" href="javascript:void(0);">查看</a>\
			<a class="pic-list-action-edit" onclick="group.edit(this);" title="编辑" href="javascript:void(0);">编辑</a>\
			<a class="pic-list-action-delete" onclick="group.drop(this);" title="删除" href="javascript:void(0);">删除</a>\
			</span>\
			</span>\
			<input type="hidden" name="pictures['+i+'][image]" id="image_'+i+'" value="'+image+'"/>\
			</div>';
		$('#pictures').append(picture);
		group.reupload(i);
		group.init();
	},
	
	reupload :function(n){
		var t = this;
		$("#uploadphoto"+n).uploader({
			script         : '?app=dms&controller=picture&action=upload',
			fileDesc		 : '图像',
			fileExt		 : '*.jpg;*.jpeg;*.gif;*.png;',
			multi          : false,
			complete:function(response,data){
				if(response != 0) {
					var img = response.split('|');
					var aid = img[0];
					var img = img[1];
					$("#image_"+n).val(img);
					$("#aid_"+n).val(aid);
					$("#thinkbox_"+n).attr('src', UPLOAD_URL+img).parent().attr('href',UPLOAD_URL+img);
				} else {
					ct.error('对不起！您上传文件过大而失败!');
				}
			},
			error:function(data) {
				alert(data.error.type);
			}
		}).next().andSelf().hover(function(){
			this.style.color = '#FFFF00';
			this.style.textDecoration = 'underline';
		},function(){this.style.color ='#FFFFFF';this.style.textDecoration = 'none'});
		return;
	},
	
	up: function (i)
	{
		var obj = $('#picture_'+i).parent();
		if (obj.prev().is('div'))
		{
			var prev_id = obj.prev().attr('id');
			var sort = $('#sort_'+i).val();
			var prev_sort = $('#sort_'+prev_id).val();
			
			$('#sort_'+i).val(prev_sort);
			$('#sort_'+prev_id).val(sort);
			
			obj.insertBefore(obj.prev());
			group.init();
		}
	},
	
	down: function (i)
	{
		var obj = $('#picture_'+i).parent();
		if (obj.next().is('div'))
		{
			var next_id = obj.next().attr('id');
			var sort = $('#sort_'+i).val();
			var next_sort = $('#sort_'+next_id).val();
			
			$('#sort_'+i).val(next_sort);
			$('#sort_'+next_id).val(sort);
			
			obj.insertAfter(obj.next());
			group.init();
		}
	},
	
	remove: function (i)
	{
		$('#picture_'+i).parent().remove();
		group.init();
	},
	
	order_sort: function (i, val)
	{
		if(isNaN(val))
		{
			ct.warn('请输入阿拉伯数字！');
			$('#sort_'+i).val('0');
			return ;
		}
		
		var data = new Array();
		$('#pictures > div').each(function(i){
			var id = $(this).attr('id');
			data[i] = [$('#aid_'+id).val(), $('#image_'+id).val(), $('#note_'+id).val(), $('#sort_'+id).val(), $('#pictureid_'+id).val()];
		});
		data.sort(function(a, b) {
			return a[3]-b[3];
		});
		
		$('#pictures').html('');
		$.each(data, function(i, r){
			group.add(r[0], r[1], r[2], r[3], r[4]);
		});
	},

	drop : function(obj) {
		$(obj).parent().parent().parent().remove();
	},

	view : function(obj) {
		thumb	= $(obj).parent().parent().parent().find('img');
		var orig = {
			width: thumb.width(),
			height: thumb.height(),
			left: thumb.offset().left,
			top: thumb.offset().top
		},
		overlay = $('<div class="cmstop-gallery-overlay"></div>').css({
            position: 'fixed',
			left: 0,
			top: 0,
			width: 9999,
			zIndex: 9998,
			background: '#FFF',
			opacity: '0.8',
			height: '9999px'
		}).appendTo(document.body),
		box = $(['<div class="cmstop-gallery-box"><img src="' + thumb.attr('src') + '" /></div>'].join('')).appendTo(document.body);
		box.css({
			position: 'absolute',
			zIndex: 9999,
			width: orig.width,
			height: orig.height,
			left: orig.left,
			top: orig.top
			
		});
		function hide() {
			overlay.remove();
			box.animate(orig, 'fast', function() {
				box.remove();
			});
		}
		overlay.click(hide);
		box.click(hide);
	},

	edit : function(obj) {
		var thumb	= $(obj).parent().parent().parent().find('img');
		var ipt		= $(obj).parent().parent().parent().find('input');
		ct.editImage(ipt.val(), function(json){
			ipt.val(json.file);
			thumb.attr('src',UPLOAD_URL+json.file+'?'+Math.random()).show();
		});
		return false;
	}
}