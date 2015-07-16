<div class="bk_8"></div>
<form name="picture_remote" id="picture_remote" method="POST" action="?app=picture&controller=picture&action=remote">
    <input type="hidden" name="catid" value="<?=$catid?>"/> <input type="hidden" name="status" value="<?=$status?>"/>
    <input type="hidden" name="modelid" value="<?=$modelid?>"/>
    <table width="99%" border="0" cellspacing="0" cellpadding="0" class="table_form">
        <tr>
            <?php if ($single): ?>
            <td>远程图片地址：</td>
            <?php else: ?>
            <td>远程图片地址（每个一行）：</td>
            <?php endif; ?>
        </tr>
        <tr>
            <td>
                <?php if ($single): ?>
                <input type="text" name="remote_pictures" id="remote_pictures" style="display:inline-block;width:96%;" />
                <?php else: ?>
                <textarea name="remote_pictures" id="remote_pictures" style="width:96%;height:100px" class="bdr"></textarea>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</form>