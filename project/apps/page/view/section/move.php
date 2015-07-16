<div class="title mar_l_8" style="width:90%">移动<b class="c_red"><?=$section['name']?></b>至页面</div>
<div style="margin-bottom:10px">
	<form method="POST" action="?app=page&controller=section&action=move">
	<input type="hidden" name="sectionid" value="<?=$sectionid?>" />
	<table width="98%" id="pagetreetable" class="table_list treeTable mar_l_8" cellpadding="0" cellspacing="0">
		<tbody></tbody>
	</table>
<script type="text/javascript">
var template = '<tr id="m_{pageid}"><td><label><input type="radio" name="pageid" value="{pageid}"/>{name}</label></td></tr>';
var t = new ct.treeTable('#pagetreetable',{
	'idField'		: 'pageid',
	'treeCellIndex'	: 0,
	'template'		: template,
	'rowIdPrefix'	: 'm_',
	'collapsed'		: 1,
	'parentField'	: 'parentid',
	'baseUrl'		: '?app=page&controller=page&action=tree',
	'rowReady'		: function(id,tr,json) {
	}});
t.load();
</script>