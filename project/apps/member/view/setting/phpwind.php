<?php $this->display('header', 'system');?>
<div class="bk_8"></div>
<?php 
$this->assign('tab',array('phpwind' => 'class="s_3"'));
$this->display('setting/header', 'member');
?>
	<div class="suggest mar_t_10 mar_l_8" style="width: 98%;">
	  <h2>友情提示</h2>
	  <p>
		方式与Ucenter类似<br/>
		整合论坛时请确认只开启Ucenter或者Phpwind其中一个整合<br/>
	  </p>
	</div>
	<div class="bk_8"></div>
	<form id="pw_setting" method="POST" action="?app=member&controller=setting&action=phpwind">
	<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>Phpwind连接配置</caption>
	<tr>
		<th width="130">启用：</th>
		<td ><input class="radio radio_style" type="radio" name="uc" value="phpwind" <?php echo ($uc == 'phpwind')?'checked':''?>> 是&nbsp;&nbsp;&nbsp;<input type="radio" name="uc" value="0" <?=$uc?'':'checked'?>> 否 </td>
	</tr>
	<tr>
		<th>连接方式：</th>
		<td>
		<input class="radio radio_style" type="radio" name="pw_connect" id="mysql" value="mysql" <?=($pw_connect == 'mysql'  ? 'checked' : '')?>> MySQL 方式
		<input class="radio radio_style" type="radio" name="pw_connect" id="socket" value="socket" <?=($pw_connect == 'socket'  ? 'checked' : '')?>> SOCKET方式
		</td>
	</tr>
	<tbody id="db_setting">
	<tr>
		<th>数据库主机名：</th>
		<td><input type="text" name="pw_dbhost" value="<?=$pw_dbhost?>" id="pw_dbhost" size="30"></td>
	</tr>
    <tr>
        <th>数据库端口号：</th>
        <td><input type="text" name="pw_dbport" value="<?=$pw_dbport?>" id="pw_dbport" size="30"></td>
    </tr>
	<tr>
		<th>数据库用户名：</th>
		<td><input type="text" name="pw_dbuser" value="<?=$pw_dbuser?>" size="20" id="pw_dbuser"></td>
	</tr>
	<tr>
		<th>数据库密码：</th>
		<td><input type="password" name="pw_dbpw" value="<?=$pw_dbpw?>" size="20" id="pw_dbpw"></td>
	</tr>
	<tr>
		<th>数据库名称：</th>
		<td><input type="text" name="pw_dbname" value="<?=$pw_dbname?>" id="pw_dbname" size="20"></td>
	</tr>        
	<tr>
		<th>数据库字符集：</th>
		<td>
		<select name="pw_dbcharset" id="pw_dbcharset">
			<option value="utf8" <?=($pw_dbcharset == 'utf8' ? 'selected' : '')?>>UTF-8</option>
			<option value="gbk"  <?=($pw_dbcharset == 'gbk'  ? 'selected' : '')?>>GBK/GB2312</option>
			<option value="big5" <?=($pw_dbcharset == 'big5' ? 'selected' : '')?>>BIG5</option>
		</select>
		</td>
	</tr>
	<tr>
		<th>数据库表前缀：</th>
		<td>
			<input type="text" name="pw_dbtablepre" id="pw_dbtablepre" value="<?=$pw_dbtablepre?>" size="20">
			<input type="button" class="button_style_1" id="test_uc" value="测试数据库连接" />
		</td>
	</tr>
	<tr>
		<th>数据库持久连接：</th>
		<td>
			<input class="radio radio_style"  type="radio" name="pw_dbconnect" value="0" <?=($pw_dbconnect == 0  ? 'checked="checked"' : '')?>/> 关闭
			<input class="radio radio_style"  type="radio" name="pw_dbconnect" value="1" <?=($pw_dbconnect == 1  ? 'checked="checked"' : '')?>/> 打开
		</td>
	</tr>
	</tbody>
	<tr>
		<th>通信密钥：</th>
		<td><input type="text" name="pw_key" value="<?=$pw_key?>" size="54"></td>
	</tr>
	<tr>
		<th>Phpwind的URL地址：</th>
		<td><input type="text" name="pw_api" value="<?=$pw_api?>" size="40"></td>
	</tr>
	<tr>
		<th>Phpwind的 IP：</th>
		<td><input type="text" name="pw_ip" value="<?=$pw_ip?>" size="18"></td>
	</tr>
	<tr>
		<th>Phpwind的字符集：</th>
		<td>
		<select name="pw_charset" id="pw_charset">
			<option value="utf8" <?=($pw_charset == 'utf8' ? 'selected' : '')?>>UTF-8</option>
			<option value="gbk"  <?=($pw_charset == 'gbk'  ? 'selected' : '')?>>GBK/GB2312</option>
			<option value="big5" <?=($pw_charset == 'big5' ? 'selected' : '')?>>BIG5</option>
		</select>
		</td>
	</tr>
	<tr>
		<th>应用ID(APP ID)：</th>
		<td><input type="text" name="pw_appid" value="<?=$pw_appid?>" size="2"></td>
	</tr>
	<tr><th>&nbsp;</th><td><input type="submit" class="button_style_2" id="submit" value="保存" /> <input type="reset" class="button_style_1" name="reset" value="重置" /></td></tr>
	</table>
</form>

<script type="text/javascript">
$(function(){
	$("#test_uc").click(function(){
		var pw_dbhost = $("#pw_dbhost").val();
        var pw_dbport = $("#pw_dbport").val();
		var pw_dbuser = $("#pw_dbuser").val();
		var pw_dbpw = $("#pw_dbpw").val();
		var pw_dbname = $("#pw_dbname").val();
		var pw_dbtablepre = $("#pw_dbtablepre").val();
		$.ajax({
			type : 'POST',
			url :"?app=member&controller=setting&action=uctest",
			data :{
				dbhost	:pw_dbhost,
                dbport  :pw_dbport,
				dbuser	:pw_dbuser,
				dbpw	:pw_dbpw,
				dbname	:pw_dbname,
				dbtablepre	:pw_dbtablepre
			},
			success : function(data){
				if(data.state){
					ct.ok('连接成功！');
				}else{
					ct.error(data.message);
				}
			},
			dataType : 'json'
		});
	});
	$('#pw_setting').ajaxForm('pw_setting_ok');

	$('button.tips').attrTips('tips', 'tips_green', 200, 'top');
});
function pw_setting_ok(data) {
	if(data.state) {
		if(data.do_merge == 1) {
			//保存开通完需要整合用户操作
			ct.form('用户同步','?app=member&controller=setting&action=ucenter_sync&type=pw',370,100,function(json){
				uc_merge(json);
				return true;
			},function(){
				return true;
			});
		} else {
			ct.ok(data.message);
		}
	}else{
		ct.error(data.message);
	}
}
function uc_merge(data) {
	if(data.state) {
		if(data.finished == 0) {
			//未完 继续请求
			ct.tips('开始第'+data.start+'条');
			$.getJSON(
				'?app=member&controller=setting&action=merge&type=pw',
				{'perpage':data.perpage,'start':data.start,'total':data.total},
				function(json) {
					uc_merge(json);
				}
			);
		} else {
			ct.endLoading();
			ct.tips('数据同步完成，请重新登录系统来完成同步', 'success', 'center', 3);
            setTimeout(function() {
                $.getJSON('?app=system&controller=admin&action=logout', function() {
                    (window.parent || window).location.href = '<?=ADMIN_URL?>';
                });
            }, 3000);
		}
	} else {
		ct.error(data.error);
	}
}
</script>
<?php $this->display('footer', 'system');?>