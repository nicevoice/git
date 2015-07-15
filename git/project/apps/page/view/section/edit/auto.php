<form method="POST" class="validator" action="?app=page&controller=section&action=edit">
<input type="hidden" name="sectionid" value="<?=$section['sectionid'];?>" />
<div class="title">
	<div class="f_r">
	<?php if ($section['published']):?>
	上次生成时间：<?=date('Y-m-d H:i:s', $section['published']);?>
	<?php else:?>
	未生成
	<?php endif;?>
	&nbsp;&nbsp;
	<input type="button" value="发布" class="button_style_1" onclick="page.publish(<?=$section['sectionid']?>); return false;" />
	</div>
	<div class="f_l"><?=$section['name']?>（编辑）</div>
</div>
<?php if ($section['description']):?>
<p><b>备注：</b><?=$section['description']?></p>
<?php endif;?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_form">
    <tr>
	  <td>
<textarea id="data" name="data"><?=htmlspecialchars($section['data'])?></textarea>
	  </td>
   </tr>
   <tr>
   	<td height="30">
	    <?php if($section['nextupdate'] < TIME):?>
	    <label><input class="checkbox_style" onclick="this.checked ? $('#nextupdate').show().removeAttr('disabled') : $('#nextupdate').hide().attr('disabled','disabled')" type="checkbox" /> 定时发布 </label>
	    <input type="text" id="nextupdate" class="input_calendar" name="nextupdate" style="display:none" disabled="disabled" value="<?=date('Y-m-d H:i:s', time()+3600);?>" onclick="DatePicker(this,{'format':'yyyy-MM-dd HH:mm:ss'});" />
	    <?php else:?>
	    <label><input class="checkbox_style" onclick="this.checked ? $('#nextupdate').show().removeAttr('disabled') : $('#nextupdate').hide().attr('disabled','disabled')" type="checkbox" checked="checked" /> 定时发布</label>
	    <input type="text" id="nextupdate" class="input_calendar" name="nextupdate" value="<?=date('Y-m-d H:i:s', $section['nextupdate'])?>" onclick="DatePicker(this,{'format':'yyyy-MM-dd HH:mm:ss'});" />
	    <?php endif;?>
   	</td>
   </tr>
   <tr>
     <td>
        <input type="submit" name="submit" value="保存" class="button_style_1" />
        <input type="button" name="b2" value="预览" onclick="page.previewSection(this.form,true); return false;" class="button_style_1" />
      	<input type="button" name="unsave" value="退出" onclick="page.unsave(<?=$section['sectionid']?>); return false;" class="button_style_1" />
     </td>
   </tr>
</table>
</form>

<div id="viewholder"></div>
<div class="title mar_10">
	<div class="f_r">
		<button class="button_style_1" onclick="page.clearLog()">清空历史记录</button>
		<button class="button_style_1" onclick="page.restoreLog('orig')">恢复到原始数据</button>
	</div>
	<div class="f_l">历史记录</div>
</div>
<div class="calendar f_l" style="width:190px"></div>
<div class="logtable f_l mar_l_8">
	<?php $this->display('section/log', 'page');?>
</div>