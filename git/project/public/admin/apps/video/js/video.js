var video = 
{
	setting : function()
	{
		ct.form('视频设置','?app=video&controller=video&action=setccid',200,50,function(response){
			if(response.state)
			{
				ct.tips('视频设置成功');
			}
			else
			{
				ct.error('视频设置失败');
			}
			self.location.reload();
			return true;
		});
	},

	select: function ()
	{
		ct.fileManager(function(videos){
			for(k = 0; k < videos.length; k++)
			{
				var at = videos[k];
				group.add(at.aid, at.src);
			}
		},null,true);
	},

	code : function(contentid) 
	{
		ct.ajax('获取调用代码', '?app=video&controller=video&action=code&contentid='+contentid, 500, 360);
	}
};

