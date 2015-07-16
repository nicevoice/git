<?php $this->display('header', 'system');?>
<div class="bk_10"></div>
<div class="suggest w_650 mar_l_8">
	<h2>友情提示</h2>
	<p>  说明：<br />
1.777属性通过：文件属性正常。<br />
2.需要设置777属性：没有设置777属性，需要管理员使用FTP工具手动设置777属性，否则程序可能无法正常运行。<br />
3.文件不存在：文件不存在，请检查文件是否上传完整。<br /></p>
</div>
<div class="bk_10"></div>
<!--img src="images/sh.gif" class="imgage"/ -->
<div class="title mar_l_8"><span class="f_l">检测结果</span></div> 
<table width="790" border="0" cellspacing="0" cellpadding="0" class="table_list mar_5 mar_l_8">
	<thead><tr><th width="20%" class="bdr_3">状态</th>
		<th>路径</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach($file as $key => $value) : ?>
	<tr>
		<td class="t_c"><?php echo $value;?></td>
		<td><?php echo $key;?></td>
	</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php $this->display('footer', 'system');?>