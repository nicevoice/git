<div class="tabs">
	<ul>
		<li index="0"><a href="javascript:;">基本信息</a></li>
		<li index="1"><a href="javascript:;">栏目权限</a></li>
		<li index="2"><a href="javascript:;">页面权限</a></li>
		<li index="3"><a href="javascript:;">权重权限</a></li>
	</ul>
</div>
<form name="edit" method="POST" action="?app=system&controller=administrator&action=<?=$action?>&userid=<?=$userid?>">
<div class="part">
	<table width="93%" border="0" cellspacing="0" cellpadding="0" class="table_form">
	  <tr>
	    <th width="80"><span class="c_red">*</span> 用户名：</th>
	    <td><input type="text" name="username" value="<?=$user['username']?>" size="20" readonly/></td>
	  </tr>
	  <tr>
	    <th><span class="c_red">*</span> E-mail：</th>
	    <td><input type="text" name="email" value="<?=$user['email']?>" size="40"/></td>
	  </tr>
	  <tr>
	    <th>重设密码：</th>
	    <td><input type="text" name="password" value="" autocomplete="off" size="20"/></td>
	  </tr>
	  <tr>
	    <th>部门：</th>
	    <td><?=element::department_dropdown('departmentid',$user['departmentid'])?></td>
	  </tr>
	  <tr>
	    <th>角色：</th>
	    <td><?=element::role_dropdown('roleid', $user['roleid'], $user['departmentid'])?></td>
	  </tr>
	  <tr>
	    <th>姓名：</th>
	    <td><input type="text" name="name" value="<?=$user['name']?>" size="20"/></td>
	  </tr>
	  <tr>
	    <th>性别：</th>
	    <td><?=$sex_radio?></td>
	  </tr>
	  <tr>
	    <th>生日：</th>
	    <td><input type="text" name="birthday" value="<?=$user['birthday']?>" size="10"/> 格式：1982-01-01</td>
	  </tr>
	  <tr>
	    <th>QQ：</th>
	    <td><input type="text" name="qq" value="<?=$user['qq']?>" size="20"/></td>
	  </tr>
	  <tr>
	    <th>MSN：</th>
	    <td><input type="text" name="msn" value="<?=$user['msn']?>" size="40"/></td>
	  </tr>
	  <tr>
	    <th>手机：</th>
	    <td><input type="text" name="mobile" value="<?=$user['mobile']?>" size="20"/></td>
	  </tr>
	  <tr>
	    <th>电话：</th>
	    <td><input type="text" name="telephone" value="<?=$user['telephone']?>" size="20"/></td>
	  </tr>
	  <tr>
	    <th>地址：</th>
	    <td><input type="text" name="address" value="<?=$user['address']?>" size="50"/></td>
	  </tr>
	  <tr>
	    <th>状态：</th>
	    <td><?=$state_radio?></td>
	  </tr>
	</table>
</div>
<div class="part">
    <input class="placetree" name="catid" value="<?=$catid?>"
           url="?app=system&controller=category&action=cate&catid=%s"
           initUrl="?app=system&controller=category&action=name&catid=%s"
           paramVal="catid"
           paramTxt="name"
           multiple="multiple" />
</div>
<div class="part">
	<table width="93%" class="table_list mar_l_8 treeTable" cellpadding="0" cellspacing="0">
	  <tbody>
	  </tbody>
	</table>
</div>
<div class="part">
	<table width="93%" class="table_form mar_l_8" cellpadding="0" cellspacing="0">
	  <tr  height="40">
	    <th width="80"> 最高权重：</th>
	    <td><input type="text" name="weight" value="<?=$user['weight']?>" size="3"/></td>
	  </tr>
	</table>
</div>
</form>