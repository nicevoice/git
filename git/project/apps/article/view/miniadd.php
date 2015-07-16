<?php $this->display('header');?>
<!-- 时间选择器 -->
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.datepicker.js"></script>
<link href="<?=IMG_URL?>js/lib/datepicker/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
var app = '<?=$app?>';
var controller = '<?=$controller?>';
var action = 'add';
var catid = '<?=$catid?>';
var modelid = '<?=$modelid?>';
var contentid = '<?=$contentid?>';
$.validate.setConfigs({
    xmlPath:'apps/<?=$app?>/validators/'
});
$(function(){
	var miniMainHeight = $(window).height()-40;
	$('.mini-main').height(miniMainHeight);
    ct.listenAjax();
});
</script>
<script type="text/javascript" src="apps/system/js/content.js"></script>
</head>
<style type="text/css">
	.mini-footer{ height:42px; background-color:#ccc;width:100%;}
	.mini-main{overflow-x:hidden; overflow-y:auto; position:relative;} /* fix IE7 bug */
	form{overflow:hidden;}
	body{overflow:hidden; position:relative;}
	.mini-footer {height:40px;background-image:url(css/images/ct-toolbox-bar.png);margin:0}
	.mini-footer .btn-ok{position:relative;float:right;top:2px;color:#fff;display:block;width:72px;height:32px;background:transparent url(css/images/ct-toolbox-boxico.png) no-repeat -202px top;border-width:0;}
	.mini-footer .btn-cancel{position:relative;float:right;top:6px;display:block;width:72px;height:32px;background:transparent url(css/images/ct-toolbox-boxico.png) no-repeat -202px -34px;color:#666;text-align:center;line-height:30px; margin-right:18px;}
	body, h1, p, a, img {margin:0;padding:0;}
	#related_keywords {margin-left:2px;padding-left:2px;}
</style>
<body onload="self.focus()">
	<form name="article_add" id="article_add" method="POST" action="?app=<?=$app?>&controller=<?=$app?>&action=add">
		<div class="mini-main">
			<input type="hidden" name="modelid" id="modelid" value="<?=$modelid?>">
			<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
				<tr>
					<th width="80"><span class="c_red">*</span> 栏目：</th>
					<td>  
						<?=element::category('catid', 'catid', $catid)?>
						&nbsp;&nbsp;<?=element::referto()?>
					</td>
				</tr>
				<tr>
					<th><span class="c_red">*</span> 标题：</th>
					<td><?=element::title('title', $title, $color, 80)?>
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
			<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form" >
				<tr>
					<th width="80">&nbsp;&nbsp;</th>
					<td width="540" style="padding-left:9px;">
						<textarea name="content" id="content" style="visibility:hidden;height:450px;width:630px;"><?=$content?></textarea>
					</td>
					<td class="vtop"></td>
				</tr>
			</table>
			<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form" >
				<tr>
					<th width="80">&nbsp;</th>
					<td>
						<table>
							<tr>
								<td width="483">
									<label>
										<input type="checkbox" name="saveremoteimage" id="saveremoteimage" value="1" <?php if ($saveremoteimage) echo 'checked';?> class="checkbox_style"/>
										远程图片本地化
									</label>
								</td>
								<td width="70"><button type="button" class="button_style_1" style="width: 80px;" onclick="get_thumb();">提取缩略图</button></td>
								<td>
									<div id="multiUp" class="uploader"></div>
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
					<td><?=element::weight($weight, $myweight);?></td>
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

			<div id="field" onclick="field.expand(this.id)" class="mar_l_8 hand title" title="点击展开"><span class="span_close">扩展字段</span></div>
			<table id="field_c" width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
			</table>
		</div>
		<div class="cmstop-message-box-bd-fd mini-footer">
			<a href="javascript:_close('取消发布');" title="" class="btn-cancel">取消</a>
			<input type="submit" name="" class="btn-ok" value="保存" />
		</div>
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
<script src="apps/article/js/field.js" type="text/javascript" ></script>

<?php
foreach(explode('|', UPLOAD_FILE_EXTS) as $exts) {
	$allow .=  '*.'.$exts.';';
}
?>
<script type="text/javascript">
$(function(){
	var ed = null;
	$("#multiUp").uploader({
			script : '?app=editor&controller=filesup&action=upload',
			fileDataName : 'multiUp',
			fileExt : '<?=$allow?>',
			buttonImg : 'images/multiup.gif',
			complete:function(response, data){
				response =(new Function("","return "+response))();
				if(response.state) {
					ed || (ed = tinyMCE.get('content'));
					ed.execCommand('mceInsertContent', false, response.code);
					ct.ok(response.msg);
				} else {
					ct.error(response.msg);
				}
			}
	});
	if ($('#title').val())
	{
		$.post('?app=system&controller=tag&action=get_tags', $('form#article_add').serialize(), function(response){
			if (response.state)
			{
				$('#tags').val(response.data);
			}
		}, 'json');
	}
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

$('#article_add').ajaxForm(function(json) {
	if (json.state){
		var text;
		if (typeof(json.url)!='undefined') {
			text	= '恭喜,添加成功.查看地址<br><a target="_BLANK" href="' + json.url + '">' + json.url + '</a>';
		} else {
			text	= '恭喜,添加成功.';
		}
		ct.confirm(text, function() {
			_close('发布成功');
		},function() {
			_close('发布成功');
		});
	}else{
		content.error(json);
	}
}, null, null);

$(function() {
	$("#catid").change(function() {
		this.value && field.get(this.value);
	});
	$("div.cs_sb").find('input').keyup(function(){
		$('#catid').val() && field.get($('#catid').val());
	});
	$('div.cs_mitem').click(function(){
		$('#catid').val() && field.get($('#catid').val());
	});
	field.get($("#catid").val());
});

function _close(s) {
	if (location.hash == "#ie") {
		window.close();	
	}
	try {
		parent.location.hash = '#close';
	} catch (e) {}
	document.body.innerHTML	= '<div class="box_border"  id="wrong" style="margin:100px auto 0;width:420px; padding:20px 0;border:1px solid #FDBD77; background:#FFFDD7 url(<?=IMG_URL?>images/bg_wrong.gif) no-repeat 10px center;" ><div class="content"><h1 style="padding-left:100px; text-align:left;font-size:14px; margin-top:12px;color:#f30;">'+s+'</h1><p style="padding-left:100px; text-align:left;font-size:14px; margin-top:12px; font-size:12px; line-height:150%;padding:10px 10px 10px 100px;line-height:18px;">点击右上角关闭按钮,关闭本页面</div></div>';
	document.body.style.textAlign = 'center';
}
$(document).ready(function() {
	checkRepeat.init(<?=$repeatcheck?>);
});
</script>
<?php $this->display('footer');