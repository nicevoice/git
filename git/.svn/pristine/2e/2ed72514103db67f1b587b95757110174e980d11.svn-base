<?php $this->display('header');?>
<style type="text/css">
.check-repeat-panel .icon {background: url(<?=IMG_URL?>js/lib/dropdown/bg.gif) no-repeat scroll 0 -50px transparent;	margin-right: 8px;	width: 16px;height: 20px;float: left;}
</style>
<script type="text/javascript">
<?php if($openserver) {
?>
function selectFlv(){
	var d = ct.iframe({
		title:'?app=video&controller=vms&action=index&selector=1',
		width:600,
		height:519
	},
	{
		ok:function(r){
			$('#title').val(r.title);
			$('#tags').val(r.tags);
            r.tags || $('#title').trigger('change');
			$('#video').val('[ctvideo]'+r.id+'[/ctvideo]');
			$('#playtime').val(r.playtime);
			$('[name="thumb"]').val(r.pic).trigger('change');
			d.dialog('close');
		}
	});
}
<?php
}
else
{
?>
$(function(){
	$("#videoInput").uploader({
			script         : '?app=video&controller=video&action=upload',
			fileDataName   : 'ctvideo',
			fileDesc		 : '视频',
			fileExt		 : '*.swf;*.flv;*.avi;*.wmv;*.rm;*.rmvb;',
			buttonImg	 	 :'images/videoupload.gif',	
			sizeLimit      : <?=$upload_max_filesize?>,
			multi:false,
			complete:function(response,data){
				console.info("ok");
				console.info(response);
				console.info(data);
				var aidaddr=response.split('|');
				$("#aid").val(aidaddr[0]);
				aidaddr[1]=UPLOAD_URL+aidaddr[1];
				$("#video").val(aidaddr[1]);
				$("#ptline").show();
			},
			error:function(data){
				console.info("error");
				console.info(data);
				var maxsize = <?=$upload_max_filesize?>;
				var m = maxsize/(1024*1024);
				if(data.file.size>maxsize)
				ct.warn('视频大小不得超过'+m+'M');
			}
	});
});	
<?php
}
?>
</script>

<form name="video_add" id="video_add" method="POST" class="validator" action="?app=video&controller=video&action=add" enctype="multipart/form-data">
<input type="hidden" name="modelid" id="modelid" value="<?=$modelid?>">
<input type="hidden" name="status" id="status" value="<?=$status?>">
<input type="hidden" name="aid" id="aid"  value="<?=$aid?>"/>
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
		<th>Tags：</th>
		<td><?=element::tag('tags', $tags)?></td>
	</tr>
    <?php $this->display('more_tag', 'system');?>
	<tr>
		<th valign="top" ><div style="height:10px;width:100%"> </div><span class="c_red">*</span>视频：</th>
		<td>
			<table border="0" width="70%" >
				<tr>
					<td><input type="text" name="video" id="video" value="<?=$video?>" size="60" /></td>
				</tr>
				<tr>
					<td>
						<table>
							<tr>
								<?php if($openserver) { ?>
									<td width="10%" valign="bottom" height="30"> <button type="button" class="button_style_4" onclick="selectFlv()">选择视频</button></td>
								<?php }else{ ?>
									<td width="10%" valign="bottom" height="30"> <div id="videoInput" name='videoInput' class="uploader"/></div></td>
								<?php } ?>
								<?php if($ccid):?>
								<td width="9%" valign="bottom" align="left">
								<div style="margin-top:2px;width:74px;height:22px;float:left">
									<object width='72' height='22'> 
										<param name='wmode' value='transparent' />  
										<param name='allowScriptAccess' value='always' /> 
										<param name='movie' value='http://union.bokecc.com/flash/plugin/plugin_12.swf?userID=<?=$ccid?>&type=normal' /> 
										<embed src='http://union.bokecc.com/flash/plugin/plugin_12.swf?userID=<?=$ccid?>&type=normal' type='application/x-shockwave-flash' width='72' height='22' allowScriptAccess='always' wmode='transparent'></embed> 
									</object> 
									<script language="javascript">   
										function InsertCC(html){
											$("#video").val(html);
										}   
									</script> 
								</div>
								</td>
								<?php endif; ?>	
								<td valign="bottom">&nbsp;<!--<a href="javascript:video.setting()">设置CC</a>--></td>
							</tr>
						</table>
					</td>
				</tr> 
			</table>
		</td>
	</tr>
	<tr id="ptline" style="display:none">
	    <th>时长：</th>
        <td><input type="text" name="playtime" id="playtime" value="<?=$playtime?>" size="10"/> 秒</td>
	</tr>
    <tr>
        <th>来源：</th>
        <td class="c_077ac7">
            <input type="text" name="source" autocomplete="1" value="<?=$source?>" url="?app=system&controller=source&action=suggest&q=%s" size="15"/>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <label for="editor">编辑： </label>
            <input type="text" name="editor" id="editor" value="<?=$editor?>" size="15"/>
        </td>
    </tr>
	<tr>
		<th>简介：</th>
		<td><textarea name="description" id="description" cols="96" rows="4"><?=$description?></textarea></td>
	</tr>
	<tr>
		<th>缩略图：</th>
		<td><?php echo element::image('thumb','',45);?></td>
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
		<th width="80"><?=element::tips('视频定时发布时间')?> 上线：</th>
		<td width="170"><input type="text" name="published" class="input_calendar" value="<?=$published?>" size="20"/></td>
		<th width="80">下线：</th>
		<td><input type="text" name="unpublished" class="input_calendar" value="<?=$unpublished?>" size="20"/></td>
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
<script type="text/javascript" src="apps/system/js/psn.js"></script>
<script type="text/javascript" src="js/related.js"></script>
<script type="text/javascript" src="apps/page/js/section.js"></script>
<script type="text/javascript" src="apps/special/js/push.js"></script>
<script src="apps/system/js/field.js" type="text/javascript" ></script>
<script type="text/javascript">
$('#video').blur(function(){
     if($(this).val()!=''){ $('#ptline').show();}
	 else{
	   if($('#ptline').css('display')!='none'){ $('#ptline').hide()}
	 }
})
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