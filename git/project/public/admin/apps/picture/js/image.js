var uploadPicList	= $('#upload_pic_list');
var uploadPicThumb	= $('#upload_pic_thumb');

var picDelete = function(obj, aid) {
	$.getJSON('?app=picture&controller=picture&action=delete_pic', {'id':aid}, function(json) {
		if (json.state) {
			$('#pic_table_'+aid).remove();
			$('#pic_list_'+aid).remove();
		} else {
			ct.error('删除失败');
		}
	});
};

$('#localUpload').uploader({
	'script' : '?app=picture&controller=picture&action=upload',
	'fileDataName' : 'multiUp',
	'fileExt' : '*.jpg;*.jpeg;*.gif;*.png;',
	'complete':function(response, data){
		response =(new Function("","return "+response))();
		if(response.state) {
			var imgUrl	= $(response.code).attr('src');
			var imgTitle= $(response.code).attr('alt');
			var s1	= new Array('<tr id="pic_table_'+response.aid+'">',
							'<td>',
								'<img src="'+imgUrl+'" alt="" width="90" height="80" />',
								'<input type="hidden" name="post['+response.aid+'][url]" value="'+imgUrl+'" />',
							'</td>',
							'<td>',
								'<input type="text" name="post['+response.aid+'][title]" value="'+imgTitle+'" class="info" placeholder="标题" />',
								'<textarea name="post['+response.aid+'][description]" class="info" placeholder="描述"></textarea>',
							'</td>',
							'<td>',
								'<a href="javascript:;" onclick="picDelete(this, '+response.aid+')"><img src="images/delete.gif" alt="" width="16" height="16" /></a>',
							'</td>',
						'</tr>').join('\r\n');
			uploadPicList.append(s1);
			var s2 = new Array('<div class="pic-list-item" id="pic_list_'+response.aid+'">',
								'<a class="pic-list-thumb" href="javascript:void(0);">',
									'<img alt="" src="'+imgUrl+'" width="110" height="80" />',
								'</a>',
								'<span class="pic-list-action">',
									'<a href="javascript:;" onclick="picDelete(this, '+response.aid+')"><img src="images/delete.gif" alt="" width="16" height="16" /></a>',
								'</span>',
							'</div>').join('\r\n');
			uploadPicThumb.append(s2);
		} else {
			ct.error(response.msg);
		}
	}
});

$('#zipUpload').uploader({
	script : '?app=system&controller=image&action=zip',
	fileDesc : 'Zip格式的文件!',
	fileExt : '*.zip;',
	multi :false,
	complete: function( response, data)
	{
		response =(new Function("","return "+response))();
		if(response.state)
		{
			// 展示图片
			$.each(response.data, function(i, k) {
				var s1	= new Array('<tr id="pic_table_'+k.aid+'">',
							'<td>',
								'<img src="'+k.url+'" alt="" width="90" height="80" />',
								'<input type="hidden" name="post['+k.aid+'][url]" value="'+k.url+'" />',
							'</td>',
							'<td>',
								'<input type="text" name="post['+k.aid+'][title]" value="'+response.title+'" class="info" />',
								'<textarea name="post['+k.aid+'][description]" class="info"></textarea>',
							'</td>',
							'<td>',
								'<a href="javascript:;" onclick="picDelete(this, '+k.aid+')"><img src="images/delete.gif" alt="" width="16" height="16" /></a>',
							'</td>',
						'</tr>').join('\r\n');
				uploadPicList.append(s1);
				var s2	= new Array('<div class="pic-list-item" id="pic_list_'+k.aid+'">',
								'<a class="pic-list-thumb" href="javascript:void(0);">',
									'<img alt="" src="'+k.url+'" width="110" height="80" />',
								'</a>',
								'<span class="pic-list-action">',
									'<a href="javascript:;" onclick="picDelete(this, '+k.aid+')"><img src="images/delete.gif" alt="" width="16" height="16" /></a>',
								'</span>',
							'</div>').join('\r\n');
				uploadPicThumb.append(s2);
			});
		}
		else
		{
			ct.error('对不起！您上传的文件非法!');
		}
	},
	error: function(data)
	{
		ct.error(data.error.type);
	}
});

//远程采集部分
$('#remoteUpload').bind('click', function() {
	ct.form('远程采集', '?app=picture&controller=picture&action=remote', 400, 220, function (response){
		if (response.state)
		{
			$.each(response.data, function(key, value){
			   img = value.split('|');
				var s1	= new Array('<tr id="pic_table_'+img[0]+'">',
							'<td>',
								'<img src="'+UPLOAD_URL+img[1]+'" alt="" width="90" height="80" />',
								'<input type="hidden" name="post['+img[0]+'][url]" value="'+UPLOAD_URL+img[1]+'" />',
							'</td>',
							'<td>',
								'<input type="text" name="post['+img[0]+'][title]" value="" class="info" />',
								'<textarea name="post['+img[0]+'][description]" class="info"></textarea>',
							'</td>',
							'<td>',
								'<a href="javascript:;" onclick="picDelete(this, '+img[0]+')"><img src="images/delete.gif" alt="" width="16" height="16" /></a>',
							'</td>',
						'</tr>').join('\r\n');
				uploadPicList.append(s1);
				var s2	= new Array('<div class="pic-list-item" id="pic_list_'+img[0]+'">',
								'<a class="pic-list-thumb" href="javascript:void(0);">',
									'<img alt="" src="'+UPLOAD_URL+img[1]+'" width="110" height="80" />',
								'</a>',
								'<span class="pic-list-action">',
									'<a href="javascript:;" onclick="picDelete(this, '+img[0]+')"><img src="images/delete.gif" alt="" width="16" height="16" /></a>',
								'</span>',
							'</div>').join('\r\n');
				uploadPicThumb.append(s2);
			});
			return true;
		}
		else
		{
			ct.error(response.error);
		}
	});
});

$('#picForm').bind('submit', function() {
	$(this).ajaxSubmit({
		'success' : function(json) {
			$.each(uploadPicList.find('tr'), function(i,k) {
				var aid = $(k).attr('id').substr(10);
				var src = $(k).find('img').attr('src');
				var note = $(k).find('textarea').val();
				window.dialogCallback.insert(aid, src, note);
			});
			window.dialogCallback.close();
		},
		'error' : function() {
			ct.error('插入失败');
		}
	});
	return false;
});

var showList	= function() {
	$('#vt_thumb').removeClass('vt_list_on');
	$('#vt_list').addClass('vt_list_on');
	uploadPicThumb.hide();
	uploadPicList.show();
};

var showThumb	= function() {
	$('#vt_list').removeClass('vt_list_on');
	$('#vt_thumb').addClass('vt_list_on');
	uploadPicList.hide();
	uploadPicThumb.show();
};