<div class="bk_8"></div>
<div style="margin-left:15px">
<form method="POST" action="?app=<?=$app?>&controller=<?=$controller?>&action=reference">
<input type="hidden" name="contentid" value="<?=$contentid?>" />
<div class="tree" id="category" idv="tree" style="position:absolute;z-index:3;"></div>
</form>
<div id="errorbox" style="margin:8px;"></div>
</div>

<script type="text/javascript">
var treeOptions = {
	url:"?app=system&controller=category&action=cate&catid=%s",
	paramId : 'catid',
	paramHaschild:"hasChildren",
	renderTxt:function(div, id, item){
		var input = !item.childids ? '<input type="checkbox" name="catid[]" value="'+id+'" class="radio_style" />': '';
		return $('<span id="'+id+'">'+input+'<label>'+item.name+'</span></label>');
	}
};
$(function(){
	$("#category").tree($.extend({}, treeOptions));
});
</script>