$(function(){
	$("#uploadimg").uploader({
			script         : '?app=system&controller=image&action=upload',
			fileDataName   : 'ctimg',
			fileDesc       : '图象',
			fileExt		   : '*.jpg;*.jpeg;*.gif;*.png;*.bmp;',
			buttonImg	   : 'uploadify/upload.gif',
			multi		   : false,
            jsonType       : 1,
			progress:function(data)
			{
				$('#src').val(data.percentage+'%');
			},
			complete:function(response,data)
			{
                var thumb, file;
                if (Object.prototype.toString.call(response) == '[object Object]') {
                    thumb = response.thumb;
                    file = response.file;
                } else {
                    thumb = file = response;
                }
				$('#src').val(file);
				ImageDialog.showPreviewImage(file);
			},
			error:function(data)
			{
				ct.error(data.error.type +':'+ data.error.info);
			}
	});
})
