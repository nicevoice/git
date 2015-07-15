<div style="width:350px;overflow:hidden;">
<div class="bk_8"></div>
<form method="POST" action="?app=member&controller=group&action=changegroup">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
	<tr>
		<td width="40"></td>
		<td>将目标移动至用户组：</td>
	</tr>
	<?php foreach($groups as $key => $value){ ?>
	<tr>
		<td></td>
		<td><label>
		<input type="radio" class="radio_style" value="<?=$value['groupid']?>" name="groupid"> <?=$value['name']?> <br/>
		</label></td>
	</tr>
	<?php }?>
	<?php if(isset($_GET['userid'])) { ?>
			<input type="hidden" name="userid" value="<?=$_GET['userid']?>"/>
		<?php }else{ ?>
			<input type="hidden" name="oldgroupid" value="<?=$_GET['groupid']?>"/>
		<?php } ?>
</table>
</form>
</div>