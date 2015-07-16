<?php $this->display('header', 'system');?>


<style>
.table_info th {
    text-align: center;
}.table_info td {
    border: 1px solid #D0E6EC !important;
    border-width: 1px 0 0 1px;
    text-align: center;
}
</style>
<script type="text/javascript" src="apps/exam/js/automatic_exam.js"></script>
<div class="bk_10"></div>
<form id="setting" action="" method="POST">
<table class="table_form mar_l_8" cellpadding="0" cellspacing="0" width="98%">
	<caption>自动出题</caption>
    <tr >
        <th width="100px"> 选择出题栏目：</th>
        <td><?=element::category('catid', 'catid', $catid)?></td>
    </tr>
	<tr >
		<th width="100px"> 卷名：</th>
		<td><input type="text" value="" name="title" size="100"></td>
	</tr>

    <tr>
        <th>简介：</th>
        <td><textarea name="description" id="description" cols="94" rows="4" class="focus"></textarea></td>
    </tr>
    <!--<tr>
        <th width="70"><span class="c_red">*</span> 科目：</th>
        <td><table width="480" border="0" cellspacing="0" cellpadding="0" class="table_info">
                <thead>
                <tr>
                    <th><div class="move_cursor"></div></th>
                    <th width="60">操作</th>
                </tr>
                </thead>
                <tbody id="subject" class="ui-sortable" unselectable="on">
                </tbody>
                <tr> <td colspan="2"><div class="mar_l_8 mar_5"><input name="add_option_btn" type="button" value="增加分类" class="hand button_style" onclick="automatic.add_tr('subject',<?/*=$pro_ids['subjectid']*/?>)"></div></tr>
            </table>
                </td>
    </tr>-->
    <tr>
        <th>科目：</th>
        <td><?=property_once('subject','subject',$pro_ids['subjectid'],$subjectid)?> </td>
    </tr>
    <tr>
        <th width="70"><span class="c_red">*</span> 考点：</th>
        <td><table width="480" border="0" cellspacing="0" cellpadding="0" class="table_info">
                <thead>
                <tr>
                    <th><div class="move_cursor"></div></th>
                    <th width="60">操作</th>
                </tr>
                </thead>
                <tbody id="knowledge" class="ui-sortable" unselectable="on">
                </tbody>
                <tr> <td colspan="2"><div class="mar_l_8 mar_5"><input name="add_option_btn" type="button" value="增加分类" class="hand button_style" onclick="automatic.add_tr('knowledge',<?=$pro_ids['knowledgeid']?>)"></div></tr>
            </table>
        </td>
    </tr>
    <tr>
        <th width="70"><span class="c_red">*</span> 类型：</th>
        <td><table width="580" border="0" cellspacing="0" cellpadding="0" class="table_info">
                <thead>
                <tr>
                    <th><div class="move_cursor"></div></th>
                    <th width="160">别名</th>
                    <th width="60">数量</th>
                    <th width="60">操作</th>
                </tr>
                </thead>
                <tbody id="qtype" class="ui-sortable" unselectable="on">
                </tbody>
                <tr> <td colspan="5"><div class="mar_l_8 mar_5"><input name="add_option_btn" type="button" value="增加分类" class="hand button_style" onclick="automatic.add_qtype2('qtype',<?=$pro_ids['qtypeid']?>)"></div></tr>
            </table>
        </td>
    </tr>

    <tr >
        <th width="100px">答题时间：</th>
        <td><input type="text" name="examtime" value="60" size="20"/></td>
    </tr>
    <tr >
        <th width="100px"> 每道题的论坛积分：</th>
        <td><input type="text" name="integral" value="" size="20"/></td>
    </tr>
    <tr>
        <th>每日一练：</th>
        <td><input type="checkbox" name="isday" id="isday" value="1" class="focus"> 是</td>
    </tr>
    <tr>
        <th>是否上线：</th>
        <td><input type="checkbox" name="pub" id="pub" value="1" class="focus" checked="checked"> 是</td>
    </tr>
	<tr>
		<th>&nbsp;</th>
		<td>
			<input type="submit" class="button_style" value="保存并生成试卷"/>
		</td>
	</tr>  

</table>
</form>
<script type="text/javascript">
$('#setting').ajaxForm(function(json){
		if (json.state) {
			ct.tips(json.info);
		} else {
			ct.error(json.error);
		}
	});
</script>
<?php $this->display('footer', 'system');?>