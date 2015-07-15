<div id="seo_option" onclick="toggle('#' + this.id)" class="mar_l_8 hand title" title="点击展开" style="display:;"><span class="span_open">SEO字段</span></div>
<table id="seo_option_sub" width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
    <tbody>
        <tr>							
            <th width="100">SEO标题：</th>							
            <td><input type="text" name="seotitle" id="seotitle" size="60" value="<?= $seotitle ?>" maxlength="80"></td>						
        </tr>
        <tr>
            <th>SEO描述：</th>
            <td>
                <textarea name="seodescription" id="seodescription" maxLength="255" style="width:627px;height:40px;" class="bdr"><?= $seodescription ?></textarea>
            </td>
        </tr>

        <tr>							
            <th width="100"><?= element::tips('结合栏目URL生成规则可以生成想要的静态文件名,参数为：{$seocode}') ?>静态化系数：</th>							
            <td><input type="text" name="seocode" id="seocode" size="32" value="<?= $seocode ?>" maxlength="30"></td>						
        </tr>
        <tr>
            <th><?= element::tips('打开SEO锁将不允许系统通过自动优化修改SEO参数') ?> SEO锁：</th>
            <td colspan="3"><label><input type="checkbox" name="seolock" id="seolock" value="1" <?php if ($seolock) echo 'checked'; ?> class="checkbox_style"/> 打开</label></td>
        </tr>
    </tbody>
</table>