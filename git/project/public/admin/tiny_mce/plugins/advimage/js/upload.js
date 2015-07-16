$(function(){
	$("#uploadimg").uploader({
			script : '?app=system&controller=image&action=upload',
			fileDataName : 'ctimg',
			fileDesc	 : '图像',
			fileExt		 : '*.jpg;*.jpeg;*.gif;*.png;*.bmp;',
			buttonImg	 	 :'images/upload.gif',
			multi:false,
			progress:function(data) {
			},
			complete:function(response, data){	 
				$('#src').val(response);
				ImageDialog.showPreviewImage(response);
			}
	});
});