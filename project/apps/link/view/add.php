<?php $this->display('header');?>
<style type="text/css">
.check-repeat-panel .icon {background: url(<?=IMG_URL?>js/lib/dropdown/bg.gif) no-repeat scroll 0 -50px transparent;	margin-right: 8px;	width: 16px;height: 20px;float: left;}
</style>
<script type="text/javascript" src="uploader/cmstop.uploader.js"></script>
<script type="text/javascript" src="imageEditor/cmstop.imageEditor.js"></script>
<script type="text/javascript" src="js/cmstop.filemanager.js"></script>
<div class="bk_8"></div>
<form name="link_add" id="link_add" method="post" action="?app=link&controller=link&action=add">
	<input type="hidden" name="modelid" id="modelid" value="<?=$modelid?>">
	<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
        <tr>
            <th width="80"><span class="c_red">*</span> 栏目：</th>
            <td><?=element::category('catid', 'catid', $catid)?>&nbsp;&nbsp;<?=element::referto()?></td>
        </tr>
		<tr>
			<th><span class="c_red">*</span> 标题：</th>
			<td>
				<?=element::title('title', $title, $color)?>
				<label><input type="checkbox" name="has_subtitle" id="has_subtitle" value="1" <?=($subtitle ? 'checked' : '')?> class="checkbox_style" onclick="show_subtitle()" /> 副题</label>
			</td>
		</tr>
		<tr id="tr_subtitle" style="display:<?=($subtitle ? 'table-row' : 'none')?>">
			<th>副题：</th>
			<td><input type="text" name="subtitle" id="subtitle" value="<?=$subtitle?>" size="100" maxlength="120" /></td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> 链接：</th>
			<td><input type="text" name="url" id="url" value="<?=$url?>" size="80" maxlength="100" /> </td>
		</tr>
		<tr>
			<th>Tags：</th>
			<td><?=element::tag('tags', $tags)?></td>
		</tr>
        <?php $this->display('more_tag', 'system');?>
		<tr>
			<th>摘要：</th>
			<td>
				<textarea name="description" id="description" maxLength="255" style="width:627px;height:40px;" class="bdr"></textarea>
			</td>
		</tr>
		<tr>
			<th>缩略图：</th>
			<td><?=element::image('thumb', '', '45')?></td>
		</tr>
        <tr>
            <th><?=element::tips('权重将决定文章在哪里显示和排序')?> 权重：</th>
            <td>
            <?=element::weight($weight, $myweight);?>
            </td>
        </tr>
        <tr>
            <th>属性：</th>
            <td><?=element::property()?></td>
        </tr>
        <tr>
            <th><?=element::tips('推送至页面')?> 页面：</th>
            <td><?=element::section()?></td>
        </tr>
        <tr>
            <th><?=element::tips('推送至专题')?> 专题：</th>
            <td><input type="hidden" value="" class="push-to-place" name="placeid" /></td>
        </tr>
	</table>
	<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
		<tr>
		    <th width="80"><?=element::tips('链接定时发布时间')?> 上线：</th>
		    <td width="170"><input type="text" name="published" id="published" class="input_calendar" value="<?=$published?>" size="20"/></td>
		    <th width="80">下线：</th>
		    <td><input type="text" name="unpublished" id="unpublished" class="input_calendar" value="<?=$unpublished?>" size="20"/></td>
		</tr>
        <tr>
            <th>状态：</th>
            <td>
                <?php
                $workflowid = table('category', $catid, 'workflowid');
                if (priv::aca($app, $app, 'publish')){
                    ?>
                    <label><input type="radio" name="status" id="status" value="6" checked="checked"/> 发布</label> &nbsp;
                    <?php
                }
                elseif ($workflowid && priv::aca($app, $app, 'approve')){
                    ?>
                    <label><input type="radio" name="status" id="status" value="3" checked="checked"/> 送审</label> &nbsp;
                    <?php }?>
                <label><input type="radio" name="status" id="status" value="1"/> 草稿</label>
            </td>
        </tr>
	</table>
	<div id="field" onclick="field.expand(this.id)" class="mar_l_8 hand title" title="点击展开" style="display:none;"><span class="span_close">扩展字段</span></div>
	<table id="field_c" width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	</table>
        <?php $this->display('content/seo', 'system');?>
	<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
		<tr>
			<th width="80"></th>
			<td width="60">
				<input type="submit" value="保存" class="button_style_2" style="float:left"/>
			</td><td style="color:#444;text-align:left">按Ctrl+S键保存</td>
		</tr>
	 </table>
</form>
<link href="<?=IMG_URL?>js/lib/colorInput/style.css" rel="stylesheet" type="text/css" />
<script src="<?=IMG_URL?>js/lib/cmstop.colorInput.js" type="text/javascript"></script>
<script type="text/javascript" src="apps/page/js/section.js"></script>
<script type="text/javascript" src="apps/special/js/push.js"></script>
<script type="text/javascript" src="js/related.js"></script>
<script src="apps/system/js/field.js" type="text/javascript" ></script>
<script type="text/javascript">
$(document).ready(function() {
	checkRepeat.init(<?=$repeatcheck?>);
});
// 获取自定义字段
$(function() {
	$("#catid").bind("changed", function() {
		this.value && field.get(this.value);
	});
	if($("#catid").val())
		field.get($("#catid").val());
});
</script>
<?php $this->display('footer', 'system');