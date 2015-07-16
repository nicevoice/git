<style tyle="text/css">
.button_style_1:hover{background-position:0 -323px !important;border-color:#FDCD99;color:#f60;}
</style>
<div class="bk_8"></div>
<form method="POST" action="?app=page&controller=section&action=edititem">
<table width="95%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
  <tbody>
     <tr>
	    <th width="80"><span class="c_red">*</span> 标题：</th>
	    <td>
	    	<input type="text" name="title" class="inputtit_focus" size="42" style="color:<?=$item['color']?>;" value="<?=htmlspecialchars($item['title'])?>"/>
	    	<img src="images/color.gif" alt="色板" height="16" width="16" style="vertical-align: middle;cursor:pointer;background-color:<?=$item['color']?>;" />
	    	<input type="hidden" name="color" />
	    	<input id="hasSub" type="checkbox" class="checkbox_style" onclick="$(this).parents('tbody').next()[this.checked ? 'show' : 'hide']()" />&nbsp;&nbsp;<label for="hasSub">副题</label>
	        <input type="hidden" name="sectionid" value="<?=$_GET['sectionid']?>" />
	        <input type="hidden" name="row" value="<?=$row?>" />
	        <input type="hidden" name="col" value="<?=$col?>" />
			<input type="hidden" name="contentid" value="<?=$item['contentid']?>" />
	    </td>
	  </tr>
	  <tr>
	  	<th>链接：</th>
	    <td><input type="text" name="url" size="50" value="<?=$item['url']?>" /></td>
	  </tr>
  </tbody>
  <tbody style="display:<?=$item['subtitle']?'':'none'?>;">
	  <tr>
	    <th>副标题：</th>
	    <td>
	    	<input type="text" name="subtitle" class="inputtit_focus" size="50" value="<?=htmlspecialchars($item['subtitle'])?>" />
	    </td>
	  </tr>
	  <tr>
	  	<th>链接：</th>
	    <td><input type="text" name="suburl" size="50" value="<?=$item['suburl']?>" /></td>
	  </tr>
  </tbody>
  <tr>
    <th>缩略图：</th>
    <td>
    	<input type="text" name="thumb" size="30" value="<?=$item['thumb']?>" />
    </td>
  </tr>
  <tr>
  	<th>摘要：</th>
    <td><textarea rows="5" cols="64" name="description" ><?=htmlspecialchars($item['description'])?></textarea></td>
  </tr>
  <tr>
  	<th>时间：</th>
  	<td><input type="text" name="time" class="input_calendar" value="<?=$item['time'] ? date('Y-m-d H:i:s', $item['time']) : ''?>" onclick="DatePicker(this,{'format':'yyyy-MM-dd HH:mm:ss'});" /></td>
  </tr>
</table>
</form>