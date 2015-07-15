<tr>
	<th>商品品牌：</th>
	<td>
    	<input type="hidden" name="prefix" value="<?=$prefix?>" />
		<input type="text" name="brand" class="suggest"
			width="300"
			url="?app=system&controller=template&action=suggestShopexBrand&dsnid=<?=$dsnid?>&prefix=<?=urlencode($prefix)?>"
			allUrl="?app=system&controller=template&action=pageShopexBrand&dsnid=<?=$dsnid?>&prefix=<?=urlencode($prefix)?>"
			paramKeyword="keyword"
			paramPage="page" />
	</td>
</tr>
<tr>
	<th>商品标签：</th>
	<td>
		<?php foreach ($tags as $k => $tag): ?>
		<label><input name="tagid" value="<?=$tag['tag_id']?>" type="checkbox"> <?=$tag['tag_name']?></label>
		<?php endforeach; ?>
	</td>
</tr>
<tr>
	<th>搜索时间：</th>
	<td>
		<select name="published">
			<option value="0">全部时间</option>
			<option value="1">1 天</option>
			<option value="1">2 天</option>
			<option value="7">1 周</option>
			<option value="31">1 个月</option>
			<option value="93">3 个月</option>
			<option value="186">6 个月</option>
			<option value="366">1 年</option>
		</select> 以内
	</td>
</tr>
<tr>
	<th>排序类型：</th>
	<td>
		<select name="orderby">
			<option selected="selected" value="uptime">发布时间</option>
			<option value="price">销售价</option>
			<option value="comments_count">评论数量</option>
			<option value="view_count">总浏览次数</option>
			<option value="view_w_count">周浏览次数</option>
			<option value="buy_count">购买次数</option>
			<option value="buy_w_count">周购买次数</option>
		</select>
		<label><input type="radio" value="asc" name="ascdesc"> 按升序排列</label>
		<label><input type="radio" checked="checked" value="desc" name="ascdesc"> 按降序排列</label>
	</td>
</tr>
<tr>
	<th><?=element::tips('按住CTRL键以多选')?> 商品分类：</th>
	<td>
		<select multiple="multiple" name="catid" style="height:80px;width:300px;padding:0;margin:0;border-width:1px;">
			<option value="all" style="font-weight:bold">全部分类</option>
			<?=$catselect?>
		</select></td>
</tr>
<tr>
	<th><?=element::tips('多个关键词用半角逗号‘,’分隔')?> 关键字：</th>
	<td><input type="text" name="keywords" style="width:300px" />&nbsp;</td>
</tr>
<tr>
	<th>条数：</th>
    <td>
        <input name="size" size="4" value="12" />
    </td>
</tr>