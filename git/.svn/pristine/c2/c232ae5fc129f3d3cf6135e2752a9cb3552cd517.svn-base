<form>
<table class="table_form" cellspacing="0" cellpadding="0" border="0" width="95%">
	<tr>
	    <th width="80">模型：</th>
	    <td>
	        <select name="model" class="modelset" multiple="multiple">
				<?php foreach (table('model') as $id=>$v):?>
				<option value="<?=$id?>" ico="<?=$v['alias']?>"><?=$v['name']?></option>
				<?php endforeach;?>
			</select>
	    </td>
	</tr>
    <tr>
    	<th>栏目：</th>
	    <td>
	    	<input width="150" name="category" class="selectree"
				url="?app=system&controller=category&action=cate&catid=%s"
				paramVal="catid"
				paramTxt="name"
				multiple="multiple"
			/>
	    </td>
	</tr>
	<tr>
		<th>属性：</th>
		<td>
			<input width="150" name="proid" class="selectree"
				url="?app=system&controller=property&action=cate&proid=%s"
				paramVal="proid"
				paramTxt="name"
				multiple="multiple"
			/>
		</td>
	</tr>
    <tr>
        <th>来源：</th>
        <td>
        	<input name="source" class="suggest" width="300"
				url="?app=system&controller=source&action=suggest&size=10&q=%s"
				listUrl="?app=system&controller=source&action=page&page=%s"
				paramVal="sourceid"
				paramTxt="name"
			/>
        </td>
	</tr>
    <tr>
        <th>创建人：</th>
        <td>
        	<input name="createdby" class="suggest" width="300"
				url="?app=system&controller=administrator&action=suggest&q=%s"
				listUrl="?app=system&controller=administrator&action=page&page=%s"
				paramVal="userid"
				paramTxt="username"
			/>
        </td>
    </tr>
    <tr>
        <th>权重：</th>
        <td>
            <input name="weight" size="4" type="text" />
            <label style="display:none">
                <span style="margin:0 5px;">~</span>
                <input name="weight" size="4" type="text" />
            </label>
            <input type="checkbox" class="checkbox_style" onclick="$(this).prev('label')[this.checked ? 'show' : 'hide']()" name="weight_range"/> 范围
            (<em>1~100数字</em>)
        </td>
    </tr>
    <tr>
        <th>发布时间：</th>
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
        <th>关键词：</th>
        <td>
            <input class="suggest" name="tag" width="300"
				url="?app=system&controller=tag&action=suggest&tag=%s"
				listUrl="?app=system&controller=tag&action=page&page=%s"
				paramVal="tag"
				paramTxt="tag"
				anytext="1"
			/>
        </td>
    </tr>
    <tr>
        <th>排序：</th>
        <td id="orderby">
            <div>
                <select>
                    <?php foreach ($sortset as $f=>$n):?>
                    <option value="<?=$f?>"><?=$n?></option>
                    <?php endforeach;?>
                </select>
                <input type="checkbox" checked="checked"  class="checkbox_style" value="1" /> 降序
                <a href="javascript:;" onclick="$(this).parent().remove()">删除</a>
            </div>
            <a href="javascript:;" onclick="var a = $(this);a.before(a.next('div').clone().show());return false;">增加</a>
            <div style="display:none">
                <select>
                    <?php foreach ($sortset as $f=>$n):?>
                    <option value="<?=$f?>"><?=$n?></option>
                    <?php endforeach;?>
                </select>
                <input type="checkbox"  class="checkbox_style" value="1" /> 降序
                <a href="javascript:;" onclick="$(this).parent().remove()">删除</a>
            </div>
        </td>
    </tr>
    <tr>
        <th>条数：</th>
        <td>
            <input name="size" size="4" value="12" />
        </td>
    </tr>
</table>
</form>