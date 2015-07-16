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
<form action="" method="POST">
<table class="table_form mar_l_8" cellpadding="0" cellspacing="0" width="98%">
	<caption>检查出题</caption>
    <tr>
        <th>科目：</th>
        <td><?=property_once('subjectid','subjectid',$pro_ids['subjectid'],$subjectid)?> </td>
    </tr>

	<tr>
		<th>&nbsp;</th>
		<td>
			<input type="submit" class="button_style" value="检查URL"/>
		</td>
	</tr>  

</table>
</form>
<?php $this->display('footer', 'system');?>