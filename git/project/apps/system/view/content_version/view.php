<?php $this->display('header', 'system');?>
<div class="bk_8"></div>
  <table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="table_info mar_l_8">
    <thead>
      <tr>
        <th width="10%" class="bdr_3">属性</th>
        <th width="45%">当前内容</th>
        <th width="45%">版本</th>
      </tr>
    </thead>
    <tbody id="list_body">
<tr>
	<th>标题：</th>
	<td id="c_title"><?=$c['title']?></td>
	<td id="v_title"><?=$v['title']?></td>
</tr>
<tr>
	<th>内容：</th>
	<td id="c_content"><?=$c['content']?></td>
	<td id="v_content"><?=$v['content']?></td>
</tr>
<tr>
	<th>Tags：</th>
	<td id="c_tags"><?=$c['tags']?></td>
	<td id="v_tags"><?=$v['tags']?></td>
</tr>
<tr>
	<th>摘要：</th>
	<td id="c_description"><?=$c['description']?></td>
	<td id="v_description"><?=$v['description']?></td>
</tr>
    </tbody>
  </table>
<script type="text/javascript" src="apps/system/js/compare.js"></script>
<script type="text/javascript">
CompareById('c_title', 'v_title');
CompareById('c_content', 'v_content');
CompareById('c_tags', 'v_tags');
CompareById('c_description', 'v_description');
</script>
<?php $this->display('footer', 'system');
