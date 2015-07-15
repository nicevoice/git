(function(){

var doc = $(),
ctrlFunc = {
	up:function(item){
		moveItem(item, -1);
	},
	down:function(item){
		moveItem(item, 1);
	},
	remove:function(item){
		var adder = item.next();
		item.slideUp(160,function(){
			item.remove();
			adder.nextAll('.list-item').each(function(){
				var s = $('span.num', this);
				s.text(parseInt(s.text()) - 1);
			});
			adder.remove();
		});
	},
	pick:function(item){
		var ctrls = item.data('ctrls');
		if (ctrls) {
			$.datapicker({
				picked:function(items){
					for (var i=0,t;t=items[i++];){
						createSpan(t, ctrls[0], ctrls[1], ctrls[2]);
					}
				},
				multiple:true
			});
		}
	}
};
function createItem(json, i){
	var l = json && json.length || 0;
	var item = $(
	'<div class="list-item">'+
		'<div class="list-ctrl">'+
			'<span class="num">'+i+'</span>'+
			'<a class="ctrl up" title="上移"></a>'+
			'<a class="ctrl pick" title="选取数据"></a>'+
			'<a class="ctrl down" title="下移"></a>'+
			'<a class="ctrl remove" title="移除"></a>'+
		'</div>'+
		'<div class="list-content"><i></i></div>'+
		'<input type="hidden" name="list[count][]" value="'+l+'" />'+
		'<div class="clear"></div>'+
	'</div>');
	var content = item.children('.list-content'),
		adder = content.children('i'),
		ctrl = createCtrl(content),
		popup = createPopup(content),
		count = item.find('input[name="list[count][]"]');
	for (var i = 0; i < l;i++) {
		createSpan(json[i], adder, ctrl, popup);
	}
	adder.click(function(e){
		createSpan({
			title:'新标题',
			url:'http://'
		}, adder, ctrl, popup).click();
		return false;
	}).bind('countChanged',function(){
		count.val(content.children('.list-content-item').length);
	});
	content.dblclick(function(){
		adder.click();
	});
	item.data('ctrls', [adder, ctrl, popup]);
	item.find('a.ctrl').click(function(){
		ctrlFunc[this.className.substr(5)](item);
	});
	return item;
}
function createCtrl(content){
	var span = null, ival, adder = content.children('i'),
	ctrl = $('<div class="list-content-ctrl">'+
		'<b class="ml"></b>'+
		'<b class="pk"></b>'+
		'<b class="rm"></b>'+
		'<b class="mr"></b>'+
	'</div>').hover(ishow, ihide).insertAfter(content),
	func = {
		ml:function(){
			var t = span.prev('.list-content-item');
			t.length && t.before(span);
			show();
		},
		mr:function(){
			var t = span.next('.list-content-item');
			t.length && t.after(span);
			show();
		},
		pk:function(){
			$.datapicker({
				picked:function(items){
					fill(span, items[0]);
					span.click();
				},
				multiple:false
			});
			ihide('now');
		},
		rm:function(){
			span.remove();
			adder.triggerHandler('countChanged');
			ihide('now');
		}
	};
	ctrl.find('b').click(function(){
		span && func[this.className]();
	});
	ctrl.bind('ishow',function(e, s){
		span = s;
		ishow();
	}).bind('ihide',function(e, s){
		span = s;
		ihide();
	});
	function show(){
		var pos = span.position();
		ctrl.css('display', 'block');
		ctrl.css({
			top:pos.top + content.offsetParent()[0].scrollTop - ctrl.outerHeight(),
			left:pos.left + (span.outerWidth() - ctrl.outerWidth())/2
		});
	}
	function ishow(){
		ival && clearTimeout(ival);
		if (!span) return;
		if (ctrl.is(':visible')) {
			ival = null;
			show();
		} else {
			ival = setTimeout(show, 100);
		}
	}
	function ihide(e){
		ival && clearTimeout(ival);
		if (ctrl.is(':hidden')) return;
		if (e == 'now') {
			ival = null;
			ctrl.css('display','none');
		} else {
			ival = setTimeout(function(){
				ctrl.css('display','none');
			}, 200);
		}
	}
	return ctrl;
}
function createPopup(content){
	var span = null,
	popup = $('<div class="list-content-popup">'+
		'<input type="text" class="title" />'+
		'<input type="text" class="url" />'+
		'<button type="button">确定</button><b></b>'+
	'</div>').insertAfter(content),
	point = popup.find('b'),
	inputs = popup.find('input').focus(function(){
		this.style.cssText = 'background-image:none';
	}).blur(function(){
		this.value == '' && (this.style.cssText = '');
	});
	popup.bind('show',function(e, s){
		span = s;
		show();
	});
	popup.find('button').click(function(){
		if (!span) return;
		var inputsa = span.find('input');
		inputs.each(function(i){
			if (i == 0) {
				if (this.value) {
					inputsa[i].value = this.value;
					span.find('a').text(this.value);
				}
			} else {
				inputsa[i].value = this.value;
			}
		});
		hide();
	});
	function hide(){
		doc.unbind('mousedown', blur);
		popup.css('display','none');
	}
	function show(){
		if (!span) return;
		setTimeout(function(){
			doc.bind('mousedown', blur);
		}, 0);
		var inputsa = span.find('input');
		inputs.each(function(i){
			this.value = inputsa[i].value;
			this.value != '' && (this.style.cssText = 'background-image:none');
		});
		var pos = span.position();
		popup.css({
			top:pos.top + content.offsetParent()[0].scrollTop + span.outerHeight() + 3,
			display:'block'
		});
		var pl = pos.left + span.outerWidth()/2 - popup.position().left - 6,
			ll = popup.outerWidth() - 3;
		point.css('left', pl < 3 ? 3 : (pl > ll ? ll : pl));
	}
	function blur(e){
		var t = e.target, tag = t.tagName||'*';
		t == popup[0] || popup.find(tag).index(t) != -1 ||
		t == span[0] || span.find(tag).index(t) != -1 || hide();
	}
	return popup;
}
function createSpan(item, adder, ctrl, popup){
	var span = $('<span class="list-content-item">'+
		'<a>'+(item.title||'')+'</a>'+
		'<input type="hidden" name="list[title][]" value="'+(item.title||'')+'" />'+
		'<input type="hidden" name="list[url][]" value="'+(item.url||'')+'" />'+
	'</span>').hover(function(){
		ctrl.triggerHandler('ishow', [span]);
	}, function(){
		ctrl.triggerHandler('ihide', [span]);
	}).click(function(){
		popup.triggerHandler('show', [span]);
	});
	adder.before(span);
	adder.triggerHandler('countChanged');
	return span;
}
function fill(span, json) {
	var c = span.children();
	json.title && (
		c.filter('a').text(json.title),
		c.filter('[name="list[title][]"]').val(json.title)
	);
	json.url && c.filter('[name="list[url][]"]').val(json.url);
}
function moveItem(item, direct) {
	var tar = item[direct > 0 ? 'nextAll' : 'prevAll']('.list-item:first');
	if (! tar.length || item.is(':animated') || tar.is(':animated')) return;
	var a1 = $(document.createElement('div')).css('height', item[0].offsetHeight),
		a2 = $(document.createElement('div')).css('height', tar[0].offsetHeight),
		s1 = item.find('span.num'), s2 = tar.find('span.num'),
		num1 = s1.text(), num2 = s2.text(),
		pos1 = item.position(), pos2 = tar.position(),
		st = item.offsetParent()[0].scrollTop;
	item.css({position:'absolute',top:pos1.top+st,left:pos1.left,zIndex:2}).after(a1);
	tar.css({position:'absolute',top:pos2.top+st,left:pos2.left,zIndex:1}).after(a2);
	item.animate({top:pos2.top+st},160,function(){
		a2.replaceWith(item);
		s1.text(num2);
		item.css({position:'',zIndex:'',top:'',left:''});
	});
	tar.animate({top:pos1.top+st},160,function(){
		a1.replaceWith(tar);
		s2.text(num1);
		tar.css({position:'',zIndex:'',top:'',left:''});
	});
}
function createAdder(){
	var adder = $('<div class="list-sepr"><div class="list-insert" title="插入一行"></div></div>').click(function(){
		var n = adder.prevAll('.list-item').length + 1;
		adder.nextAll('.list-item').each(function(i){
			$('span.num', this).text(i+1+n);
		});
		var item = createItem({}, n).hide();
		adder.after(item);
		item.after(createAdder());
		item.slideDown(160);
        ct.widget.dragSort(item.parent());
	});
	return adder;
}
var _init = {
	0:function(){},
	1:function(form, dialog){
		var listArea = dialog.find('div.list-area'),
			items = (new Function('return ' + listArea.text()))();
		listArea.html(createAdder());
		items && items.length && setTimeout(function(){
			for (var i=0,t;t=items[i++];) {
				var item = createItem(t, i);
				listArea.append(item);
				item.after(createAdder());
			}
            ct.widget.dragSort(listArea);
			dialog.dialog('option','position','center');
		}, 0);
		dialog.find('#adder').click(function(){
			$.datapicker({
				picked:function(items){
					var n = listArea.children('.list-item').length;
					for (var i=0,t;t=items[i++];) {
						var item = createItem([t], n+i);
						listArea.append(item);
						item.after(createAdder());
					}
                    ct.widget.dragSort(listArea);
				},
				multiple:true
			});
		});
		dialog.find('#clear').click(function(){
			ct.confirm('确定要清空吗？',function(){
				listArea.empty().append(createAdder());
			});
		});
	},
	2:function(form, dialog){
		dialog.find('.modelset').modelset();
		dialog.find('.selectree').selectree();
		setTimeout(function(){dialog.find('.suggest').suggest();}, 0);
	}
};
function setTemplate(dialog, form, engine) {
	var url = '?app=special&controller=online&action=getTemplate&engine='+engine;
	var a = dialog.find('#template').click(function(){
		var textarea = $('<textarea style="width:100%;" wrap="off" name="template" ></textarea>');
		a.replaceWith(textarea);
		textarea.editplus({
			buttons: 'fullscreen,wrap,|,loop,ifelse,elseif',
			height:150
		});
		if (form[0].widgetid && form[0].widgetid.value) {
			url += '&widgetid='+form[0].widgetid.value;
		}
		$.get(url, function(html){
			textarea.val(html);
		});
	});
}
DIY.registerEngine('title', {
	dialogWidth:500,
    support:['morelist'],
	addFormReady:function(form, dialog) {
		DIY.tabs(dialog,function(i){
			form[0].method.value = i;
			if (! $.data(this, 'inited')) {
				$.data(this, 'inited', 1);
				_init[i](form, dialog);
			}
		});
		setTemplate(dialog, form, 'title');
	},
	editFormReady:function(form, dialog) {
		DIY.tabs(dialog,function(i){
			form[0].method.value = i;
			if (! $.data(this, 'inited')) {
				$.data(this, 'inited', 1);
				_init[i](form, dialog);
			}
		}, form[0].method.value);
		setTemplate(dialog, form, 'title');
	},
	afterRender:function(widget){},
	beforeSubmit:function(form, dialog){
		form.find('tbody:hidden')
		.find('input,select,textarea').each(function(){
			if (!this.disabled) {
				this.setAttribute('notsubmit','1');
				this.disabled = true;
			}
		});
	},
	afterSubmit:function(form, dialog){
		form.find('tbody:hidden')
			.find('input,select,textarea')
			.filter('[notsubmit]').removeAttr('disabled');
	}
});
})();