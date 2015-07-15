<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>添加图片</title>
    <link href="<?=ADMIN_URL?>css/admin.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.js"></script>
	<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/jquery-ui/dialog.css" />
	<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.ui.js"></script>
	<script type="text/javascript" src="<?php echo IMG_URL?>js/config.js"></script>
	<script type="text/javascript" src="<?php echo IMG_URL?>js/cmstop.js"></script>
	<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.dialog.js"></script>
	<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.cookie.js"></script>		

	<script type="text/javascript" src="uploader/cmstop.uploader.js"></script>

    <script type="text/javascript" src="js/cmstop.imageList.js"></script>
    <link rel="stylesheet" type="text/css" href="js/imageList/style.css" />

	<style type="text/css">
        html,
        body {
            height: 100%;
        }
        .button {
            display: inline-block;
        }
        .btn_area {
            border: 1px solid #fff;
            border-top-width: 0;
            padding: 8px 2px 8px 0;
            background: url(<?=IMG_URL?>js/lib/jquery-ui/images/dialog.png) repeat-x 0 -60px;
            margin-top: 5px;
            text-align: right;
        }
        .panel-action,
        .panel-content {
            padding: 0 10px;
        }
	</style>
</head>
<body>
<div class="bk_8" style="height: 5px;"></div>
<div class="tag_1">
    <ul class="tag_list">
        <li class="s_3"><span><a href="javascript:;">本地上传</a></span></li>
        <li id="library_link"><span><a href="javascript:;">资源库</a></span></li>
    </ul>
</div>
<form id="image_thumb_form" method="post" action="?app=system&controller=image&action=set_pic">
    <div class="panel-action" style="margin-top: 10px;">
        <span style="float:right;">
			<?php if($use_watermark):?>
            <label for="watermark">水印方案：</label>
            <select id="watermark" name="watermark">
                <option value="">无水印</option>
                <?php foreach($watermark as $item):?>
                <option value="<?=$item['id']?>"<?php if($item['id']==$default_watermark):?> selected="selected"<?php endif;?>><?=$item['name']?></option>
                <?php endforeach;?>
            </select>
			<?php endif;?>
        </span>
        <?php if ($single): ?>
        <span id="local_upload" class="button" style="margin-left: 0;">上传图片</span>
        <?php else: ?>
        <span id="local_upload" class="button" style="margin-left: 0;">批量上传</span>
        <span id="zip_upload" class="button">上传zip包</span>
        <?php endif; ?>
        <span id="remote_upload" class="button">远程采集</span>
    </div>
    <div class="panel-content image-list" style="position: relative;">
        <div id="local_image_list">
            <ul>
                <li class="image-thumb-empty">暂无图片</li>
            </ul>
        </div>
    </div>
    <div class="btn_area">
        <input type="submit" name="insert" class="button" value="确定" />
        <input type="button" name="cancel" class="button" value="取消" onclick="window.dialogCallback.close();" />
    </div>
</form>
<script type="text/javascript">
$(function() {
    var multiple = <?php echo $single ? 0 : 1; ?>,
        imageWidth = <?php echo intval(setting('picture', 'thumb_width')); ?>,
        imageHeight = <?php echo intval(setting('picture', 'thumb_height')); ?>,
        imageList = ct.imageList({
            uploader: $('#local_upload'),
            uploaderParams: {
                image_width: imageWidth,
                image_height: imageHeight
            },
            multiple: multiple,
            localImageList: $('#local_image_list'),
            remoteImageList: false
        });
    multiple && $('#zip_upload').uploader({
        script: '?app=system&controller=image&action=zip',
        fileDesc: 'zip压缩包',
        fileExt: '*.zip;',
        multi: 0,
        jsonType: 1,
        params: {
            thumb_width: 213,
            thumb_height: 160,
            image_width: imageWidth,
            image_height: imageHeight
        },
        complete: function(json, data) {
            if (json.data && json.data.length) {
                var image,
                    delayAdd = function() {
                        image = json.data.shift();
                        imageList.addLocalImage({
                            aid: image.aid,
                            desc: image.desc && image.desc.substr(0, image.desc.lastIndexOf('.')) || image.aid,
                            thumb: image.thumb,
                            image: image.url
                        }, json.data.length);
                        json.data.length && setTimeout(delayAdd, 50);
                    };
                delayAdd();
            }
        },
        error: function(id, file, type, info) {
            ct.error('上传失败');
        }
    });
    $('#remote_upload').click(function() {
        ct.form('远程采集', '?app=picture&controller=picture&action=remote' + (multiple ? '' : '&single=1'), 400, multiple ? 220 : 50, function(json) {
            if (json.state) {
                if (multiple) {
                    $.each(json.data, function(index, item) {
                        item = item.split('|');
                        imageList.addLocalImage({
                            aid: item[0],
                            image: item[1]
                        });
                    });
                } else {
                    var item = json.data.pop();
                    item = item.split('|');
                    imageList.addLocalImage({
                        aid: item[0],
                        image: item[1]
                    });
                }
                return true;
            }
        });
    });
    $('#image_thumb_form').submit(function() {
        ct.endLoading();
        ct.stopListenOnce();
        ct.startLoading('center', '正在处理图片...');
        imageList.getLocalImageItems(this, function(items) {
            var item, delayAdd;
            ct.endLoading();
            if (multiple) {
                ct.stopListenOnce();
                ct.startLoading('center', '正在添加 ' + items.length + ' 张图片...');
                delayAdd = function() {
                    item = items.shift();
                    window.dialogCallback.insert(item.aid, item.src, item.desc, item.sort, items.length);
                    items.length && setTimeout(delayAdd, 50);
                };
                delayAdd();
            } else {
                item = items.pop();
                window.dialogCallback.insert(item.aid, item.src, item.desc, item.sort);
            }
        }, function() {
             ct.endLoading();
            ct.error('添加失败');
        });
        return false;
    });
    $('#library_link').click(function() {
        imageList.confirm(false, function() {
            location.href = '?app=system&controller=attachment&action=picture&ext_limit=jpg,jpeg,png,gif&select=1<?php if ($single): ?>&single=1<?php endif; ?>';
        });
    });
});
</script>
</body>
</html>