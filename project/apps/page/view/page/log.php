<?php if (empty($logs)):?>
	<tr>
      <td>没有任何操作记录</td>
    </tr>
<?php else:?>
    <?php foreach($logs as $key => $value):?>
    <tr>
      <td width="24%" class="t_c"><?=date('Y-m-d H:i:s', $value['created']);?></td>
      <td width="76%"><a href="javascript:url.member(<?=$value['createdby']?>);" ><?=username($value['createdby']);?></a> 对区块“<a href="javascript:;" onclick="page.clickSection(<?=$value['sectionid']?>); return false;"><?=$value['sectionname']?></a>”执行了“<?=$value['action']?>”操作</td>
    </tr>
    <?php endforeach;?>
<?php endif;?>