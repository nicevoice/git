<div class="bk_8"></div>
<form method="POST" action="?app=page&controller=page&action=edit&pageid=<?=$pageid?>">
<table width="95%" border="0" cellspacing="0" cellpadding="0" class="table_form">
  <tr>
    <th><span class="c_red">*</span> 页面名称：</th>
    <td><input type="text" name="name" id="name" value="<?=$page['name'];?>" size="20"/></td>
  </tr>
  <tr>
    <th><span class="c_red">*</span> 网址：</th>
    <td>
    	<?=element::psn('path', 'path', $page['path'], $size = 24, $type = 'file')?>
    </td>
  </tr>
  <tr>
    <th class="t_t">模板：</th>
    <td>
		<?=element::template('template', 'template', $page['template'], 24);?>
		<div style="margin:5px 0;">
		<label>
			<input type="checkbox" onclick="var t = this; this.checked && ct.confirm('<font color=red>清空区块不可恢复，确定选中以清空吗？</font>',null,function(){t.checked=false;})" name="clearsection" value="1" />
			&nbsp; 清空此页面所有区块以重建
		</label>
		</div>
    	<input type="hidden" name="pageid" value="<?=$_GET['pageid']?>" />
    </td>
  </tr>
  <tr>
  	<th>更新频率：</th>
    <td>
    	<input type="text" name="frequency" size="5"  value="<?=$page['frequency'];?>" /> 秒 (0表示手动)
    </td>
  </tr>
  <tr>
  	<th>下次更新</th>
    <td><input type="text" class="input_calendar" name="nextpublish" value="<?=date('Y-m-d H:i:s',$page['nextpublish']);?>" onclick="DatePicker(this,{'format':'yyyy-MM-dd HH:mm:ss'});" size="20" /></td>
  </tr>
  <tr>
  	<th>排序</th>
    <td><input name="sort" size="5" value="<?=$page['sort'];?>" /></td>
  </tr>
</table>
</form>