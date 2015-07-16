/**
 * based jQuery 1.2+ javascript famework
 *
 * @author     muqiao
 * @copyright  2010 (c) cmstop.com
 * @version    $Id: cmstop.dropdown.js 4433 2012-03-06 01:43:32Z liyawei $
 */
;(function($){
var List = function(elem,options,conf){
	var valBox = $(elem);
	var _s = valBox.attr('selected') || valBox.val();
	if (_s != '' && !conf.selected) {
		conf.selected = _s;
	}
	var _t = valBox.attr('title');
	if (_t != '') {
		conf.title = _t;
	}
	// valBox.css({'visibility':'hide',display:'block'});
	var _w = valBox.outerWidth();
	var _mart = valBox.attr('marginTop');
	_mart !== undefined && (conf.marginTop = parseFloat(_mart));
	var _marl = valBox.attr('marginLeft');
	_marl !== undefined && (conf.marginLeft = parseFloat(_marl));
	var _tabIndex = valBox.attr('tabIndex');
	if($.nodeName(elem,'SELECT')){
		var _temp = $('<input name="'+elem.name+'" id="'+elem.id+'" />');
		valBox.after(_temp).remove();
		valBox = _temp;
	}
	var txtBox = $('<div class="'+CLASSES.selector+'" />');
	var ivalcallout = null;
	elem.className && txtBox.addClass(elem.className);
	txtBox.css('width',_w).attr('tabIndex', _tabIndex||0).css('outline','none');
	txtBox.mouseover(function(){
		txtBox.addClass(CLASSES.hover);
		ivalcallout = window.setTimeout(function(){
			txtBox.mousedown();
		},800);
	}).mouseout(function(){
		ivalcallout && window.clearTimeout(ivalcallout);
		txtBox.removeClass(CLASSES.hover);
	});
	valBox.css('display','none').after(txtBox);
	var dropdown = $('<ul class="'+CLASSES.dropdown+'"/>');
	var hoverIndex = -1;
	var selectIndex = -1;
	var visable = false;
	var itemBox = [];
	var showList = function(){
		if(visable) return;
		ivalcallout && clearTimeout(ivalcallout);
		txtBox.addClass(CLASSES.focus);
		var offset = txtBox.position();
		dropdown.css({
			width:txtBox.width()+3,
			top: ( offset.top + parseInt(txtBox.outerHeight(true)) + conf.marginTop ),
			left: offset.left + conf.marginLeft,
			display:'block'
		});
		visable = true;
		setTimeout(function(){
			$(document).mousedown(blurList);
		}, 0);
	};
	var hideList = function(){
		if(!visable) return;
		$(document).unbind('mousedown', blurList);
		txtBox.removeClass(CLASSES.focus);
		visable = false;
		itemBox[hoverIndex] && itemBox[hoverIndex].mouseout();
		hoverIndex = -1;
		dropdown.css('display','none');
	};
	var blurList = function(e){
		(e.target == txtBox[0] || e.target == dropdown[0]
			|| dropdown.find('*').index(e.target) != -1) || hideList();
		return true;
	};
	/**
		props{}
			text      plain text to show
			value     the value when choice
			onselect  when select this clause this function
			ico       a img prefix
	 */
	var addItem = function(props){
		var iconClass = (props.getAttribute ? props.getAttribute('ico') : props.ico) || 'noico';
		var item = $('<li class="'+CLASSES.item+'" id="dropdown_'+props.value+'"><span class="'+iconClass+'">'+props.text+'</span></li>');
		itemBox.push(item);
		var index = itemBox.length - 1;
		var t = typeof props.onselect;
		var onselect = t == 'string'
			? (function(){eval('(function(value,text){'+props.onselect+'})').call(item[0],props.value,props.text);})
			: ( t == 'function' ? (function(){props.onselect.call(item[0],props.value,props.text);}) : null);
		item.click(function(){
			// change something
			onselect && onselect();
			valBox.val(props.value).trigger('change');
			txtBox.html(this.innerHTML);
			if(index != selectIndex){
				itemBox[selectIndex] && itemBox[selectIndex].removeClass(CLASSES.selected);
				// typeof conf.onchange == 'function' && conf.onchange(item,itemBox[selectIndex]);
				typeof conf.onchange == 'function' && conf.onchange(props.value,props.text);
				selectIndex = index;
			}
			item.addClass(CLASSES.selected);
			hideList();
		}).mouseover(function(){
			if(hoverIndex != index) {
				itemBox[hoverIndex] && itemBox[hoverIndex].mouseout();
				hoverIndex = index;
			}
			item.addClass(CLASSES.hover);
		}).mouseout(function(){
			item.removeClass(CLASSES.hover);
		}).appendTo(dropdown);
		$('*',item).click(function(e){
			e.stopPropagation();
			e.preventDefault();
			item.click();
		});
		(props.selected || (props.value != undefined && props.value == conf.selected)) && (selectIndex = index);
	};
	
	for(var i=0, l = options.length; i < l; i++){
		addItem(options[i]);
	}
	txtBox.mousedown(showList).focus(showList).after(dropdown);
	txtBox.find('*').mousedown(function(e){
		e.stopPropagation();
		e.preventDefault();
		txtBox.mousedown();
	});
	selectIndex == -1 ? txtBox.html('<span>'+conf.title+'</span>') : itemBox[selectIndex].click();
};
var CLASSES = List.CLASSES = {
	'dropdown' : 'ct_dropdown',
	'hover' : 'ct_hover',
	'selector':'ct_selector',
	'selected' : 'ct_selected',
	'item' : 'ct_item',
	'focus' : 'ct_focus'
};
List.CONFIG = {
	title:'Make choice...',
	marginTop:1,
	marginLeft:0,
	onchange:null,
	selected:null
};
$.fn.dropdown = function(settings,options){
	options || (options = {});
	for ( var i = 0, l = this.length, el = this[0]; i < l ; el = this[++i] )
	{
		if (!el.options && (!el.name || !options[el.name])) {
			continue;
		}
		var o = $.makeArray(el.options), c = $.extend({}, List.CONFIG);
		var name = el.name || el.id;
		if (name) {
			options[name] && (o = o.concat(options[name]));
			$.extend(c, (settings && settings[name]) || {});
		}
		List(el, o, c);
	}
	return this;
}
})(jQuery);