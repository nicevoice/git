<form>
<table class="table_form" cellspacing="0" cellpadding="0" border="0" width="95%">
    <tr>
        <th width="60">数据源：</th>
        <td>
            <select name="dsnid">
                <option value="0">本系统</option>
                <?php foreach ($dsnset as $id=>$d):?>
                <option value="<?=$id?>"<?=$id==$selectedDsn ? ' selected="selected"' : ''?>><?=$d['name']?></option>
                <?php endforeach;?>
            </select>
            &nbsp;&nbsp;<a href="javascript:ct.assoc.open('?app=system&controller=dsn','newtab');">管理数据源</a>
        </td>
    </tr>
    <tr>
        <th>数据表：</th>
        <td>
            <select name="table">
                <?php foreach ($tableset as $t):?>
                <option value="<?=$t?>"<?=$t==$selectedTable ? ' selected="selected"' : ''?>><?=$t?></option>
                <?php endforeach;?>
            </select>
        </td>
    </tr>
    <tr>
        <th>条数：</th>
        <td>
            <input name="size" size="4" value="12" />
        </td>
    </tr>
</table>
<table width="610" cellpadding="0" cellspacing="0" class="table_list mar_l_8">
  <thead>
    <tr>
      <th class="bdr_3">字段名</th>
      <th width="150" class="t_c">类型</th>
      <th width="50" class="t_c">读取</th>
      <th width="110" class="t_c">条件</th>
      <th width="110" class="t_c">值</th>
      <th width="60" class="t_c">排序</th>
    </tr>
  </thead>
  <tbody>
      <?php foreach ($fieldset as $f):?>
      <tr>
          <td><?=$f['Field']?></td>
          <td><?=$f['Type']?></td>
          <td class="t_c"><input type="checkbox"  class="checkbox_style" name="read" /></td>
          <td>
             <select name="condition">
                <option <?=preg_match('/int/i',$f['Type']) ? 'selected="selected" ': ''?>value="=">=</option>
                <option value=">">&gt;</option>
                <option value=">=">&gt;=</option>
                <option value="<">&lt;</option>
                <option value="<=">&lt;=</option>
                <option value="<>">!=</option>
                <option <?=preg_match('/char/i',$f['Type']) ? 'selected="selected" ': ''?>value="LIKE">LIKE</option>
                <option value="LIKE %*%">LIKE % * %</option>
                <option value="LIKE *%">LIKE * %</option>
                <option value="LIKE %*">LIKE % *</option>
                <option value="NOT LIKE">NOT LIKE</option>
                <option value="IS NULL">IS NULL</option>
                <option value="IS NOT NULL">IS NOT NULL</option>
             </select>
           </td>
           <td><input type="text" width="95%" /></td>
           <td>
               <select name="sort">
                   <option value="0"></option>
                   <option value="asc">升序</option>
                   <option value="desc">降序</option>
               </select>
           </td>
       </tr>
       <?php endforeach;?>
  </tbody>
</table>
</form>