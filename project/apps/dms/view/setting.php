<?php $this->display('header', 'system');?>
<link rel="stylesheet" type="text/css" href="apps/dms/css/style.css"/>
<style type="text/css">
.dms_content .button_style_1 { margin-left: 0; }
.dms_content .pagination { position: static; }
</style>
<?php $this->display('sider'); ?>
<div id="dms_content" class="dms_content">
	<div class="dms_inner">
        <div class="bk_8"></div>
        <form id="setting" method="POST" action="?app=<?=$app?>&controller=<?=$controller?>&action=index">
        <table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
            <caption>基础设置</caption>
            <tr>
                <th width="100">系统名称：</th>
                <td><input type="text" name="setting[name]" value="<?=$setting['name']?>" size="20"/></td>
            </tr>
            <tr>
                <th>系统状态：</th>
                <td><input type="radio" name="setting[status]" value="1" class="radio" <?php if ($setting['status'] == 1) echo 'checked';?>/> 启用 &nbsp; <input type="radio" name="setting[status]" value="0" class="radio" <?php if ($setting['status'] == 0) echo 'checked';?>/> 禁用</td>
                </td>
            </tr>
            <tr>
                <th>附件存储路径：</th>
                <td><input type="text" name="setting[pic_local_path]" value="<?=$setting['pic_local_path']?>" size="60"/></td>
            </tr>
			<tr>
				<th>允许附件类型：</th>
				<td><input type="text" name="setting[allowed_ext]" value="<?=$setting['allowed_ext']?>" size="60"/></td>
			</tr>
			<tr>
				<th>队列附件大小：</th>
				<td>
					<input type="text" name="setting[queue_size]" value="<?=$setting['queue_size']?>" size="4" />&nbsp;MB
				</td>
			</tr>
        </table>
        <div class="bk_8"></div>
        <table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
            <caption>Coreseek 设置</caption>
            <tr>
                <th width="100">主机地址：</th>
                <td><input id="host" type="text" name="setting[search_host]" value="<?=$setting['search_host']?>" size="20"/></td>
            </tr>
            <tr>
                <th>端口号：</th>
                <td><input id="port" type="text" name="setting[search_port]" value="<?=$setting['search_port']?>" size="10"/> <input type="button" value="连接测试" id="search_ping" class="button_style_1"/></td>
            </tr>
        </table>
        <div class="bk_8"></div>
        <table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
            <tr>
                <th width="100"></th>
                <td valign="middle">
                <input type="submit" id="submit" value=" 保存 " class="button_style_2"/>
                </td>
            </tr>
        </table>
        </form>
    </div>
</div>
<script type="text/javascript">
$(function(){
    function submit_ok(data) {
        if(data.state) {
            ct.tips(data.message);
        } else {
            ct.error(data.error);
        }
    }

    $('.tips').attrTips('tips', 'tips_green', 200, 'top');

	$('#setting').ajaxForm(function(response) {
		ct.tips('保存成功');
	});

    $("#search_ping").click(function(){
		var host = $("#host").val();
		var port = $("#port").val();
		$.ajax({
			type : 'POST',
			url :"?app=<?=$app?>&controller=<?=$controller?>&action=search_ping",
			data :{
				host	:host,
				port	:port
			},
			success : submit_ok,
			dataType : 'json'
		});
	});
});
</script>
<?php $this->display('footer', 'system');?>