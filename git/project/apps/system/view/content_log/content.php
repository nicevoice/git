<?php $this->display('header', 'system');?>
<!--tablesorter-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<!--contextmenu-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<!--pagination-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.pagination.js"></script>

<script type="text/javascript" src="apps/system/js/content_log.js"></script>

  <table width="100%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list">
    <thead>
      <tr>
        <th class="bdr_3">操作</th>
        <th width="100">操作人</th>
        <th width="130">操作时间</th>
        <th width="180">IP</th>
      </tr>
    </thead>
    <tbody id="list_body">
    </tbody>
  </table>
<div class="table_foot">
  <div id="pagination" class="pagination f_r"></div>
  <p class="f_l">
  </p>
</div>
<script type="text/javascript">
var row_template = '<tr id="row_{logid}">';
row_template +='	<td>{action_name}({action})</td>';
row_template +='	<td class="t_c"><a href="javascript: url.member({createdby});">{createdbyname}</a></td>';
row_template +='	<td class="t_c">{created}</td>';
row_template +='	<td class="t_c">{ip}({iparea})</td>';
row_template +='</tr>';

var tableApp = new ct.table('#item_list', {
    rowIdPrefix : 'row_',
    rightMenuId : null,
    pageField : 'page',
    pageSize : 10,
    dblclickHandler : null,
    rowCallback     : 'init_row_event',
    template : row_template,
    baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page&contentid=<?=$contentid?>'
});

function init_row_event(id, tr)
{
}

tableApp.load();
</script>
<?php $this->display('footer');
