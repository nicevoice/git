<?php $this->display('header', 'system');?>
<div class="bk_10"></div>
<div class="suggest w_650 mar_l_8">
	<h2>友情提示</h2>
	<p>该功能只提供简单的查看修改，如需要详细查看文件请通过FTP直接查看服务器上文件加以编辑。</p>
</div>
<div class="bk_10"></div>
<form id="edit_file" action="?app=system&controller=file&action=editunsafe&file=<?php echo $file;?>" method="POST">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>查看可疑文件代码</caption>
	<tr><th>特征函数：</th><td><?php foreach ($unsafecode['func'] as $key  => $value): if(!in_array($value[1],$temp)):$temp[] = $value[1];?><button type="button"><?php echo $value[1];?></button><?php endif;endforeach;?></td></tr>
	<tr><th>特征代码：</th><td><?php foreach ($unsafecode['code'] as $key  => $value):if(!in_array($value[1],$temp)):$temp[] = $value[1];?><button type="button"><?php echo $value[1];?></button><?php endif;endforeach;?></td></tr>
	<tr>
		<th width="80">文件内容：</th>
		<td><textarea rows="30" id="content" name="content" style="width:700px;"><?php echo $str;?></textarea></td>
	</tr>
	<tr><th></th><td><input type="submit" value="保存" class="button_style_2"/></td></tr>
</table>
</form>
<script type="text/javascript">
$(function() {
	$('#edit_file').ajaxForm(function(json){
		if(json.state) {
			ct.ok('操作成功');
			setTimeout(function(){location.reload()},500);
		} else {
			ct.error(json.error);
		}
	});
});
$("button[type=button]").click(function(){findInPage(document.getElementById('content'),this.innerHTML)});
var n = 0;
function findInPage(obj, str) {
	var txt, i, found;
	if (str == '') {
		return false;
	}

	if (document.layers) {
		if (!obj.find(str)) {
			while(obj.find(str, false, true)) {
				n++;
			}
		} else {
			n++;
		}
		if (n == 0) {
			alert('未找到指定字串！');
		}
	}

	if (document.all) {
		txt = obj.createTextRange();
		for (i = 0; i <= n && (found = txt.findText(str)) != false; i++) {
			txt.moveStart('character', 1);
			txt.moveEnd('textedit');
		}
		if (found) {
			txt.moveStart('character', -1);
			txt.findText(str);
			txt.select();
			txt.scrollIntoView();
			n++;
		} else {
			if (n > 0) {
				n = 0;
				findInPage(obj,str);
			} else {
				alert("未找到指定字串！");
			}
		}
	} else {
		var range = document.createRange(); 
		
	}
	return false;
}
</script>
<?php $this->display('footer', 'system'); ?>