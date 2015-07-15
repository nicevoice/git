<?php $this->display('header','article');?>
<form name="contribution_add" id="contribution_add" method="POST" action="?app=contribution&controller=index&action=add">
	<input type="hidden" name="modelid" id="modelid" value="1">
	<input type="hidden" name="iscontribute" value="1">
	<input type="hidden" name="contributionid" value="<?php echo $contributionid;?>">
	<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
		<tr>
			<th width="80"><span class="c_red">*</span> 栏目：</th>
			<td>  
				<?=element::category('catid', 'catid', $catid)?>
				&nbsp;&nbsp;<?=element::referto()?>
			</td>
		</tr>
		<tr>
			<th width="60"><span class="c_red">*</span> 标题：</th>
			<td><?=element::title('title', $title, $color, 80)?>
			<label><input type="checkbox" name="has_subtitle" id="has_subtitle" value="1" <?=($subtitle ? 'checked' : '')?> class="checkbox_style" onclick="show_subtitle()" /> 副题</label>
			</td>
		</tr>
		<tr id="tr_subtitle" style="display:<?=($subtitle ? 'table-row' : 'none')?>">
			<th width="60">副题：</th>
			<td><input type="text" name="subtitle" id="subtitle" value="<?=$subtitle?>" size="100" maxlength="120" /></td>
		</tr>
		<tr>
			<th>Tags：</th>
			<td><?=element::tag('tags', $tags)?></td>
		</tr>
		<tr>
			<th width="60">来源：</th>
			<td class="c_077ac7">
				<input type="text" name="source" autocomplete="1" value="<?=$source?>" url="?app=system&controller=source&action=suggest&q=%s" size="15"/>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<label for="author">作者： </label>
				<input type="text" name="author" autocomplete="1" value="<?=$author?>" url="?app=space&controller=index&action=suggest&q=%s" size="15"/>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<label for="editor">编辑： </label>
				<input type="text" name="editor" value="<?=$editor?>" size="15"/>
			</td>
		</tr>
	</table>
        
	<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form" >
		<tr>
			<th width="80">&nbsp;&nbsp;</th>
			<td width="540" style="padding-left:9px;position:relative">
				<textarea name="content" id="content" style="visibility:hidden;height:450px;width:630px;"><?=$content?></textarea>
			</td>
			<td class="vtop"></td>
		</tr>
	</table>
        
	<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form" >
		<tr>
			<th width="80"></th>
			<td>
				<table>
					<tr>
						<td width="483">
							<label>
								<input type="checkbox" name="saveremoteimage" id="saveremoteimage" value="1" <?php if ($saveremoteimage) echo 'checked';?> class="checkbox_style"/>
								远程图片本地化
							</label>
						</td>
						<td width="70"><a href="javascript:;" onclick="get_thumb();">提取缩略图</a></td>
						<td>
							<div id="multiUp" name="multiUp"></div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<th>摘要：</th>
			<td>
				<textarea name="description" id="description" maxLength="255" style="width:627px;height:40px;" class="bdr"><?=$description?></textarea>
			</td>
		</tr>
		<tr>
			<th>缩略图：</th>
			<td><?=element::image('thumb', '', 60)?></td>
		</tr>
		<tr>
			<th>属性：</th>
			<td><?=element::property()?></td>
		</tr>
		<tr>
			<th><?=element::tips('权重将决定文章在哪里显示和排序')?> 权重：</th>
			<td><?=element::weight();?></td>
		</tr>
		<tr>
			<th><?=element::tips('可将文章推送至指定页面的区块，给页面编辑提供参考')?> 推荐：</th>
			<td><?=element::section()?></td>
		</tr>
		<tr>
			<th><?=element::tips('推送到专题')?> 专题：</th>
			<td><input type="hidden" value="" class="push-to-place" name="placeid" /></td>
		</tr>
		<tr>
			<th>状态：</th>
			<td>
<?php 
$workflowid = table('category', $catid, 'workflowid');
if ($_roleid < 3 || !$workflowid){
?>
                <label><input type="radio" name="status" id="status" value="6" <?php if ($status == 6) echo 'checked';?> /> 发布</label> &nbsp;
<?php 
}
if ($workflowid){
?>
                <label><input type="radio" name="status" id="status" value="3" <?php if ($_roleid > 2) echo 'checked';?> /> 送审</label> &nbsp;
<?php }?>
                <label><input type="radio" name="status" id="status" value="1" <?php if ($status == 1) echo 'checked';?> /> 草稿</label>
			</td>
		</tr>
	</table>

	<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
		<tr>
			<th width="80">评论：</th>
			<td width="170"><label><input type="checkbox" name="allowcomment" id="allowcomment" value="1" <?php if ($allowcomment) echo 'checked';?> class="checkbox_style"/> 允许</label></td>
		</tr>
		<tr>
			<th><?=element::tips('文章定时发布时间')?> 上线：</th>
			<td><input type="text" name="published" id="published" class="input_calendar" value="<?=$published?>" size="20"/></td>
			<th>下线：</th>
			<td><input type="text" name="unpublished" id="unpublished" class="input_calendar" value="<?=$unpublished?>" size="20"/></td>
		</tr>
		<tr>
			<th class="vtop">相关：</th>
			<td colspan="3"><?=element::related()?></td>
		</tr>
	</table>

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

<script type="text/javascript" src="uploader/cmstop.uploader.js"></script>
<script type="text/javascript" src="imageEditor/cmstop.imageEditor.js"></script>
<script type="text/javascript" src="js/cmstop.filemanager.js"></script>
<script src="tiny_mce/tiny_mce.js" type="text/javascript"></script>
<script src="tiny_mce/editor.js" type="text/javascript"></script>
<script src="js/related.js" type="text/javascript"></script>
<script src="apps/page/js/section.js" type="text/javascript"></script>
<script src="apps/special/js/push.js" type="text/javascript"></script>
<script type="text/javascript">

$(function(){
	var ed = null;
	$("#multiUp").uploader({
			script         : '?app=editor&controller=filesup&action=upload',
			fileDataName   : 'multiUp',
			buttonImg	 	 : 'images/multiup.gif',
			complete:function(response,data){	 
				ed || (ed = tinyMCE.get('content'));
				ed.execCommand('mceInsertContent',false,response);
			}
	});
});

function get_thumb(){
	var content = tinyMCE.activeEditor.getContent(),
	reg = /<img\s+[^>]*src\s*=\s*(["\']?)([^>"\']+)\1[^>]*[\/]?>/img,imgs;
	while(imgs = reg.exec(content)){
		if(imgs[2].indexOf('/images/ext/') != -1) continue;
		$.post('?app=article&controller=article&action=thumb',{url:imgs[2]},function(url){
			$('input[name="thumb"]:text').val(url).nextAll('button:last').show();
		});
		break;
	}
}
$('#content').editor();
</script>
<?php $this->display('footer','article');?>