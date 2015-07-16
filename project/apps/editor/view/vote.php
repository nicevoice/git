<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$CONFIG['charset']?>" />
<title>插入投票</title>

<!--gloabl-->
<link rel="stylesheet" type="text/css" href="css/admin.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/validator/style.css"/>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/jquery-ui/dialog.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.ui.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/config.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/cmstop.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.validator.js"></script>
<!--global -->
<!--for table-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/dropdown/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.dropdown.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.pagination.js"></script>

<!--for table -->

<link href="<?=IMG_URL?>js/lib/tree/style.css" rel="stylesheet" type="text/css" />
<script src="<?=IMG_URL?>js/lib/cmstop.tree.js" type="text/javascript"></script>

<!--tinymce-->
<script type="text/javascript" src="<?=ADMIN_URL?>tiny_mce/tiny_mce_popup.js"></script>
<script type="text/javascript" src="<?=ADMIN_URL?>tiny_mce/utils/mctabs.js"></script>
<script type="text/javascript">
var ed = tinyMCEPopup.editor;
    mcTabs.init({
    	selection_class:'s_3'
    });
//$(function(){
//	var ncat = tinyMCEPopup.getWindowArg('catid');
//	$('#catid,#catidb').each(function(){
//		if(ncat!=null)
//		{
//			if(typeof ncat == 'object')
//			$(this).val(ncat[0]).next().html(ncat[1]);
//			else this.value = ncat;
//		}
//	});
//})
</script>
<!--tinymce-->



<style  type="text/css">
body{background-color:#FFFFFF}
fieldset{ margin:0px; padding:4px;}
input,select{ font-size:12px;}
.button_float{float:right; margin-right:0; margin-left:3px;}
</style>
</head>
<body>
<div class="error" style="display:none"><sub></sub><span></span></div>
<div class="tag_1" style="margin-top:5px;">
			<ul class="tabs tag_list">
				<li id="insert_tab" class="s_3"><span><a href="javascript:mcTabs.displayTab('insert_tab','insert_panel');" onMouseDown="return false;">创建</a></span></li>
				<li id="search_tab"><span><a href="javascript:mcTabs.displayTab('search_tab','search_panel');" onMouseDown="return false;">选择</a></span></li>
			</ul>	
		</div>

<div class="panel_wrapper" style="padding:5px 0px 10px" >


<div id="insert_panel" class="panel current" style="overflow-y:auto;"> 
<form id="vote_add" name="vote_add" id="vote_add" method="post" action="?app=editor&controller=vote&action=add">
	<input type="hidden" name="modelid" id="modelid" value="<?=$modelid?>">
	<input type="hidden" name="status" id="status" value="6">
	<input type="hidden" name="mininterval" id="mininterval" value="24" />
	<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
          <tr>
            <th width="60"><span class="c_red">*</span> 栏目：</th>
            <td><?=element::category('catid', 'catid')?>
            </td>
          </tr>
		<tr>
			<th width="60"><span class="c_red">*</span> 标题：</th>
			<td><input name="title" id="title" style="width: 300px;" size="50" maxlength="80" class="bdr inputtit_focus" type="text" />
			
			 <img id="title_cp" src="images/color.gif" alt="色板" style="cursor: pointer;" height="16" width="16" />
			 <input name="color" id="color" style="" type="hidden" />
			 <script src="<?=IMG_URL?>js/lib/jquery.colorPicker.js" type="text/javascript"></script>
			 <script type="text/javascript">$('#title_cp').titleColorPicker("#title","#color")</script></td>
		</tr>
		<tr>
			<th>类型：</th>
			<td class="lh_24"><input name="type" type="radio" value="radio" checked="checked" class="checkbox_style" onclick="$('#maxoptions_span').hide();" /> 单选
			    <input name="type" type="radio" class="checkbox_style" value="checkbox" onclick="$('#maxoptions_span').show();" /> 多选
			    <span id="maxoptions_span" style="display: none">最多可选  <input id="maxoptions" name="maxoptions" type="text" size="2"/> 项 <img src="images/question.gif" width="16" height="16" class="tips hand" tips="留空表示不限制" align="absmiddle"/></span>
			</td>
		</tr>
	</table>
	
	<table width="98%" border="0" cellspacing="0" cellpadding="0" >
		<tr>
			<th width="60" style="color:#077AC7;font-weight:normal;" class="t_r"><span class="c_red">*</span> 选项：</th>
			<td>
				<table id="vote_options" width="420" border="0" cellspacing="0" cellpadding="0" class="table_info">
					<thead>
						<tr>
							<th width="30">排序</th>
							<th>选项</th>
							<th width="60">初始票数</th>
							<th width="30">操作</th>
						</tr>
					</thead>
					<tbody id="options"></tbody>
				</table>
			</td>
	    </tr>
		<tr>
			<th width="60"></th>
			<td><div class="mar_l_8 mar_5"><input name="add_option_btn" type="button" value="增加选项" class="hand button_style" onclick="option.add()" /></div></td>
		</tr>
	</table>
	
</form>
</div>

<div id="search_panel" class="panel" style="height:300px">
   <div id="published_x" class="th_pop" style="display:none;width:110px">
     <div>
        <a href="javascript: tableApp.load('published_min=<?=date('Y-m-d', TIME)?>');">今日</a> |
        <a href="javascript: tableApp.load('published_min=<?=date('Y-m-d', strtotime('yesterday'))?>&published_max=<?=date('Y-m-d', strtotime('yesterday'))?>');">昨日</a> | 
        <a href="javascript: tableApp.load('published_min=<?=date('Y-m-d', strtotime('last monday'))?>');">本周</a> | 
        <a href="javascript: tableApp.load('published_min=<?=date('Y-m-01', strtotime('this month'))?>');">本月</a>
     </div>
     <ul>
       <?php 
       for ($i=2; $i<=7; $i++) { 
       	  $publishdate = date('Y-m-d', strtotime("-$i day"));
       ?>
        <li><a href="javascript: tableApp.load('published_min=<?=$publishdate?>&published_max=<?=$publishdate?>');"><?=$publishdate?></a></li>
       <?php } ?>
     </ul>
</div>

<div style="padding:5px 0 2px 0;margin-top:-5px;margin-bottom:5px;">
		<div id="search" class="search_icon mar_l_8" style="width:330px">
			<input id="keywords" type="text" name="keywords" value="" size="20"/>
			<?=element::category('catidb', 'catid')?>
			<input class="button_style_2" type="button" id="vsearch" value="搜索"/>
		</div>
</div>
<table width="100%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list">
    <thead>
      <tr>
        <th width="30" class="t_l bdr_3">&nbsp;</th>
        <th >标题</th>
        <th width="50">查看</th>
        <th width="135" class="ajaxer"><em class="more_pop" name="published_x"></em><div name="published">发布时间</div></th>
      </tr>
    </thead>
    <tbody id="list_body">
    </tbody>
</table>
<div class="table_foot" style="padding-right:0px;margin-right:0px;"><div id="pagination" class="pagination f_r"></div></div>
</div>


</div>

<div class="mceActionPanel" align="center">
    <input type="button"  name="cancel" class="button_style_1 button_float" value="取消" onClick="tinyMCEPopup.close();" />
    <input type="button" onclick="insertcode()" name="insert" id="insertcode" class="button_style_1 button_float" value="插入" />
</div>
<!--vote-->
<script type="text/javascript" src="<?=ADMIN_URL?>apps/vote/js/option.js"></script>
<!--vote-->
<script type="text/javascript">
$('.tips').attrTips("tips", "tips_green");
//insert
$('#title').focus();
option.add();
option.add();
option.add();
option.add();
$('#title').maxLength();

//rewrite option.remove()
option.remove = function(i)
{
	if($('#options>tr').length < 3)
	{
		return false;
	}
	$('#'+i).remove();
}

//search 
var row_template ='<tr id="row_{contentid}">\
                	    <td><input type="radio" class="raido_style" name="radio_row"  value="{contentid}" /></td>\
	                	<td><span style="color:{color}" class="title_list">{title}</a></td>\
	                	<td class="t_c"><a target="_blank" href="{url}"><img src="images/view.gif" /></a></td>\
	                	<td class="t_c">{published}</td>\
	                </tr>';

var tableApp = new ct.table('#item_list', {
	rowIdPrefix : 'row_',
	pageField : 'page',
	pageSize : 7,
	rowCallback : 'init_row_event',
	template : row_template,
	baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page&status=6'
});
tableApp.load();

$('#vsearch').click(function(){
	var catid = $('#catidb');
	var keywords = $('#keywords');
	tableApp.load('catid='+catid.val()+'&keywords='+keywords.val());
})

function init_row_event(id, tr)
{
	var title = tr.find('span.title_list');
	title.html(title.html().substr(0,15))
	tr.children(1).click(function(){
		var radio = $(this).prev().find('input:radio');
		radio.attr('checked',(radio.attr('checked')?false:true));
	}).css('cursor','pointer');
}

function insertcode(){
	var contentid = $('input.raido_style:checked').val();
	if(contentid){
		$.get('?app=editor&controller=vote&action=getVotecode&contentid='+contentid,function(code){
			ed.execCommand('mceInsertContent', false, code);
			tinyMCEPopup.close();
		})
	}else{
		$('#vote_add').ajaxSubmit({
			type        : 'post',
		    beforeSubmit:  function(){
		    	if($('#title').val() == '')
		    	{
		    		$('.error').find('span').html('标题不得为空').end().show();
		    		setTimeout(function(){$('.error').hide()},2000);
		    		return false;
		    	};
		    	if($('input[id^="name"]')[0].value==''||$('input[id^="name"]')[1].value=='')
		    	{
		    		$('.error').find('span').html('前两个选项不得为空').end().show();
		    		setTimeout(function(){$('.error').hide()},2000);
		    		return false;
		    	}
		    },
			success    :  function(code){
				if(code)
				{
					ed.execCommand('mceInsertContent', false, code);
					tinyMCEPopup.close();
				}
			}
		});
	}
}

</script>

</body></html>


