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
		<div class="search_icon mar_l_8" style="width:330px;text-align:left">
			<input type="text" name="keywords" value="" size="20"/>
            <input id="catid" class="selectree" name="catid" value="" ending="1" initUrl="?app=system&controller=category&action=name&catid=%s" url="?app=system&controller=category&action=cate&catid=%s" paramVal="catid" paramTxt="name"/>
            <link href="<?=IMG_URL?>js/lib/tree/style.css" rel="stylesheet" type="text/css"/>
            <script type="text/javascript">
                $.getScript('<?=IMG_URL?>js/lib/cmstop.tree.js', function() {
                    $('#catid').selectree();
                });
            </script>
			<input class="button_style_2"  type="button" id="vsearch" value="搜索"/>
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
	pageSize : 12,
	rowCallback : 'init_row_event',
	template : row_template,
	baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page&status=6'
});

tableApp.load();

$('#vsearch').click(function(){
	var catid = $('#catid');
	var keywords = $('#catid').prev();
	tableApp.load('catid='+catid.val()+'&keywords='+keywords.val());
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
