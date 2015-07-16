<tr>
	<th>作者：</th>
	<td>
    	<input type="hidden" name="prefix" value="<?=$prefix?>" />
    	<input type="hidden" name="discuzX" value="<?=$discuzX?>" />
		<input type="text" name="author" class="suggest"
			width="300"
			url="?app=system&controller=template&action=suggestDiscuzUser&dsnid=<?=$dsnid?>&prefix=<?=urlencode($prefix)?>&discuzX=<?=$discuzX?>"
			allUrl="?app=system&controller=template&action=pageDiscuzUser&dsnid=<?=$dsnid?>&prefix=<?=urlencode($prefix)?>&discuzX=<?=$discuzX?>"
			paramKeyword="keyword"
			paramPage="page" />
	</td>
</tr>
<tr>
	<th>主题范围：</th>
	<td>
		<label><input name="filter" value="all" checked="checked" type="radio" /> 全部主题</label>
		<label><input name="filter" value="digest" type="radio" /> 精华主题</label>
		<label><input name="filter" value="top" type="radio" /> 置顶主题</label>
	</td>
</tr>
<tr>
	<th>特殊主题：</th>
	<td>
		<label><input name="special" value="1" type="checkbox"> 投票主题</label>
		<label><input name="special" value="2" type="checkbox"> 商品主题</label>
		<label><input name="special" value="3" type="checkbox"> 悬赏主题</label>
		<label><input name="special" value="4" type="checkbox"> 活动主题</label>
		<label><input name="special" value="5" type="checkbox"> 辩论主题</label>
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
			<option selected="selected" value="lastpost">回复时间</option>
			<option value="dateline">发布时间</option>
			<option value="replies">回复数量</option>
			<option value="views">浏览次数</option>
		</select>
		<label><input type="radio" value="asc" name="ascdesc"> 按升序排列</label>
		<label><input type="radio" checked="checked" value="desc" name="ascdesc"> 按降序排列</label>
	</td>
</tr>
<tr>
	<th><?=element::tips('按住CTRL键以多选')?> 搜索范围：</th>
	<td>
		<select multiple="multiple" name="fid" style="height:100px;width:300px;padding:0;margin:0;border-width:1px;">
			<option value="all">全部版块</option>
			<?=$forumselect?>
		</select></td>
</tr>
<tr>
	<th><?=element::tips('多个关键词用半角逗号‘,’分隔')?> 关键字：</th>
	<td><input type="text" name="keywords" style="width:300px" /></td>
</tr>
<tr>
	<th>条数：</th>
    <td>
        <input name="size" size="4" value="12" />
    </td>
</tr>