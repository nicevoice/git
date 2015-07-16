<div class="bk_8"></div>
<form name="magazine_page_edit" id="magazine_page_edit" method="POST" class="validator" action="?app=magazine&controller=page&action=add&eid=<?=$_GET['eid']?>">
<input type="hidden" name="eid" value="<?=$eid?>"/>
<input type="hidden" name="mid" value="<?=$mid?>"/>
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
  <tr>
    <th width="100"><span class="c_red">*</span> 栏目名：</th>
    <td><input type="text" name="name" id="name" size="30"/></td>
  </tr>
  <tr>
    <th>主编：</th>
    <td><input type="text" name="editor" id="editor" size="30"/></td>
  </tr>
  <tr>
    <th>美编：</th>
    <td><input type="text" name="arteditor" id="arteditor" size="30"/></td>
  </tr>
  <tr>
    <th>栏目号：</th>
    <td><input type="text" name="pageno" id="pageno" size="10" value="<?=$pageno?>"/></td>
  </tr>
</table>
</form>