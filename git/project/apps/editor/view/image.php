<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>批量添加图片</title>
    <script type="text/javascript" src="<?=IMG_URL?>js/config.js"></script>
    <script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.js"></script>
    <script type="text/javascript" src="<?=IMG_URL?>js/cmstop.js"></script>
	<script type="text/javascript" src="<?=ADMIN_URL?>tiny_mce/tiny_mce_popup.js"></script>
	<script type="text/javascript" src="<?=ADMIN_URL?>tiny_mce/utils/mctabs.js"></script>
	<script type="text/javascript" src="<?=ADMIN_URL?>tiny_mce/utils/validate.js"></script>
	<script type="text/javascript" src="<?=ADMIN_URL?>tiny_mce/utils/form_utils.js"></script>
	<script type="text/javascript" src="<?=ADMIN_URL?>tiny_mce/utils/editable_selects.js"></script>

    <script type="text/javascript" src="<?=ADMIN_URL?>uploader/cmstop.uploader.js"></script>
    <script type="text/javascript" src="<?=ADMIN_URL?>js/cmstop.imageList.js"></script>
    <link rel="stylesheet" type="text/css" href="<?=ADMIN_URL?>js/imageList/style.css" />
    <script type="text/javascript" src="<?=ADMIN_URL?>tiny_mce/plugins/ct_image/js/dialog.js"></script>

    <link href="<?=ADMIN_URL?>css/admin.css" rel="stylesheet" type="text/css" />
    <style type="text/css">
        select {
            font-size: 12px !important;
        }
    </style>
    <script type="text/javascript">
        var EDITOR_IMAGE_SETTINGS = {
            image_width: <?php echo intval(setting('editor', 'thumb_width')); ?>,
            image_height: <?php echo intval(setting('editor', 'thumb_height')); ?>
        };
    </script>
</head>
<body style="display: none;" role="application">

    <div class="tabs" role="presentation">
        <ul>
            <li id="local_tab" onmousedown="mcTabs.displayTab('local_tab','local_panel'); return false;" class="current"><span><a href="javascript:;">本地上传</a></span></li>
            <li id="library_tab" onmousedown="CmsTopImageDialog.confirm($(this).hasClass('current'), function() { location.href = '?app=system&controller=attachment&action=tinymce&select=1&ext_limit=jpg,jpeg,png,gif'; }); $(this).removeClass('current'); return false;"><span><a href="javascript:;">资源库</a></span></li>
            <li id="remote_tab" onmousedown="mcTabs.displayTab('remote_tab','remote_panel'); return false;"><span><a href="javascript:;">网络图片</a></span></li>
        </ul>
    </div>

    <div class="panel_wrapper">

        <div id="local_panel" class="panel current">
            <form onsubmit="CmsTopImageDialog.localImageInsert(this);return false;" method="post" action="?app=system&controller=image&action=set_pic">
                <div class="panel-action">
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
                    <span id="local_upload" class="button">批量上传</span>
                </div>
                <div class="panel-content presentation image-list" style="position: relative;">
                    <div id="local_image_list">
                        <ul>
                            <li class="image-thumb-empty">暂无图片</li>
                        </ul>
                    </div>
                </div>
                <div class="mceActionPanel">
                    <input type="submit" name="insert" class="button" value="插入" />
                    <input type="button" name="cancel" class="button" value="取消" onclick="tinyMCEPopup.close();" />
                </div>
            </form>
        </div>

        <div id="remote_panel" class="panel">
            <form onsubmit="CmsTopImageDialog.remoteImageInsert();return false;" action="#">
                <div class="panel-action">
                    <span data-role="add" class="button_style_1">添加图片</span>
                </div>
                <div class="panel-content presentation">
                    <div class="image-panel-list-header">
                        <table cellpadding="0" cellspacing="0" width="98%" class="tablesorter table_list">
                            <thead>
                                <tr>
                                    <th width="30" class="t_c bdr_3"></th>
                                    <th width="290">地址</th>
                                    <th>简介</th>
                                    <th width="30" class="t_c"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div id="remote_image_list" class="image-panel-list">
                        <table cellpadding="0" cellspacing="0" width="98%" class="tablesorter table_list">
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="mceActionPanel">
                    <input type="submit" name="insert" class="button" value="插入" />
                    <input type="button" name="cancel" class="button" value="取消" onclick="tinyMCEPopup.close();" />
                </div>
            </form>
        </div>
    </div>
</body>
</html>
