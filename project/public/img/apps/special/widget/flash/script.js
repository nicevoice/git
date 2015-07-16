(function(){
	DIY.registerEngine('flash', {
		dialogWidth:400,
		addFormReady:function(form, dialog) {
			setTimeout(function(){
				flash_common(dialog);
			}, 0);

		},
		editFormReady:function(form, dialog) {
			setTimeout(function(){
				flash_common(dialog);
			}, 0);
		},
		beforeSubmit:function(form, dialog) {},
		afterSubmit:function(form, dialog) {}
	});
	function flash_common(dialog)
	{
		var upload_max_filesize = dialog.find('#upload_max_filesize').val();
		var progress = dialog.find('#progress');
		var flashsrc = dialog.find('#src');
		var filebtnflash = dialog.find('#filebtn_flash').click(function(){
			ct.fileManager(function(at){
				var file = at.src;
				flashsrc.val(UPLOAD_URL+file);
			}, 'flv,swf');
		});
		var up = $('<span class="button">上传Flash</span>').insertAfter(filebtnflash);
		up.uploader({
			fileExt:'*.swf;*.flv;',
			fileDesc:'FLASH文件',
			fileDataName:'ctvideo',
			sizeLimit:upload_max_filesize,
			multi:false,
			script:'?app=video&controller=video&action=upload',
			complete:function(response){
				var aidaddr = response.split('|');
				if (aidaddr[1]) {
					aidaddr[1] = UPLOAD_URL+aidaddr[1];
					flashsrc.val(aidaddr[1]);
				} else {
					ct.warn('视频上传失败');
				}
			},
			error:function(data){
				ct.warn(data.file.name+'：视频上传失败，'+data.error.type+':'+data.error.info);
			}
		});
	}
})()