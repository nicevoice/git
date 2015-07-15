<?php $this->display('header', 'system');?>
<!--tree-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tree/style.css"/>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.tree.js"></script>
<div style="width: 98%;" class="suggest mar_t_10 mar_l_8">
  <h2>友情提示</h2>
  <p> RSS 是站点用来和其他站点之间共享内容的简易方式（也叫聚合内容）。 RSS使用XML作为彼此共享内容的标准方式。它代表了Really Simple Syndication （或RDF Site Summary，RDF站点摘要）。它能让别人很容易的发现你已经更新了你的站点，并让人们很容易的追踪他们阅读的所有weblogs。<br />
  RSS输出地址：<a target="_blank" href="<?=APP_URL?>?app=rss&controller=index&action=index
"><?=APP_URL?>?app=rss&controller=index&action=index</a>
  </p>
</div>
<div class="bk_10"></div>
<form id="rss_setting" action="?app=rss&controller=setting&action=index" method="POST">
<table class="table_form mar_l_8" cellpadding="0" cellspacing="0" width="98%">
	<caption>RSS配置</caption>
	<tr>
		<th width="80" class="t_t">输出栏目：</th>
		<td id="category"></td>
	</tr>
	<tr>
		<th>权重范围：</th>
		<td><input name="setting[weight][min]" type="text" value="<?=$weight['min']?>" size="5"/> ~ <input name="setting[weight][max]" type="text" value="<?=$weight['max']?>" size="5"/></td>
	</tr>
	<tr>
		<th>内容输出：</th>
		<td>
			<label><input type="radio" name="setting[output]" <?=$output!='digest'?' checked="checked"' :''?> value="all"> 全文</label>
			<label><input type="radio" name="setting[output]" <?=$output=='digest'?' checked="checked"' :''?> value="digest"> 摘要</label>
		</td>
	</tr> 
	<tr>
		<th>输出条数：</th>
		<td>
			<input type="text" name="setting[size]" value="<?=$size?>" size="5"/>
		</td>
	</tr>
	<tr>
		<th>&nbsp;</th>
		<td>
			<input type="submit" class="button_style_2" value="保存"/>
		</td>
	</tr>  
</table>
</form>
<link href="<?=IMG_URL?>js/lib/treeview/treeview.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.treeview.js"></script>
<script type="text/javascript" src="apps/system/js/treeview_selector.js"></script>
<script type="text/javascript">
treeOptions = {
	url:"?app=system&controller=administrator&action=catetree&type=add&catid=%s",
	paramId : 'catid',
	paramHaschild:"hasChildren",
	renderTxt:function(div, id, item){
			var input = '<input type="checkbox" id="category_'+id+'" name="setting[category][]" value="'+id+'" class="radio_style" />';
		return $('<span id="'+id+'">'+input+'<label>'+item.name+'</span></label>');
	},
	itemReady:function() {
		$.each(<?=$categorys?>, function(i, id){
			$('#category_'+id).attr('checked', true);
		});
	},
	click : function(div, id, item){
		if(item.hasChildren)
		{
			div.find('input:checkbox').attr('checked')
			? div.next('ul').find('input:checkbox').attr('checked',true)
			: div.next('ul').find('input:checkbox').attr('checked', false);
		}
	}
};

$(function(){
	$('#category').tree(treeOptions);
	$('#rss_setting').ajaxForm(function(json){
		if (json.state) {
			ct.tips("保存成功");
		} else {
			ct.error(json.error);
		}
	});
});
</script>
<?php $this->display('footer', 'system');?>