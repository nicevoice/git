(function(){
var checkedPlace,
	checkedArea, ctrlArea, loadBox, loadUl,
	showMoreLock = false, count = 0,
	total=0, page = 0, lastWhere = '';

var action = {
	ok:function(){
		var checked = [];
		for (var k in checkedPlace) {
			checked = checked.concat(checkedPlace[k]);
		}
		if (parent) {
			if (window.dialogCallback && dialogCallback.ok) {
				dialogCallback.ok(checked);
			} else {
				window.getDialog && getDialog().dialog('close');
			}
		}
	},
	cancel:function(){
		if (parent) {
			window.getDialog && getDialog().dialog('close');
		}
	}
};
function createCheckedItem(item) {
	var li = $('<li contentid="'+item.contentid+'">'+
		'<input type="checkbox" checked="checked" />'+
        '<a href="'+item.url+'" title="'+item.title+'">'+
			'<img height="16" width="16" alt="访问" src="images/view.gif" />'+
		'</a>'+
		'<span>'+item.title+'</span>'+
		'<em></em>'+
	'</li>');
	var pagearea = null;
	li.click(function(){
		if (! li.hasClass('checked')) {
			pagearea ? pagearea.show() : (pagearea = createPagearea(item.contentid));
			pagearea.siblings().hide();
			li.addClass('checked');
			li.siblings().removeClass('checked');
		}
	}).bind('uncheck',function(){
		delete checkedPlace[item.contentid];
		var t = li.prev();
		(t.length ? t.click() : li.next()).click();
		li.remove();
		pagearea && pagearea.remove();
		loadUl.find('li[contentid="'+item.contentid+'"]').triggerHandler('uncheck');
		loadBox.triggerHandler('autoHeight');
	});
	li.find('input').click(function(){
		li.triggerHandler('uncheck');
		return false;
	});
	checkedArea.append(li);
	loadBox.triggerHandler('autoHeight');
	return li;
}

function createPagearea(contentid){
	var pagearea = $('<div class="spe-index"></div>').appendTo(ctrlArea);
	$.getJSON('?app=special&controller=online&action=getPlace&contentid='+contentid,
	function(json){
		for (var i=0,t;t=json[i++];) {
			pagearea.append(createPage(t));
		}
	});
	return pagearea;
}

function createPage(item){
	var page = $('<fieldset><legend>'+item.name+'</legend><div class="fieldset"></div></fieldset>');
	for (var i=0,t;t=item.places[i++];) {
		page.append(createPlace(t));
	}
	return page;
}

function createPlace(item){
	var place = $('<label><input type="checkbox" />'+item.name+'</label>');
	var input = place.find('input');
	var c = checkedPlace[item.contentid];
	if (! c) {
		c = [];
		checkedPlace[item.contentid] = c;
	}
	input.click(function(){
		if (this.checked) {
			c.push(item.placeid);
		} else {
			var i = c.indexOf(item.placeid);
			i == -1 || c.splice(i, 1);
		}
	});
	c.indexOf(item.placeid) == -1 || input.attr('checked', true);
	return place;
}

function createLoadItem(item){
	var li = $(
	'<li contentid="'+item.contentid+'">'+
		'<input type="checkbox" />'+
        '<a href="'+item.url+'" target="_blank" title="访问"><img height="16" width="16" alt="访问" src="images/view.gif" /></a>'+
		'<span>'+item.title+'</span>'+
	'</li>');
	var input = li.find('input');
	li.click(function(){
		var s = checkedArea.find('li[contentid="'+item.contentid+'"]');
		if (li.hasClass('checked')) {
			s.length && s.triggerHandler('uncheck');
			li.removeClass('checked');
			input[0].checked = false;
		} else {
			s.length ? s.click() : createCheckedItem(item).click();
			li.addClass('checked');
			input[0].checked = true;
		}
	}).bind('uncheck', function(){
		li.removeClass('checked');
		input[0].checked = false;
	});
	if (checkedArea.find('li[contentid="'+item.contentid+'"]').length) {
		li.addClass('checked');
		input[0].checked = true;
	}
	return li;
}
function scroll(){
	if (!showMoreLock && count < total 
		&& loadBox.scrollTop() + loadBox.height() > loadBox[0].scrollHeight - 20)
	{
		loadPage();
	}
	return false;
}
function query(where){
	lastWhere = where;
	load(lastWhere, queryOk);
}
function ajaxStart(){
	showMoreLock = true;
}
function ajaxEnd(){
	showMoreLock = false;
}
function load(where, success) {
	$.ajax({
		url:'?app=special&controller=online&action=search',
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
			loadUl.append(createLoadItem(json.data[i]));
		}
	}
}
function queryOk(json){
	loadUl.empty();
	if (json && json.data) {
		total = parseInt(json.total);
		page = 1;
		count = json.data ? json.data.length : 0;
		for (var i=0;i<count;i++) {
			loadUl.append(createLoadItem(json.data[i]));
		}
	} else {
		total = 0;
		page = 1;
		count = 0;
	}
}
function loadPage(){
	load(lastWhere+'&page='+(++page), pageOk);
}
function init(place, content){
	checkedPlace = place || {};
	checkedArea = $('.checked-area>ul');
	ctrlArea = $('.spe-recommend-ctrl');
	loadBox = $('.check-area');
	loadUl = loadBox.children('ul');
	loadBox.bind('mousewheel', function(e, delta){
		delta < 0 && scroll();
	}).bind('scroll', scroll).bind('autoHeight', function(){
		loadBox.height(358 - loadBox.position().top);
	});
	
	var filter = $('.date-list').click(function(e){
		e.preventDefault();
		e.stopPropagation();
		var t = e.target;
		if (t.tagName == 'A') {
			t = $(t);
			if (! t.hasClass('current')) {
				t.addClass('current');
				search.val('');
				t.siblings().removeClass('current');
				search.keyup();
			}
		}
	});
	var search = $('.search>input').keyup(function(){
		query('range='+filter.find('.current').attr('range')+'&keywords='+encodeURIComponent(this.value));
	}).keyup();
	
	if (content) {
		var f;
		for (var i=0,t;t=content[i++];) {
			if (f) {
				createCheckedItem(t);
			} else {
				f = true;
				createCheckedItem(t).click();
			}
		}
		loadBox.triggerHandler('autoHeight');
	}
	$('.btn_area').click(function(e){
		var t = e.target;
		if (t.tagName == 'BUTTON') {
			action[t.getAttribute('action')]();
		}
	});
}
window.init = init;
})();