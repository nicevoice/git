<div class="bk_8"></div>
<div class="tag_1" style="position:static;">
  <ul class="tag_list">
    <li index="0"><a href="javascript:;" onclick="return false;">推荐</a></li>
    <li index="1"><a href="javascript:;" onclick="return false;">搜索</a></li>
    <li index="2"><a href="javascript:;" onclick="return false;">录入</a></li>
  </ul>
</div>
<div class="part">
<table width="95%" border="0" cellspacing="0" cellpadding="0" class="table_list mar_l_8">
    <thead>
        <tr>
            <th class="bdr_3" width="30">&nbsp;</th>
            <th width="280">标题</th>
            <th width="80">作者</th>
            <th width="80">时间</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
</div>
<div class="part">
	<div class="operation_area" style="padding:5px 0 2px 0;margin-top:-5px;margin-bottom:5px;">
		<div class="search_icon mar_l_8">
			<input type="text" name="keywords" value="" size="20"/>
            <label><input type="checkbox" name="thumb" value="1" />有缩略图</label>&nbsp;
			<?=element::model('modelid', 'modelid')?>
			<?=element::category('catid', 'catid', $catid, 1, null, '请选择', true, true)?>
			<button class="button_style_4" type="button">搜索</button>
		</div>
	</div>
	<div class="clear"></div>
	<table width="95%" border="0" cellspacing="0" cellpadding="0" class="table_list mar_l_8">
	    <thead>
	        <tr>
	            <th class="bdr_3" width="30">&nbsp;</th>
	            <th width="280">标题</th>
	            <th width="80">作者</th>
	            <th width="80">时间</th>
	        </tr>
	    </thead>
	    <tbody></tbody>
	</table>
</div>
<form method="POST" action="?app=page&controller=section&action=replaceitem">
<div class="part">
<table width="95%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
   <tbody>
      <tr>
	    <th width="80"><span class="c_red">*</span> 标题：</th>
	    <td>
	    	<input type="text" name="title" class="inputtit_focus" size="42" style="color:<?=$item['color']?>;" value="<?=$item['title']?>"/>
	    	<img src="images/color.gif" alt="色板" height="16" width="16" style="vertical-align: middle;cursor:pointer;background-color:<?=$item['color']?>;" />
	    	<input type="hidden" name="color" value="<?=$item['color']?>" />
	    	<input id="hasSub" type="checkbox" class="checkbox_style" onclick="$(this).parents('tbody').next()[this.checked ? 'show' : 'hide']()" />&nbsp;&nbsp;<label for="hasSub">副题</label>
	        <input type="hidden" name="sectionid" value="<?=$sectionid?>" />
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
</div>
</form>