<?php $this->display('header', 'system');?>
<!--tree-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tree/style.css"/>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.tree.js"></script>
<div class="bk_10"></div>
<form id="wap_setting" name="wap_setting" method="post" action="?app=wap&controller=setting&action=index">
	<input name="setting[model][]" type="checkbox" value="1" checked="checked" style="display:none;" /><input name="setting[model][]" type="checkbox" value="2" checked="checked" style="display:none;" />
	<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
		<caption>WAP设置</caption>
		<tr>
			<th width="180"><span class="c_red">*</span> WAP 状态：</th>
			<td><input name="setting[open]" type="radio" value="1"<?php if($setting['open'] == 1) { ?> checked="checked"<?php } ?> class="bdr_5" />开启<input name="setting[open]" type="radio" value="0"<?php if($setting['open'] == 0) { ?> checked="checked"<?php } ?> class="bdr_5" />关闭</td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> WAP 网站名称：</th>
			<td><input id="webname" name="setting[webname]" type="text" size="15" value="<?=$setting['webname']?>" /></td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> WAP 网站LOGO：</th>
			<td><input id="webname" name="setting[logo]" type="text" size="50" value="<?=$setting['logo']?>" /></td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> 首页栏目内容条数：</th>
			<td><input id="index_pagesize" name="setting[index_pagesize]" type="text" size="5" value="<?=$setting['index_pagesize']?>" /></td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> 首页显示最低权重：</th>
			<td><input id="index_weight" name="setting[index_weight]" type="text" size="5" value="<?=$setting['index_weight']?>" /></td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> 栏目首页子栏目显示条数：</th>
			<td><input id="category_pagesize" name="setting[category_pagesize]" type="text" size="5" value="<?=$setting['category_pagesize']?>" /></td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> 终极列表页条数：</th>
			<td><input id="list_pagesize" name="setting[list_pagesize]" type="text" size="5" value="<?=$setting['list_pagesize']?>" /></td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> 栏目显示最低权重：</th>
			<td><input id="list_weight" name="setting[list_weight]" type="text" size="5" value="<?=$setting['list_weight']?>" /></td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> 评论列表页条数：</th>
			<td><input id="comment_pagesize" name="setting[comment_pagesize]" type="text" size="5" value="<?=$setting['comment_pagesize']?>" /></td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> 模型开启：</th>
			<td id="model"><input type="checkbox" name="setting[modelids][]" value="1" <?=in_array(1, $setting['modelids']) ? 'checked' : ''?>/> 文章 &nbsp;&nbsp;&nbsp;&nbsp; <input type="checkbox" name="setting[modelids][]" value="2" <?=in_array(2, $setting['modelids']) ? 'checked' : ''?>/> 组图</td>
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
			<th><span class="c_red">*</span> 图片尺寸：</th>
			<td>宽 <input id="" name="setting[image_width]" type="text" size="5" value="<?=$setting['image_width']?>" /> px　高 <input id="" name="setting[image_height]" type="text" size="5" value="<?=$setting['image_height']?>" /> px</td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> 内容每页文字数：</th>
			<td><input id="" name="setting[content_words]" type="text" size="5" value="<?=$setting['content_words']?>" /></td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> 内容页缓存周期：</th>
			<td><input id="" name="setting[content_expires]" type="text" size="5" value="<?php if($setting['content_expires']) { ?><?=$setting['content_expires']?><?php }else{ ?>0<?php } ?>" /> 秒 0为不缓存，-1为与全局设置中的"内容页缓存有效期"保持一致</td>
		</tr>
	</table>

	<table width="800" border="0" cellspacing="0" cellpadding="0" class="mar_l_8 mar_t_10 table_form">
		<caption>模板设置</caption>
		<tr>
			<th width="15%"><span class="c_red">*</span> 首页模板：</th>
			<td><?=element::template('template_index', 'setting[template_index]', $setting['template_index'], 40)?></td>
		</tr>
		<tr>
			<th width="15%"><span class="c_red">*</span> 列表页模板：</th>
			<td><?=element::template('template_list', 'setting[template_list]', $setting['template_list'], 40)?></td>
		</tr>
		<tr>
			<th width="15%"><span class="c_red">*</span> 文章内容模板：</th>
			<td><?=element::template('template_article', 'setting[template_article]', $setting['template_article'], 40)?></td>
		</tr>
		<tr>
			<th width="15%"><span class="c_red">*</span> 组图内容模板：</th>
			<td><?=element::template('template_picture', 'setting[template_picture]', $setting['template_picture'], 40)?></td>
		</tr>
		<tr>
			<th width="15%"><span class="c_red">*</span> 评论模板：</th>
			<td><?=element::template('template_comment', 'setting[template_comment]', $setting['template_comment'], 40)?></td>
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
	xmlPath:'/apps/system/validators/wap/'
});

$(function(){
	$('#category').placetree();
});

$('#wap_setting').ajaxForm('wap_setting_ok');
function wap_setting_ok(response){
	if (response.state) {
		ct.tips(response.data);
	}else{
		ct.error(response.error);
	}
}
</script>

<?php $this->display('footer', 'system');?>