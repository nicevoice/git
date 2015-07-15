(function(){
var box, url, multi,
	showMoreLock = false, count=0,
	total=0, page = 0, lastWhere = '', checked=[],
	table, tbody, template;
function init(u, m){
	box = $('#box');
	url = u;
	multi = m;
	var tabs = $('.tabs li'), val = 'internal', extension = 'internal';
	if (tabs.length) {
		var active = tabs.filter('.active');
		tabs.click(function(){
			if (! $.className.has(this, 'active')) {
				window.location = location.href.replace(/&pickid=\w+/i,'') + '&pickid=' + this.getAttribute('pickid');
			}
		});
	}
	PICKER.ready($('form:first'));
	table = $('\
	<table cellpadding="0" cellspacing="0" class="table_list">\
		<thead>\
			<th class="bdr" width="30">'+(multi ? '<input type="checkbox" />' : '&nbsp;')+'</th>\
			<th>标题</th>\
			<th width="50">权重</th>\
			<th width="120">时间</th>\
		</thead>\
		<tbody></tbody>\
	</table>').appendTo(box);
	tbody = table.find('tbody');
	template = '\
	<tr>\
		<td class="t_c"><input type="'+(multi ? 'checkbox' : 'radio')+'" /></td>\
		<td><a href="{url}" target="_blank" tips="{tips}">{title}</a></td>\
		<td class="t_r">{weight}</td>\
		<td class="t_r">{date}</td>\
	</tr>';
	multi && table.find('input').click(function(){
		tbody.find('tr').trigger(this.checked ? '_check_' : '_uncheck_');
	});
	box.bind('mousewheel.scrolltable',function(e, delta){
		delta < 0 && scroll();
		e.stopPropagation();
	}).bind('scroll.scrolltable', scroll);
}

function scroll(){
	if (!showMoreLock && count < total 
		&& box.scrollTop() + box.height() > box[0].scrollHeight - 20)
	{
		loadPage();
	}
	return false;
}
function query(where){
	checked = [];
	lastWhere = where.jquery ? where.serialize() : where;
	query(lastWhere, queryOk);
}
function check(tr, input, json){
	tr.addClass('active');
	checked.indexOf(json) == -1 && checked.push(json);
	input[0].checked = true;
}
function uncheck(tr, input, json){
	tr.removeClass('active');
	var i = checked.indexOf(json);
	i == -1 || checked.splice(i, 1);
	input[0].checked = false;
}
function buildRow(json){
	var tr = $(template.replace(/{(\w+)}/gm, function(s,k){
		return (k in json) ? json[k] : s;
	}));
	var input = tr.find('input:'+(multi ? 'checkbox' : 'radio')+':first');
	tr.click(multi ? function(){
		tr.hasClass('active') ? uncheck(tr, input, json) : check(tr, input, json);
	} : function(){
		tbody.find('tr.active').triggerHandler('_uncheck_');
		check(tr, input, json);
	}).bind('_uncheck_',function(){
		uncheck(tr, input, json);
	}).bind('_check_',function(){
		check(tr, input, json);
	});
	multi || tr.dblclick(ok);
	
	var a = tr.find('a');
	a.attrTips('tips');
	if (json.thumb && json.thumb != 'null') {
		var img = $('<img thumb="'+json.thumb+'" style="margin-right:3px;vertical-align:middle;" src="images/thumb.gif"/>');
		img.floatImg({url:UPLOAD_URL,height:200});
		a.before(img);
	}
	return tr;
}
function query(where, success) {
	$.ajax({
		url:url,
		type:'GET',
		dataType:'json',
		data:where,
		beforeSend:function(){showMoreLock=1;},
		success:success,
		complete:function(){showMoreLock=0;}
	});
}
function pageOk(json){
	var l;
	if (json && json.data && (l = json.data.length)) {
		total || (total = parseInt(json.total));
		count += l;
		for (var i=0;i<l;i++) {
			tbody.append(buildRow(json.data[i]));
		}
	}
}
function queryOk(json){
	tbody.empty();
	if (json && json.data) {
		total = parseInt(json.total);
		page = 1;
		count = json.data ? json.data.length : 0;
		for(var i=0;i<count;i++){
			tbody.append(buildRow(json.data[i]));
		}
	} else {
		total = 0;
		page = 1;
		count = 0;
	}
}
function loadPage(){
	query(lastWhere+'&page='+(++page), pageOk);
}
function ok(){
	if (! checked.length) {
		ct.warn('未选择');
		return;
	}
	if (parent) {
		if (window.dialogCallback && dialogCallback.ok) {
			dialogCallback.ok(checked);
		} else {
			window.getDialog && getDialog().dialog('close');
		}
	}
}
function cancel(){
	if (parent) {
		window.getDialog && getDialog().dialog('close');
	}
}
window.PICKER = {
	ok:ok,
	cancel:cancel,
	query:function(where){
		checked = [];
		lastWhere = where.jquery ? where.serialize() : where;
		query(lastWhere, queryOk);
	},
	checked:function(){
		return checked;
	},
	ready:function(form){},
	init:init
};
})();