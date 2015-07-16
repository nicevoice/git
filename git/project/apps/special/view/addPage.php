<?php if (! $isajax):?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>新建专题页面</title>
<link rel="stylesheet" type="text/css" href="css/admin.css"/>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/config.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/cmstop.js"></script>
<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/jquery-ui/dialog.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.ui.js"></script>
<script type="text/javascript" src="uploader/cmstop.uploader.js"></script>
<!-- 时间选择器 -->
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.datepicker.js"></script>
<link href="<?=IMG_URL?>js/lib/datepicker/style.css" rel="stylesheet" type="text/css" />

<style type="text/css">
.addpage-wrapper{width:960px; margin:50px auto 0; border:5px solid #D0E6EC; padding:0 0 5px;background:url(apps/special/images/bg-right.gif) repeat-y 720px 0; overflow:hidden;}
.addpage-wrapper .left {width:100%;float:left;}
.addpage-wrapper .inner {margin-right: 250px;}
.addpage-wrapper .right {width:230px;margin-left:-230px;float:left;}
.addpage-wrapper .right label{margin-top:10px; line-height:18px;}
.addpage-wrapper .item-option {	margin:15px 0 5px 5px;position:relative;border:1px solid #CBE2E9;}
.addpage-wrapper .item-option h2 {font-size:12px;color:#fff;background:#95D7EA;position:absolute;top:-10px;left:5px;padding:0 10px;margin:0;height:20px;line-height:20px;z-index:1;}
.addpage-wrapper .item-list {overflow-x:hidden;overflow-y:auto;	height:100px;padding:10px 0 5px;}
.addpage-wrapper .item {display:inline-block;border:1px solid #E4F0F3;cursor:pointer;margin:2px 5px;padding:2px;text-align:center;width:80px;overflow:hidden;}
.addpage-wrapper .uploader {display:inline-block;border:1px solid #E4F0F3;height:80px;margin:5px;padding:2px;text-align:center;width:80px;z-index:0;}
.addpage-wrapper .uploader:hover{background-color:#fdcd99;}
.addpage-wrapper .item {display:inline-block;border:1px solid #E4F0F3;cursor:pointer;margin:2px 5px;padding:2px;text-align:center;width:80px;overflow:hidden;}
.addpage-wrapper .item.active {	background-color:#fdcd99;}
.addpage-wrapper .item img {height:64px;width:64px;display:block;margin:0 auto;}
.addpage-wrapper .item span {overflow:hidden;display:block;height:16px;}
.addpage-wrapper label {display:block;margin:3px 0;	color:#077AC7;}
.pad-5{ padding:5px 5px 0;}
</style>
</head>
<body>
<?php endif;?>
<form>
<div class="addpage-wrapper">
	<div class="left">
		<div class="inner">
			<div class="item-option">
	            <h2>空白页创建</h2>
	            <div class="item" style="margin-top:15px"><img src="images/blankpage.gif" /><span>空白页</span></div>
			</div>
			<div class="item-option">
				<h2>从方案创建</h2>
				<div class="item-list">
					<?php foreach ($scheme as $s):?>
					<div class="item" name="scheme" title="<?=$s['name']?>" value="<?=$s['entry']?>"><img src="<?=$s['thumb']?>" /><span><?=$s['name']?></span></div>
					<?php endforeach;?>
				</div>
			</div>
			<div class="item-option">
				<h2>从模板创建</h2>
				<div class="item-list">
					<div class="uploader">上传模板</div>
					<?php foreach ($template as $t):?>
					<div class="item" name="template" title="<?=$s['name']?>" value="<?=$t['entry']?>"><img src="<?=$t['thumb']?>" /><span><?=$t['name']?></span></div>
					<?php endforeach;?>
				</div>
			</div>
		</div>
	</div>
	<div class="right">
		<label><span class="c_red">*</span> 名称：<br />
			<input type="text" name="name" value="<?=$name?>" />
		</label>
		<label><span class="c_red">*</span> 文件名：<br />
			<input type="text" name="file" value="<?=$file?>" /> <?=SHTML?>
		</label>
		<label>标题：<br />
			<input type="text" name="title" value="" />
		</label>
		<label>关键字：<br />
			<input type="text" name="keywords" value="" />
		</label>
		<label>描述：<br />
			<textarea name="description"></textarea>
		</label>
		<label>更新频率：<br />
			<input type="text" name="frequency" value="3600" /> 秒
		</label>
		<?php if (! $isajax):?>
		<div style="margin-top:5px;"><input class="button_style_4" type="submit" value="创建页面" /></div>
		<?php endif;?>
	</div>
    <div class="clear"></div>
</div>
</form>

<?php if (! $isajax):?>
<script type="text/javascript">
    var initScheme = function() {
        var scheme = $(this),
            title = scheme.attr('title'),
            value = scheme.attr('value');
        scheme.append($('<a href="">删除</a>').click(function(){
			ct.confirm('此操作不可恢复，确定要删除方案 <b style="color:red">'+title+'</b> ？',function(){
				$.getJSON('?app=special&controller=online&action=delScheme&scheme='+encodeURIComponent(value),
				function(json){
					if (json.state) {
						scheme.remove();
						active = null;
                        ct.ok('删除成功');
					} else {
						ct.error(json.error);
					}
				});
			});
			return false;
		}));
    };
	var form = $('form').submit(function(e){
		e.stopPropagation();
		e.preventDefault();
		var data = form.serializeArray();
		var a = form.find('.item.active[name]');
		if (a.length) {
			data.push({name:a.attr('name'), value:a.attr('value')});
		}
		$.ajax({
    		dataType:'json',
    		url:'?app=special&controller=online&action=addPage&contentid=<?=$_REQUEST['contentid']?>',
    		type:'post',
    		data:$.param(data),
    		success:function(json){
    			if (json.state) {
    				var url = '?app=special&controller=online&action=design&contentid=<?=$_REQUEST['contentid']?>&pageid='+json.data.pageid;
					ct.timer('保存成功, %s秒后进入设计模式，<a href="javascript:;" class="clause">立即进入</a>', 3, 'success',
					function(){
						window.location = url;
					});
				} else {
					ct.error(json.error);
				}
    		},
    		error:function(){
    			ct.error('请求异常');
    		}
    	});
	});
	var up = form.find('.uploader');
	var active = null;
	var click = function(){
		active && $.className.remove(active, 'active');
		active = this;
		$.className.add(active, 'active');
	};
	var initT = function(){
		var t = $(this),
			value = t.attr('value'),
			title = t.attr('title');
		t.append($('<a href="">删除</a>').click(function(){
			ct.confirm('此操作不可恢复，且会影响已使用模板<b style="color:red">"'+title+'"</b>的页面，确定要删除？',function(){
				$.getJSON('?app=special&controller=online&action=delTemplate&template='+encodeURIComponent(value),
				function(json){
					if (json.state) {
						t.remove();
						active = null;
					} else {
						ct.error(json.error);
					}
				});
			});
			return false;
		}));
	};
	var items = form.find('.item').click(click);
	items.eq(0).click();
    items.filter('[name="scheme"]').each(initScheme);
	items.filter('[name="template"]').each(initT);
	up.uploader({
		fileExt:'*.zip',
		fileDesc:'ZIP文件',
		jsonType : 1,
		script:'?app=special&controller=online&action=addTemplate&contentid=<?=$_REQUEST['contentid']?>',
		multi:false,
		complete:function(json, data){
			if (json) {
				if (json.state) {
					$('<div class="item" name="template" title="'+json.data.name+'" value="'+json.data.entry+'"><img src="'+json.data.thumb+'" /><span>'+json.data.name+'</span></div>')
					.insertAfter(up).click(click).each(initT).click();
				} else {
					ct.error(json.error);
				}
			} else {
				ct.error('上传失败!');
			}
		},
		error:function(data){
			ct.warn(data.file.name+'：上传失败，'+data.error.type+':'+data.error.info);
		}
	});
</script>
</body>
</html>
<?php endif;?>