<div class="bk_8"></div>
<form name="sign_edit" id="sign_edit" method="POST" action="?app=<?=$app?>&controller=sign&action=edit">
    <input type="hidden" name="signid" value="<?=$signid?>"/>
    	<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
    	<?php if($fields['name']['have']) {?>
    	<tr>
			<th width="65"><?php if($fields['name']['need']) {?><span class="c_red">*</span><?php } ?>&nbsp;&nbsp;姓名：</th>
			<td><input type="text" name="name" id="name" value="<?=$data['name']?>" size="40"/></td>
		</tr>
		<?php } ?>
		<?php if($fields['sex']['have']) {?>
		<tr>
			<th>&nbsp;&nbsp;性别：</th>
			<td><input name="sex" type="radio" value="1" <?php if($data['sex']=='男') {?> checked="checked"<?php }?> /> 男 <input name="sex" type="radio" value="0" <?php if($data['sex']=='女') {?> checked="checked"<?php }?> /> 女</td>
		</tr>
		<?php } ?>
		<?php if($fields['photo']['have']) {?>
		<tr>
			<th><?php if($fields['photo']['need']) {?><span class="c_red">*</span><?php } ?>&nbsp;&nbsp;照片：</th>
			<td>
			<?php echo element::image('thumb',$data['photo'],5);?></td>
		</tr>
		<?php } ?>
		<?php if($fields['identity']['have']) {?>
		<tr>
			<th><?php if($fields['identity']['need']) {?><span class="c_red">*</span><?php } ?>&nbsp;&nbsp;身份证：</th>
			<td><input type="text" name="identity" id="identity" value="<?=$data['identity']?>" size="40"/></td>
		</tr>
		<?php } ?>
		<?php if($fields['company']['have']) {?>
		<tr>
			<th><?php if($fields['company']['need']) {?><span class="c_red">*</span><?php } ?>&nbsp;&nbsp;公司：</th>
			<td><input type="text" name="company" id="company" value="<?=$data['company']?>" size="40"/></td>
		</tr>
		<?php } ?>
		<?php if($fields['job']['have']) {?>
		<tr>
			<th><?php if($fields['job']['need']) {?><span class="c_red">*</span><?php } ?>&nbsp;&nbsp;工作：</th>
			<td><input type="text" name="job" id="job" value="<?=$data['job']?>" size="40"/></td>
		</tr>
		<?php } ?>
		<?php if($fields['telephone']['have']) {?>
		<tr>
			<th><?php if($fields['telephone']['need']) {?><span class="c_red">*</span><?php } ?>&nbsp;&nbsp;电话：</th>
			<td><input type="text" name="telephone" id="telephone" value="<?=$data['telephone']?>" size="40"/> </td>
		</tr>
		<?php } ?>
		<?php if($fields['mobile']['have']) {?>
		<tr >
			<th><?php if($fields['mobile']['need']) {?><span class="c_red">*</span><?php } ?>&nbsp;&nbsp;手机：</th>
			<td><input type="text" name="mobile" id="mobile" value="<?=$data['mobile']?>" size="40"/></td>
		</tr>
		<?php } ?>
		<?php if($fields['email']['have']) {?>
		<tr>
			<th> <?php if($fields['email']['need']) {?><span class="c_red">*</span><?php } ?>&nbsp;&nbsp;email：</th>
			<td><input type="text" name="email" id="email" value="<?=$data['email']?>" size="40"/></td>
		</tr>
		<?php } ?>
		<?php if($fields['qq']['have']) {?>
		<tr>
			<th><?php if($fields['qq']['need']) {?><span class="c_red">*</span><?php } ?>&nbsp;&nbsp;qq：</th>
			<td><input type="text" name="qq" id="qq" value="<?=$data['qq']?>" size="40"/>
			</td>
		</tr>
		<?php } ?>
		<?php if($fields['msn']['have']) {?>
		<tr>
			<th> <?php if($fields['msn']['need']) {?><span class="c_red">*</span><?php } ?>&nbsp;&nbsp;msn：</th>
			<td><input type="text" name="msn" id="msn" value="<?=$data['msn']?>" size="40"/></td>
		</tr>
		<?php } ?>
		<?php if($fields['site']['have']) {?>
		<tr>
			<th> <?php if($fields['site']['need']) {?><span class="c_red">*</span><?php } ?>&nbsp;&nbsp;主页：</th>
			<td><input type="text" name="site" id="site" value="<?=$data['site']?>" size="40"/></td>
		</tr>
		<?php } ?>
		<?php if($fields['address']['have']) {?>
		<tr>
	        <th> <?php if($fields['address']['need']) {?><span class="c_red">*</span><?php } ?>&nbsp;&nbsp;地址：</th>
			<td><input type="text" name="address" id="address" value="<?=$data['address']?>" size="40"/></td>
		</tr>
		<?php } ?>
		<?php if($fields['zipcode']['have']) {?>
		<tr>
			 <th> <?php if($fields['zipcode']['need']) {?><span class="c_red">*</span><?php } ?>&nbsp;&nbsp;邮编：</th>
			<td><input type="text" name="zipcode" id="zipcode" value="<?=$data['zipcode']?>" size="40"/></td>
		</tr>
		<?php } ?>
		<?php if($fields['note']['have']) {?>
		<tr>
			 <th> <?php if($fields['note']['need']) {?><span class="c_red">*</span><?php } ?>&nbsp;&nbsp;备注：</th>
			<td><textarea type="text" name="note" id="note"  cols="37" rows="6"><?=$data['note']?></textarea></td>
		</tr>
		<?php } ?>
		
	</table>
<script type="text/javascript">
$(function (){

	$.validate.setConfigs({
    xmlPath:'apps/<?=$app?>/validators/'
});
})
</script>
</form>