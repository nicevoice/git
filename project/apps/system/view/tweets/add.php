<div class="bk_8"></div>
<form name="add" method="POST" class="validator" action="?app=system&controller=tweets&action=add">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
  <tr>
    <th><span class="c_red">*</span> 标识名称：</th>
    <td><input name="name" /></td>
  </tr>
  <tr>
    <th width="120">微博类型：</th>
    <td>
        <select name="driver">
            <option value="sina">新浪微博</option>
            <!--<option value="qq">腾讯微博</option>
            <option value="renren">人人网</option>
            <option value="kaixin">开心网</option>-->
        </select>
    </td>
  </tr>  
  <tr>
    <th><span class="c_red">*</span> 用户名： </th>
    <td><input name="username" /></td>
  </tr>
  <tr>
    <th><span class="c_red">*</span> 密码： </th>
    <td><input name="password" type="password" /></td>
  </tr>
  
  <tbody id="sina" style="display:none;">
  <tr>
  	<th>新浪 API Key：</th>
  	<td><input name="key" /></td>
  </tr>
  <tr>
  	<th>新浪 API Secret：</th>
  	<td><input name="secret" /></td>
  </tr>
  </tbody>
  <tbody id="qq">
  </tbody>
  <tbody id="renren">
  </tbody>
  <tbody id="kaixin">
  </tbody>
</table>
</form>
<script>
var driver = $(':input[name=driver]');
$('#'+driver.val()).show();
driver.change(function(){
	$('#sina, #qq, #renren, #kaixin').hide();
	$('#'+driver.val()).show();
});
</script>