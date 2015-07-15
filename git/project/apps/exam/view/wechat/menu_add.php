<div class="bk_8"></div>
<form id="menu_add" name="menu_add" method="POST" class="validator" action="?app=<?=$app?>&controller=<?=$controller?>&action=<?=$action?>">
    <input type="hidden" name="menuid" value="<?=$menuid?>"/>
    <table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
        <tr>
            <th width="100"><span class="c_red">*</span> 名称：</th>
            <td><input type="text" name="name" value="<?=$name?>" size="20"/></td>
        </tr>
        <tr>
            <th><span class="c_red">*</span> 链接：</th>
            <td><input type="text" name="link" value="<?=$link?>" size="20"/></td>
        </tr>

        <tr>
            <th>缩略图：</th>
            <td><?=element::image('logo', $logo, 25)?></td>
        </tr>

        <tr>
            <th>简介：</th>
            <td><textarea name="description" cols="40" rows="3" class=""><?=$description?></textarea></td>
        </tr>
        <tr>
            <th width="100"><span class="c_red">*</span> 排序：</th>
            <td><input type="text" name="sort" value="<?=$sort?>" size="20"/></td>
        </tr>
    </table>
</form>