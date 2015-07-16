<?php $this->display('header');?>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/dropdown/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.dropdown.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.pagination.js"></script>

<div class="bk_8"></div>
  <table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8">
    <thead>
      <tr>
        <th width="50" class="bdr_3">ID</th>
        <th width="120">投票时间</th>
        <th width="100">投票人</th>
        <th>IP</th>
      </tr>
    </thead>
    <tbody id="list_body">
    </tbody>
  </table>
  <div class="table_foot">
    <div id="pagination" class="pagination f_r"></div>
    <p class="f_l">
      <span class="c_red"><?=$people?></span> 人共投 <span class="c_red"><?=$total?></span> 票
    </p>
  </div>
<?php $this->display('footer');?>

<script type="text/javascript">
var row_template = '<tr id="row_{logid}" option="<b>投票选项：</b><p>{option}</p>">\
	                	<td class="t_r">{logid}</td>\
	                	<td class="t_c">{created}</td>\
	                	<td class="t_c"><a href="javascript: url.member({createdby});">{createdbyname}</a></td>\
	                	<td class="t_l">{ip}({area})</td>\
	                </tr>';

var tableApp = new ct.table('#item_list', {
    rowIdPrefix : 'row_',
    rightMenuId : null,
    pageField : 'page',
    pageSize : 10,
    dblclickHandler : 'vote.log_data',
    rowCallback     : 'init_row_event',
    template : row_template,
    baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page&contentid=<?=$contentid?>'
});

function init_row_event(id, tr)
{
	tr.attrTips('option');
}

tableApp.load();
</script>