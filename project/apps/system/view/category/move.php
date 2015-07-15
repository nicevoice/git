<div class="bk_8"></div>
<div class="mar_l_8">
<form method="POST" action="?app=system&controller=category&action=move">
<input type="hidden" name="catid" value="<?=$catid?>" />
<div class="tree" id="category" idv="tree" style="position:absolute;z-index:3;"></div>
</form>
</div>
<script type="text/javascript">
var treeOptions = {
	url:"?app=system&controller=category&action=cate&catid=%s",
	paramId : 'catid',
	paramHaschild:"hasChildren",
	renderTxt:function(div, id, item){
		var input = '<input type="radio" name="parentid" value="'+id+'" class="radio_style" />';
		return $('<span id="'+id+'">'+input+'<label>'+item.name+'</span></label>');
	}
};
$(function(){
	$("#category").tree($.extend({}, treeOptions));
});
</script>

