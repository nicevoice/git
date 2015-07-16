<div class="bk_8"></div>
<form name="type_add" id="type_add" method="POST" action="?app=cdn&controller=setting&action=add">
	<div class="part">
		<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form" id="add_table">
		  <tr>
			<th><span class="c_red">*</span> 接口名称:</th>
			<td><input type="text" name="name" id="name" value="" size="20"/></td>
		  </tr>
		  <tr>
			<th><span class="c_red">*</span> 类型：</th>
			<td>
				<select name="type">
					<option value=''>请选择</option>
					<?php foreach($type as $item):?>
					<option value="<?=$item?>"><?=$item?></option>
					<?php endforeach;?>
				</select>
			</td>
		  </tr>
		</table>
		<div class="bk_8"></div>
		<table id="paralist">
		</table>
		<a href="javascript:;" onclick="addLine();return false;" style="display: table; margin: 0pt auto;">添加</a>
	</div>
</form>
<script type="text/javascript">
var i		= 0
var paraLi	= '';
var paralist	= $("table#paralist");
function addLine() {
	paraLi	= '<tr><td>参数:<input type="text" name="para[' + i + '][name]" value="" />说明:<input type="text" name="para[' + i + '][info]" value="" /> <img src="/images/delete.gif" onclick="deleteLine(this);" /></td></tr>'
	paralist.append(paraLi);
	i++;
}

function deleteLine(obj) {
	$(obj).parent().parent().remove();
}

$(document).ready(function() {
	var divs = $('div.part');
	$('div.tabs>ul').tabnav({
		dataType:null,
		forceFocus:true,
		focused:function(li){
			divs.hide();
			divs.eq(li.attr('index')).show();
		}
	});
});
</script>