<?php $this->display('header');?>
<div style="margin:5px">
  <table width="100%" cellpadding="0" cellspacing="0" id="item_list" class="table_list">
    <thead>
      <tr>
        <th class="t_l bdr_3">ID</th>
        <th width="100">用户名</th>
        <th width="120">提交时间</th>
        <th width="180">IP</th>
      </tr>
    </thead>
    <tbody id="list_body">
    </tbody>
  </table>
  
  <div class="table_foot">
    <div id="pagination" class="pagination f_r"></div>
  </div>
</div>
<script type="text/javascript">
var row_template = '<tr id="row_{answerid}">\
	                	<td>{answerid}</td>\
	                	<td class="t_c"><a href="javascript: url.member({createdby});">{createdbyname}</a></td>\
	                	<td>{created}</td>\
	                	<td>{ip}（{iparea}）</td>\
	                </tr>';

function init_row_event(id, tr)
{

}
</script>

<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css"/>

<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.pagination.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript">
var tableApp = new ct.table('#item_list', {
    rowIdPrefix : 'row_',
    rightMenuId : null,
    pageField : 'page',
    pageSize : 10,
    dblclickHandler : 'report.view',
    rowCallback     : 'init_row_event',
    template : row_template,
    baseUrl  : '?app=survey&controller=report&action=option&optionid=<?=$optionid?>&report=1'
});

tableApp.load();
</script>
<?php $this->display('footer', 'system');?>