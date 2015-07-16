<div class="bk_8"></div>
<form name="edit" method="POST" class="validator" action="?app=system&controller=dsn&action=edit">
<input type="hidden" value="<?=$dsnid?>" name="dsnid" />
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
  <tr>
    <th><span class="c_red">*</span> 名称：</th>
    <td><input name="name" value="<?=$name?>" /></td>
  </tr>
  <tr>
    <th>驱动：</th>
    <td>
        <select name="driver">
            <option value="mysql"<?=$driver=='mysql'?' selected="selected"':''?>>MySQL</option>
            <!--<option value="oci"<?=$driver=='oci'?' selected="selected"':''?>>Oracle</option>-->
            <option value="mssql"<?=$driver=='mssql'?' selected="selected"':''?>>MS SQL Server</option>
            <!--<option value="pgsql"<?=$driver=='pgsql'?' selected="selected"':''?>>PostgreSQL</option>-->
            <!--<option value="sqlite"<?=$driver=='sqlite'?' selected="selected"':''?>>SQLite</option>-->
            <!--<option value="odbc"<?=$driver=='odbc'?' selected="selected"':''?>>ODBC</option>-->
        </select>
    </td>
  </tr>
  <tr>
    <th>主机：</th>
    <td><input name="host" value="<?=$host?>" /></td>
  </tr>
  <tr>
    <th>端口：</th>
    <td><input name="port" value="<?=$port?>" /></td>
  </tr>
  <tr>
    <th>用户名：</th>
    <td><input name="username" value="<?=$username?>" /></td>
  </tr>
  <tr>
    <th>密码：</th>
    <td><input name="password" value="<?=$password?>" /></td>
  </tr>
  <tr>
    <th><span class="c_red">*</span> 数据库：</th>
    <td><input name="dbname" value="<?=$dbname?>" />&nbsp;&nbsp;<button type="button" class="button_style_1" onclick="App.testlink(this.form);return false;">测试连接</button></td>
  </tr>
  <tr>
    <th>持久连接：</th>
    <td>
        <input type="radio" name="pconnect" class="radio_style" value="0"<?=empty($pconnect)?' checked="checked"':''?> />否
        <input type="radio" name="pconnect" class="radio_style" value="1"<?=$pconnect?' checked="checked"':''?> />是
    </td>
  </tr>
  <tr>
    <th>字符集：</th>
    <td>
        <select name="charset">
            <option value="utf8"<?=$charset=='utf8'?' selected="selected"':''?>>UTF-8</option>
            <option value="gbk"<?=$charset=='gbk'?' selected="selected"':''?>>GBK</option>
        </select>
    </td>
  </tr>
</table>
</form>