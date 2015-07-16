<table width="95%" cellspacing="0" cellpadding="0" border="0" style="table-layout:fixed;" class="table_form">
    <tr>
	  <td>
<textarea id="data" name="data"><?=htmlspecialchars($section['data'])?></textarea>
	  </td>
   </tr>
   <tr>
   	<td height="30">
	    <?php if($section['nextupdate'] < TIME):?>
	    <label><input name="commit_publish" class="checkbox_style" onclick="this.checked ? $('#nextupdate').show() : $('#nextupdate').hide()" type="checkbox" /> 定时发布</label>
	    <input type="text" id="nextupdate" class="input_calendar" name="nextupdate" style="display:none" value="<?=date('Y-m-d H:i:s', time()+3600);?>" onclick="DatePicker(this,{'format':'yyyy-MM-dd HH:mm:ss'});" />
	    <?php else:?>
	    <label><input name="commit_publish" class="checkbox_style" onclick="this.checked ? $('#nextupdate').show() : $('#nextupdate').hide()" type="checkbox" checked="checked" /> 定时发布</label>
	    <input type="text" id="nextupdate" class="input_calendar" name="nextupdate" value="<?=date('Y-m-d H:i:s', $section['nextupdate'])?>" onclick="DatePicker(this,{'format':'yyyy-MM-dd HH:mm:ss'});" />
	    <?php endif;?>
   	</td>
   </tr>
</table>