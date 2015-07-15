<div class="bk_8"></div>
<form name="question_edit" method="POST" action="?app=interview&controller=question&action=edit">
    <input type="hidden" name="questionid" value="<?=$questionid?>" />
    <table width="99%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
        <tr>
            <th width="60"><span class="c_red">*</span> 昵称：</th>
            <td><input type="text" name="nickname" value="<?=$nickname?>" size="30"/></td>
        </tr>
        <tr>
            <th><span class="c_red">*</span> 内容：</th>
            <td>
                <textarea name="content" rows="5" cols="60" style="width:340px;"><?=$content?></textarea>
            </td>
        </tr>
        <tr>
            <th><span class="c_red">*</span> IP：</th>
            <td><input type="text" name="ip" value="<?=$ip?>" size="15" /> </td>
        </tr>
    </table>
</form>