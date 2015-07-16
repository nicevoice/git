<?php $this->display('header');?>
<div class="mar_l_8 mar_t_8">
		<input name="contentid" type="hidden" value="<?=$contentid?>" />
		<div id="questions">
<?php 
$this->assign('optionid', $optionid);
$this->assign('record', $record);

foreach ($question as $k=>$r)
{
	$this->assign('n', $k+1); 
	$this->assign($r); 
	$this->display('question/'.$r['type'].'/view');
}
?>
		</div>
</div>
<?php $this->display('footer', 'system');?>