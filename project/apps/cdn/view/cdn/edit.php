<div class="tabs">
	<ul>
		<li index="0"><a href="javascript:;">CDN修改</a></li>
		<li index="1"><a href="javascript:;">规则管理</a></li>
	</ul>
</div>
<div class="bk_8"></div>
<div class="part">
	<form name="cdn_add" id="cdn_add" method="POST" action="?app=cdn&controller=cdn&action=edit&cdnid=<?=$cdnid?>">
	<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form" id="edit_table">
	  <tr>
		<th><span class="c_red">*</span> CDN名称：</th>
		<td><input type="text" name="name" id="name" value="<?=$cdn['name']?>" size="20"/></td>
	  </tr>
	  <tr>
		<th><span class="c_red">*</span> 类型：</th>
		<td>
			<select name="tid">
				<option value=''>请选择</option>
				<?php foreach($type as $item):?>
				<option value="<?=$item['tid']?>" par='<?=$item["parameter"]?>'<?php if($cdn['tid']==$item['tid']):?> selected="selected"<?php endif;?>><?=$item['name']?></option>
				<?php endforeach;?>
			</select>
		</td>
	  </tr>
	</table>
	</form>
</div>
<div class="part">
	<div class="table_head">
		<input type="button" name="" value="添加" class="button_style_2 f_l" onclick="cdn.add_rules();"/>
		<input type="button" name="" value="快速" class="button_style_2 f_l" onclick="quick_add();"/>
	</div>
	<div class="bk_8"></div>
	<table width="98%" cellpadding="0" cellspacing="0" id="rules_list" class="tablesorter table_list" style="margin-left:6px;">
	  <thead>
		<tr>
		  <th width="20%" class="bdr_3">ID</th>
		  <th width="30%">路径</th>
		  <th width="30%">URL</th>
		  <th width="20%">管理操作</th>
		</tr>
	  </thead>
	  <tbody id="rules_list_body">
	  </tbody>
	</table>
</div>
<script type="text/javascript">
var cdnid	= <?=$cdnid?>;
var select	= $("select[name='tid']");
var checkedpsn	= new Array();
function init() {
	<?php foreach($par as $key=>$item):?>
	var c = "input[name='par[<?=$item['key']?>]']";
	$("tr.par").find(c).val("<?=$item['value']?>");
	<?php endforeach;?>
}
function selectChange() {
	try {
		var par	= eval('('+select.find(':selected').attr('par')+')');
	}
	catch (e) {
		//no par
	}
	if (!par) return;
	$("tr.par").remove();
	$.each(par, function(k,v) {
		var s	= '<tr class="par"><th>'+v+'</th><td><input type="text" name="par['+k+']" value="" size="20"/></td></tr>';
		$("#edit_table").append(s);
	});
}

function init_row_event(id, tr)
{
	tr.find('img.edit_rules').click(function(){
		cdn.edit_rules(id);
	});
	tr.find('img.delete_rules').click(function(){
		cdn.delete_rules(id);
	});    
}

function quick_add() {
	ct.ajaxDialog('快速选择','?app=cdn&controller=cdn&action=getpsn', null, function(c) {
		var list	= $(c).find('#psn_list').find('tbody').find('tr');
		var data	= {};
		var index	= 0;
		$.each(list, function(i, v) {
			var v = $(v);
			if (v.find('input').attr('checked')) {
				var path	= v.find('.path').html();
				var url		= v.find('.url').html();
				var flag	= true;
				$.each($('table#rules_list').find('td.url'), function(i, rules_url) {
					if (url == $(rules_url).html()) {
						flag = false;
					}
				});
				if (flag) {
					$.post('?app=cdn&controller=rules&action=add', {'cdnid':cdnid, 'path':path , 'url':url}, function(json) {
						//
					}, 'json');
				}
			}
		});
		rules_tableApp.reload();
		return true;
	}, function() {
		rules_tableApp.reload();
		return true;
	});
}
var rules_row_template = '<tr id="rules_row_{id}">\
	                 	<td class="t_c">{id}</td>\
	                	<td class="t_l">{path}</td>\
	                	<td class="t_l url">{url}</td>\
	                	<td class="t_c"><img src="images/edit.gif" alt="编辑" width="16" height="16" class="hand edit_rules"/> &nbsp;<img src="images/delete.gif" alt="删除" width="16" height="16" class="hand delete_rules" /></td>\
	                </tr>';

var rules_tableApp = new ct.table('#rules_list', {
    rowIdPrefix : 'rules_row_',
    pageField : 'rules_page',
    pageSize : 10,
    template : rules_row_template,
	dblclickHandler : cdn.edit_rules,
    baseUrl  : '?app=<?=$app?>&controller=rules&action=page&cdnid='+cdnid
});
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
	select.change(selectChange);
	selectChange();
	rules_tableApp.load();
	init();
});

</script>