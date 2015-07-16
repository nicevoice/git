$(function(){
	$("#videoInput").uploader({
			script         : '?app=video&controller=video&action=upload',
			fileDataName   : 'ctvideo',
			fileDesc		 : '视频',
			fileExt		 : '*.swf;*.flv;*.avi;*.wmv;*.rm;*.rmvb;*.mp4;',
			buttonImg	 	 :'images/videoupload.gif',	
			multi		:false,
			complete:function(response,data)
			{
				var aidaddr=response.split('|');
				$("#aid").val(aidaddr[0]);
				aidaddr[1]=UPLOAD_URL+aidaddr[1];
				$("#video").val(aidaddr[1]);
				$("#ptline").show();
			}
	})
})