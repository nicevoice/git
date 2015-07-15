<div class="bk_8"></div>
<form name="add" method="POST" class="validator" action="?app=system&controller=dsn&action=add">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
  <tr>
    <th><span class="c_red">*</span> 名称：</th>
    <td><input name="name" /></td>
  </tr>
  <tr>
    <th>驱动：</th>
    <td>
        <select name="driver">
            <option value="mysql">MySQL</option>
            <!--<option value="oci">Oracle</option>-->
            <option value="mssql">MS SQL Server</option>
            <!--<option value="pgsql">PostgreSQL</option>-->
            <!--<option value="sqlite">SQLite</option>-->
            <!--<option value="odbc">ODBC</option>-->
        </select>
    </td>
  </tr>
  <tr>
    <th>主机：</th>
    <td><input name="host" value="localhost" /></td>
  </tr>
  <tr>
    <th>端口：</th>
    <td><input name="port" /></td>
  </tr>
  <tr>
    <th>用户名：</th>
    <td><input name="username" /></td>
  </tr>
  <tr>
    <th>密码：</th>
    <td><input name="password" /></td>
  </tr>
  <tr>
    <th><span class="c_red">*</span> 数据库：</th>
    <td><input name="dbname" />&nbsp;&nbsp;<button type="button" class="button_style_1" onclick="App.testlink(this.form);return false;">测试连接</button></td>
  </tr>
  <tr>
    <th>持久连接：</th>
    <td>
        <input type="radio" name="pconnect" class="radio_style" value="1" />是
        <input type="radio" name="pconnect" class="radio_style" value="0" checked="checked" />否
    </td>
  </tr>
  <tr>
    <th>字符集：</th>
    <td>
        <select name="charset">
            <option value="utf8">UTF-8</option>
            <option value="gbk">GBK</option>
        </select>
    </td>
  </tr>
</table>
</form>