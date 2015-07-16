<div class="bk_8"></div>
<form name="content_search" id="content_search" method="GET" action="">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
  <tr>
    <th width="60">栏目：</th>
    <td>
		<input id="catid" class="selectree" name="catids" value="<?=$catid?>" ending="1" />
	</td>
  </tr>
  <tr>
    <th>模型：</th>
    <td><?=element::model('modelid', 'modelid', $modelid)?></td>
  </tr>
  <tr>
    <th>状态：</th>
    <td><?=element::status('status', 'status', $status)?></td>
  </tr>
  <tr>
    <th>缩略图：</th>
    <td><label><input type="checkbox" name="thumb" value="1" /> 有缩略图</label></td>
  </tr>
  <tr>
    <th>关键词：</th>
    <td><input type="text" name="keywords" value="<?=$keywords?>" size="20" maxlength="20"/></td>
  </tr>
  <tr>
    <th>发布人：</th>
    <td><input type="text" name="createdbyname" value="<?=$createdbyname?>" size="15"/></td>
  </tr>
   <tr>
    <th>时间：</th>
    <td><input type="text" name="published_min" value="<?=$published_min?>" size="18" class="input_calendar" style="width:100px;"/> ~ <input type="text" name="published_max" value="<?=$published_max?>" size="18" class="input_calendar" style="width:100px;"/></td>
  </tr>
  <tr>
    <th>权重：</th>
    <td><input type="text" name="weight_min" value="<?=$weight_min?>" size="3"/> ~ <input type="text" name="weight_max" value="<?=$weight_max?>" size="3"/></td>
  </tr>
  <tr>
    <th>来源：</th>
    <td><input type="text" name="source" value="<?=$source?>" size="20"/></td>
  </tr>
  <tr>
    <th>排序：</th>
    <td><select name="orderby">
           <option value="published|desc">发布时间降序</option>
           <option value="published|asc">发布时间升序</option>
           <option value="weight|desc">权重降序</option>
           <option value="weight|asc">权重升序</option>
           <option value="pv|desc">浏览次数降序</option>
           <option value="pv|asc">浏览次数升序</option>
           <option value="comments|desc">评论数降序</option>
           <option value="comments|asc">评论数升序</option>
        </select>
    </td>
  </tr>
</table>
</form>
<script type="text/javascript">
$(function(){
	$('#catid').selectree({
		"url"	:"?app=system&controller=category&action=cate&catid=%s",
		"paramId"	: 'catid',
		"paramVal"	: "catid",
		"paramTxt"	: "name",
		"initUrl"	: "?app=system&controller=category&action=name&catid=%s",
		"multiple"	: true,
		"selectMult": true
	});
});
</script>