<?php $this->display('header');?>
<style type="text/css">
.check-repeat-panel .icon {background: url(<?=IMG_URL?>js/lib/dropdown/bg.gif) no-repeat scroll 0 -50px transparent;	margin-right: 8px;	width: 16px;height: 20px;float: left;}
</style>
<form name="special_add" id="special_add" method="POST" action="?app=special&controller=special&action=add">
<input type="hidden" name="modelid" value="<?=$modelid?>"/>
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<tr>
		<th width="135"><span class="c_red">*</span> 栏目：</th>
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
		<th>Tags：</th>
		<td><?=element::tag('tags', $tags)?></td>
	</tr>
    <?php $this->display('more_tag', 'system');?>
	<tr>
		<th>摘要：</th>
		<td><textarea name="description" maxLength="255" style="width:440px;height:120px"  class="bdr"><?=$description?></textarea></td>
	</tr>
	<tr>
		<th>缩略图：</th>
		<td><?php echo element::image('thumb','',45,false);?></td>
	</tr>
	<tr>
		<th><span class="c_red">*</span> 网址：</th>
		<td>
			<div id="template_input">
			<?php echo element::psn('path', 'path', '');?>
			</div>
		</td>
	</tr>
    <tr>
        <th><?=element::tips('专题的内容模块可以有更多链接，链接向独立的页面展示该模块的更多内容列表，每个专题可以独立设置一个模板文件')?> 列表页模板：</th>
		<td><?=element::template('morelist_template', 'morelist_template', 'special/morelist.html', 50)?></td>
    </tr>
    <tr>
        <th><?=element::tips('更多内容列表每页显示多少条信息')?> 列表页每页信息数：</th>
        <td><input type="text" name="morelist_pagesize" size="8" value="50" /></td>
    </tr>
    <tr>
        <th><?=element::tips('更多内容列表最多展示多少页内容，0 为不限制；超出限制页的内容将不可见')?> 列表最多显示：</th>
        <td><input type="text" name="morelist_maxpage" size="8" value="100" /> 页</td>
    </tr>
	<tr>
		<th>属性：</th>
		<td><?=element::property()?></td>
	</tr>
	<tr>
		<th><?=element::tips('权重将决定专题在哪里显示和排序')?> 权重：</th>
		<td>
			<?=element::weight($weight, $myweight);?>
		</td>
	</tr>
	<tr>
		<th><?=element::tips('可将文章推送至指定页面的区块，给页面编辑提供参考')?> <span style="color:#077ac7">推荐：</span></th>
		<td><?=element::section()?></td>
	</tr>
</table>

<?php
$catid && $allowcomment = table('category', $catid, 'allowcomment');
?>
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<tr>
		<th width="135"><?=element::tips('文章定时发布时间')?> 上线：</th>
		<td width="170"><input type="text" name="published" class="input_calendar" value="" size="20"/></td>
		<th width="80">下线：</th>
		<td><input type="text" name="unpublished" class="input_calendar" value="" size="20"/></td>
	</tr>
	<?php if(priv::aca('system', 'related')): ?>
	<tr>
		<th class="vtop">相关：</th>
		<td colspan="3"><?=element::related()?></td>
	</tr>
	<?php endif;?>
    <tr>
        <th>评论：</th>
        <td colspan="3"><label><input type="checkbox" name="allowcomment" value="1" <?php if ($allowcomment) echo 'checked';?> class="checkbox_style"/> 允许</label></td>
    </tr>
    <tr>
        <th>状态：</th>
        <td colspan="3">
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
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
    <tr>
        <th width="135">&nbsp;</th>
        <td>
            <input id="submit_button" type="submit" value="下一步" class="button_style_2"/>
            <span id="submit_ok_tips" class="c_gray">设计界面将在弹出窗口或新标签页中打开，如果浏览器提示被拦截，请设置为总是允许弹出</span>
		</td>
	</tr>
</table>
</form>
<script type="text/javascript" src="apps/system/js/content.js"></script>
<script type="text/javascript" src="apps/system/js/psn.js"></script>
<script type="text/javascript" src="js/related.js"></script>
<script type="text/javascript" src="apps/page/js/section.js"></script>
<script type="text/javascript" src="apps/system/js/field.js" ></script>

<script type="text/javascript">
content.success = function(json){
	if (json.state) {
        $('#submit_button').attr('disabled', 'disabled');
        $('#submit_ok_tips').html('专题已保存成功，如未能成功进入设计界面，<a href="' + json.redirect + '" target="_blank">请点击此处开始设计专题页面</a>');
		content.unload_alert = 0;
		ct.timer('保存成功, %s秒后跳转，<a href="javascript:;" class="clause">立即进入</a>', 3, 'success',
		function(){
			window.open(json.redirect, '_blank');
		});
	} else {
		ct.tips(json.error,'error');
	}
};
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
<?php $this->display('footer');