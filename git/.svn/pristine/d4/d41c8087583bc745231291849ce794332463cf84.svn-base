<div>
<div class="bk_8"></div>
<form name="member_index_edit" id="member_index_edit" method="POST" class="validator" action="?app=member&controller=index&action=edit&userid=<?=$userid?>">
<table border="0" cellspacing="0" cellpadding="0" class="table_form">
	<tbody id="profilebasic">
		<tr>
			<th>用户名：</th>
			<td><?=$username?></td>
		</tr>
		<tr>
			<th>用户组：</th>
			<td id="group_td" class="lh_24"><?=element::member_groups(5, 'groupid', $groupid);?></td>
			
		</tr>
		<tr>
			<th>E-mail：</th>
			<td><input type="text" name="email" id="email" value="<?=$email?>" size="30" /></td>
		</tr>
		<tr>
			<th width="100">真实姓名：</th>
			<td><input type="text" name="name" id="name" value="<?=$name?>" size="12" /></td>
		</tr>
		<tr>
			<th>性别：</th>
			<td>
			<input class="radio radio_style" type="radio" name="sex" value="1" <? if($sex == 1){?> checked<? }?> /> 男 
			<input class="radio radio_style" type="radio" name="sex" value="2" <? if($sex == 2){?> checked<? }?> /> 女 </td>
		</tr>
		<tr>
			<th>生日：</th>
			<td>
			<select name="birthday[year]" id="year" style="width:60px"><option value="0">----</option></select>&nbsp;年 
			<select name="birthday[month]" id="month"><option value="0">----</option></select>&nbsp;月 
			<select name="birthday[day]" id="day"><option value="0">----</option></select>&nbsp;日</td>
		</tr>
		<tr>
			<th>电话：</th>
			<td><input type="text" name="telephone" id="telephone" value="<?=$telephone?>" size="16" /></td>
		</tr>
		<tr>
			<th>手机：</th>
			<td><input type="text" name="mobile" id="mobile" value="<?=$mobile?>" size="20" /></td>
		</tr>
		<tr>
		<th>职业：</th>
			<td><input type="text" name="job" id="job" value="<?=$job?>" size="10"/></td>
		</tr>
		<tr>
			<th>地址：</th>
			<td><input type="text" name="address" id="address" value="<?=$address?>" size="50" /></td>
		</tr>
		<tr>
			<th>邮编：</th>
			<td><input type="text" name="zipcode" id="zipcode" value="<?=$zipcode?>" size="20" /></td>
		</tr>
		<tr>
			<th>QQ：</th>
			<td><input type="text" name="qq" id="qq" value="<?=$qq?>" size="16" /></td>
		</tr>
		<tr>
			<th>MSN：</th>
			<td><input type="text" name="msn" id="msn" value="<?=$msn?>" size="30" /></td>
		</tr>
		<tr>
			<th>备注：</th>
			<td><textarea name="remarks" id="remarks" style="width:220px;height:25px;"><?=$remarks?></textarea></td>
		</tr>
	</tbody>
</table>
</form>
</div>
<script language="JavaScript" type="text/JavaScript">
	var u_birthday = "<?=$birthday?>";
	var days = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	var selectStr = '<option value="0">----</option>';
	var check_m_d = false;
	function isLeap(year) {
		return ((0 == year % 4) && (0 != (year % 100))) || (0 == year % 400) ? true : false; 
	}
	function isDate(date) {
		var d = date.split('-');
		return (d[0] =='0000' || d[1] == '00' || d[2] == '00')?false:true;
	}
	function setYear(b) {
			var html = '';
			var now = new Date(); 
			var year = now.getFullYear(); 
			var select = '';
			var b_array =new Array();
			if(isDate(b)) {
				b_array = b.split('-');
				check_m_d = true;
			} else {
				b_array = [0,0,0];
			}
			
			for(var i=year;i>1909;i--) {
				select = (i ==b_array[0] )?' selected="selected"':'';
				html += '<option value="'+i+'" '+select+'>'+i+'</option>';
			}
			$("#year").append(html);
			if(check_m_d) {
				setMonth(b_array[1]);
				setDay(b_array[0],b_array[1],b_array[2]);
			}
	};
	function setMonth(m) {
		var html = selectStr;
		for(var i=1;i<13;i++) {
			i = (i>=10) ? i : '0' + i;
			select = (i ==m )?' selected="selected"':'';
			html += '<option value="'+i+'" '+select+'>'+i+'</option>';
		}
		$("#month").empty().append(html);
	}
	function setDay(y,m,d) {
		ds = days[m-1];
		if(isLeap(y) && (m == '02')) {
			ds++;
		}
		var html = selectStr;
		for(var i=1;i<=ds;i++) {
			i = (i>=10) ? i : '0' + i;
			select = (i == d )?' selected="selected"':'';
			html += '<option value="'+i+'" '+select+'>'+i+'</option>';
		}
		$("#day").empty().append(html);
	}
	$(document).ready(function(){
		var y = $("#year");
		var m = $("#month");
		var d = $("#day");
		var daySelect = function(){
			if(y.val() ==0 || m.val() == 0) {
				d.empty().append(selectStr);
				return;
			}
			setDay(y.val(),m.val(),1);
		};
		var daySelect2 = function() {
			if(y.val() == 0) {
				m.empty().append(selectStr);
				d.empty().append(selectStr);
				return;
			} else {
				var m_set = m.val()>1?m.val():1;
				setMonth(m_set);
				setDay(y.val(),m_set,1);
			}
		};
		y.change(daySelect2);
		m.change(daySelect);
		setYear(u_birthday);
		if($("#group_td > select").val()==1) {
			$("#group_td > select")[0].disabled = true;
		} else {
			$("#group_td > select > option:lt(2)").remove();
		}
	});
</script>