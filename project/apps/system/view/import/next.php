<?php $this->display('header', 'system');?>
<div class="bk_8"></div>
<div>
<form id="imports_add" name="imports_add" method="post" action="?app=system&controller=import&action=add">
<input name="type" type="hidden" value="<?=$types?>" />
<input name="save" type="hidden" id="saveid" value="0" />
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>数据导入配置</caption>
	<tr>
		<th width="150"><span class="c_red">*</span>配置文件名：</th>
		<td><input type="text" name="setting[names]" value="<?=$names?>" onblur="checkname(this.value,'<?=$types?>');" /><span id="nameMessage"></span><?=element::tips('只能由小写字母和数字组成')?></td>
	</tr>
	<tr>
		<th>配置说明：</th>
		<td><input type="text" name="setting[notes]" value="<?=$setting['notes']?>" size="40"/></td>
	</tr>
	<tr>
		<th><span class="c_red">*</span>数据库类型：</th>
		<td>
			<label><input type="radio" name="setting[dbtype]" class="radio_style" onclick="change_value(this.value)" value="mysql" <?php echo ($setting['dbtype']=='mysql' || $setting['dbtype']=='') ? 'checked="checked"' : ''?>/> MySQL </label>
		</td>
	</tr>
	<tr>
		<th><span class="c_red">*</span><span id="myhost" >主机地址</span>：</th>
		<td><input type="text" id="dbhost" name="setting[dbhost]" value="<?=$setting['dbhost']?>" size="20"/>
		<?=element::tips('不同的数据库类型使用不同的配置')?>
		</td>
	</tr>
    <tr>
		<th><span class="c_red">*</span><span id="myport" >主机端口</span>：</th>
		<td><input type="text" id="dbport" name="setting[dbport]" value="<?=$setting['dbport']?>" size="20"/>
		<?=element::tips('缺省为 3306')?>
		</td>
	</tr>
	<tr>
		<th><span class="c_red">*</span>用户名：</th>
		<td><input type="text" id="dbuser" name="setting[dbuser]" value="<?=$setting['dbuser']?>" size="20"/></td>
	</tr>
	<tr>
		<th><span class="c_red">*</span>密码：</th>
		<td><input type="text" id="dbpw" name="setting[dbpw]" value="<?=$setting['dbpw']?>" size="20" ></td>
	</tr>
	<tr id="db_charset">
		<th><span class="c_red">*</span>字符集：</th>
		<td><?php echo element::charset($setting['charset'],'setting[charset]')?></td>
	</tr>
	<tr>
		<th><span class="c_red">*</span>数据库名称：</th>
		<td><input type="text" id="dbname" name="setting[dbname]" value="<?=$setting['dbname']?>" size="20" />&nbsp;&nbsp;
		<input type="button" class="button_style_1" onclick="checkLinks();" value="测试连接" /> &nbsp;<span id="checkLinksInfo"></span>
		</td>
	</tr>
	<tr>
		<th><span class="c_red">*</span>数据表：</th>
		<td><input type="text" id="db_tables" name="setting[table]" value="<?=$setting['table']?>" size="80" /> <span id="select_tables"></span><?=element::tips('多个表建议使用 LEFT JOIN 或 RIGHT JOIN 联结，严重不推荐逗号分隔，执行速度会大为降低')?></td>
	</tr>
	<tr>
		<th>数据提取条件：</th>
		<td><input type="text" name="setting[condition]" value="<?=$setting['condition']?>" size="80" /></td>
	</tr>
	<tr>
		<th>上次导入最大ID：</th>
		<td><input type="text" name="setting[maxid]" value="<?=$setting['maxid'] ? $setting['maxid'] : 0?>" size="10" maxlength="10" />
		<?=element::tips('请确保此值小于源数据的总数大小')?>
		</td>
	</tr>
</table>

<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>数据表字段对应关系</caption>
	<tr>
		<th width="200" >&nbsp;</th>
		<td width="220">源数据表<?php if($types=='member'){?>用户<?php }else{?>文章<?php }?>字段</td>
		<td width="150">默认值</td>
		<td >处理函数</td>
	</tr>
	<?php if(is_array($fields) && !empty($fields)) { foreach($fields as $k=>$field) { ?>
	<tr>
		<th width="200"><?=$field['name']?>&nbsp;(<?=$k?>)：</th>
		<td ><input name="setting[<?=$k?>][field]" id="field_<?=$k?>" class="fields" type="text" size="35" value="<?=$setting[$k]['field']?$setting[$k]['field']:''?>" > <span></span></td>
		<td><input type="text" name="setting[<?=$k?>][value]" value="<?=$setting[$k]['value']?$setting[$k]['value']:''?>" ></td>
		<td><input name="setting[<?=$k?>][func]" type="text" value="<?=$setting[$k]['func']?$setting[$k]['func']:''?>" /></td>
	</tr>
	<?php } } ?>
</table>

<!-- type begin -->
<?php if($types=='member'){?>
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>用户组对应关系</caption>
	<tr>
		<th width="150">默认导入用户组：</th>
		<td>
		<?php echo element::member_groups($groups, 'setting[defaultgroupid]',$setting['defaultgroupid'])?>
		</td>
	</tr>
	<tr>
		<th>系统会员组</th>
		<td>原系统会员组 ID <?=element::tips('多个ID请用逗号(,)分隔')?></td>
	</tr>
	<?php foreach($groups as $k=>$group){?>
	<tr>
		<th><?=$group['name'];?>：</th>
		<td><input type="text" name="setting[groupids][<?=$group['groupid'];?>]" size="35" <?php if(isset($setting['groupids'][$k])){?>value="<?=$setting['groupids'][$k]?>" <?php } ?> /> </td>
	</tr>
	<?php }?>
</table>

<?php }else{?>

<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>文章栏目对应关系</caption>
	<tbody>
	<tr>
		<th width="200">默认导入栏目：</th>
		<td><?php echo element::category('defaultcatid', 'setting[defaultcatid]',$setting['defaultcatid'])?></td>
	</tr>
	<tr>
		<th>现有系统文章栏目[ID]</th>
		<td>原系统栏目ID <?=element::tips('多个ID请用逗号(,)分隔')?></td>
	</tr>
	<?php if(is_array($category))foreach ($category as $keys=>$value) {?>
	<tr>
		<th><?=$value['name']?> [<?=$value['catid']?>]：</th>
		<td><input gtbfieldid="<?=$keys?>" class="input_blur" name="setting[catids][<?=$value['catid']?>]"  <?php if(isset($setting['catids'][$value['catid']])){?>value="<?=$setting['catids'][$value['catid']]?>" <?php } ?>size="35" type="text"> </td>
	</tr>
	<?}?>
	</tbody>
</table>
<?php } ?>
<!-- type end -->

<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption><?php if($types=='member'){?>用户<?php }else{?>文章<?php }?>数据导入执行设置</caption>
	<?php if($types=='member'){?>
	<tr>
		<th width="150">检查同名帐号或邮件 <?=element::tips('如果选“是”，则系统会自动丢弃同名帐号')?></th>
		<td><input type="radio" name='setting[membercheck]' value='1' <? if($setting['membercheck'] || empty($setting['membercheck'])) { ?>checked<? } ?>> 是&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name='setting[membercheck]' value='0' <? if($setting['membercheck'] == 0) { ?>checked<? } ?>> 否 </td>
	</tr>
	<?php } ?>
	<tr>
		<th>每次提取并导入数据条数：</th>
		<td><input type="text" name="setting[number]" value="<?=$setting['number']?$setting['number']:100?>" size="20"></td>
	</tr>
	<tr>
		<th>php脚本执行超时时限：</th>
		<td><input type="text" name="setting[expire]" value="<?=$setting['expire']?$setting['expire']:90?>"> <?=element::tips('当数据较多时程序执行时间会较长')?></td>
	</tr>
	<tr><th>&nbsp;</th><td></td></tr>
	<tr>
		<th>&nbsp;</th>
		<td colspan="2">
			<input type="submit" name="dosubmit" value="保存配置" class="button_style_4" /> &nbsp;&nbsp;
			<input type="submit" name="savesubmit"  id="savesubmit"  value=" 保存并执行导入" class="button_style_1" />
			<div id="showprogressbar" style="display:none;padding-top:10px;text-align:center;line-height:26px;">
				<div class="progressbar" id="uploadprogressbar">0%</div>
				<div id="importProgressInfo"></div>
			</div>
		</td>
	</tr>
</table>
</form>
</div>
<div class="bk_8"></div>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.blockUI.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.progressbar.js"></script>
<script type="text/javascript">
$.fn.floatDiv=function(options) {
	var defaults = {
		hiddenid:'cmstopHiddenImg',//隐藏层id
		thtml:'body',
		InnerHtmls:'CMSTOP漂浮层内容!',
		Xoffset:'6',//距离鼠标X值
		Yoffset:'10',//距离鼠标Y值
		background: '#ccc',//显示层的背景
		padding:'5px',
		border:'1px solid #fff',
		index:'888888888',//z-index
		width: '',//层的宽度.默认自适应
		height:''//层的高度.默认自适应
	};
	var opts = $.extend(defaults, options);
	$(opts.thtml).append('<div id="'+opts.hiddenid+'"></div>'); 
	var hiddenObject = $("#"+opts.hiddenid);
	hiddenObject.css({//给对象定义样式
		'position':'absolute',
		'overflow':'hidden',
		'display':'none',
		'z-index':opts.index,
		'cursor' : 'pointer'
	});
	
	//清除原来的内容
	hiddenObject.html('');
	var offset = $(this).offset();
	var left=offset.left+parseInt($(this).width())+parseInt(opts.Xoffset)+"px";
	var top =offset.top-1+"px";
	
	var imges='<div>';
	imges+='<div ><a style="color:red;" href="javascript:;" id="Close_FloatDiv" title="关闭">关闭</a></div>';
	imges+='<div>';
	imges+=opts.InnerHtmls;
	imges+='</div>';
	imges+='</div>';
	hiddenObject.html(imges);
	
	hiddenObject.css({//给返回的数据定义样式
		'position':'absolute',
		'overflow':'hidden',
		'top':top,
		'left':left,
		'padding':opts.padding,
		'background':opts.background,
		'border':opts.border,
		'z-index':opts.index,
		'cursor' : 'pointer',
		'width':opts.width,
		'height':opts.height
	}).show();
			
	$("#Close_FloatDiv").bind('click',function(){
		hiddenObject.html('');
		hiddenObject.hide();
		return false;
	});
};
</script>
<script type="text/javascript">
var thisConfigName = '<?=$names?>';
var ajaxClose = false;
var offset = <?=$setting['maxid'] ? $setting['maxid'] : 0?>;

function change_value(value) {
	if(value == "odbc"){
		$("#myhost").html("DSN名");
	} else if (value == "access_odbc" ){
		$("#myhost").html("Access文件地址");
	}else{
		$("#myhost").html("主机地址");
	}
	checkLinks();
}
function checkLinks() {
	var check = false;
	$("input:checked[name=setting\[dbtype\]],#dbhost,#charset").each(function(){
		if($(this).val() == '') check = true;
	});
	if(check) return;
	
	$.post("?app=system&controller=import&action=testhost",{
        dbtype:$("input:checked[name=setting\[dbtype\]]").val(),
        dbhost:$('#dbhost').val(),
        dbport:$("#dbport").val(),
        dbuser:$('#dbuser').val(),
        dbpw:$('#dbpw').val(),
        dbname:$('#dbname').val(),
        charset:$('#charset').val() || ''
    }, function(data){
		if(data.state) {
			$.growlUI('恭喜您', '数据库连接成功!');
			$("#checkLinksInfo").html('<img src="images/sh.gif" align="middle"/>');
			$("#savesubmit").attr('disabled','');
		} else {
			$.growlUI('对不起', '数据库连接出现错误!请检查连接信息！'); 
			$("#savesubmit").attr('disabled','disabled');
			$("#checkLinksInfo").html('<img src="images/del.gif" align="middle"/>');
		}
	},'json');
	setTimeout($.unblockUI, 2000); 
}

$(document).ready(function() {
	$.validate.setConfigs({
		xmlPath:'/apps/system/validators/import/'
	});
	$.post("?app=system&controller=import&action=testhost",{
        dbtype:$("input:checked[name=setting\[dbtype\]]").val(),
        dbhost:$('#dbhost').val(),
        dbport:$("#dbport").val(),
        dbuser:$('#dbuser').val(),
        dbpw:$('#dbpw').val(),
        dbname:$('#dbname').val(),
        charset:$('#charset').val() || ''
    }, function(data){
		if(data.state) {
			$("#savesubmit").attr('disabled','');
		}else{
			$("#savesubmit").attr('disabled','disabled');
		}
	},'json');
	$('#savesubmit').click(function() {
		ajaxClose = false;
		$('#saveid').val('1');
		var box = $('#showprogressbar');
		if (box.data('init')) {
			box.dialog('open');
		} else {
			box.dialog({
				autoOpen:true,
				width  : 360,
				height : 150,
				modal  : true,
				title  : '正在导入数据....',
				buttons:{
					'暂停':function(){
						box.dialog('close');
						var url = "?app=system&controller=import&action=getinfo&type=<?=$types?>&name=<?=$setting['names']?>";
						$.getJSON(url,function(json){
							$("input[name=setting\[maxid\]]").val(json.offset);
						});
					},
					'终止':function() {
						box.dialog('close');
						var url = "?app=system&controller=import&action=getinfo&type=<?=$types?>&name=<?=$setting['names']?>";
						$.getJSON(url,function(json){
							var html = '总共已导入数据 '+json.offset+' 条 ，本次导入'+(json.offset-offset)+'条';
							ct.ok(html);
							setTimeout(function(){
									window.location.href='?app=system&controller=import&action=index';
							}, 1000);
						});
					}
				},
				close: function(){
					setTimeout($.unblockUI,100);
					ajaxClose = true;
				}
			}).data('init',1);
		}
		
		$.blockUI({
				message: '',
				css:{
					top:  ($(window).height() - 200) /2 + 'px', 
					left: ($(window).width() - 200) /2 + 'px', 
					width: '200px',
					height:'30px'
				}
		}); 
	});
	
	$('#imports_add').ajaxForm('content_add_import_ok');

});

function content_add_import_ok(response) {
	if(response.save == 1) {
		if (response.state) {
			$("#uploadprogressbar").progressBar(100,{barImage:'images/progressbg_yellow.gif'} );
			$("#importProgressInfo").html("已导入 "+response.total+" 条数据,共 "+response.total+" 条");
			setTimeout($.unblockUI,1000);
			setTimeout(function(){
				$('#showprogressbar').dialog('close');
				ct.ok('数据导入成功!');
				setTimeout(function(){window.location.href=response.url;}, 1000);
			}, 5000);
		} else {
			var percentage = Math.floor(100 * parseInt(response.offset) / parseInt(response.total));
			$("#uploadprogressbar").progressBar(percentage,{barImage:'images/progressbg_yellow.gif'} );
			$("#importProgressInfo").html("已导入 "+response.offset+" 条数据,共 "+response.total+" 条");
			setTimeout('add_data("'+response.url+'")', 400);
		}
	} else {
		//只是保存
		if(response.state) {
			ct.ok('配置信息保存成功!');
			setTimeout(function(){window.location.href=response.url;}, 1000);
		} else {
			ct.error(response && response.error || '发生错误');
			$.unblockUI();
		}
	}
}

function add_data(url) {
	if(!ajaxClose) {
		$.getJSON(url, function(response) {
			content_add_import_ok(response);
		});
	}
}


$(document).ready(function() {
	$("#uploadprogressbar").progressBar();
});

function checkname(name,type){
	var allow = /^[a-zA-Z0-9]+$/;
	if(!allow.test(name)) {
		return ;
	}
	if(name !='' && thisConfigName != name){
		$.post("?app=system&controller=import&action=checkname",{name:name,type:type}, function(data){
			if(data.state){
				$("#nameMessage").html('<font color="green">此名称可用!</font>');
			}else{
				$("#nameMessage").html('<font color="red">此名称的配置文件已存在!</font>');
			}
		},'json');
	}
}

function in_tables(val) {
	if($('#db_tables').val()!=''){
		$('#db_tables').val($('#db_tables').val()+','+val);
	} else {
		$('#db_tables').val(val);
	}
}

var html='';
var id = '';

$("#dbname").focus(function() {
	//判断如果连接信息是否填写
	if($("input:checked[name=setting\[dbtype\]]").val()!='mysql') return;
	$.post("?app=system&controller=import&action=database",{
        dbtype:$("input:checked[name=setting\[dbtype\]]").val(),
        dbhost:$('#dbhost').val(),
        dbport:$("#dbport").val(),
        dbuser:$('#dbuser').val(),
        dbpw:$('#dbpw').val(),
        dbname:$('#dbname').val(),
        charset:$('#charset').val() || ''
    }, function(data){
		if(data.state) {
			//ct.alert(data.datas);
			$("#dbname").floatDiv({InnerHtmls:data.datas});
			$("#savesubmit").attr('disabled','');
		}else{
			ct.error('数据库连接出现错误!');
			$("#savesubmit").attr('disabled','disabled');
		}
	},'json');
});

$("#db_tables").focus(function(){
	//判断如果连接信息是否填写
	if($("input:checked[name=setting\[dbtype\]]").val()!='mysql') return;
	$.post("?app=system&controller=import&action=tables",{
        dbtype:$("input:checked[name=setting\[dbtype\]]").val(),
        dbhost:$('#dbhost').val(),
        dbport:$("#dbport").val(),
        dbuser:$('#dbuser').val(),
        dbpw:$('#dbpw').val(),
        dbname:$('#dbname').val()
    }, function(data){
		if(data.state) {
			//ct.alert(data.datas);
			$("#db_tables").floatDiv({InnerHtmls:data.datas});
			$("#savesubmit").attr('disabled','');
		}else{
			ct.error('数据库连接出现错误!');
			$("#savesubmit").attr('disabled','disabled');
		}
	},'json');
});

$(".fields").focus(function() {
	if($("input:checked[name=setting\[dbtype\]]").val()!='mysql') return;
	id = $(this).attr('id');
	html = $.ajax({
		type: "GET",
		url:"<?php echo ADMIN_URL; ?>", 
		data:'?app=system&controller=import&action=fields&dbtype='+$("input:checked[name=setting\[dbtype\]]").val()+'&dbhost='+$('#dbhost').val()+'&dbport='+$('#dbport').val()+'&dbuser='+$('#dbuser').val()+'&dbpw='+$('#dbpw').val()+'&dbname='+$('#dbname').val()+'&charset='+($('#charset').val() || '')+'&tables='+$('#db_tables').val()+'',
		async: false 
	}).responseText;
	if(html!='' && html != 'no') {
		//ct.ok(html);
		$(this).floatDiv({InnerHtmls:html});
	}
});

function put_fields(obj) {
	if(obj!='') {
		$("#"+id).val(obj);
	}
}

function put_database(obj) {
	if(obj!='') {
		$("#dbname").val(obj);
	}
}
$('img.tips').attrTips('tips', 'tips_green', 200, 'top');
</script>
<?php $this->display('footer', 'system');?>