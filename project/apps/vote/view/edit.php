<?php $this->display('header');?>
<form name="vote_edit" id="vote_edit" method="post" action="?app=vote&controller=vote&action=edit">
<input type="hidden" name="modelid" id="modelid" value="<?=$modelid?>" />
<input type="hidden" name="contentid" id="contentid" value="<?=$contentid?>" />
<? if($status == 6): ?>
<input type="hidden" name="url" id="url" value="<?=$url?>" />
<? endif; ?>
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<tr>
		<th width="80"><span class="c_red">*</span> 栏目：</th>
		<td><?=element::category('catid', 'catid', $catid)?>&nbsp;&nbsp;<?= element::referto_pro('referto',$typeid.','.$subtypeid.','.$zoneid,$cattypeid.','.$catsubtypeid.','.$catzoneid) ?></td>
	</tr>
	<tr>
		<th><span class="c_red">*</span> 标题：</th>
		<td><?=element::title('title', $title, $color)?></td>
	</tr>
	<tr id="tr_subtitle" style="display:<?=($subtitle ? 'table-row' : 'none')?>">
		<th>副题：</th>
		<td><input type="text" name="subtitle" id="subtitle" value="<?=$subtitle?>" size="100" maxlength="120" /></td>
	</tr>
	<tr>
		<th>类型：</th>
		<td>
			<input name="type" type="radio" class="checkbox_style" value="radio"<? if($type == "radio"){?> checked="checked"<? } ?> onclick="$('#maxoptions_span').hide();" /> 单选
			<input name="type" type="radio" class="checkbox_style" value="checkbox"<? if($type == "checkbox"){?> checked="checked"<? } ?> onclick="$('#maxoptions_span').show();" /> 多选
			<span id="maxoptions_span" <?php if($type == "radio"){?>style="display:none;"<?php } ?>>最多可选  <input id="maxoptions" name="maxoptions" type="text" size="2" value="<?=$maxoptions?>" /> 项 <?=element::tips('留空为不限制')?></span>
		</td>
	</tr>
</table>
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="mar_l_8">
	<tr>
		<th width="80" style="color:#077AC7;font-weight:normal;" class="t_r"><span class="c_red">*</span> 选项：</th>
		<td>
			<table id="vote_options" width="480" border="0" cellspacing="0" cellpadding="0" class="table_info">
				<thead>
					<tr>
						<th width="30"><div class="move_cursor"></div></th>
						<th width="360">选项</th>
						<th width="60">初始票数</th>
						<th width="">删</th>
					</tr>
				</thead>
				<tbody id="options">
				</tbody>
			</table>
		</td>
	</tr>
	<tr>
		<th>&nbsp;</th>
		<td><div class="mar_l_8 mar_5"><input type="button" name="add_option_btn" value="增加选项" class="hand button_style" onclick="option.add()" /></div></td>
	</tr>
</table>
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="mar_l_8 mar_t_10 table_form">
	<tr>
		<th width="80">介绍：</th>
		<td><textarea name="description" id="description" maxLength="255" style="width:710px;height:40px" class="bdr"><?=$description?></textarea> </td>
	</tr>
	<tr>
		<th>缩略图：</th>
		<td><?=element::image('thumb', $thumb, 60)?></td>
	</tr>
	<tr>
		<th> Tags：</th>
		<td><?=element::tag('tags', $tags)?></td>
	</tr>
    <?php $this->display('more_tag', 'system');?>
	<tr>
		<th>防刷限制：</th>
		<td>同IP <input id="mininterval" name="mininterval" type="text" value="<? if($mininterval){ echo $mininterval; }?>" size="4" />小时内不得重复投票 <?=element::tips('0或者留空为不限制')?></td>
	</tr>
	<tr>
		<th>开始时间：</th>
		<td><input id="starttime" name="starttime" type="text" class="input_calendar" value="<?=$starttime ? date('Y-m-d H:i:s', $starttime) : ''?>" size="20"/></td>
	</tr>
	<tr>
		<th>截止时间：</th>
		<td><input id="endtime" name="endtime" type="text" class="input_calendar" value="<?=$endtime ? date('Y-m-d H:i:s', $endtime) : ''?>" size="20"/></td>
	</tr>
	<tr>
		<th>属性：</th>
		<td><?=element::property("proid", "proids", $proids)?></td>
	</tr>
	<tr>
		<th><?=element::tips('权重将决定文章在哪里显示和排序')?> 权重：</th>
		<td>
		<?=element::weight($weight, $myweight);?>
		</td>
		</tr>
	<tr>
		<th><?=element::tips('推送至页面')?> 页面：</th>
		<td><?=element::section($contentid)?></td>
	</tr>
	<tr>
		<th><?=element::tips('推送至专题')?> 专题：</th>
		<td><input type="hidden" value="<?=$placeid?>" class="push-to-place" name="placeid" /></td>
	</tr>
</table>

<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<tr>
		<th width="80"><?=element::tips('投票定时发布时间')?> 上线：</th>
		<td width="170"><input type="text" name="published" id="published" class="input_calendar" value="<?=$published?>" size="20"/></td>
		<th width="80">下线：</th>
		<td><input type="text" name="unpublished" id="unpublished" class="input_calendar" value="<?=$unpublished?>" size="20"/></td>
	</tr>
	<?php if(priv::aca('system', 'related')): ?>
		<tr>
			<th class="vtop">相关：</th>
			<td colspan="3"><?=element::related($contentid)?></td>
		</tr>
	<?php endif;?>
    <tr>
        <th>评论：</th>
        <td colspan="3"><label><input type="checkbox" name="allowcomment" id="allowcomment" value="1" <?php if ($allowcomment) echo 'checked';?> class="checkbox_style"/> 允许</label></td>
    </tr>
    <tr>
        <th>状态：</th>
        <td colspan="3"><?=table('status', $status, 'name')?></td>
    </tr>
</table>

<div id="field" onclick="field.expand(this.id)" class="mar_l_8 hand title" title="点击展开" style="display:none;"><span class="span_close">扩展字段</span></div>
<table id="field_c" width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
</table>
<?php $this->display('content/seo', 'system');?>
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<tr><th width="80"></th>
		<td width="60">
			<input type="submit" value="保存" class="button_style_2" style="float:left"/>
		</td><td style="color:#444;text-align:left">按Ctrl+S键保存</td>
	</tr>
</table>
</form>

<link href="<?=IMG_URL?>js/lib/colorInput/style.css" rel="stylesheet" type="text/css" />
<script src="<?=IMG_URL?>js/lib/cmstop.colorInput.js" type="text/javascript"></script>
<script type="text/javascript" src="uploader/cmstop.uploader.js"></script>
<script type="text/javascript" src="imageEditor/cmstop.imageEditor.js"></script>
<script type="text/javascript" src="js/cmstop.filemanager.js"></script>
<script type="text/javascript" src="apps/vote/js/option.js"></script>
<script type="text/javascript" src="js/related.js"></script>
<script type="text/javascript" src="apps/page/js/section.js"></script>
<script type="text/javascript" src="apps/special/js/push.js"></script>
<script src="apps/system/js/field.js" type="text/javascript" ></script>
<script type="text/javascript">
<?php foreach ($option as $k=>$r):?>
option.add('<?=$r['name'];?>', '<?=$r['votes'];?>', '<?=$r['sort'];?>', '<?=$r['optionid'];?>');
<?php endforeach;?>
// 获取自定义字段
$(function() {
	$("#catid").bind("changed", function() {
		this.value && field.get(this.value);
	});
	if($("#catid").val())
		field.get($("#catid").val());
});
</script>

<?php $this->display('footer');