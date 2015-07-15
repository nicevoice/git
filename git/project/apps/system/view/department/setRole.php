<div class="title mar_l_8" style="width:90%">设置<b class="c_red"><?=$department['name']?></b>角色</div>
<div style="margin-left:20px;margin-bottom:10px;">
	<form action="?app=system&controller=department&action=setRole" method="POST">
		<input type="hidden" value="<?=$_REQUEST['departmentid']?>" name="departmentid" />
		<?php foreach ($roles as $r):?>
		<label style="line-height:25px"><input type="checkbox" value="<?=$r['roleid']?>" <?=in_array($r['roleid'],$selected) ? 'checked="checked"' : ''?> name="roleid[]" /> <?=$r['name']?></label><br />
		<?php endforeach;?>
	</form>
</div>