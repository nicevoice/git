<?php $this->display('header');?>
<style type="text/css">
.check-repeat-panel .icon {background: url(<?=IMG_URL?>js/lib/dropdown/bg.gif) no-repeat scroll 0 -50px transparent;	margin-right: 8px;	width: 16px;height: 20px;float: left;}
</style>
<form name="interview_add" id="interview_add" method="POST" action="?app=interview&controller=interview&action=add">
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
            <th><span class="c_red">*</span> 期号：</th>
            <td><input type="text" name="number" id="number" value="" size="5" maxlength="5" /></td>
        </tr>
        <tr>
            <th><span class="c_red">*</span> Tags：</th>
            <td><?=element::tag('tags', $tags)?></td>
        </tr>
        <?php $this->display('more_tag', 'system');?>
        <tr>
            <th>缩略图：</th>
            <td><?php echo element::image('thumb', $thumb, 45);?></td>
        </tr>
        <tr>
            <th>访谈状态：</th>
            <td><label><input type="radio" name="state" id="state" value="0" <?php if ($state == 0) echo 'checked';?>/> 未开始</label>&emsp;
                <label><input type="radio" name="state" id="state" value="1" <?php if ($state == 1) echo 'checked';?>/> 进行中</label>&emsp;
                <label><input type="radio" name="state" id="state" value="2" <?php if ($state == 2) echo 'checked';?>/> 已结束</label>
            </td>
        </tr>
        <tr>
            <th><span class="c_red">*</span> 访谈介绍：</th>
            <td><textarea name="description" id="description" maxLength="255" style="width:600px;height:80px" class="bdr"><?=$description?></textarea> </td>
        </tr>
        <tr>
            <th><span class="c_red">*</span> 访谈方式：</th>
            <td><input type="radio" name="mode" id="mode" value="text"  checked="checked" onclick="$('#mode_text').show();$('#mode_video').hide();"/> 图文 <input type="radio" name="mode" id="mode" value="video" onclick="$('#mode_text').hide();$('#mode_video').show();"/> 视频</td>
        </tr>
        <tr id="mode_text">
            <th><span class="c_red">*</span> 访谈图片：</th>
            <td><input type="text" name="photo" id="photo" value="<?=$photo?>" upbtn="#photo_upbtn" filebtn="#photo_filebtn" editbtn="#photo_editbtn" size="45" readonly="readonly"/></td>
        </tr>
        <tr id="mode_video" style="display: none">
            <th><span class="c_red">*</span> 访谈视频：</th>
            <td><input type="text" name="video" value="<?=$video?>" size="60" maxlength="200"/></td>
        </tr>
        <tr>
            <th><span class="c_red">*</span> 访谈时间：</th>
            <td><input type="text" name="starttime" id="starttime" class="input_calendar" value="<?=$starttime?>" size="20"/> ~ <input type="text" name="endtime" id="endtime" class="input_calendar" value="<?=$endtime?>" size="20"/></td>
        </tr>
        <tr>
            <th><span class="c_red">*</span> 访谈地点：</th>
            <td><input type="text" name="address" id="address" value="<?=$address?>" size="60" maxlength="100"/></td>
        </tr>
        <tr>
            <th><span class="c_red">*</span> 主持人：</th>
            <td><input type="text" name="compere" id="compere" value="<?=$compere?>" size="20" maxlength="20"/></td>
        </tr>
        <tr>
            <th><span class="c_red">*</span> 嘉宾：</th>
            <td id="guests"></td>
        </tr>
        <tr>
            <th>&nbsp;</th>
            <td><input type="button" name="button" id="button" value="增加嘉宾" class="button_style_1" onclick="guest.add()"/></td>
        </tr>
        <tr>
            <th>编辑：</th>
            <td><input type="text" name="editor" id="editor" value="<?=$editor?>" size="12"/> </td>
        </tr>
		<tr>
			<th>自定义模板：</th>
			<td><?=element::template('template', 'template', '', 40);?><td>
		<tr>
        <tr>
            <th>网友发言：</th>
			<td><label><input type="checkbox" name="allowchat" id="allowchat" value="1" <?=$allowchat ? 'checked' : ''?>/> 允许</label></td> 
		</tr>
        <tr>
            <th>游客发言：</th>
			<td><label><input type="checkbox" name="visitorchat" id="visitorchat" value="1" <?=$visitorchat ? 'checked' : ''?>/> 允许</label></td> 
		</tr>
		<tr>
			<th>属性：</th>
			<td><?=element::property()?></td>
		</tr>
		<tr>
            <th>发言审核：</th>
            <td><label><input type="checkbox" name="ischeck" id="ischeck" value="1" <?=$ischeck ? 'checked' : ''?>/> 是</label></td>
        </tr>
        <tr>
	        <th><?=element::tips('设置网友发言时间段，留空则不限制')?>发言时段：</th>
	        <td><input type="text" name="startchat" id="startchat" class="input_calendar" value="<?=$startchat?>" size="20"/> ~ <input type="text" name="endchat" id="endchat" class="input_calendar" value="<?=$endchat?>" size="20"/></td>
        </tr>
        <tr>
            <th><?=element::tips('权重将决定文章在哪里显示和排序')?> 权重：</th>
            <td>
                <?=element::weight($weight, $myweight);?>
            </td>
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

    <?php
    $catid && $allowcomment = table('category', $catid, 'allowcomment');
    ?>
    <table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
		<tr>
			<th width="80"><?=element::tips('访谈定时发布时间')?> 上线：</th>
			<td width="170"><input type="text" name="published" id="published" class="input_calendar" value="<?=$published?>" size="20"/></td>
			<th width="80">下线：</th>
			<td><input type="text" name="unpublished" id="unpublished" class="input_calendar" value="<?=$unpublished?>" size="20"/></td>
		</tr>
        <?php if(priv::aca('system', 'related')): ?>
		<tr>
			<th class="vtop">相关：</th>
			<td colspan="3"><?=element::related()?></td>
		</tr>
        <?php endif;?>
        <tr>
            <th>评论：</th>
            <td colspan="3"><label><input type="checkbox" name="allowcomment" id="allowcomment" value="1" <?php if ($allowcomment) echo 'checked';?> class="checkbox_style"/> 允许</label></td>
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
            <th width="80"></th>
            <td width="60">
				<input type="submit" value="保存" class="button_style_2" style="float:left"/>
			</td><td style="color:#444;text-align:left">按Ctrl+S键保存</td>
        </tr>
    </table>
</form>
<link href="<?=IMG_URL?>js/lib/autocomplete/style.css" rel="stylesheet" type="text/css" />
<link href="<?=IMG_URL?>js/lib/colorInput/style.css" rel="stylesheet" type="text/css" />
<script src="<?=IMG_URL?>js/lib/cmstop.autocomplete.js" type="text/javascript"></script>
<script src="<?=IMG_URL?>js/lib/cmstop.colorInput.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.colorPicker.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.lightbox.js"></script>
<script type="text/javascript" src="js/related.js"></script>
<script type="text/javascript" src="apps/page/js/section.js"></script>
<script type="text/javascript" src="apps/special/js/push.js"></script>
<script type="text/javascript" src="apps/system/js/field.js" ></script>
<link rel="stylesheet" type="text/css" href="css/imagesbox.css" media="screen" />

<script type="text/javascript">
var unload_alert = true;
window.onbeforeunload = onBeforeUnload;
function onBeforeUnload()
{
	if (unload_alert && $('#title').val() != '')
	{
		return '访谈尚未保存，您确认放弃发布吗？';
	}
}

content.tags('<?=$controller?>_<?=$action?>');

$('input.input_calendar').DatePicker({'format':'yyyy-MM-dd HH:mm:ss'});

$('.tips').attrTips('tips', 'tips_green', 120, 'top');
$("#photo").imageInput();
guest.add();

$("#catid option[childids='1']").attr("disabled", "disabled");
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