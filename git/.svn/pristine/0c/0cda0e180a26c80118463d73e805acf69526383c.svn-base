<?php $this->display('header');?>
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

<script type="text/javascript" src="apps/system/js/content_note.js"></script>

<div class="table_head">
  <form method="POST" id="note_add" name="note_add" action="?app=system&controller=content_note&action=add">
  <input type="hidden" name="contentid" id="contentid" value="<?=$contentid?>" size="30" />
    <table>
    <tr>
    <td>批注：<input type="text" name="note" id="note" value="" size="60" maxlength="255" title="请输入批注内容"/></td>
    <td><input type="submit" name="submit" id="submit" value="添加" class="button_style_1"/></td>
    </tr>
    </table>
    </form>
</div>
  <table width="100%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list">
    <thead>
      <tr>
        <th class="bdr_3">批注</th>
        <th width="80">批注人</th>
        <th width="120">批注时间</th>
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
$(function(){
	$.validate.setConfigs({
		xmlPath:'<?=ADMIN_URL?>apps/<?=$app?>/validators/content/'
	});
})
var row_template = '<tr id="row_{noteid}">';
row_template +='	<td>{note}</td>';
row_template +='	<td class="t_c"><a href="javascript: url.member({createdby});">{createdbyname}</a></td>';
row_template +='	<td class="t_c">{created}</td>';
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

tableApp.load();

$(function(){
	$('#note_add').ajaxForm('content_note.add_submit');
});
</script>
<?php $this->display('footer');