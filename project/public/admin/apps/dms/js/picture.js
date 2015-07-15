var picture = 
{
	upload: function ()
	{
		$("#uploadify").uploader({
			script         :'?app=dms&controller=picture&action=upload',
			fileDesc		 : '图像',
			fileExt		 : '*.jpg;*.jpeg;*.gif;*.png;',
			buttonImg	 	 :'images/upload.gif',
			 complete:function(response, data){
			 	if(response != 0){
					var img = response.split('|');
					group.add(img[0], img[1]);
			 	}else{
			 		ct.error('对不起！您上传文件过大而失败!');
			 	}
			 },
			 error:function(data)
			 {
			 	ct.error(data.error.type +':'+ data.error.info);
			 }

		
		});
	},

	zip: function ()
	{
		$("#uploadzip").uploader({
			script         : '?app=dms&controller=picture&action=upload',
			fileDesc		 : 'Zip格式的文件!',
			fileExt		 : '*.zip;',
			buttonImg	 	 :'images/upzip.gif',
			multi			:false,
			complete: function( response, data)
			{
			 	if(response != 0)
			 	{
					var imgs = response.split(',');
					for(k = 0; k < imgs.length; k++)
					{
						var img = imgs[k].split('|');
						group.add(img[0], img[1]);
					}
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
	},
	
	remote: function ()
	{
		ct.form('远程采集', '?app=picture&controller=picture&action=remote', 400, 220, function (response){
			if (response.state)
			{
				$.each(response.data, function(key, value){
				   img = value.split('|');
				   group.add(img[0], img[1]);
				});
				return true;
			}
			else
			{
				ct.error(response.error);
			}
		});
	},
	
	select: function ()
	{
		ct.fileManager(function(pictures){
			for(k = 0; k < pictures.length; k++)
			{
				var at = pictures[k];
				group.add(at.aid, at.src);
			}
		},null,true);
	}
}