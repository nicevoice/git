<?php $this->display('header', 'system');?>
<div class="bk_8"></div>
<?php 
$this->assign('tab',array('ucenter' => 'class="s_3"'));
$this->display('setting/header', 'member');
?>
	<div class="suggest mar_t_10 mar_l_8" style="width: 98%;">
	  <h2>友情提示</h2>
	  <p>
		mysql连接方式：使用mysql连接直接操作ucenter数据表，效率高，推荐。<br/>
		socket连接方式：使用http方式发送控制指令给ucenter<br/>
		整合前用户不一致可以使用mysql方式填写正确配置进行整合，同步前请做好数据备份，以免发生意外情况导致损失。<br/>
	  </p>
	</div>
	<div class="bk_8"></div>
	<form name="uc_setting" id="uc_setting" method="POST" action="?app=member&controller=setting&action=ucenter">
	<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>Ucenter连接配置</caption>
	<tr>
		<th width="130">启用：</th>
		<td ><input class="radio radio_style" type="radio" name="uc" value="ucenter" <?php echo ($uc == 'ucenter')?'checked':''?>> 是&nbsp;&nbsp;&nbsp;<input type="radio" name="uc" value="0" <?=$uc?'':'checked'?>> 否 </td>
	</tr>
	<tr>
		<th>连接方式：</th>
		<td>
		<input class="radio radio_style" type="radio" name="uc_connect" id="mysql" value="mysql" <?=($uc_connect == 'mysql'  ? 'checked' : '')?>> MySQL 方式
		<input class="radio radio_style" type="radio" name="uc_connect" id="socket" value="socket" <?=($uc_connect == 'socket'  ? 'checked' : '')?>> SOCKET方式
		</td>
	</tr>
	<tbody id="db_setting">
	<tr>
		<th>数据库主机名：</th>
		<td><input type="text" name="uc_dbhost" value="<?=$uc_dbhost?>" id="uc_dbhost" size="30"></td>
	</tr>
    <tr>
        <th>数据库端口号：</th>
        <td><input type="text" name="uc_dbport" value="<?=$uc_dbport?>" id="uc_dbport" size="30"></td>
    </tr>
	<tr>
		<th>数据库用户名：</th>
		<td><input type="text" name="uc_dbuser" value="<?=$uc_dbuser?>" size="20" id="uc_dbuser"></td>
	</tr>
	<tr>
		<th>数据库密码：</th>
		<td><input type="password" name="uc_dbpw" value="<?=$uc_dbpw?>" size="20" id="uc_dbpw"></td>
	</tr>
	<tr>
		<th>数据库名称：</th>
		<td><input type="text" name="uc_dbname" value="<?=$uc_dbname?>" id="uc_dbname" size="20"></td>
	</tr>        
	<tr>
		<th>数据库字符集：</th>
		<td>
		<select name="uc_dbcharset" id="uc_dbcharset">
			<option value="utf8" <?=($uc_dbcharset == 'utf8' ? 'selected' : '')?>>UTF-8</option>
			<option value="gbk"  <?=($uc_dbcharset == 'gbk'  ? 'selected' : '')?>>GBK/GB2312</option>
			<option value="big5" <?=($uc_dbcharset == 'big5' ? 'selected' : '')?>>BIG5</option>
		</select>
		</td>
	</tr>
	<tr>
		<th>数据库表前缀：</th>
		<td>
			<input type="text" name="uc_dbtablepre" id="uc_dbtablepre" value="<?=$uc_dbtablepre?>" size="20">
			<input type="button" class="button_style_1" id="test_uc" value="测试数据库连接" />
		</td>
	</tr>
	<tr>
		<th>数据库持久连接：</th>
		<td>
			<input class="radio radio_style"  type="radio" name="uc_dbconnect" value="0" <?=($uc_dbconnect == 0  ? 'checked="checked"' : '')?>/> 关闭
			<input class="radio radio_style"  type="radio" name="uc_dbconnect" value="1" <?=($uc_dbconnect == 1  ? 'checked="checked"' : '')?>/> 打开
		</td>
	</tr>
	</tbody>
	<tr>
		<th>通信密钥：</th>
		<td><input type="text" name="uc_key" value="<?=$uc_key?>" size="54"></td>
	</tr>
	<tr>
		<th>UCenter的 URL 地址：</th>
		<td><input type="text" name="uc_api" value="<?=$uc_api?>" size="40"></td>
	</tr>
	<tr>
		<th>UCenter 的 IP：</th>
		<td><input type="text" name="uc_ip" value="<?=$uc_ip?>" size="18"></td>
	</tr>
	<tr>
		<th>UCenter 的字符集：</th>
		<td>
		<select name="uc_charset" id="uc_charset">
			<option value="utf8" <?=($uc_charset == 'utf8' ? 'selected' : '')?>>UTF-8</option>
			<option value="gbk"  <?=($uc_charset == 'gbk'  ? 'selected' : '')?>>GBK/GB2312</option>
			<option value="big5" <?=($uc_charset == 'big5' ? 'selected' : '')?>>BIG5</option>
		</select>
		</td>
	</tr>
	<tr>
		<th>应用ID(APP ID)：</th>
		<td><input type="text" name="uc_appid" value="<?=$uc_appid?>" size="2"></td>
	</tr>
	<tr><th>&nbsp;</th><td><input type="submit" class="button_style_2" id="submit" value="保存" /> <input type="reset" class="button_style_1" name="reset" value="重置" /></td></tr>
	</table>
</form>

<script type="text/javascript">
$(function(){
	$("#test_uc").click(function(){
		var uc_dbhost = $("#uc_dbhost").val();
        var uc_dbport = $("#uc_dbport").val();
		var uc_dbuser = $("#uc_dbuser").val();
		var uc_dbpw = $("#uc_dbpw").val();
		var uc_dbname = $("#uc_dbname").val();
		var uc_dbtablepre = $("#uc_dbtablepre").val();
		$.ajax({
			type : 'POST',
			url :"?app=member&controller=setting&action=uctest",
			data :{
				dbhost	:uc_dbhost,
                dbport  :uc_dbport,
				dbuser	:uc_dbuser,
				dbpw	:uc_dbpw,
				dbname	:uc_dbname,
				dbtablepre	:uc_dbtablepre
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
	if($("#socket").attr('checked')==true){
		$("#db_setting").hide();
	}
	$("#mysql").click(function(){
		$("#db_setting").fadeIn("slow");
	});
	$("#socket").click(function(){
		$("#db_setting").fadeOut("slow");
	});
	$('#uc_setting').ajaxForm('uc_setting_ok');

	$('button.tips').attrTips('tips', 'tips_green', 200, 'top');
});

function uc_setting_ok(data){
	if(data.state) {
		if(data.do_merge == 1) {
			//保存开通完需要整合用户操作
			ct.form('用户同步','?app=member&controller=setting&action=ucenter_sync&type=uc',370,100,function(json){
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
				'?app=member&controller=setting&action=merge&type=uc',
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