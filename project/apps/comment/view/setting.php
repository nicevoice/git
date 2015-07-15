<?php $this->display('header', 'system');?>
<div class="bk_10"></div>
<form id="comment_setting" action="?app=comment&controller=setting&action=index" method="POST">
	<table class="table_form mar_l_8" cellpadding="0" cellspacing="0" width="98%">
	<caption>评论配置</caption>
	<tr>
		<th width="120">允许游客评论：</th>
		<td><input type="radio" value="0" name="setting[islogin]" <?php if(!$setting['islogin']){echo "checked='true'";}?>>是<input type="radio" value="1" name="setting[islogin]" <?php if($setting['islogin']){echo "checked='true'";}?>>否 </td>
	</tr>
	<tr>
		<th width="120">评论名前缀：</th>
		<td><input type="text" name="setting[defaultname]" value="<?php echo $setting['defaultname']?>"></td>
	</tr>
	<tr>
		<th>评论需要审核：</th>
		<td><input type="radio" value="1" name="setting[ischeck]" <?php if($setting['ischeck']){echo "checked='true'";}?>>是<input type="radio" value="0" name="setting[ischeck]"<?php if(!$setting['ischeck']){echo "checked='true'";}?>>否 </td>
	</tr>
	<tr>
		<th>开启验证码：</th>
		<td><input type="radio" value="1" name="setting[isseccode]" <?php if($setting['isseccode']){echo "checked='true'";}?> />是<input type="radio" value="0" name="setting[isseccode]" <?php if(!$setting['isseccode']){echo "checked='true'";}?> />否 </td>
	</tr>
	<tr>
		<th><?=element::tips('发布、回复、支持、举报评论的操作时间间隔')?> 时间间隔：</th>
		<td>
			<input type="text" name="setting[timeinterval]" value="<?php echo $setting['timeinterval']?>" size="5"> 秒
		</td>
	</tr>  
	<tr>
		<th>屏蔽ip时间：</th>
		<td><input type="text" name="setting[iptime]" value="<?php echo $setting['iptime']?>"  size="5"> 小时</td>
	</tr> 
	<tr>
		<th>每页评论数：</th>
		<td><input type="text" name="setting[pagesize]" value="<?php echo $setting['pagesize']?>" size="5"></td>
	</tr>
	<tr>
		<th>热门评论数：</th>
		<td><input type="text" name="setting[hotcomment]" value="<?php echo $setting['hotcomment']?>" size="5"></td>
	</tr>
	<tr>
		<th><?=element::tips('超过该层数的回复将自动隐藏，点击展开')?>超过：</th>
		<td><input type="text" name="setting[floorno]" value="<?php echo $setting['floorno']?>" size="5"> 层隐藏回复</td>
	</tr>
	<tr>
		<th>评论字数限制：</th>
		<td><input type="text" name="setting[wordage]" value="<?php echo $setting['wordage']?>" size="5"> 字</td>
	</tr>
	<tr>
		<th><?=element::tips('一行一条数据，凡是包含此字符的评论，会自动加入到审核列表')?> 审核敏感字符：</th>
		<td><textarea name="setting[sensekeyword]" rows="10" cols="60"><?php echo $setting['sensekeyword']?></textarea><span onclick="zoomin(this);" style="cursor:pointer">+</span></td>
	</tr>
	<tr>
		<th><?=element::tips('一行一条数据，凡是包含此字符的评论，则不允许提交')?> 屏蔽非法字符：</th>
		<td><textarea name="setting[unsafekeyword]" rows="10" cols="60"><?php echo $setting['unsafekeyword']?></textarea><span onclick="zoomin(this);" style="cursor:pointer">+</span></td>
	</tr>
	<tr>
		<th></th>
		<td valign="middle"><input type="submit" id="submit" value="保存" class="button_style_2"/></td>
	</tr>
	</table>
</form>
<script type="text/javascript">
function zoomin(obj) {
	$(obj).prev().attr('rows',$(obj).prev().attr('rows')+2);
}

$(function(){
	$('#comment_setting').ajaxForm('submit_ok');
	$('img.tips').attrTips('tips', 'tips_green', 200, 'top');
})
function submit_ok(data) {
	if(data.state) ct.tips("保存成功");
	else ct.error(data.error);
}
</script>
<?php $this->display('footer', 'system');?>