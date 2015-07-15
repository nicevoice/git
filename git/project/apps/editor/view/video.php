<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>插入视频</title>
	
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/jquery-ui/dialog.css" />
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.ui.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/config.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/cmstop.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.dialog.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.cookie.js"></script>
<script type="text/javascript" src="uploader/cmstop.uploader.js"></script>
<!--tinymce-->
<script type="text/javascript" src="<?=ADMIN_URL?>tiny_mce/tiny_mce_popup.js"></script>
<script type="text/javascript" src="<?=ADMIN_URL?>tiny_mce/plugins/ct_media/js/video.js"></script>
<script type="text/javascript" src="<?=ADMIN_URL?>tiny_mce/utils/mctabs.js"></script>
<script type="text/javascript" src="<?=ADMIN_URL?>tiny_mce/utils/validate.js"></script>
<script type="text/javascript" src="<?=ADMIN_URL?>tiny_mce/utils/form_utils.js"></script>
<script type="text/javascript" src="<?=ADMIN_URL?>tiny_mce/utils/editable_selects.js"></script>
<!--tinymce-->
<link rel="stylesheet" type="text/css" href="<?=ADMIN_URL?>css/admin.css"/>
<link rel="stylesheet" type="text/css" href="<?=ADMIN_URL?>tiny_mce/plugins/ct_media/css/media.css"/>

<!--tree-->
<link href="<?=IMG_URL?>js/lib/tree/style.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.tree.js"></script>
<style  type="text/css">
body{background-color:#FFFFFF}
fieldset{ margin:0px; padding:2px;width:98%}
select{ font-size:12px; border:}
.button_style_1{width:94px;}
.btn_float{float:right; margin-right:0; margin-left:3px;}
div.current{height:365px}
.operation_area{background:none}
.mceActionPanel{clear:both; margin:0; overflow:hidden;}
</style>
<script type="text/javascript">
    mcTabs.init({
    	selection_class:'s_3'
    });
</script>


<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>
<body style="display:none">
	<div class="bk_8"></div>
    <form onSubmit="insertVideo(this);return false;" action="#" style="overflow:hidden">
		<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.dropdown.js"></script>
		<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
		<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.tablesorter.js"></script>
		<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
		<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.pagination.js"></script>

		<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css"/>
		<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css"/>
		<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css"/>
		<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/dropdown/style.css"/>

		<div id="mediadepot_panel" style="height:375px;" class="panel">
			<div id="published_x" class="th_pop" style="display:none;width:150px">
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
				<div class="search_icon mar_l_8" style="text-align:left;">
					<input type="text" name="keywords" value="" size="20" style="margin:0;padding:0;height:20px; vertical-align:bottom" />
					<?=element::category('catid', 'catids', $catid, 1, null, '请选择', true, true)?>
					<input class="button_style_2"  type="button" id="vsearch" value="搜索"/>
					<input type="button" value="添加" class="button_style_2" onclick="ct.assoc.open('?app=video&controller=video&action=add', 'newtab');" />
				</div>
			</div>
			<table width="100%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list">
				<thead>
				  <tr>
					<th width="30" class="t_l bdr_3">&nbsp;</th>
					<th>标题</th>
					<th width="50">查看</th>
					<th width="135" class="ajaxer"><em class="more_pop" name="published_x"></em><div name="published">发布时间</div></th>
				  </tr>
				</thead>
				<tbody id="list_body">
				</tbody>
			</table>
			<div class="table_foot" style="padding-right:0px;margin-right:0px;"><div id="pagination" class="pagination f_r"></div></div>
			<script type="text/javascript">
				var row_template ='<tr id="row_{contentid}">\
										<td><input type="radio" class="raido_style" name="radio_row"  value="{contentid}" /></td>\
										<td><span style="color:{color}" class="title_list">{title}</span> </td>\
										<td class="t_c"><a target="_blank" href="{url}"><img src="images/view.gif" /></a></td>\
										<td class="t_c">{published}</td>\
									</tr>';

				var tableApp = new ct.table('#item_list', {
					rowIdPrefix : 'row_',
					pageField : 'page',
					pageSize : 10,
					rowCallback : 'init_row_event',
					template : row_template,
					baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page&status=6'
				});

				tableApp.load();

				$('#vsearch').click(function(){
					var catid = $('#catid');
					var keywords = $('#catid').prev();
					tableApp.load('catids='+catid.val()+'&keywords='+keywords.val());
				})

				function init_row_event(id, tr)
				{
					var title = tr.find('span.title_list');
					title.html(title.html().substr(0,15)).css('cursor','pointer').click(function(){
						var radio = $(this).parent().prev().find('input:radio');
						radio.attr('checked',(radio.attr('checked')?false:true));
					});
				}
			</script>
		</div>
	</div>

	<div class="mceActionPanel" align="center">
		<div class="bk_8"></div>
        <input type="button"  name="cancel" class="button_style_1 btn_float" value="取消" onClick="tinyMCEPopup.close();" />
		<input type="submit"  name="insert" class="button_style_1 btn_float" value="插入" />
	</div>
</form>
</body>
</html>