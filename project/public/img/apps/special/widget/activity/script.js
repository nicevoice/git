(function(){

CLASSES = {
	active:'active'
};
function scrollTable(box, url, multi, dblclick) {
	var showMoreLock = false, count=0,
		total=0, page = 0, lastWhere = '',checked=[],
		table = $('\
		<table cellpadding="0" cellspacing="0" width="99%" class="table_list">\
			<thead>\
				<th class="bdr" width="30">'+(multi ? '<input type="checkbox" />' : '&nbsp;')+'</th>\
				<th>标题</th>\
				<th width="50">权重</th>\
				<th width="120">时间</th>\
			</thead>\
			<tbody></tbody>\
		</table>').appendTo(box),
		tbody = table.find('tbody'),
		template = '\
		<tr>\
			<td class="t_c"><input type="'+(multi ? 'checkbox' : 'radio')+'" name="data[contentid]" value="{contentid}" /></td>\
			<td><a href="{url}" target="_blank" tips="{tips}">{title}</a></td>\
			<td class="t_r">{weight}</td>\
			<td class="t_r">{date}</td>\
		</tr>';
	multi && table.find('input').click(function(){
		tbody.find('tr').trigger(this.checked ? '_check_' : '_uncheck_');
	});
	function _scroll(){
		if (!showMoreLock && count < total 
			&& box.scrollTop() + box.height() > box[0].scrollHeight - 20)
		{
			loadPage();
		}
		return false;
	}
	box.bind('mousewheel.scrolltable',function(e, delta){
		delta < 0 && _scroll();
		e.stopPropagation();
	}).bind('scroll.scrolltable', _scroll);
	this.checked = function(){
		return checked;
	};
	this.query = function(where){
		checked = [];
		lastWhere = where.jquery ? where.serialize() : where;
		query(lastWhere, queryOk);
	};
	function check(tr, input, json){
		tr.addClass(CLASSES.active);
		checked.indexOf(json) == -1 && checked.push(json);
		input[0].checked = true;
	}
	function uncheck(tr, input, json){
		tr.removeClass(CLASSES.active);
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
			tr.hasClass(CLASSES.active) ? uncheck(tr, input, json) : check(tr, input, json);
		} : function(){
			tbody.find('tr.'+CLASSES.active).triggerHandler('_uncheck_');
			check(tr, input, json);
		}).bind('_uncheck_',function(){
			uncheck(tr, input, json);
		}).bind('_check_',function(){
			check(tr, input, json);
		});
		dblclick && tr.dblclick(dblclick);
		
		var a = tr.find('a');
		a.attrTips('tips');
		if (json.thumb && json.thumb != 'null') {
    		var img = $('<img thumb="'+json.thumb+'" style="margin-right:3px;vertical-align:middle;" src="images/thumb.gif"/>');
    		img.floatImg({url:UPLOAD_URL,height:200});
    		a.before(img);
    	}
		return tr;
	}
	function ajaxStart(){
		showMoreLock = true;
	}
	function ajaxEnd(){
		showMoreLock = false;
	}
	function query(where, success) {
		$.ajax({
			url:url,
			type:'GET',
			dataType:'json',
			data:where,
			beforeSend:ajaxStart,
			success:success,
			complete:ajaxEnd
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
}
function _init(form, dialog) {
	var table = new scrollTable($('#list'),'?app=system&controller=picker&action=page&pickid=1',false);
	function query(e){
		table.query({'catid':form[0].catid.value,'modelid':form[0].modelid.value,'keywords':form[0].keywords.value},__ok);
		return false;
	}
	function __ok(){
		var checked = table.checked();
		if (! checked.length) {
			ct.warn('未选择');
			return;
		}
		alert(checked);
	}
	form[0].catid && $(form[0].catid).selectree().bind('changed',query);
	form[0].keywords && $(form[0].keywords).keyup(query);
	table.query({'catid':form[0].catid.value,'modelid':form[0].modelid.value,'keywords':form[0].keywords.value},__ok);
}
DIY.registerEngine('activity', {
	//dialogWidth : 540,
	addFormReady:function(form, dialog) { _init(form,dialog);},
	editFormReady:function(form, dialog) {_init(form,dialog);},
	afterRender: function(widget) { },
	beforeSubmit:function(form, dialog){},
	afterSubmit:function(form, dialog){}
});

})()