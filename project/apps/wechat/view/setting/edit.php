<div class="bk_8"></div>
<form name="wechat_add" id="type_edit" method="POST" action="?app=wechat&controller=setting&action=edit&wechatid=<?= $wechatid ?>">
    <div class="part">
        <table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form" id="add_table">
            <tr>
                <th><span class="c_red">*</span> 账号名称:</th>
                <td><input type="text" name="wxname" id="wxname" value="<?= $data['wxname'] ?>" size="36"/></td>
            </tr>		  
            <tr>
                <th><span class="c_red">*</span> 微信号:</th>
                <td><input type="text" name="account" id="account" value="<?= $data['account'] ?>" size="36" /></td>
            </tr>		  
            <tr>
                <th><span class="c_red">*</span> 密码:</th>
                <td><input type="password" name="repassword" id="repassword" value="" size="36" /></td>
            </tr>	  
            <tr>
                <th><span class="c_red">*</span> Token:</th>
                <td><input type="text" name="token" id="token" value="<?= $data['token'] ?>" size="36" maxlength="32" /></td>
            </tr>	  
            <tr>
                <th>启用:</th>
                <td><input type="checkbox" name="status" id="status" value="1" <?php if ($data['status']) echo 'checked'; ?> class="checkbox_style"/></td>
            </tr>
        </table>
        <div class="bk_8"></div>
    </div>
</form>