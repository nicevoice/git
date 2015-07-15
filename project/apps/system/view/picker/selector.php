<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$CONFIG['charset']?>" />
<title><?=$head['title']?></title>
<?=$head['resource']?>
</head>
<body>
<?php if ($pickers): ?>
<div class="tabs">
	<ul>
		<?php foreach($pickers as $id=>$v):?>
		<li pickid="<?=$id?>" extension="<?=$v['extension']?>" <?=$pickid==$id?'class="active"':''?>><?=$v['name']?></li>
		<?php endforeach;?>
	</ul>
</div>
<?php endif;?>
<div class="where">
<?php $this->display("picker/form/$extension");?>
</div>
<div id="box"></div>
<div class="button-area">
	<button onclick="PICKER.ok()" type="button">确定</button>
	<button onclick="PICKER.cancel()" type="button">取消</button>
</div>
<script type="text/javascript">
PICKER.init('?app=system&controller=picker&action=page&pickid=<?=$pickid?>',<?=$_GET['multi']?1:0?>);
</script>
</body>
</html>