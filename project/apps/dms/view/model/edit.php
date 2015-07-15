<div class="bk_10"></div>
<div class="bk_10"></div>
<div class="bk_8"></div>
<form name="model_edit" method="POST" action="?app=dms&controller=model&action=edit">
    <input type="hidden" name="modelid" value="<?=$modelid?>" />
    <table width="90%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
        <tr>
            <th width="90"><span class="c_red">*</span> 模型名称：</th>
            <td><input type="text" name="name" value="<?=$name?>" size="30"/></td>
        </tr>
        <tr>
            <th><span class="c_red">*</span> 模型别名：</th>
            <td><input type="text" name="alias" value="<?=$alias?>" size="30"/></td>
        </tr>
        <tr>
            <th><span class="c_red">*</span> 主索引名称：</th>
            <td><input type="text" name="mainindex" value="<?=$mainindex?>" size="30" /> </td>
        </tr>
        <tr>
            <th>增量索引名称：</th>
            <td><input type="text" name="deltaindex" value="<?=$deltaindex?>" size="30" /></td>
        </tr>
    </table>
</form>