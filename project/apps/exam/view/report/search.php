<script type="text/javascript">
function commit(form)
{
	tableApp.load($(form),'post');
}
</script>
<div class="mar_l_8 mar_t_8">
		<input name="contentid" type="hidden" value="<?=$contentid?>" />
<form action="?app=exam&controller=report&action=answer&contentid=<?=$contentid?>&report=1">
		<div id="questions">
<?php 
foreach ($question as $k=>$r)
{
	$this->assign('n', $k+1); 
	$this->assign($r); 
	$this->display('question/'.$r['type'].'/view');
}
?>
		</div>
		
		<div align="center" style="margin-bottom:10px;"><input type="button" class="button_style_1" onclick="commit(this.form)" value="搜索"/></div>
</form>
</div>
<?php $this->display('footer', 'system');?>