function default_select_treeview_channel(categorys){
	$.each(categorys, function(i, id){
		$('#category_'+id).attr('checked', true);
	});
}
function select_treeview_children_channel(parentid){
	var parent_channel = $('#category_'+parentid);
	var parent_li;
	var child_ul;

	var checked = parent_channel.attr('checked');

	$.each(parent_channel, function(p, parent){
		parent = $(parent);
		parent_li = parent.parent('li');
		child_ul = parent_li.children('ul');
		child_ul.children('li').children('input').attr('checked', checked);
	});
}