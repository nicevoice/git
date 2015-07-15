<?php $this->display('header');?>
<form name="article_edit" id="article_edit" method="POST" action="?app=<?=$app?>&controller=<?=$app?>&action=edit">
<input type="hidden" name="contentid" id="contentid" value="<?=$contentid?>" />
<? if($status == 6): ?>
<input type="hidden" name="url" id="url" value="<?=$url?>" />
<? endif; ?>
<input type="hidden" name="modelid" id="modelid" value="<?=$modelid?>">
<input type="hidden" name="old_pagecount" id="old_pagecount" value="<?=$pagecount?>">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<tr>
		<th width="80"><span class="c_red">*</span> 栏目：</th>
                <td><?=element::category('catid', 'catid', $catid)?>
                    &nbsp;&nbsp;<?= element::referto_pro('referto',$typeid.','.$subtypeid.','.$zoneid,$cattypeid.','.$catsubtypeid.','.$catzoneid) ?></td>
	</tr>
	<tr>
		<th><span class="c_red">*</span> 标题：</th>
		<td><?=element::title('title', $title, $color)?>
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
		<th>来源：</th>
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

<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
	<tr>
		<th width="80">&nbsp;&nbsp;</th>
		<td width="540" style="padding-left:9px;position:relative"><textarea name="content" id="content" style="visibility:hidden;height:450px;width:630px;"><?=$content?></textarea></td>
		<td class="vtop"></td>
	</tr>
</table>
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
	<tr>
		<th width="80"></th>
		<td>
			<table>
				<tr>
					<td width="350">
						<label>
							<input type="checkbox" name="saveremoteimage" id="saveremoteimage" value="1" <?php if ($saveremoteimage) echo 'checked';?> class="checkbox_style"/>
							远程图片本地化
						</label>
					</td>
					<td width="200">
                        <button type="button" class="button_style_1" style="width: 80px;" onclick="get_thumb();">提取缩略图</button>
                        <span style="visibility:hidden;" id="wordUp" class="button">上传Office文档</span>
					</td>
					<td>
						<div id="multiUp" class="uploader"></div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<th>摘要：</th>
		<td><textarea name="description" id="description" maxLength="255" style="width:627px;height:40px" class="bdr"><?=$description?></textarea></td>
	</tr>
	<tr>
		<th>缩略图：</th>
		<td><?=element::image('thumb', $thumb, 60)?></td>
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

<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
	<tr>
		<th width="80"><?=element::tips('文章定时发布时间')?> 上线：</th>
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

<div id="field" onclick="field.expand(this.id)" class="mar_l_8 hand title" title="点击展开"><span class="span_close">扩展字段</span></div>
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
<script type="text/javascript" src="uploader/cmstop.uploader.js"></script>
<script type="text/javascript" src="imageEditor/cmstop.imageEditor.js"></script>
<script type="text/javascript" src="js/cmstop.filemanager.js"></script>
<script type="text/javascript" src="tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="tiny_mce/editor.js"></script>
<script type="text/javascript" src="js/related.js"></script>
<script type="text/javascript" src="apps/page/js/section.js"></script>
<script type="text/javascript" src="apps/special/js/push.js"></script>
<!--2011/05/9 加载自定义字段js-->
<script src="apps/system/js/field.js" type="text/javascript" ></script>
<script type="text/javascript">
$(function(){
	$("#multiUp").uploader({
		script : '?app=editor&controller=filesup&action=upload',
		fileDataName : 'multiUp',
		fileExt : '<?=$allow?>',
		buttonImg : 'images/multiup.gif',
		complete:function(response, data){
			response =(new Function("","return "+response))();
			if(response.state) {
				tinyMCE.activeEditor.execCommand('mceInsertContent', false, response.code);
				ct.ok(response.msg);
			} else {
				ct.error(response.msg);
			}
		}
	});
    $.ajax({type: 'GET', url: 'http://o2h.cmstop.com/cmstop.o2h.js', success: function() {
        var wordUp = document.getElementById('wordUp');
        if (! wordUp) return false;
        wordUp.style.visibility = 'visible';
        new O2H(wordUp, {
            uploadComplete:function(html){
                tinyMCE.activeEditor.execCommand('mceInsertContent', false, html);
            },
            uploadError:function(err){
                ct.error(err);
            }
        });
    }, dataType: 'script', ifModified: false, cache: true});
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
$('#content').editor(undefined, {'onchange_callback':'editCallback'});
$('input[name*=related_keywords]').val($('#tags').val());

// 获取自定义字段
$(function() {
	$("#catid").bind("changed", function() {
		this.value && field.getbycid(<?=$contentid?>, this.value);
	});
	if($("#catid").val())
		field.getbycid(<?=$contentid?>, $('#catid').val());
});

$('#title').one('change', function() {
	window.changed = true;
});
var editCallback = function() {
	window.changed = true;
};
</script>
<?php $this->display('footer');