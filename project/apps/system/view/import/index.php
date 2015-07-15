<?php $this->display('header', 'system');?>
<div class="bk_10"></div>
<div class="suggest w_650 mar_l_8">
  <h2>友情提示</h2>
  <p> 第三方数据导入功能是CmsTop特色功能之一，通过本功能能将外部 Mysql 数据库的已有内容导入到本地数据库。从而快速便捷地实现数据迁移。
<br/>通过自定义导入规则，您可以将任何CMS系统生成的数据导入到本系统，系统内置了部分常用系统的导入规则，请根据实际情况修改配置。
本系统可以实现分批导入。</p>
</div>
<div class="bk_8"></div>
<form id="myform" method="POST" action="?app=system&controller=import&action=next">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>添加数据导入规则</caption>
	<tr>
		<th width="120"><span class="c_red">*</span>数据导入类型：</th>
		<td><input type="radio" name="types" value="article" checked="checked" /> 文章 <input type="radio" name="types" value="member" /> 会员</td>
	</tr>
	<tr>
		<th valign="top">请选择配置文件：</th>
		<td>
			<div id="articleList">
				<input type="radio" class="radio_style" name="configfile" value="custom" checked="checked" />新建规则<br/>
			<?php foreach ($list['article'] as $key => $value) { ?>
				<input type="radio" class="radio_style" name="configfile" value="<?php echo $value;?>" /><?php echo $value; ?> <a href="javascript:;" onclick="delete_file('article','<?php echo $value; ?>')">[删除]</a><br/>
			<?php } ?>
			</div>
			<div id="memberList" style="display:none">
				<input type="radio" class="radio_style" name="configfile" value="custom"/>新建规则<br/>
			<?php foreach ($list['member'] as $key => $value) { ?>
				<input type="radio" class="radio_style" name="configfile" value="<?php echo $value;?>" /><?php echo $value; ?> <a href="javascript:;" onclick="delete_file('member','<?php echo $value; ?>')">[删除]</a><br/>
			<?php } ?>
			</div>
		</td>
	</tr>
	<tr><th></th><td>
		<div class="tips"><span class="tl"></span><span class="tips_arrow"></span><span class="tr"></span>
			<div class="tips_c t_c" id="tips_data">自定义新的导入任务,自行配置导入规则</div>
			<span class="bl"></span><span class="br"></span>
			<div class="clear"></div>
		</div>
	</td></tr>
	<tr><th></th>
		<td>
			<input type="submit" name="submit" class="button_style_1" value="下一步" />
	</td></tr>
</table>
</form>
<style>
.tips{background:#fffdd7; border:1px solid #fed669; position:relative; width:300px; color:#f60; margin:6px;}
.tl,.tr,.bl,.br,.tips_arrow{background:url(images/bg.gif) no-repeat -14px -557px;width:4px;height:4px; position:absolute;overflow:hidden; display:block;}
.tl{top:-1px; left:-1px;}
.tr{top:-1px; right:-1px; background-position:-14px -561px;}
.bl{bottom:-1px; left:-1px; background-position:-14px -565px;}
.br{bottom:-1px; right:-1px; background-position:-14px -569px;}
.tips_arrow{top:-4px; left:10px; background-position:-11px -573px; width:7px;}
.tips_c{padding:6px 10px; line-height:150%;}
</style>
<script type="text/javascript">
function delete_file(type,name) {
	ct.confirm('确定删除吗',function(dialog){
		$.post("?app=system&controller=import&action=delete",{type:type,name:name}, function(data){
			if(data.state) {
				ct.ok('配置文件删除成功!',function(){
					setTimeout('location.reload()',500);
				});
			}else{
				ct.error('配置文件删除失败!');
			}
		},'json');
	},function(dialog){});
}

$(document).ready(function() {
	$("input[name=types]").click(function(e){
		t = e.target.value;
		s = (t == 'member')?'article':'member';
		$('#'+t+'List').show().children('input').eq(0).attr("checked","checked");
		$('#tips_data').html('自定义新的导入任务，自行配置导入规则');
		$('#'+s+'List').hide();
	});
	$("input[name=configfile]").click(function(e){
		t = e.target.value;
		if(t != 'custom') {
			var type = $("input[name=types]:checked").val();
			var url = "?app=system&controller=import&action=getinfo&type="+type+"&name="+t;
			$.getJSON(url,function(json){
				$('#tips_data').html(json.data);
			}); 
		} else {
			$('#tips_data').html('自定义新的导入任务，自行配置导入规则');
		}
	});
});

</script>
<?php $this->display('footer', 'system');?>