<div class="bk_8"></div>
<div class="tag_1">
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
            <th>标题</th>
            <th width="80">作者</th>
            <th width="116">时间</th>
            <th width="30">删除</th>
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
	<table width="95%" border="0" cellspacing="0" cellpadding="0" class="table_list mar_l_8 clear">
	    <thead>
	        <tr>
	            <th class="bdr_3" width="30">&nbsp;</th>
	            <th>标题</th>
	            <th width="80">作者</th>
	            <th width="116">时间</th>
	        </tr>
	    </thead>
	    <tbody></tbody>
	</table>
</div>
<form method="POST" action="?app=page&controller=section&action=additem">
<div class="part">
<table width="95%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
  <tbody>
	  <tr>
	    <th width="80"><span class="c_red">*</span> 标题：</th>
	    <td>
	    	<input type="text" class="inputtit_focus" name="title" size="42" value=""/>
	    	<img src="images/color.gif" alt="色板" height="16" width="16" style="vertical-align: middle;cursor:pointer;" />
	    	<input type="hidden" name="color" />
	    	<input id="hasSub" type="checkbox" class="checkbox_style" onclick="$(this).parents('tbody').next()[this.checked ? 'show' : 'hide']()" />&nbsp;&nbsp;<label for="hasSub">副题</label>
	        <input type="hidden" name="sectionid" value="<?=$sectionid?>" />
	        <input type="hidden" name="row" value="<?=$row?>" />
			<input type="hidden" name="contentid" value="<?=$item['contentid']?>" />
	    </td>
	  </tr>
	  <tr>
	  	<th>链接：</th>
	    <td><input type="text" name="url" size="50" value="" /></td>
	  </tr>
  </tbody>
  <tbody style="display:none;">
	  <tr>
	    <th>副标题：</th>
	    <td>
	    	<input type="text" name="subtitle" class="inputtit_focus" size="50" value="" />
	    </td>
	  </tr>
	  <tr>
	  	<th>链接：</th>
	    <td><input type="text" name="suburl" size="50" value="" /></td>
	  </tr>
  </tbody>
  <tr>
  	<th>缩略图：</th>
    <td>
    	<input type="text" name="thumb" size="30" value="" />
    </td>
  </tr>
  <tr>
  	<th>摘要：</th>
    <td><textarea rows="5" cols="64" name="description"></textarea></td>
  </tr>
  <tr>
  	<th>时间：</th>
  	<td><input type="text" name="time" class="input_calendar" value="<?=date('Y-m-d H:i:s')?>" onclick="DatePicker(this,{'format':'yyyy-MM-dd HH:mm:ss'});" /></td>
  </tr>
</table>
</div>
</form>