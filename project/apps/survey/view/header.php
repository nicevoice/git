<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$CONFIG['charset']?>" />
<title><?=$head['title']?></title>
<link rel="stylesheet" type="text/css" href="css/admin.css"/>
<link rel="stylesheet" type="text/css" href="apps/system/css/content.css"/>
<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/jquery-ui/dialog.css" />
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/validator/style.css"/>
<style type="text/css">
.ct_overlay{background-color:#44DAF4;height:1500px;width:100%;text-align:center;position:absolute;top:0;left:0;z-index:88;}
.ct_loadingbox {position:absolute;z-index:1; top:200px; left:50%;margin-left:-120px;background-color:#FFFDD7;border:1px solid #FDBD77;padding:16px;width:240px;}
 .ct_loadingbox p{line-height:24px;}
 .ct_loadingbox .percent{ font-family:Tahoma, Verdana, Arial; font-size:11px; font-weight:bold; color:#f60; line-height:18px; text-align:center;}
 .ct_loadingbox .bar{ margin-top:8px;border:1px solid #ccc; background:#fff;text-align:left;}
 .ct_loadingbox .bar div{ background:url(images/bg_loading.gif) repeat-x 0 1px; height:12px; line-height:12px; font-size:0; width:0px;}
</style>

<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.ui.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.form.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/config.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/cmstop.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.validator.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.dialog.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.cookie.js"></script>
<link href="<?=IMG_URL?>js/lib/treeview/treeview.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.treeview.js"></script>
<link href="<?=IMG_URL?>js/lib/tree/style.css" rel="stylesheet" type="text/css" />
<script src="<?=IMG_URL?>js/lib/cmstop.tree.js" type="text/javascript"></script>
<script type="text/javascript" src="apps/system/js/content.js"></script>
<script type="text/javascript" src="apps/survey/js/survey.js"></script>
<!-- 时间选择器 -->
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.datepicker.js"></script>
<link href="<?=IMG_URL?>js/lib/datepicker/style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript">
var app = '<?=$app?>';
var controller = '<?=$controller?>';
var action = '<?=$action?>';
var catid = '<?=$catid?>';
var modelid = '<?=$modelid?>';
var contentid = '<?=$contentid?>';
$.validate.setConfigs({
    xmlPath:'apps/<?=$app?>/validators/'
});
$(function(){
    ct.listenAjax();
});
</script>
</head>
<body>