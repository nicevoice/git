<?php $this->display('header');?>
<style type="text/css">
.check-repeat-panel .icon {background: url(<?=IMG_URL?>js/lib/dropdown/bg.gif) no-repeat scroll 0 -50px transparent;	margin-right: 8px;	width: 16px;height: 20px;float: left;}
.table_info th {
    text-align: center;
}.table_info td {
     border: 1px solid #D0E6EC !important;
     border-width: 1px 0 0 1px;
     text-align: center;
 }
</style>
<form name="exam_add" id="exam_add" method="post" action="?app=exam&controller=exam&action=add">
	<input type="hidden" name="modelid" id="modelid" value="<?=$modelid?>">
	<table width="98%" border="0" cellspacing="0" cellpadding="0" class="mar_l_8 mar_t_10 table_form">
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
		    <th>简介：</th>
		    <td><textarea name="description" id="description" cols="94" rows="4"><?=$description?></textarea></td>
	    </tr>
	    <tr>
		    <th>Tags：</th>
		    <td><?=element::tag('tags', $tags)?></td>
	    </tr>
	    <tr>
		    <th>缩略图：</th>
		    <td><?=element::image('thumb', '', '45')?></td>
	    </tr>
        <?php /*<tr>
		    <th>自定义模板：</th>
		    <td><?=element::template('template', 'template', '', 40);?><td>
	    <tr>
	    <tr>
	        <th><?=element::tips('邮件正文的模板放在 ' . config('template', 'name') . '/exam/mail.html ，如遇到邮件正文无内容的情况，请检查该模板是否存在')?> 接收人：</th>
	        <td><input type="text" name="mailto" id="mailto"  value="<?=$mailto?>" size="20"/></td>
        </tr>*/?>


        <tr>
            <th width="70"><span class="c_red">*</span> 类型：</th>
            <td><table width="580" border="0" cellspacing="0" cellpadding="0" class="table_info">
                    <thead>
                    <tr>
                        <th><div class="move_cursor"></div></th>
                        <th width="160">别名</th>
                        <th width="60">数量</th>
                        <th width="60">操作</th>
                    </tr>
                    </thead>
                    <tbody id="qtype" class="ui-sortable" unselectable="on">
                    </tbody>
                    <tr> <td colspan="5"><div class="mar_l_8 mar_5"><input name="add_option_btn" type="button" value="增加分类" class="hand button_style" onclick="automatic.add_qtype('qtype',<?=$pro_ids['qtypeid']?>)"></div></tr>
                </table>
            </td>
        </tr>
	    <!--<tr>
		    <th>时间限制：</th>
		    <td><input id="starttime" name="starttime" type="text" class="input_calendar" value="<?/*=$starttime*/?>" size="20"/>&nbsp;&nbsp;&nbsp;至&nbsp;&nbsp;&nbsp;<input id="endtime" name="endtime" type="text" class="input_calendar" value="<?/*=$endtime*/?>" size="20"/></td>
	    </tr>-->
        <tr>
		    <th>时间限制：</th>
		    <td><input id="examtime" name="examtime" type="text" class="" value="60" size="20"/>分钟</td>
	    </tr>
	    <tr>
		    <th>人数限制：</th>
		    <td><input type="text" id="maxanswers" name="maxanswers" value="<?=$maxanswers?>" size="4" /> <span id="maxanswers_msg"></span></td>
	    </tr>
	    <tr>
		    <th>防刷限制：</th>
		    <td>同IP <input type="text" id="minhours" name="minhours" value="<?=$minhours?>" size="2" />小时内不得重复提交  <?=element::tips('0或为空时为不限制')?> </td>
	    </tr>
	    <tr>
		    <th>仅会员参与：</th>
            <td><input type="checkbox" name="checklogined" id="checklogined" value="1" <?php if ($checklogined == 1) echo 'checked';?>/> 是</td>
        </tr>
	    <tr>
		    <th>属性：</th>
		    <td><?=element::property()?></td>
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
		    <th width="80"><?=element::tips('题库定时发布时间')?> 上线：</th>
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
<link href="<?=IMG_URL?>js/lib/colorInput/style.css" rel="stylesheet" type="text/css" />
<script src="<?=IMG_URL?>js/lib/cmstop.colorInput.js" type="text/javascript"></script>
<script type="text/javascript" src="uploader/cmstop.uploader.js"></script>
<script type="text/javascript" src="imageEditor/cmstop.imageEditor.js"></script>
<script type="text/javascript" src="js/cmstop.filemanager.js"></script>
<script type="text/javascript" src="js/related.js"></script>
<script type="text/javascript" src="apps/page/js/section.js"></script>
<script type="text/javascript" src="apps/special/js/push.js"></script>
<script type="text/javascript" src="apps/system/js/field.js" ></script>
<script type="text/javascript" src="apps/exam/js/automatic_exam.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/dropdown/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.dropdown.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.pagination.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/list/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.list.js"></script>
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