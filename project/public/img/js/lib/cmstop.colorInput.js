/**
 * cmstop colorinput&colorpicker plugin base on jquery < 1.4
 *
 * @author     kakalong
 * @copyright  2010 (c) cmstop.com
 * @version    $Id: cmstop.colorInput.js 4575 2012-03-19 08:47:08Z liyawei $
 */
(function($){
var pane = null;
var doc = $();
var CLASSES = {
	active:'active',
	pane:'color-pannel',
	box:'input-box',
	input:'color-input',
	picker:'color-picker'
};
var LANG = {
	selectColor:'选择颜色....'
};
function innerWidth(){
	return document.compatMode == "CSS1Compat" ? document.documentElement.clientWidth : document.body.clientWidth;
}
function innerHeight(){
	return document.compatMode == "CSS1Compat" ? document.documentElement.clientHeight : document.body.clientHeight;
}
var bindTarget = null;
function hidePane(elem) {
	if (bindTarget == elem) {
		pane.hide();
		doc.unbind('.colorinput');
		bindTarget = null;
	}
}
function showPane(val, elem, onpicked, getCapture) {
	pane || createPane(val, onpicked);
	setTimeout(function(){
		doc.bind('mousedown.colorinput', function(e){
			var t = e.target;
			pane[0] == t || pane.find(t.nodeName).index(t) != -1
			|| getCapture(t) || hidePane(elem);
		});
	}, 0);
	bindTarget = elem;
	pane.unbind('.colorinput').bind('click.colorinput', function(e){
        var target = e.target;
		target.nodeName == 'LI' && !($(target).hasClass('colorinput-control')) && onpicked(target.getAttribute('val'));
		return false;
	});
	pane.css({
		visibility : 'hidden',
		display : 'block'
	});
	// set offset
	var pH = pane[0].offsetHeight, pW = pane[0].offsetWidth,
		eH = elem[0].offsetHeight, eW = elem[0].offsetWidth,
		off = elem.offset(), sL = doc.scrollLeft(), sT = doc.scrollTop(),
		left, top;
	if (off.top + eH + pH - sT > innerHeight()) {
		top = off.top - pH;
	} else {
		top = off.top + eH;
	}
	if (off.left + pW - sL > innerWidth()) {
		left = off.left + eW - pW;
	} else {
		left = off.left;
	}
	pane.css({
		top:top,
		left:left,
		visibility: 'visible'
	});
}
function toRGB(color){
	return [
		parseInt(color.substr(1, 2), 16),
		parseInt(color.substr(3, 2), 16),
		parseInt(color.substr(5, 2), 16)
	];
}
function negateColor(color) {
    var rgb = toRGB(color);
    rgb[0] = Math.max(0, 255 - rgb[0]);
    rgb[1] = Math.max(0, 255 - rgb[1]);
    rgb[2] = Math.max(0, 255 - rgb[2]);
    return 'rgb('+rgb.join(', ')+')';;
}
function createColor(color) {
	var li = $(document.createElement('li'));
	li.css({
		'backgroundColor':color
	}).attr('val',color);
	return li;
}
function createPane(val, onpicked) {
    var hc = ["FF","CC","99","66","33","00"],
        r, g, b, c,
        control, show, clear, trans;
    pane = $('<ul class="'+CLASSES.pane+'"></ul>');
    for (r = 0; r < 6; r++) {
        for (g = 0; g < 6; g++) {
            for(b = 0; b < 6; b++) {
                c = '#' + hc[r] + hc[g] + hc[b];
                pane.append(createColor(c));
            }
        }
    }
    control = $('<li class="color-control"></li>');
    show = $('<span class="color-control-show">&nbsp;</span>').appendTo(control);
    clear = $('<span title="清除" val="blank">E</span>').appendTo(control).click(function() {
        onpicked('');
    });
    trans = $('<span title="透明" val="transparent">T</span>').appendTo(control).click(function() {
        onpicked('transparent');
    });
    control.appendTo(pane);
	pane.appendTo(document.body);
    pane.find('[val]').mouseenter(function() {
        var val = $(this).attr('val');
        show.css({
            'background-color': val || '',
            'color': val && val != 'transparent' && val != 'blank' ? negateColor(val) : '#000'
        });
        show.text(val && val != 'blank' ? val : '');
    }).filter('[val='+val+']').trigger('mouseenter');
}
var colorInput = function(input) {
	var $input = $(input).addClass(CLASSES.input),
		$box = $('<label class="'+CLASSES.box+'"></label>'),
		$picker = $('<span class="'+CLASSES.picker+'" title="'+LANG.selectColor+'"></span>'),
		_color = $input.val() || '',
		_oninited = new Function('color', $input.attr('oninited') || ''),
		_onpicked = new Function('color', $input.attr('onpicked') || '');
	
	// init
	$picker.css('backgroundColor', _color);
	_oninited.apply($input, [_color]);
	
	var onpicked = function(color){
		$picker.css('backgroundColor', color || '');
		$input.val(color || '');
		_onpicked.apply($input, [color]);
        hidePane($picker);
	},
	_getCapture = function(t){
		return t == input || t == $picker[0];
	},
	_showPane = function(){
		showPane(input.value, $picker, onpicked, _getCapture);
	};
	$input.focus(function(){
		$box.addClass(CLASSES.active);
	}).blur(function(){
		$box.removeClass(CLASSES.active);
	}).keyup(function(){
		var color = this.value;
		$picker.css('backgroundColor', color || '');
		_onpicked.apply($input, [color]);
		$input.triggerHandler('picked', [color]);
	});
	$.event.add(input, $input.attr('events') || 'focus', _showPane);
	$.event.add($picker[0], 'click', _showPane);
	$.event.add(input, 'keydown', function(e){
		if (e.keyCode == 9 || e.keyCode == 27) {
			hidePane($picker);
		}
	});
	$input.after($box);
	$box.append($input).append($picker);
};
$.fn.colorInput = function(){
	this.each(function(){
		colorInput(this);
	});
	return this;
}
})(jQuery);