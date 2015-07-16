<form>
<table class="table_form mar_l_8" cellspacing="0" cellpadding="0" border="0" width="95%">
    <tr>
        <th width="80">数据源：</th>
        <td>
            <select name="dsnid">
                <option value="">请选择</option>
                <option value="0">本系统</option>
                <?php foreach ($dsnset as $id=>$d):?>
                <option value="<?=$id?>" <?=$dsnid==$id?' selected':''?> ><?=$d['name']?></option>
                <?php endforeach;?>
            </select>
            &nbsp;&nbsp;<a href="javascript:ct.assoc.open('?app=system&controller=dsn','newtab');">管理数据源</a>
        </td>
    </tr>
    <tbody>
    	
    </tbody>
</table>
</form>