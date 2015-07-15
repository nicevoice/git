
<div class="bk_10"></div>
    	<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
    	<?php if($fields['name']['have']) {?>
    	<tr>
			<th width="65">姓名：</th>
			<td><?=$data['name']?></td>
		</tr>
		<?php } ?>
		<?php if($fields['sex']['have']) {?>
		<tr>
			<th>性别：</th>
			<td><?=$data['sex']?></td>
		</tr>
		<?php } ?>
		<?php if($fields['photo']['have']) {?>
		<tr>
			<th>照片：</th>
			<td><?php if($data['photo']) {?> <img width="100" src="<?=UPLOAD_URL.$data['photo']?>"/><?php };?></td>
		</tr>
		<?php } ?>
		<?php if($fields['identity']['have']) {?>
		<tr>
			<th>身份证：</th>
			<td><?=$data['identity']?></td>
		</tr>
		<?php } ?>
		<?php if($fields['company']['have']) {?>
		<tr>
			<th>公司	：</th>
			<td><?=$data['company']?></td>
		</tr>
		<?php } ?>
		<?php if($fields['job']['have']) {?>
		<tr>
			<th>工作：</th>
			<td><?=$data['job']?></td>
		</tr>
		<?php } ?>
		<?php if($fields['telephone']['have']) {?>
		<tr>
			<th>电话：</th>
			<td><?=$data['telephone']?> </td>
		</tr>
		<?php } ?>
		<?php if($fields['mobile']['have']) {?>
		<tr >
			<th>手机：</th>
			<td><?=$data['mobile']?></td>
		</tr>
		<?php } ?>
		<?php if($fields['email']['have']) {?>
		<tr>
			<th> email：</th>
			<td><?=$data['email']?></td>
		</tr>
		<?php } ?>
		<?php if($fields['qq']['have']) {?>
		<tr>
			<th> qq：</th>
			<td><?=$data['qq']?></td>
		</tr>
		<?php } ?>
		<?php if($fields['msn']['have']) {?>
		<tr>
			<th> msn：</th>
			<td><?=$data['msn']?></td>
		</tr>
		<?php } ?>
		<?php if($fields['site']['have']) {?>
		<tr>
			<th> 主页：</th>
			<td><?=$data['site']?></td>
		</tr>
		<?php } ?>
		<?php if($fields['address']['have']) {?>
		<tr>
	        <th> 地址：</th>
			<td><?=$data['address']?></td>
		</tr>
		<?php } ?>
		<?php if($fields['zipcode']['have']) {?>
		<tr>
			 <th> 邮编：</th>
			<td><?=$data['zipcode']?></td>
		</tr>
		<?php } ?>
		<?php if($fields['aid']['have']) {?>
		<tr>
			 <th> 附件：</th>
			<td><a href="<?=$data['aid']?>" style="text-decoration:underline" target="_blank">下载</a></td>
		</tr>
		<?php } ?>
		<?php if($fields['note']['have']) {?>
		<tr>
			 <th> 个人说明：</th>
			<td><?=$data['note']?></td>
		</tr>
		<?php } ?>
		
	</table>