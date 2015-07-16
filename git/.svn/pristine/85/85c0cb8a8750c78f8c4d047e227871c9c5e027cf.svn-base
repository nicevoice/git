<?php $this->display('header', 'system');?>
<div style="width: 98%;" class="suggest mar_t_10 mar_l_8">
  <h2>友情提示</h2>
  <p> CC视频是中国最大的视频网站服务平台，为超过十万家视频网站提供视频产品和服务。

CC视频旗下的著名产品包括：广泛应用于论坛、博客、CMS等的视频通，灵活、强大、稳定的威视视频建站系统、便捷易用的马克斯网站系统。

CC视频立足于为互联网网站提供最好的视频建站服务。经过2005年以来的艰苦创业，CC视频的视频软件产品都是中国互联网最流行的建站程序软件。作为中国最大的视频网站服务提供商，CC视频每天服务几十万个网站，数千万次的视频播放。“影视建站有马克斯，垂直建站有威视”，CC视频凭借旗下两款视频建站程序正在成为视频建站的不二选择，不断推动着中国互联网视频应用的普及。构建于CC旗下网站群体的CC视频媒体，也已经获得了众多的广告伙伴的认同而进行合作，其中包括盛大、九城、奥美、阿里巴巴等等。此外，CC视频也获得了知名风险投资企业IDG的认同和投资。这些投资合作伙伴除了给CC视频带来了更加雄厚的资金实力，也给CC视频带来了国际化公司运作的经验。
  </p>
</div>
<div class="bk_10"></div>
<form id="setting" action="?app=video&controller=setting&action=index" method="POST">
<table class="table_form mar_l_8" cellpadding="0" cellspacing="0" width="98%">
	<caption>视频配置</caption>
	<tr >
		<th width="70px">CC用户ID：</th>
		<td><input type="text" name="ccid" value="<?=$SETTING['ccid']?>" size="5"/></td>
	</tr>
	<tr>
		<th>&nbsp;</th>
		<td>
			<input type="submit" class="button_style_2" value="保存"/>
		</td>
	</tr>  

</table>
</form>
<script type="text/javascript">
$('#setting').ajaxForm(function(json){
		if (json.state) {
			ct.tips(json.info);
		} else {
			ct.error(json.error);
		}
	});
</script>
<?php $this->display('footer', 'system');?>