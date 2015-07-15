<?php $this->display('header', 'system');?>
<script type="text/javascript" src="uploader/cmstop.uploader.js"></script>
<script type="text/javascript" src="imageEditor/cmstop.imageEditor.js"></script>
<script type="text/javascript" src="js/cmstop.filemanager.js"></script>
<!--tree-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tree/style.css"/>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.tree.js"></script>
<div class="bk_10"></div>
<form id="mobile_setting" name="mobile_setting" method="post" action="?app=mobile&controller=setting&action=index">
	<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
		<caption>基本设置</caption>
		<tr>
			<th width="180"><span class="c_red">*</span> 状态：</th>
			<td><input name="setting[open]" type="radio" value="1"<?php if($setting['open'] == 1) { ?> checked="checked"<?php } ?> class="bdr_5" />开启<input name="setting[open]" type="radio" value="0"<?php if($setting['open'] == 0) { ?> checked="checked"<?php } ?> class="bdr_5" />关闭</td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> 网站名称：</th>
			<td><input id="webname" name="setting[webname]" type="text" size="15" value="<?=$setting['webname']?>" /></td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> 网站LOGO：</th>
			<td><input id="logo" name="setting[logo]" type="text" size="50" value="<?=$setting['logo']?>" /></td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> 模型开启：</th>
			<td id="model">
				<input type="hidden" name="setting[modelids][]" value="1" />
				<input type="checkbox" name="setting[modelids][]" value="2" <?=in_array(2, $setting['modelids']) ? 'checked' : ''?>/> 组图 &nbsp;&nbsp;&nbsp;&nbsp; 
				<input type="checkbox" name="setting[modelids][]" value="4" <?=in_array(4, $setting['modelids']) ? 'checked' : ''?>/> 视频
			</td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> 栏目开启：</th>
			<td>
                <input type="hidden" id="category" name="setting[catids]" value="<?=$catids?>"
                    class="placetree"
                    url="?app=system&controller=category&action=cate&catid=%s"
                    initUrl="?app=system&controller=category&action=name&catid=%s"
                    paramVal="catid"
                    paramTxt="name"
                    multiple="multiple" />
			</td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> 内容筛选最低权重：</th>
			<td><input id="weight" name="setting[weight]" type="text" size="5" value="<?=$setting['weight']?>" /></td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> 首页头条绑定区块编号：</th>
			<td><input id="index_banner_section" name="setting[index_banner_section]" type="text" size="5" value="<?=$setting['index_banner_section']?>" /></td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> 首页推荐内容管理方式：</th>
			<td><input id="index_rec_1" onclick="changeRecTr(1)" name="setting[index_recommend_type]" type="radio" value="1"<?php if($setting['index_recommend_type'] == 1) { ?> checked="checked"<?php } ?> class="bdr_5" />区块管理<input id="index_rec_0" onclick="changeRecTr(0)" name="setting[index_recommend_type]" type="radio" value="0"<?php if($setting['index_recommend_type'] == 0) { ?> checked="checked"<?php } ?> class="bdr_5" />按权重自动调用</td>
		</tr>
		<tr id="index_rec_tr_1">
			<th><span class="c_red">*</span> 首页推荐绑定区块编号：</th>
			<td><input id="index_recommend_section" name="setting[index_recommend_section]" type="text" size="5" value="<?=$setting['index_recommend_section']?>" /></td>
		</tr>
		<tr id="index_rec_tr_2">
			<th><span class="c_red">*</span> 首页推荐内容最低权重：</th>
			<td><input id="index_weight" name="setting[index_weight]" type="text" size="5" value="<?=$setting['index_weight']?>" /></td>
		</tr>
		<tr id="index_rec_tr_3">
			<th><span class="c_red">*</span> 首页推荐内容显示条数：</th>
			<td><input id="index_recommend_size" name="setting[index_recommend_size]" type="text" size="5" value="<?=$setting['index_recommend_size']?>" /></td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> 列表页单次加载条数：</th>
			<td><input id="list_pagesize" name="setting[list_pagesize]" type="text" size="5" value="<?=$setting['list_pagesize']?>" /></td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> 评论页单次加载条数：</th>
			<td><input id="comment_pagesize" name="setting[comment_pagesize]" type="text" size="5" value="<?=$setting['comment_pagesize']?>" /></td>
		</tr>
        <tr>
            <th><span class="c_red">*</span> 评论排行天数：</th>
            <td><input id="comment_days" name="setting[comment_days]" type="text" size="5" value="<?=$setting['comment_days']?>" /></td>
        </tr>
		<tr>
			<th><span class="c_red">*</span> 缓存时间：</th>
			<td><input id="cache" name="setting[cache]" type="text" size="5" value="<?=$setting['cache']?>" /></td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> 开启图片BASE64：</th>
			<td><input name="setting[open_base64]" type="radio" value="1"<?php if($setting['open_base64'] == 1) { ?> checked="checked"<?php } ?> class="bdr_5" />开启<input name="setting[open_base64]" type="radio" value="0"<?php if($setting['open_base64'] == 0) { ?> checked="checked"<?php } ?> class="bdr_5" />关闭</td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> 推荐页头图尺寸：</th>
			<td>宽 <input id="" name="setting[image_banner_width]" type="text" size="5" value="<?=$setting['image_banner_width']?>" /> px　高 <input id="" name="setting[image_banner_height]" type="text" size="5" value="<?=$setting['image_banner_height']?>" /> px</td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> 通用列表页图片尺寸：</th>
			<td>宽 <input id="" name="setting[image_list_width]" type="text" size="5" value="<?=$setting['image_list_width']?>" /> px　高 <input id="" name="setting[image_list_height]" type="text" size="5" value="<?=$setting['image_list_height']?>" /> px</td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> 内容页小图片尺寸：</th>
			<td>宽 <input id="" name="setting[image_content_small_width]" type="text" size="5" value="<?=$setting['image_content_small_width']?>" /> px　高 <input id="" name="setting[image_content_small_height]" type="text" size="5" value="<?=$setting['image_content_small_height']?>" /> px</td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> 内容页大图片尺寸：</th>
			<td>宽 <input id="" name="setting[image_content_big_width]" type="text" size="5" value="<?=$setting['image_content_big_width']?>" /> px　高 <input id="" name="setting[image_content_big_height]" type="text" size="5" value="<?=$setting['image_content_big_height']?>" /> px</td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> 组图列表小图尺寸：</th>
			<td>宽 <input id="" name="setting[image_picture_list_width]" type="text" size="5" value="<?=$setting['image_picture_list_width']?>" /> px　高 <input id="" name="setting[image_picture_list_height]" type="text" size="5" value="<?=$setting['image_picture_list_height']?>" /> px</td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> 组图浏览大图尺寸：</th>
			<td>宽 <input id="" name="setting[image_picture_show_width]" type="text" size="5" value="<?=$setting['image_picture_show_width']?>" /> px　高 <input id="" name="setting[image_picture_show_height]" type="text" size="5" value="<?=$setting['image_picture_show_height']?>" /> px</td>
		</tr>
		<tr>
			<th></th>
			<td valign="middle"><input type="submit" id="submit" value="保存" class="button_style_2"/></td>
		</tr>
	</table>
</form>
<script type="text/javascript" src="apps/system/js/treeview_selector.js"></script>
<script type="text/javascript">
$.validate.setConfigs({
	xmlPath:'/apps/system/validators/mobile/'
});

$(function(){
	$('#category').placetree();
});

function changeRecTr(t){
	if(t == 1){
		$("#index_rec_tr_1").show();
		$("#index_rec_tr_2").hide();
		$("#index_rec_tr_3").hide();
	}else{
		$("#index_rec_tr_1").hide();
		$("#index_rec_tr_2").show();
		$("#index_rec_tr_3").show();
	}
}
changeRecTr(<?php echo $setting['index_recommend_type']; ?>);

$('#mobile_setting').ajaxForm('mobile_setting_ok');
function mobile_setting_ok(response){
	if (response.state) {
		ct.tips(response.data);
	}else{
		ct.error(response.error);
	}
}
</script>

<?php $this->display('footer', 'system');?>