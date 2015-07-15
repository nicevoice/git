/**
 * CmsTop DIY Engine
 *
 * @author		kakalong
 * @copyright	2010 (c) cmstop.com
 * @version		$Id: diy.js 5385 2012-04-27 02:36:37Z kakalong $
 * @depends
 *		uidialog
 *		cmstop
 */
(function($, window){
var document = window.document, $doc = $(),
	head = document.head || document.getElementsByTagName('head')[0] || document.documentElement,
	body = null,
	guidStack = [], ostyle = null, osheet = null,
	COLORS = ['6694E3', 'f69', '9c9', 'fc3', '0cf', '090', 'c00'],
    READY;
function toInt(num) {
	num = parseInt(num);
	return isNaN(num) ? 0 : num;
}
function toFloat(num) {
	num = parseFloat(num);
	return isNaN(num) ? 0 : num;
}
function toFixed(num) {
	return toFloat(num).toFixed(1);
}
function hasClass(node, className) {
	return (" " + node.className + " ").indexOf(" " + className + " ") > -1;
}
function guid(prefix) {
	var id;
	do {
		id = prefix + (new Date()).getTime().toString().substr(11) + (Math.random()*100).toFixed();
	} while ($.inArray(id, guidStack) != -1 || $('#'+id).length);
	return id;
}
function ival(v) {
	v = $.trim(v);
	if (!v) return null;
	var m = RE.ival.exec(v);
	if (m) {
		return m[0];
	}
	v = parseFloat(v);
	return isNaN(v) ? null : (v == 0 ? '0' : v+'px');
}
function bval(v) {
	v = $.trim(v);
	if (!v) return null;
	var m = RE.bval.exec(v);
	if (m) {
		return m[0];
	}
	v = parseFloat(v);
	return isNaN(v) ? null : (v == 0 ? '0' : v+'px');
}
function sval(v) {
	v = $.trim(v);
	return v && RE.sval.test(v) ? v : null;
}
function addClass(elem, classNames) {
	return $.className.add(elem, classNames);
}
function removeClass(elem, classNames) {
	return $.className.remove(elem, classNames);
}
function switchClass(elem, orig, add, prefix) {
	if (orig == add) return;
	orig && elem.removeClass(prefix+orig);
	add && elem.addClass(prefix+add);
}
function setRule(selectorText, ruleText) {
	if (!osheet) {
		ostyle = document.getElementById('ostyle');
		if (!ostyle || ostyle.tagName != 'STYLE') {
			ostyle = document.createElement('style');
			ostyle.type = 'text/css';
			head.appendChild(ostyle);
		}
		osheet = ostyle.sheet || ostyle.styleSheet;
	}
	var oRules = osheet.cssRules || osheet.rules, i = oRules.length-1, r;
	while (i >= 0) {
		r = oRules[i];
		if (r.selectorText == selectorText) {
			osheet.deleteRule ? osheet.deleteRule(i) : osheet.removeRule(i);
			break;
		} else {
			i--;
		}
	}
	if (ruleText) {
		if ( osheet.insertRule ) {
			osheet.insertRule(selectorText + '{' + ruleText + '}', osheet.cssRules.length);
		} else if ( osheet.addRule ) {
			osheet.addRule(selectorText, ruleText);
		}
	}
}
$.fn.isnot = function(filter, is, not){
	return this.each(function(i){
		(typeof filter == 'function'
			? filter.call(this)
			: ($.multiFilter(filter, [this]).length > 0)
		) ? (is && is.apply(this, [i]))
		  : (not && not.apply(this, [i]));
	});
};
$.fn.themeInput = function(){
	var _cached = {},
		url = '?app=special&controller=online&action=getTheme';
	return function(ok, set){
		var oknum = 0, total = this.length;
		return this.each(function(){
			var t=this, input=$(t), actived=null, cat=input.attr('cat'),
				data = 'cat='+encodeURIComponent(cat),
				ul = $('<ul class="item-list"></ul>').insertAfter(t);
			function ali(item) {
				var title = item.text||item.name;
				return $('<li name="'+item.name+'" title="'+title+'"><a><img src="'+item.thumb+'" /></a></li>').click(function(){
					t.value = item.name;
					input.triggerHandler('changed', [item.name, item.thumb]);
					actived && removeClass(actived, 'actived');
					actived = this;
					addClass(actived, 'actived');
					return false;
				});
			}
			function b(data){
				for (var k in data) {
					ul.append(ali(data[k]));
				}
				var lis = ul.children();
				if (!set && lis.length) {
					var type = cat.split('/'), c = Theme.def[type[0]]; 
					if (c && type[0] == 'content') {
						c = type[1] ? c[type[1]] : null;
					}
					ul.prepend('<li class="clear"></li>');
					ul.prepend(ali($.extend({
						thumb:'apps/special/images/default-style.gif'
					}, c && data[c]||{}, {
						name:'',
						text:'默认'
					})));
				}
				var n = ali({
					name:'(empty)',
					thumb:'apps/special/images/no-style.gif',
					text:'无样式'
				});
				ul.prepend(n);
				var v = ul.children('[name="'+t.value+'"]');
				(!set || v.length ? v : n).click();
				++oknum == total && ok && ok();
			}
			var cdata = _cached[data];
			if (cdata) {
				b(cdata);
			} else {
				$.ajax({
					type:'GET',
					dataType:'json',
					url:url,
					data:data,
					success:function(json){
						_cached[data] = json;
						b(json);
					}
				});
			}
		});
	};
}();
var UI_URL = IMG_URL+'apps/special/ui',
LANG = {
	BUTTON_OK : '确定',
	BUTTON_CANCEL:'取消'
},
elementFromEvent = function(){
	var userAgent = navigator.userAgent.toLowerCase(), v;
	return (window.opera
		? ((v = /version\/([\d\.]+)/.exec(userAgent)) && toFloat(v[1]) < 10.5)
		: ((v = /webkit\/([\d\.]+)/.exec(userAgent)) && toFloat(v[1]) < 533))
	? function(e){
		return document.elementFromPoint(e.pageX, e.pageY);
	} : function(e){
		return document.elementFromPoint(e.clientX, e.clientY);
	};
}(),
getOffset = function() {
	var jq = $();
	return function(el){
		jq[0] = el;
		return jq.offset();
	};
}(),
getPosition = function(){
	var jq = $();
	return function(el){
		jq[0] = el;
		return jq.position();
	};
}(),
each = function(){
	var re = /\s+/;
	return function(o, fn){
		typeof o == 'string' && (o = o.split(re));
		return $.each(o, fn);
	};
}(),
RE = function(){
	var s = {
		center:/\bmargin *: *(?:[^ ]+ +)?auto *(?:;|$)/i,
		'text-center':/\btext\-align *: *center *(?:;|$)/i,
		block:/\bdisplay *: *block *(?:;|$)/i,
		offset:/\bmargin-(?:left|right) *: *([^;]*) *(?:;|$)/i,
		'background-image':/\bbackground-image *: *url *\( *["']?([^;"']*)["']? *\) *(?:;|$)/i,
		ival:/[\d\.]+(?:px|pt|em)|[\d\.]+%|auto/i,
		bval:/^-?[\d\.]+(?:px|pt|em)|[\d\.]+%|left|right|center|bottom|top$/i,
		sval:/^[^;'"]+$/,
		percent:/([\d\.]+)%/,
		colorful:/\bcolor\-(\w+)\b/
	};
	each('height width color', function(){
		s[this] = new RegExp('(?:[; ]|^)'+this+' *: *([^;]*) *(?:;|$)', 'i');
	});
	each('line-height float margin padding font-size font-weight font-style font-family '+
		'border-width border-color border-style '+
		'border-top-width border-right-width border-bottom-width border-left-width '+
		'border-top-color border-right-color border-bottom-color border-left-color '+
		'border-top-style border-right-style border-bottom-style border-left-style '+
		'margin-top margin-right margin-bottom margin-left '+
		'padding-top padding-right padding-bottom padding-left '+
		'background-color background-repeat background-position',
	function(){
		s[this] = new RegExp('\\b'+this+' *: *([^;]*) *(?:;|$)', 'i');
	});
	each('padding margin border-style border-width border-color',
	function(){
		s[this+'-all'] = new RegExp('\\b'+this+'-', 'i');
	});
	return s;
}(),
TEMPLATE = function(){
return {
PANNEL:
'<div id="diy-pannel"></div>',
PANNEL_HEADER:
'<div id="diy-header">'+
	'<div id="diy-logo"></div>'+
	'<ul id="diy-nav"></ul>'+
	'<div id="diy-addbtn"></div>'+
'</div>',
PANNEL_CENTER:
'<div id="diy-center">'+
	'<div id="diy-toolset">'+
		'<ul class="diy-tool" name="frame">'+
			'<li class="diy-toolwrap">'+
				'<div class="diy-toolbox"></div>'+
				'<p class="diy-tooltitle">比例布局</p>'+
			'</li>'+
		'</ul>'+
		'<ul class="diy-tool" name="widget">'+
			'<li class="diy-toolwrap diy-w-2-1">'+
				'<div class="diy-toolbox"></div>'+
				'<p class="diy-tooltitle">系统模块</p>'+
			'</li>'+
			'<li class="diy-toolwrap diy-w-2-1">'+
				'<div class="diy-toolbox"></div>'+
				'<p class="diy-tooltitle">&nbsp;</p>'+
			'</li>'+
		'</ul>'+
		'<ul class="diy-tool" name="theme">'+
			'<li class="diy-toolwrap">'+
				'<div class="diy-toolbox"></div>'+
				'<p class="diy-tooltitle">&nbsp;</p>'+
			'</li>'+
		'</ul>'+
		'<ul class="diy-tool" name="setting">'+
			'<li class="diy-toolwrap diy-w-5-2">'+
				'<div class="diy-toolbox"></div>'+
				'<p class="diy-tooltitle">SEO 相关</p>'+
			'</li>'+
			'<li class="diy-toolwrap diy-w-5-3">'+
				'<div class="diy-toolbox"></div>'+
				'<p class="diy-tooltitle">资源文件</p>'+
			'</li>'+
		'</ul>'+
	'</div>'+
	'<div id="diy-tab">'+
		'<ul>'+
			'<li target="theme">风格</li>'+
			'<li target="frame">布局</li>'+
			'<li target="widget">模块</li>'+
			'<li target="setting">设置</li>'+
		'</ul>'+
	'</div>'+
'</div>'+
'<div id="diy-toggle"></div>',
CONTEXT_MENU:
'<ul class="contextMenu" id="page-menu">'+
	'<li class=""><a href="#viewPage">查看</a></li>'+
	'<li class=""><a href="#publish">发布</a></li>'+
	'<li class=""><a href="#offline">下线</a></li>'+
	'<li class=""><a href="#copyPage">拷贝</a></li>'+
	'<li class=""><a href="#setPage">设置</a></li>'+
	'<li class=""><a href="#editTemplate">编辑模板</a></li>'+
	'<li class=""><a href="#delPage">删除</a></li>'+
'</ul>',
CONTROL:
'<div id="diy-control">'+
    '<div id="diy-control-mark">正在准备页面布局，请稍后...</div>'+
	'<span class="back" title="撤销（CTRL+SHIFT+Z）"></span>'+
	'<span class="forward" title="重做（CTRL+SHIFT+Y）"></span>'+
	'<span action="scheme" title="保存方案（CTRL+SHIFT+Q）"></span>'+
	'<span action="preview" title="预览（CTRL+SHIFT+V）"></span>'+
	'<span action="save" title="保存（CTRL+SHIFT+S）">保存</span>'+
	'<span action="publish" title="发布（CTRL+SHIFT+P）">发布</span>'+
'</div>',
MORE:
'<div id="diy-more">'+
	'<i class="edit" action="editWidget" title="编辑"></i>'+
	'<i class="style" action="setStyle" title="样式"></i>'+
	'<i class="title" action="setTitle" title="标题"></i>'+
	'<i class="share" action="shareWidget" title="共享"></i>'+
	'<i class="publish" action="pubWidget" title="发布"></i>'+
	'<i class="remove" action="remove" title="移除"></i>'+
'</div>',
NEW_FRAME:
'<form><div style="text-align:center;padding:10px;">'+
	'列数：<input class="diy-size-7" type="text" name="column" value="" />'+
'</div></form>',
SAVE_AS:
'<form><div style="padding: 10px 15px;line-height:28px">'+
	'名称：<input value="" type="text" name="name" /><br/>'+
	'缩略图：<input value="" type="text" name="thumb" class="diy-size-15" />'+
'</div></form>',
SET_UI:
'<div class="head"><span></span></div>'+
'<form><div class="set-ui"><ul></ul></div></form>',
SET_PAGE:
'<form>'+
	'<table width="95%" border="0" cellspacing="0" cellpadding="0">'+
		'<tbody>'+
			'<tr>'+
				'<th width="80">字体：</th>'+
				'<td>'+
					'<label>大小：<input name="font-size" type="text" class="diy-size-5" /></label>'+
					'<label>颜色：<input name="color" class="diy-size-7 color-input" /></label>'+
					'<br />'+
					'<label>样式：<input name="font-style" type="text" class="diy-size-5" /></label>'+
					'<label>分量：<input name="font-weight" type="text" class="diy-size-5" /></label>'+
				'</td>'+
			'</tr>'+
			'<tr>'+
				'<th>链接：</th>'+
				'<td>'+
					'<label>大小：<input name="a-font-size" type="text" class="diy-size-5" /></label>'+
					'<label>颜色：<input name="a-color" class="diy-size-7 color-input" /></label>'+
					'<br />'+
					'<label>样式：<input name="a-font-style" type="text" class="diy-size-5" /></label>'+
					'<label>分量：<input name="a-font-weight" type="text" class="diy-size-5" /></label>'+
				'</td>'+
			'</tr>'+
			'<tr>'+
				'<th>背景：</th>'+
				'<td>'+
					'颜色：<input name="background-color" class="color-input diy-size-7" value="" /><br />'+
					'图像：<input name="background-image" type="text" class="image-input" value="" /><br />'+
					'重复：<select name="background-repeat">'+
						'<option></option>'+
						'<option value="repeat">平铺</option>'+
						'<option value="no-repeat">不平铺</option>'+
						'<option value="repeat-x">横向平铺</option>'+
						'<option value="repeat-y">纵向平铺</option>'+
					'</select><br />'+
					'位置：<input name="background-position-x" type="text" class="diy-size-5" value="" /> - <input name="background-position-y" type="text" class="diy-size-5" value="" />'+
				'</td>'+
			'</tr>'+
		'</tbody>'+
	'</table>'+
'</form>',
SET_FRAME:
'<div class="tabs">'+
	'<ul target=".item-option">'+
		'<li>外框样式</li>'+
		'<li>标题样式</li>'+
	'</ul>'+
'</div>'+
'<form>'+
'<div class="set-style">'+
	'<div class="item-option">'+
		'<input type="hidden" class="theme-input" name="frame-theme" />'+
		'<div class="item-detail"><i></i>自定义</div>'+
		'<table width="470" border="0" cellspacing="0" cellpadding="0">'+
			'<tbody>'+
				'<tr>'+
					'<th width="80">高度：</th>'+
					'<td>'+
						'<input name="frame-height" type="text" class="diy-size-5" />'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<th>宽度：</th>'+
					'<td>'+
						'<input name="frame-width" type="text" class="diy-size-5" />'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<th>居中：</th>'+
					'<td><input name="frame-center" type="checkbox" value="1" /></td>'+
				'</tr>'+
				'<tr>'+
					'<th>边框：</th>'+
					'<td>'+
						'<p>厚度：<span style="display:inline-block">'+
							'<label style="float:right"><input type="checkbox" name="frame-border-width-all" /> 分别设置</label>'+
							'<label><span>上：</span><input name="frame-border-top-width" type="text" class="diy-size-3" value=""></label>'+
							'<label><span>右：</span><input name="frame-border-right-width" type="text" class="diy-size-3" value=""></label>'+
							'<br />'+
							'<label><span>下：</span><input name="frame-border-bottom-width" type="text" class="diy-size-3" value=""></label>'+
							'<label><span>左：</span><input name="frame-border-left-width" type="text" class="diy-size-3" value=""></label>'+
						'</span></p>'+
						'<p>样式：<span style="display:inline-block">'+
							'<label style="float:right"><input type="checkbox" name="frame-border-style-all" /> 分别设置</label>'+
							'<label><span>上：</span>'+
								'<select name="frame-border-top-style">'+
									'<option></option>'+
									'<option value="none">无样式</option>'+
									'<option value="solid">实线</option>'+
									'<option value="dotted">点线</option>'+
									'<option value="dashed">虚线</option>'+
								'</select>'+
							'</label>'+
							'<label><span>右：</span>'+
								'<select name="frame-border-right-style">'+
									'<option></option>'+
									'<option value="none">无样式</option>'+
									'<option value="solid">实线</option>'+
									'<option value="dotted">点线</option>'+
									'<option value="dashed">虚线</option>'+
								'</select>'+
							'</label>'+
							'<br />'+
							'<label><span>下：</span>'+
								'<select name="frame-border-bottom-style">'+
									'<option></option>'+
									'<option value="none">无样式</option>'+
									'<option value="solid">实线</option>'+
									'<option value="dotted">点线</option>'+
									'<option value="dashed">虚线</option>'+
								'</select>'+
							'</label>'+
							'<label><span>左：</span>'+
								'<select name="frame-border-left-style">'+
									'<option></option>'+
									'<option value="none">无样式</option>'+
									'<option value="solid">实线</option>'+
									'<option value="dotted">点线</option>'+
									'<option value="dashed">虚线</option>'+
								'</select>'+
							'</label>'+
						'</span></p>'+
						'<p>颜色：<span style="display:inline-block">'+
							'<label style="float:right"><input type="checkbox" name="frame-border-color-all" /> 分别设置</label>'+
							'<label><span>上：</span><input name="frame-border-top-color" class="color-input diy-size-7" value="" /></label>'+
							'<label><span>右：</span><input name="frame-border-right-color" class="color-input diy-size-7" value="" /></label>'+
							'<br />'+
							'<label><span>下：</span><input name="frame-border-bottom-color" class="color-input diy-size-7" value="" /></label>'+
							'<label><span>左：</span><input name="frame-border-left-color" class="color-input diy-size-7" value="" /></label>'+
						'</span></p>'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<th>边距：</th>'+
					'<td>'+
						'<label style="float:right"><input type="checkbox" name="frame-margin-all" /> 分别设置</label>'+
						'<label><span>上：</span><input name="frame-margin-top" type="text" class="diy-size-3" value=""></label>'+
						'<label><span>右：</span><input name="frame-margin-right" type="text" class="diy-size-3" value=""></label>'+
						'<br />'+
						'<label><span>下：</span><input name="frame-margin-bottom" type="text" class="diy-size-3" value=""></label>'+
						'<label><span>左：</span><input name="frame-margin-left" type="text" class="diy-size-3" value=""></label>'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<th>背景：</th>'+
					'<td>'+
						'颜色：<input name="frame-background-color" class="color-input diy-size-7" value="" /><br />'+
						'图像：<input name="frame-background-image" type="text" class="image-input diy-size-20" value="" /><br />'+
						'重复：<select name="frame-background-repeat">'+
							'<option></option>'+
							'<option value="repeat">平铺</option>'+
							'<option value="no-repeat">不平铺</option>'+
							'<option value="repeat-x">横向平铺</option>'+
							'<option value="repeat-y">纵向平铺</option>'+
						'</select><br />'+
						'位置：<input name="frame-background-position-x" type="text" class="diy-size-5" value="" /> - <input name="frame-background-position-y" type="text" class="diy-size-5" value="" />'+
					'</td>'+
				'</tr>'+
			'</tbody>'+
		'</table>'+
	'</div>'+
	'<div class="item-option">'+
		'<input type="hidden" class="theme-input" name="title-theme" />'+
		'<div class="item-detail"><i></i>自定义</div>'+
		'<table width="470" border="0" cellspacing="0" cellpadding="0">'+
			'<tbody>'+
				'<tr>'+
					'<th width="80">字体：</th>'+
					'<td>'+
						'<label>大小：<input name="title-w-font-size" type="text" class="diy-size-5" /></label>'+
						'<label>颜色：<input name="title-w-color" class="diy-size-7 color-input" /></label>'+
						'<br />'+
						'<label>样式：<input name="title-w-font-style" type="text" class="diy-size-5" /></label>'+
						'<label>分量：<input name="title-w-font-weight" type="text" class="diy-size-5" /></label>'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<th>链接：</th>'+
					'<td>'+
						'<label>大小：<input name="title-a-font-size" type="text" class="diy-size-5" /></label>'+
						'<label>颜色：<input name="title-a-color" class="diy-size-7 color-input" /></label>'+
						'<br />'+
						'<label>样式：<input name="title-a-font-style" type="text" class="diy-size-5" /></label>'+
						'<label>分量：<input name="title-a-font-weight" type="text" class="diy-size-5" /></label>'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<th>边距：</th>'+
					'<td>'+
						'<label style="float:right"><input type="checkbox" name="title-padding-all"  /> 分别设置</label>'+
						'<label><span>上：</span><input name="title-padding-top" type="text" class="diy-size-3" value=""></label>'+
						'<label><span>右：</span><input name="title-padding-right" type="text" class="diy-size-3" value=""></label>'+
						'<br />'+
						'<label><span>下：</span><input name="title-padding-bottom" type="text" class="diy-size-3" value=""></label>'+
						'<label><span>左：</span><input name="title-padding-left" type="text" class="diy-size-3" value=""></label>'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<th>背景：</th>'+
					'<td>'+
						'颜色：<input name="title-background-color" class="color-input diy-size-7" value="" /><br />'+
						'图像：<input name="title-background-image" type="text" class="image-input diy-size-20" value="" /><br />'+
						'重复：<select name="title-background-repeat">'+
							'<option></option>'+
							'<option value="repeat">平铺</option>'+
							'<option value="no-repeat">不平铺</option>'+
							'<option value="repeat-x">横向平铺</option>'+
							'<option value="repeat-y">纵向平铺</option>'+
						'</select><br />'+
						'位置：<input name="title-background-position-x" type="text" class="diy-size-5" value="" /> - <input name="title-background-position-y" type="text" class="diy-size-5" value="" />'+
					'</td>'+
				'</tr>'+
			'</tbody>'+
		'</table>'+
	'</div>'+
'</div></form>',
SET_WIDGET:
'<div class="tabs">'+
	'<ul target=".item-option">'+
		'<li>外框样式</li>'+
		'<li>标题样式</li>'+
		'<li>内容样式</li>'+
	'</ul>'+
'</div>'+
'<form>'+
'<div class="set-style">'+
	'<div class="item-option">'+
		'<input type="hidden" class="theme-input" name="widget-theme" />'+
		'<div class="item-detail"><i></i>自定义</div>'+
		'<table width="470" border="0" cellspacing="0" cellpadding="0">'+
			'<tbody>'+
				'<tr>'+
					'<th width="80">文本：</th>'+
					'<td>'+
						'<label>大小：<input name="inner-w-font-size" type="text" class="diy-size-5" /></label>'+
						'<label>颜色：<input name="inner-w-color" class="diy-size-7 color-input" /></label>'+
						'<br />'+
						'<label>样式：<input name="inner-w-font-style" type="text" class="diy-size-5" /></label>'+
						'<label>分量：<input name="inner-w-font-weight" type="text" class="diy-size-5" /></label>'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<th>链接：</th>'+
					'<td>'+
						'<label>大小：<input name="inner-a-font-size" type="text" class="diy-size-5" /></label>'+
						'<label>颜色：<input name="inner-a-color" class="diy-size-7 color-input" /></label>'+
						'<br />'+
						'<label>样式：<input name="inner-a-font-style" type="text" class="diy-size-5" /></label>'+
						'<label>分量：<input name="inner-a-font-weight" type="text" class="diy-size-5" /></label>'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<th>高度：</th>'+
					'<td>'+
						'<input name="widget-height" type="text" class="diy-size-5" />'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<th>宽度：</th>'+
					'<td>'+
						'<input name="widget-width" type="text" class="diy-size-5" />'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<th>边框：</th>'+
					'<td>'+
						'<p>厚度：<span style="display:inline-block">'+
							'<label style="float:right"><input type="checkbox" name="inner-border-width-all" /> 分别设置</label>'+
							'<label><span>上：</span><input name="inner-border-top-width" type="text" class="diy-size-3" value=""></label>'+
							'<label><span>右：</span><input name="inner-border-right-width" type="text" class="diy-size-3" value=""></label>'+
							'<br />'+
							'<label><span>下：</span><input name="inner-border-bottom-width" type="text" class="diy-size-3" value=""></label>'+
							'<label><span>左：</span><input name="inner-border-left-width" type="text" class="diy-size-3" value=""></label>'+
						'</span></p>'+
						'<p>样式：<span style="display:inline-block">'+
							'<label style="float:right"><input type="checkbox" name="inner-border-style-all" /> 分别设置</label>'+
							'<label><span>上：</span>'+
								'<select name="inner-border-top-style">'+
									'<option></option>'+
									'<option value="none">无样式</option>'+
									'<option value="solid">实线</option>'+
									'<option value="dotted">点线</option>'+
									'<option value="dashed">虚线</option>'+
								'</select>'+
							'</label>'+
							'<label><span>右：</span>'+
								'<select name="inner-border-right-style">'+
									'<option></option>'+
									'<option value="none">无样式</option>'+
									'<option value="solid">实线</option>'+
									'<option value="dotted">点线</option>'+
									'<option value="dashed">虚线</option>'+
								'</select>'+
							'</label>'+
							'<br />'+
							'<label><span>下：</span>'+
								'<select name="inner-border-bottom-style">'+
									'<option></option>'+
									'<option value="none">无样式</option>'+
									'<option value="solid">实线</option>'+
									'<option value="dotted">点线</option>'+
									'<option value="dashed">虚线</option>'+
								'</select>'+
							'</label>'+
							'<label><span>左：</span>'+
								'<select name="inner-border-left-style">'+
									'<option></option>'+
									'<option value="none">无样式</option>'+
									'<option value="solid">实线</option>'+
									'<option value="dotted">点线</option>'+
									'<option value="dashed">虚线</option>'+
								'</select>'+
							'</label>'+
						'</span></p>'+
						'<p>颜色：<span style="display:inline-block">'+
							'<label style="float:right"><input type="checkbox" name="inner-border-color-all" /> 分别设置</label>'+
							'<label><span>上：</span><input name="inner-border-top-color" class="color-input diy-size-7" value="" /></label>'+
							'<label><span>右：</span><input name="inner-border-right-color" class="color-input diy-size-7" value="" /></label>'+
							'<br />'+
							'<label><span>下：</span><input name="inner-border-bottom-color" class="color-input diy-size-7" value="" /></label>'+
							'<label><span>左：</span><input name="inner-border-left-color" class="color-input diy-size-7" value="" /></label>'+
						'</span></p>'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<th>外边距：</th>'+
					'<td>'+
						'<label style="float:right"><input type="checkbox" name="inner-margin-all" /> 分别设置</label>'+
						'<label><span>上：</span><input name="inner-margin-top" type="text" class="diy-size-3" value=""></label>'+
						'<label><span>右：</span><input name="inner-margin-right" type="text" class="diy-size-3" value=""></label>'+
						'<br />'+
						'<label><span>下：</span><input name="inner-margin-bottom" type="text" class="diy-size-3" value=""></label>'+
						'<label><span>左：</span><input name="inner-margin-left" type="text" class="diy-size-3" value=""></label>'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<th>内边距：</th>'+
					'<td>'+
						'<label style="float:right"><input type="checkbox" name="inner-padding-all"  /> 分别设置</label>'+
						'<label><span>上：</span><input name="inner-padding-top" type="text" class="diy-size-3" value=""></label>'+
						'<label><span>右：</span><input name="inner-padding-right" type="text" class="diy-size-3" value=""></label>'+
						'<br />'+
						'<label><span>下：</span><input name="inner-padding-bottom" type="text" class="diy-size-3" value=""></label>'+
						'<label><span>左：</span><input name="inner-padding-left" type="text" class="diy-size-3" value=""></label>'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<th>居中：</th>'+
					'<td><input name="widget-center" type="checkbox" value="1" /></td>'+
				'</tr>'+
				'<tr>'+
					'<th>背景：</th>'+
					'<td>'+
						'颜色：<input name="inner-background-color" class="diy-size-7" class="color-input" value="" /><br />'+
						'图像：<input name="inner-background-image" type="text" class="diy-size-25 image-input" value="" /><br />'+
						'重复：<select name="inner-background-repeat">'+
							'<option></option>'+
							'<option value="repeat">平铺</option>'+
							'<option value="no-repeat">不平铺</option>'+
							'<option value="repeat-x">横向平铺</option>'+
							'<option value="repeat-y">纵向平铺</option>'+
						'</select><br />'+
						'位置：<input name="inner-background-position-x" type="text" class="diy-size-5" value="" /> - <input name="inner-background-position-y" type="text" class="diy-size-5" value="" />'+
					'</td>'+
				'</tr>'+
			'</tbody>'+
		'</table>'+
	'</div>'+
	'<div class="item-option">'+
		'<input type="hidden" class="theme-input" name="title-theme" />'+
		'<div class="item-detail"><i></i>自定义</div>'+
		'<table width="470" border="0" cellspacing="0" cellpadding="0">'+
			'<tbody>'+
				'<tr>'+
					'<th width="80">文本：</th>'+
					'<td>'+
						'<label>大小：<input name="title-w-font-size" type="text" class="diy-size-5" /></label>'+
						'<label>颜色：<input name="title-w-color" class="diy-size-7 color-input" /></label>'+
						'<br />'+
						'<label>样式：<input name="title-w-font-style" type="text" class="diy-size-5" /></label>'+
						'<label>分量：<input name="title-w-font-weight" type="text" class="diy-size-5" /></label>'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<th>链接：</th>'+
					'<td>'+
						'<label>大小：<input name="title-a-font-size" type="text" class="diy-size-5" /></label>'+
						'<label>颜色：<input name="title-a-color" class="diy-size-7 color-input" /></label>'+
						'<br />'+
						'<label>样式：<input name="title-a-font-style" type="text" class="diy-size-5" /></label>'+
						'<label>分量：<input name="title-a-font-weight" type="text" class="diy-size-5" /></label>'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<th>边距：</th>'+
					'<td>'+
						'<label style="float:right"><input type="checkbox" name="title-padding-all"  /> 分别设置</label>'+
						'<label><span>上：</span><input name="title-padding-top" type="text" class="diy-size-3" value=""></label>'+
						'<label><span>右：</span><input name="title-padding-right" type="text" class="diy-size-3" value=""></label>'+
						'<br />'+
						'<label><span>下：</span><input name="title-padding-bottom" type="text" class="diy-size-3" value=""></label>'+
						'<label><span>左：</span><input name="title-padding-left" type="text" class="diy-size-3" value=""></label>'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<th>背景：</th>'+
					'<td>'+
						'颜色：<input name="title-background-color" class="color-input diy-size-7" value="" /><br />'+
						'图像：<input name="title-background-image" type="text" class="image-input diy-size-20" value="" /><br />'+
						'重复：<select name="title-background-repeat">'+
							'<option></option>'+
							'<option value="repeat">平铺</option>'+
							'<option value="no-repeat">不平铺</option>'+
							'<option value="repeat-x">横向平铺</option>'+
							'<option value="repeat-y">纵向平铺</option>'+
						'</select><br />'+
						'位置：<input name="title-background-position-x" type="text" class="diy-size-5" value="" /> - <input name="title-background-position-y" type="text" class="diy-size-5" value="" />'+
					'</td>'+
				'</tr>'+
			'</tbody>'+
		'</table>'+
	'</div>'+
	'<div class="item-option">'+
		'<input type="hidden" class="theme-input" name="content-theme" />'+
		'<div class="item-detail"><i></i>自定义</div>'+
		'<table width="470" border="0" cellspacing="0" cellpadding="0">'+
			'<tbody>'+
				'<tr>'+
					'<th width="80">文本：</th>'+
					'<td>'+
						'<label>大小：<input name="content-w-font-size" type="text" class="diy-size-5" /></label>'+
						'<label>颜色：<input name="content-w-color" class="diy-size-7 color-input" /></label>'+
						'<br />'+
						'<label>样式：<input name="content-w-font-style" type="text" class="diy-size-5" /></label>'+
						'<label>分量：<input name="content-w-font-weight" type="text" class="diy-size-5" /></label>'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<th>链接：</th>'+
					'<td>'+
						'<label>大小：<input name="content-a-font-size" type="text" class="diy-size-5" /></label>'+
						'<label>颜色：<input name="content-a-color" class="diy-size-7 color-input" /></label>'+
						'<br />'+
						'<label>样式：<input name="content-a-font-style" type="text" class="diy-size-5" /></label>'+
						'<label>分量：<input name="content-a-font-weight" type="text" class="diy-size-5" /></label>'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<th>行高：</th>'+
					'<td>'+
						'<input name="content-line-height" type="text" class="diy-size-5" />'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<th>高度：</th>'+
					'<td>'+
						'<input name="content-height" type="text" class="diy-size-5" />'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<th>文字居中：</th>'+
					'<td><input name="content-text-center" type="checkbox" value="1" /></td>'+
				'</tr>'+
				'<tr>'+
					'<th>边距：</th>'+
					'<td>'+
						'<label style="float:right"><input type="checkbox" name="content-padding-all"  /> 分别设置</label>'+
						'<label><span>上：</span><input name="content-padding-top" type="text" class="diy-size-3" value=""></label>'+
						'<label><span>右：</span><input name="content-padding-right" type="text" class="diy-size-3" value=""></label>'+
						'<br />'+
						'<label><span>下：</span><input name="content-padding-bottom" type="text" class="diy-size-3" value=""></label>'+
						'<label><span>左：</span><input name="content-padding-left" type="text" class="diy-size-3" value=""></label>'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<th>背景：</th>'+
					'<td>'+
						'颜色：<input name="content-background-color" class="color-input diy-size-7" value="" /><br />'+
						'图像：<input name="content-background-image" type="text" class="image-input diy-size-20" value="" /><br />'+
						'重复：<select name="content-background-repeat">'+
							'<option></option>'+
							'<option value="repeat">平铺</option>'+
							'<option value="no-repeat">不平铺</option>'+
							'<option value="repeat-x">横向平铺</option>'+
							'<option value="repeat-y">纵向平铺</option>'+
						'</select><br />'+
						'位置：<input name="content-background-position-x" type="text" class="diy-size-5" value="" /> - <input name="content-background-position-y" type="text" class="diy-size-5" value="" />'+
					'</td>'+
				'</tr>'+
			'</tbody>'+
		'</table>'+
	'</div>'+
'</div></form>',
SET_TITLE: 
'<div class="bk_8"></div>'+
'<form>'+
'<table width="95%" border="0" cellspacing="0" cellpadding="0">'+
	'<tbody>'+
		'<tr>'+
			'<th width="80">标题：</th>'+
			'<td><input name="text" type="text" class="diy-size-35" value="" /></td>'+
		'</tr>'+
		'<tr>'+
			'<th>链接地址：</th>'+
			'<td>'+
                '<input name="href" type="text" class="diy-size-35" value="" />'+
                ' <a class="fn-widget-morelist" href="javascript:;">使用更多链接</a>'+
            '</td>'+
		'</tr>'+
		'<tr>'+
			'<th>图标：</th>'+
			'<td><input name="img" type="text" class="image-input diy-size-15" value="" /></td>'+
		'</tr>'+
		'<tr>'+
			'<th>位置：</th>'+
			'<td>'+
				'<label><input type="radio" name="float" value="left" />浮左</label> '+
				'<label><input type="radio" name="float" value="center" />居中</label> '+
				'<label><input type="radio" name="float" value="right" />浮右</label> '+
				'<label>偏移：<input name="offset" type="text" class="diy-size-3" value="" /> px</label>'+
			'</td>'+
		'</tr>'+
		'<tr>'+
			'<th>字体：</th>'+
			'<td>'+
				'<label>大小：<input name="font-size" type="text" class="diy-size-3" value="" /></label>'+
				'<label>颜色：<input name="color" class="color-input diy-size-7" value="" /></label>'+
			'</td>'+
		'</tr>'+
        '<tr class="fn-widget-morelist">'+
            '<th>更多链接：</th>'+
            '<td>'+
                '<label><input type="checkbox" name="morelist.add" />添加</label>'+
            '</td>'+
        '</tr>'+
	'</tbody>'+
'</table></form>'
};
}();
function moveChilren(src, target){
	var el = src[0].firstChild, temp, nodes = [];
	target = target[0];
	while (el) {
		temp = el;
		nodes.push(el);
		el = el.nextSibling;
		target.appendChild(temp);
	}
	return nodes;
}
function scrollIntoView(elem) {
	var mt = toFloat(document.body.style.marginTop),
		offset = elem.offset(), st = $doc.scrollTop(), h = document.documentElement.clientHeight;
	if (offset.top < st + mt) {
		$doc.scrollTop(offset.top - mt);
	} else if (offset.top + elem[0].offsetHeight - st > h) {
		$doc.scrollTop(offset.top + elem[0].offsetHeight - h);
	}
}
function Load(url, ok, reload){
	var m = /css|js$/.exec(url.toLowerCase()), tag = 'img', attr = 'src';
	if (!m) {
		return Load.img(url, ok);
	}
	if (m[0] == 'css') {
		tag = 'link';
		attr = 'href';
	} else {
		tag = 'script';
	}
	var exists = $(tag).filter(function(){
		var val = this.getAttribute(attr), c = val && val.charAt(url.length);
		return val && val.indexOf(url) === 0 && (c == '?' || c == '&' || c === '');
	});
	if (exists.length) {
		if (!reload) {
			return ok && ok.call(exists[0]);
		}
		exists.remove();
		url += (url.indexOf('?') != -1 ? '&' : '?') + Math.random(5);
	}
	Load[tag](url, ok);
}
$.extend(Load, {
	link:function(url, ok){
		typeof ok == 'function' || (ok = function(){});
		var link = document.createElement('link');
		link.rel = 'stylesheet';
		link.href = url;
		link.type = 'text/css';
	    if (/msie|opera/i.test(navigator.userAgent)) {
			link.onload = link.onreadystatechange = function(){  
				if ( !link.readyState || /loaded|complete/.test(link.readyState) )
				{
					ok.call(link);
					link.onload = link.onreadystatechange = null;
				}
			};
		} else {
			//FF, Safari, Chrome
			var ival = setInterval(function(){  
				try {
					link.sheet.cssRules;
				} catch(e) {
					if (e.code != 1e3 && !e.message.match(/(?:security|denied)/i)) {
						return;
					}
				}
                if (ival) {
                    clearInterval(ival);
                    ival = undefined;
                }
				ok.call(link);
			}, 13);
		}
		if (ostyle) {
			ostyle.parentNode.insertBefore(link, ostyle);
		} else {
			head.appendChild(link);
		}
	    return link;
	},
	script:function(url, ok){
		typeof ok == 'function' || (ok = function(){});
		var script = document.createElement('script'), done = false;
		script.onload = script.onreadystatechange = function(){
			var rs = this.readyState;
			if ( !done && (!rs || rs == 'loaded' || rs == 'complete') )
		    {
				done = true;
				script.onload = script.onreadystatechange = null;
				ok.call(script);
			}
		};
		script.src = url;
		document.body.appendChild(script);
		return script;
	},
	img:function(url, ok){
		var img = new Image();
	    img.onload = function(){ok && ok.call(img, true)}; 
	    img.onerror = function(){ok && ok.call(img, false)};
	    img.src = url;
	}
});

var Theme = {
	locked:false,
	stoped:false,
	name:null,
	link:null,
	pname:null,
	plink:null,
	willset:null,
	def:null,
	lastDef:null,
	init:function(theme, usedDir){
		Theme.name = theme;
		Theme.pname = theme;
		Theme.usedDir = usedDir || [];
		Theme.link = $('link[theme]');
		Theme.ulink = $('link[used]');
		Theme.def = ENV.themes[theme] && ENV.themes[theme]['define'] || {};
		Theme.lastDef = Theme.def;
	},
	addDir:function(dir){
		if ($.inArray(dir, Theme.usedDir) != -1) return;
		Theme.usedDir.push(dir);
		var used = Theme.usedDir.join(',');
		Theme.paramUse('used='+encodeURIComponent(used),
		function(link){
			link.attr('used', used);
			Theme.ulink.after(link);
			setTimeout(function(){
				Theme.ulink.remove();
				Theme.ulink = link;
			}, 1);
		});
	},
	replace:function(pre, cur) {
		$('.diy-widget').each(function(){
			var w = $(this),
				t = w.find('.diy-title'),
				c = w.find('.diy-content'),
				engine = w.attr('engine');
			w.attr('widget-theme')
				|| switchClass(w, pre.widget, cur.widget, 'widget-');
			w.attr('title-theme')
				|| switchClass(t, pre.title, cur.title, 'title-');
			w.attr('content-theme')
				|| switchClass(c, 
						pre.content && pre.content[engine] || '',
						cur.content && cur.content[engine] || '',
						'content-'+engine+'-');
		});
		$('.diy-frame').each(function(){
			var f = $(this), t = f.children('.diy-title');
			f.attr('frame-theme')
				|| switchClass(f, pre.frame, cur.frame, 'frame-');
			f.attr('title-theme')
				|| switchClass(t, pre.title, cur.title, 'title-');
		});
	},
	paramUse:function(param, ok, stoped) {
		if (Theme.locked) return false;
		Theme.stoped = false;
		Theme.locked = true;
		function roll(){
			Theme.locked = false;
			stoped && stoped();
		}
		json('?app=special&controller=online&action=css&pageid='+ENV.pageid, param,
		function(json){
			if (Theme.stoped || !json.url) return roll();
			Load.link(json.url, function(){
				var l = $(this);
				if (Theme.stoped) {
					l.remove();
					return roll();
				}
				Theme.locked = false;
				return ok && ok(l);
			});
		},
		function(){
			return roll();
		});
	},
	use:function(theme){
		if (Theme.locked
			|| theme == Theme.pname
			|| (Theme.willset && theme != Theme.willset)
			|| !ENV.themes[theme]) return false;
		var def = ENV.themes[theme]['define'];
		if (! def) return false;
		Theme.pname = theme;
		Theme.paramUse('theme='+theme, function(link){
			link.attr('theme', theme);
			Theme.link.after(link);
			Theme.replace(Theme.def, def);
			Theme.link.appendTo(DIY.fragments);
			if (Theme.willset) {
				Theme.willset = null;
				Theme.name = theme;
				Theme.link = link;
				Theme.plink = null;
				Theme.def = def;
			} else {
				Theme.plink = link;
				Theme.cdef = def;
			}
		}, function(){
			Theme.plink = null;
			Theme.pname = Theme.name;
			if (Theme.willset) {
				Theme.use(Theme.willset);
			}
		});
	},
	set:function(theme){
		if (theme == Theme.name) {
			return;
		}
		Theme.willset = theme;
		if (theme == Theme.pname){
			if (! Theme.locked) {
				Theme.willset = null;
				Theme.link = Theme.plink;
				Theme.name = Theme.pname;
				Theme.plink = null;
				Theme.def = Theme.cdef;
				Theme.cdef = null;
			}
		} else {
			if (! Theme.locked) {
				return Theme.use(theme);
			}
			Theme.stoped = true;
		}
	},
	reset:function(){
		if (Theme.willset && Theme.pname == Theme.willset) return;
		Theme.stoped = true;
		if (Theme.plink && Theme.pname != Theme.name) {
			Theme.plink.replaceWith(Theme.link);
			Theme.plink = null;
			Theme.pname = Theme.name;
			Theme.replace(Theme.cdef, Theme.def);
			Theme.cdef = null;
		}
	}
};
function _url(url){
	var hasHash = url.indexOf('?') != -1;
	if (!/[&?]pageid=\d+/.test(url)) {
		url += (hasHash ? '&' : '?') + 'pageid='+ENV.pageid;
		hasHash = true;
	}
	if (!/[&?]contentid=\d+/.test(url)) {
		url += (hasHash ? '&' : '?') + 'contentid='+ENV.contentid;
	}
	return url;
}
function json(url, data, success, error, method) {
	if (typeof data == 'function') {
		method = error;
		error = success;
		success = data;
		data = null;
	}
	if (typeof error == 'string') {
		method = error;
		error = null;
	}
	return $.ajax({
		type:method||'POST',
		dataType:'json',
		data:data||'',
		url:_url(url),
		success:success||function(){},
		error:error||function(){}
	});
}
function localForm(opt, tpl, ready, ok, cancel) {
	var dialog = $(document.createElement('DIV')),
		form = null,
		submit = function(flag) {
			ok && ok(form, flag);
			(flag == 'ok' || flag == 'nextstep') && dialog.dialog('destroy').remove();
		}, buttons = {};
	
	LANG.BUTTON_OK && (buttons[LANG.BUTTON_OK] = function(){
		form && form.length && submit('ok');
	});
	LANG.BUTTON_NEXTSTEP && (buttons[LANG.BUTTON_NEXTSTEP] = function(){
		form && form.length && submit('nextstep');
	});
	LANG.BUTTON_PREVIEW && (buttons[LANG.BUTTON_PREVIEW] = function(){
		form && form.length && submit('preview');
	});
	LANG.BUTTON_SAVEAS && (buttons[LANG.BUTTON_SAVEAS] = function(){
		form && form.length && submit('saveas');
	});
	LANG.BUTTON_CANCEL && (buttons[LANG.BUTTON_CANCEL] = function() {
		dialog.dialog('close');
	});
	LANG.BUTTON_OK = '确定';
	LANG.BUTTON_CANCEL = '取消';
	LANG.BUTTON_NEXTSTEP = null;
	LANG.BUTTON_PREVIEW = null;
	LANG.BUTTON_SAVEAS = null;
	
	typeof opt == 'object' || (opt = {title:opt ? opt.toString() : ''});
	opt = $.extend({
		width :450,
		height:'auto',
		minHeight:80,
		maxHeight:500,
		resizable:false,
		modal : true
	}, opt, {
		buttons : buttons,
		close:function(){
			dialog.dialog('destroy').remove();
			cancel && cancel();
		}
	});
	dialog.html(TEMPLATE[tpl]||'').dialog(opt).css('position', 'relative');
	form = dialog.find('form:first');
	form && form.length && form.submit(function(e){
		e.preventDefault();
		e.stopPropagation();
		submit();
	});
	ready && ready(form, dialog);
	return dialog;
}
function serializeArray(form) {
	var rselectTextarea = /select|textarea/i,
		rinput = /text|hidden|password|search/i;
	return form.find('input,select,textarea')
	.filter(function(){
		return this.name && !this.disabled &&
			(this.checked || rselectTextarea.test(this.nodeName) ||
				rinput.test(this.type));
	})
	.map(function(i, elem){
		var val = jQuery(this).val();

		return val == null ?
			null :
			jQuery.isArray(val) ?
				jQuery.map( val, function(val, i){
					return {name: elem.name, value: val};
				}) :
				{name: elem.name, value: val};
	}).get();
}
function ajaxForm(opt, url, jsonok, formReady, beforeSubmit, afterSubmit, cancel, afterSerialize, thisVar) {
	url = _url(url);
    thisVar = thisVar || window;
	var dialog = $(document.createElement('DIV')),
	wrap, masker, warn, wival = null,
	form = null, buttons = {}, buttonArea,
	submit = function(flag) {
		buttonArea.children('button').attr('disabled', 'disabled');
		masker.css({height:wrap.height(), width:wrap.width()}).show();
		if (beforeSubmit && beforeSubmit.call(thisVar, form, dialog) === false) {
			complete();
			return;
		}
		
		var data = serializeArray(form);
		afterSerialize && afterSerialize.call(thisVar, data);
		$.ajax({
			url:flag == 'preview' ? (url + (url.indexOf('?') != -1 ? '&' : '?') + 'preview=1') : url,
			type:'POST',
			dataType:'json',
			data:$.param(data),
			success:function(json){
				if (json.state) {
					jsonok.call(thisVar, json, flag);
					flag != 'preview' && dialog.dialog('destroy').remove();
				} else {
					showwarn(json.error);
				}
			},
			error:function(){
				showwarn('请求异常');
			},
			complete:complete
		});
		afterSubmit && afterSubmit.call(thisVar, form,dialog);
	},
	viewReady = function(){
		wrap = dialog.parent();
		masker = $('<div class="masker"></div>').insertBefore(dialog);
		form.submit(function(e){
			e.preventDefault();
			e.stopPropagation();
			buttonArea.children('button').eq(0).click();
		});
		formReady && formReady.call(thisVar, form, dialog);
	},
	complete = function(){
		masker.hide();
		buttonArea.children('button').attr('disabled', false).removeAttr('disabled');
	},
	showwarn = function(msg){
		warn || (warn = $('<div class="warning"></div>').prependTo(dialog));
		clearTimeout(wival);
		wival = null;
		dialog.scrollTop(0);
		warn.html(msg).show();
		wival = setTimeout(function(){
			warn.slideUp();
		}, 3000);
	};
	buttons[LANG.BUTTON_OK] = function(){
		form && form.length && submit('ok');
	};
	LANG.BUTTON_NEXTSTEP && (buttons[LANG.BUTTON_NEXTSTEP] = function(){
		form && form.length && submit('nextstep');
	});
	LANG.BUTTON_PREVIEW && (buttons[LANG.BUTTON_PREVIEW] = function(){
		form && form.length && submit('preview');
	});
	buttons[LANG.BUTTON_CANCEL] = function() {
		dialog.dialog('close');
	};
	LANG.BUTTON_OK = '确定';
	LANG.BUTTON_CANCEL = '取消';
	LANG.BUTTON_NEXTSTEP = null;
	LANG.BUTTON_PREVIEW = null;
	typeof opt == 'object' || (opt = {title:opt ? opt.toString() : ''});
	opt = $.extend({
		width :450,
		height:'auto',
		minHeight:80,
		maxHeight:500,
		resizable:false,
		modal : true
	}, opt, {
		autoOpen: false,
		buttons : buttons,
		close:function(){
			dialog.dialog('destroy').remove();
			cancel && cancel.call(thisVar);
		}
	});
	dialog.dialog(opt).load(url, function(){
		form = dialog.find('form:first');
		form && form.length && viewReady();
		dialog.dialog('open');
	}).css('position', 'relative');
	buttonArea = dialog.nextAll('div.btn_area');
	return dialog;
}
function blink(elem, dur) {
	var i = 0, name = 'diy-blink-hover',
	ival = setInterval(function() {
		elem.hasClass(name)
			? elem.removeClass(name)
			: elem.addClass(name);
		if (++i > 2) {
			clearInterval(ival);
			ival = null;
		}
	}, dur||250);
	elem.addClass(name);
}
function hasNext(elem, className){
	while (elem = elem.nextSibling) {
		if ( elem.nodeType == 1 && hasClass(elem, className)) {
			return true
		}
	}
	return false;
};
function children(elem, filter){
	return $.find('>'+filter, elem);
}
function fold(name, frm){
	var checkbox = $(frm[name]),
	label = checkbox.closest('label').next(),
	labelo = label.nextAll('label'),
	topsign = label.children('span:first'),
	minput = topsign.next(),
	inputs = labelo.find('select,input'),
	toggle = function(checked) {
		if (checked) {
			inputs.attr('disabled','').removeAttr('disabled');
			labelo.show();
			topsign.show();
			minput.is(':text') && minput.removeClass('diy-size-15').addClass('diy-size-3');
		} else {
			inputs.attr('disabled','disabled');
			labelo.hide();
			topsign.hide();
			minput.is(':text') && minput.removeClass('diy-size-3').addClass('diy-size-15');
		}
	};
	toggle(checkbox[0].checked);
	checkbox.click(function(){
		toggle(this.checked);
	});
}
window.DIY = {
	fragments:null,
	more:null,
	designMode:true,
	_widgetEngine:{},
	_hooks:{},
	_changed:0,
	bind:function(event, fn){
		if (!(event in this._hooks)) {
			this._hooks[event] = [];
		}
		this._hooks[event].push(fn);
		return this;
	},
	trigger:function(event){
		if (event in this._hooks) {
			var hooks = this._hooks[event];
			for (var i=0,fn;fn=hooks[i++];) {
				fn();
			}
		}
		return this;
	},
	init:function(){
		if (! ENV.contentid) {
			alert('缺少参数:contentid');
			return;
		}
		if (! ENV.pageid) {
			alert('缺少参数:pageid');
			return;
		}
		body = $(document.body).addClass('design-mode');
		body.append(TEMPLATE.PANNEL);
		body.append(TEMPLATE.CONTEXT_MENU);
		if (initHeader()) {
			var p = $('#diy-pannel').addClass('readonly');
			ct.warn('当前页面正被其TA人编辑');
			return;
		}
		
		body.append(TEMPLATE.CONTROL);
		DIY.fragments = $(document.createElement('DIV'));
		DIY.designMode = true;
		DIY.more = $(TEMPLATE.MORE).mousedown(function(e){
			e.stopPropagation();
		}).click(function(e){
			var d = e.target;
			if (d.tagName == 'I') {
				var o = $(this.parentNode);
				DIY[d.getAttribute('action')](o);
				d = null;
			}
		}).appendTo(body);
		// init theme
		Theme.init(ENV.theme, ENV.usedDir);
		// init control pannel
		initPannel();
		
		var ival = setTimeout(prepare, 5000),
        pval = setInterval(lock, 170000);
		function lock(){
			$.get('?app=special&controller=online&action=lock&pageid='+ENV.pageid);
		}
		function prepare(){
			if (READY) return;
			clearTimeout(ival);
			ival = null;
			$('.diy-frame').each(function(){
				initFrame(this);
			});
			$('.diy-widget').each(function(){
				initWidget(this);
			});
            $('#diy-control-mark').remove();
            READY = 1;
		}
		lock();
		$.event.add(window, 'load', prepare);
		
		window.onbeforeunload = function(){
			if (DIY._changed) {
				return '尚未保存，您确认放弃更改吗？';
			}
		};
		window.onunload = function(){
			pval && clearInterval(pval);
			ival && clearTimeout(ival);
			$.get('?app=special&controller=online&action=unlock&pageid='+ENV.pageid);
		};
		
		Hotkey.bind('S', function(e){
			e.preventDefault();
			e.stopPropagation();
			DIY.save();
		}).bind('V', function(e){
			e.preventDefault();
			e.stopPropagation();
			DIY.preview();
		}).bind('P', function(e){
			e.preventDefault();
			e.stopPropagation();
			DIY.publish();
		}).bind('Z', function(e){
			e.preventDefault();
			e.stopPropagation();
			History.back();
		}).bind('Y', function(e){
			e.preventDefault();
			e.stopPropagation();
			History.forward();
		}).init();
	},
	registerEngine:function(engine, o){
		DIY._widgetEngine[engine] = o;
	},
	addWidget:function(engine){
		var widget = Gen.renderWidget(engine), content = widget.find('.diy-content'),
			hooks = (engine in DIY._widgetEngine) ? DIY._widgetEngine[engine] : {},
			url = '?app=special&controller=online&action=addWidget&engine='+encodeURIComponent(engine);
        hooks.widgetid = widget[0].id;
        hooks.content = content;
		initWidget(widget, 1);
		LANG.BUTTON_OK = '完成';
		LANG.BUTTON_NEXTSTEP = '下一步';
		LANG.BUTTON_PREVIEW = '预览';
		ajaxForm({
			width:hooks.dialogWidth || 450,
			title:'添加模块'
		}, url, function(json, flag) {
			content.html(json.html||'');
			hooks.afterRender && hooks.afterRender.call(hooks, widget);
			if (flag != 'preview') {
				initWidget(widget, 2);
				widget.attr('widgetid', json.widgetid);
				widget.attr('engine', engine);
				blink(widget);
				History.log('add', [widget, widget[0].parentNode, widget.next()[0]]);
				if (flag == 'nextstep') {
					LANG.BUTTON_OK = '完成';
					LANG.BUTTON_NEXTSTEP = '下一步';
					LANG.BUTTON_CANCEL = null;
					DIY.setTitle(widget);
				}
			}
		}, hooks.addFormReady, hooks.beforeSubmit, hooks.afterSubmit,
		function(){
			DIY.more.appendTo(DIY.fragments);
			widget.remove();
		}, null, hooks);
		return widget;
	},
	useWidget:function(engine, widgetid){
		var widget = Gen.renderWidget(engine), content = widget.find('.diy-content'),
			hooks = (engine in DIY._widgetEngine) ? DIY._widgetEngine[engine] : {},
			url = '?app=special&controller=online&action=useWidget&widgetid='+widgetid;
        hooks.widgetid = widget[0].id;
        hooks.content = content;
		initWidget(widget, 1);
		LANG.BUTTON_OK = '完成';
		LANG.BUTTON_NEXTSTEP = '下一步';
		LANG.BUTTON_PREVIEW = '预览';
		var hasSkin = 0;
		ajaxForm({
			title:'使用模块',
			width:300,
			height:110
		}, url, function(json, flag){
			if (! json.state) {
				return ct.error(json.error);
			}
			content.html(json.html||'');
			if (!hasSkin) {
				Gen.useSkin(widget, engine, json.skin);
				hasSkin = 1;
			}
			hooks.afterRender && hooks.afterRender.call(hooks, widget);
			if (flag != 'preview') {
				initWidget(widget, 2);
				widget.attr('widgetid', json.widgetid);
				widget.attr('engine', engine);
				json.modified || widget.removeClass('modified');
				blink(widget);
				History.log('add', [widget, widget[0].parentNode, widget.next()[0]]);
				if (flag == 'nextstep') {
					LANG.BUTTON_OK = '完成';
					LANG.BUTTON_NEXTSTEP = '下一步';
					LANG.BUTTON_CANCEL = null;
					DIY.setTitle(widget);
				}
			}
		}, null, null, null, function(){
			DIY.more.appendTo(DIY.fragments);
			widget.remove();
		}, null, hooks);
		return widget;
	},
	setStyle:function(place){
		place.hasClass('diy-widget')
			? Gen.setWidgetDialog(place)
			: Gen.setFrameDialog(place.children('.diy-frame'));
	},
	setTitle:function(place){
		Gen.setTitleDialog(place.hasClass('diy-widget') ? place : place.children('.diy-frame'));
	},
	remove:function(elem){
		// log history
		History.log('remove', [elem, elem[0].parentNode, elem.next()[0]]);
		// put elem into global fragments
		elem.fadeOut('fast',function(){
			elem.appendTo(DIY.fragments);
		});
	},
	shareWidget:function(widget){
		var widgetid = widget.attr('widgetid'),
			url = '?app=special&controller=online&action=shareWidget&widgetid='+widgetid;
		ajaxForm('共享模块', url, function(json){
			if (json.state) {
				ct.ok(json.info);
				DIY.trigger('query-widget');
			} else {
				ct.error(json.error);
			}
		}, null, null, null, null, function(data){
			data.push({
				name:'skin',
				value:Gen.genWidget(widget[0])
			});
		});
	},
	editWidget:function(widget){
		var engine = widget.attr('engine'), content = widget.find('.diy-content'),
			hooks = (engine in DIY._widgetEngine) ? DIY._widgetEngine[engine] : {},
			widgetid = widget.attr('widgetid'),
			url = '?app=special&controller=online&action=editWidget&engine='+encodeURIComponent(engine)+'&widgetid='+widgetid,
			fg = $(document.createElement('div')),
			bak = null, bakd = 0;
        hooks.widgetid = widgetid;
        hooks.content = content;
		LANG.BUTTON_OK = '保存';
		LANG.BUTTON_PREVIEW = '预览';
		ajaxForm({
			width:hooks.dialogWidth || 450,
			title:'编辑模块'
		}, url, function(json, flag){
			var tobak = !bakd && flag == 'preview';
			tobak && (bakd = 1);
			if (flag == 'preview') {
				tobak && (bak = {
					0:moveChilren(content, fg),
					1:widget.hasClass('modified')
				});
				content.html(json.html);
				hooks.afterRender && hooks.afterRender.call(hooks, widget);
				widget.addClass('modified');
			} else {
				bakd = 0;
				$('div.diy-widget[widgetid="'+widgetid+'"]').each(function(){
					var w = $(this), c = w.find('.diy-content');
					c.html(json.html);
					hooks.afterRender && hooks.afterRender(w);
					w.addClass('modified');
				});
			}
		}, hooks.editFormReady, hooks.beforeSubmit, hooks.afterSubmit,
		function(){
			if (bakd) {
				content.html(bak[0]);
				bak[1] || widget.removeClass('modified');
				bak = null;
			}
			fg.remove();
		}, null, hooks);
	},
	pubWidget:function(widget){
		var widgetid = widget.attr('widgetid'),
			url = '?app=special&controller=online&action=pubWidget&widgetid='+widgetid;
		json(url, function(json){
			if (json.state) {
				$('div.diy-widget[widgetid="'+widgetid+'"]').removeClass('modified');
				ct.ok(json.info);
			} else {
				ct.error(json.error);
			}
		});
	},
	setUI:function(){
		function term(cat, txt, val) {
			var li = $('<li><a><img src="apps/special/images/no-style.gif" /></a><p class="val">'+(val||'(empty)')+'</p><p>['+txt+']</p><div class="set-ui-pop"><input cat="'+cat+'" type="hidden" class="theme-input" value="'+(val||'(empty)')+'" ></div></li>'),
				img = li.find('img'), span = li.find('p.val'),
				div = li.find('div'), input = div.find('input');
			
			input.bind('changed', function(e, val, thumb){
				img.attr('src', thumb);
				span.html(val);
				hide();
			});
			var hide = function(){
				div.appendTo(li);
				$doc.unbind('mousedown', ihide);
			};
			var ihide = function(e){
				var t = e.target, tag = t.nodeName || '*';
				div[0] == t || div.find(tag).index(t) != -1 ||
				li[0] == t || li.find(tag).index(t) != -1 || hide();
			};
			li.click(function(){
				body.append(div);
				var pos = li.offset();
				div.css({
					top:pos.top,
					left:pos.left,
					zIndex:parseInt(li.closest('.ui-dialog').css('zIndex'))
				});
				$doc.mousedown(ihide);
			});
			return li;
		}
		return function(theme){
			var themeName = theme && ENV.themes[theme] && ENV.themes[theme].name || theme || '&nbsp;';
			var def = theme && ENV.themes[theme] && ENV.themes[theme]['define'] || {};
			var origTheme = Theme.link.attr('theme');
			var title = '设置风格';
			if (theme == 'custom') {
				LANG.BUTTON_OK = '保存';
				LANG.BUTTON_PREVIEW = '预览';
				LANG.BUTTON_SAVEAS = '另存为';
			} else if (theme) {
				LANG.BUTTON_OK = (ENV.themes[theme] && ENV.themes[theme].reserved) ? null : '保存';
				LANG.BUTTON_PREVIEW = '预览';
				LANG.BUTTON_SAVEAS = '另存为';
			} else {
				LANG.BUTTON_OK = '保存';
				LANG.BUTTON_PREVIEW = '预览';
				title = '添加风格';
			}
			
			
			var d = localForm({title:title, width:720}, 'SET_UI', function(form, dialog){
				dialog.find('.head>span').html(themeName);
				var ul = dialog.find('ul');
				var o = {page:'页面', title:'标题', frame:'布局外框', widget:'模块外框'};
				for (var k in o) {
					ul.append(term(k, o[k], def[k] || ''));
				}
				for (var i=0,l=ENV.engines.length;i<l;i++) {
					var e = ENV.engines[i];
					ul.append(term('content/'+e.engine, e.text, def.content && def.content[e.engine] || ''));
				}
				dialog.dialog('option', 'position', 'center');
				ul.find('input').themeInput(null, true);
			}, function(form, flag){
				var nr = {}, dr = [], nc = {}, dc = [];
				form.find('.theme-input').each(function(){
					var t = this, cat = t.getAttribute('cat'), s = cat.split('/'), v = t.value;
					if (s[0] == 'content') {
						if (v && v != '(empty)') {
							nc[s[1]] = v;
							dc.push('"'+s[1]+'":"'+v+'"');
						}
					} else {
						if (v && v != '(empty)') {
							nr[cat] = v;
							dr.push('"'+cat+'":"'+v+'"');
						}
					}
				});
				dr.push('"content":{'+dc.join(',')+'}');
				var data = 'data='+encodeURIComponent('{'+dr.join(',')+'}');
				nr.content = nc;
				flag || (flag = 'ok');
				var url = '?app=special&controller=online&action=setUI&theme='+(theme||'')+'&flag='+flag+'&pageid='+ENV.pageid;
				if (flag == 'preview') {
					$.post(url, data, function(json){
						if (json.state) {
							ENV.themes.preview = json.theme;
							Theme.name = null;
							Theme.pname = null;
							Theme.set('preview');
						} else {
							ct.error(json.error);
						}
					}, 'json');
				} else if (flag == 'saveas' || (!theme && flag == 'ok')) {
					var t = flag == 'saveas' ? '另存为' : '保存';
					var dialog = localForm({title:t, width:400}, 'SAVE_AS', function(form){
						$(form[0].thumb).imageInput();
					}, function(form){
						data += '&'+form.serialize();
						$.post(url, data, function(json){
							if (json.state) {
								ENV.themes[json.name] = json.theme;
								DIY.addTheme(json.name, json.theme).click();
								ct.ok(t+'完毕');
								d.dialog('close');
							} else {
								ct.error(json.error);
							}
						}, 'json');
						dialog.dialog('close');
					});
				} else {
					$.post(url, data, function(json){
						if (json.state) {
							ENV.themes[theme]['define'] = nr;
							Theme.name = null;
							Theme.pname = null;
							Theme.set(theme);
							ct.ok('保存完毕');
							d.dialog('close');
						} else {
							ct.error(json.error);
						}
					}, 'json');
				}
			}, function(){
				Theme.name == 'preview' && Theme.set(origTheme);
			});
		};
	}(),
	publish:function(page){
		var pageid = page ? page.attr('pageid') : ENV.pageid;
		var data = pageid == ENV.pageid
			? 'jsondata='+encodeURIComponent(Gen())
			: null;
		var url = '?app=special&controller=online&action=publish&pageid='+pageid;
		json(url, data, function(json){
			if (json.state) {
				$('div.diy-widget').removeClass('modified');
				History.savePoint();
				ct.ok('发布成功&nbsp;<a href="'+json.url+'" target="_blank">点击查看</a>&nbsp;&nbsp;', null, 5);
			} else {
				ct.error(json.error);
			}
		});
	},
	offline:function(page){
		ct.confirm('确定要将页面<b style="color:red">'+page.find('a').text()+'</b>下线吗?',
		function(){
			var url = '?app=special&controller=online&action=offline&pageid='+page.attr('pageid');
			json(url, function(json){
				if (json.state) {
					ct.ok('下线成功');
				} else {
					ct.error(json.error);
				}
			});
		});
	},
	save:function(){
		var url = '?app=special&controller=online&action=save',
		data = 'jsondata='+encodeURIComponent(Gen());
		json(url, data, function(json){
			if (json.state) {
				ct.ok(json.info);
				History.savePoint();
			} else {
				ct.error(json.error);
			}
		});
	},
	delPage:function(page){
		var title = page.find('a').text(),
			pageid = page.attr('pageid');
		ct.confirm('此操作不可恢复，确定要删除页面<b style="color:red">'+title+'</b>吗？',function(){
			json('?app=special&controller=online&action=delPage&pageid='+pageid,
			function(json){
				if (json.state) {
					if (pageid == ENV.pageid) {
						var x = page.prev();
						if (x.length) {
							x.click();
							return;
						}
						x = page.next();
						if (x.length) {
							x.click();
							return;
						}
						ct.ok('已删除');
						setTimeout(function(){
							window.location = location.href.replace(/&?pageid=\d*/,'');
						}, 500);
					} else {
						page.remove();
					}
				} else {
					ct.error(json.error);	
				}
			});
		});
	},
	scheme:function(){
		var url = '?app=special&controller=online&action=scheme';
		ajaxForm({
			title:'保存方案',
			width:400
		}, url, function(json){
			if (json.state) {
				ct.ok(json.info);
			} else {
				ct.error(json.error);
			}
		}, null, null, null, null, function(data){
			data.push({
				name:'jsondata',
				value:Gen()
			})
		});
	},
	setPage:function(page){
		var url = '?app=special&controller=online&action=setPage&pageid='+page.attr('pageid');
		var a = page.find('a');
		ajaxForm({
			title:"设置页面:"+a.text(),
			width:320
		},url,function(json){
			a.text(json.name);
			page.attr('url', json.url);
		});
	},
	editTemplate:function(page){
		var pageid = page.attr('pageid');
		var url = '?app=special&controller=online&action=editTemplate&pageid='+pageid;
		json('?app=special&controller=online&action=tplCanEdit&pageid='+pageid,
		function(json){
			if (json.state) {
				LANG.BUTTON_OK = '保存';
				ct.confirm('此操作会影响使用到该模板的页面，确定继续？',function(){
					ajaxForm('编辑模板'+json.name, url, function(json){
						ct.ok('保存成功');
					}, function(form){
						setTimeout(function(){
							form.find('textarea').editplus({
								buttons: 'save,fullscreen,wrap'
							});
						}, 0);
					});
				});
			} else {
				ct.error(json.error);
			}
		});
	},
	viewPage:function(page){
		window.open(page.attr('url'),'_blank');
	},
	tabs:function(dialog, click, focused, event){
		dialog.find('.tabs>ul').each(function(){
			var target = dialog.find(this.getAttribute('target'));
			var tabs = $('li', this).each(function(i){
				$.event.add(this, event || 'click', function(){
					if ($.className.has(this, 'active')) return;
					tabs.removeClass('active');
					$.className.add(this,'active');
					var t = target.hide().eq(i).show();
					typeof click == 'function' && click.apply(this, [i, t]);
				});
			});
			tabs.eq(focused||0).triggerHandler(event || 'click');
		});
	},
	form:ajaxForm,
	use:Load
};
function initHeader() {
	$('#diy-pannel').append(TEMPLATE.PANNEL_HEADER);
	var nav = $('#diy-nav'), addbtn = $('#diy-addbtn'),
		rePage = /pageid=\d+/, bUrl, lHref = location.href, locked;
	if (rePage.test(lHref)) {
		bUrl = lHref.replace(rePage, 'pageid={id}');
	} else {
		bUrl = lHref + (lHref.indexOf('?') != -1 ? '&' : '?') + 'pageid={id}';
	}
	function buildPageItem(item) {
		var li = $('<li pageid="'+item.pageid+'" url="'+item.url+'"><span><a>'+item.name+'</a><b></b></span></li>');
		if (item.locked) {
			li.addClass('locked')
				.attr('tips', '<b>'+item.lockedby+'</b>正在编辑')
				.attrTips('tips');
		} else {
			li.contextMenu($('#page-menu'), function(action){
				DIY[action](li);
			});
			li.find('b').click(function(e){
				e.stopPropagation();
				li.triggerHandler('contextMenu', [e]);
			});
		}
		if (ENV.pageid == item.pageid) {
			locked = item.locked;
			li.addClass('active');
			li.click(function(){DIY._togglefn && DIY._togglefn()});
			var span = li.find('span');
			DIY.bind('changed', function(){
				span.find('em').length || span.prepend('<em>*</em>');
				DIY._changed = true;
			});
			DIY.bind('unchanged', function(){
				span.find('em').remove();
				DIY._changed = false;
			});
		} else {
			var url = bUrl.replace('{id}', item.pageid);
			li.click(function(){ window.location = url; });
		}
		return li;
	}
	
	for (var i=0,d;d=ENV.pages[i++];) {
		nav.append(buildPageItem(d));	
	}
	DIY.copyPage = function(page){
		var pageid = page.attr('pageid');
		var url = '?app=special&controller=online&action=copyPage&pageid='+pageid;
		ajaxForm({
			title:'拷贝页面',
			width:320
		}, url, function(json){
			var li = buildPageItem(json.data).appendTo(nav);
			ct.confirm('页面拷贝成功，开始设计此页？',function(){
				li.click();
			});
		});
	};
	
	addbtn.click(function(){
		var url = '?app=special&controller=online&action=addPage';
		var dialog = ajaxForm({
			title:'添加页面',
			width:660
		}, url, function(json){
			var li = buildPageItem(json.data).appendTo(nav);
			ct.confirm('页面添加成功，开始设计此页？',function(){
				li.click();
			});
		}, function(form){
            var initS = function(){
				var t = $(this),
					value = t.attr('value'),
					title = t.attr('title');
				t.append($('<a href="">删除</a>').click(function(){
					ct.confirm('此操作不可恢复，确定要删除方案 <b style="color:red">'+title+'</b> ？',function(){
						json('?app=special&controller=online&action=delScheme&scheme='+encodeURIComponent(value),
						function(json){
							if (json.state) {
								t.remove();
								active = null;
                                ct.ok('删除成功');
							} else {
								ct.error(json.error);
							}
						});
					});
					return false;
				}));
			};
			var up = dialog.find('.uploader');
			var active = null;
			var click = function(){
				active && removeClass(active, 'active');
				active = this;
				addClass(active, 'active');
			};
			var initT = function(){
				var t = $(this),
					value = t.attr('value'),
					title = t.attr('title');
				t.append($('<a href="">删除</a>').click(function(){
					ct.confirm('此操作不可恢复，且会影响已使用模板<b style="color:red">"'+title+'"</b>的页面，确定要删除？',function(){
						json('?app=special&controller=online&action=delTemplate&template='+encodeURIComponent(value),
						function(json){
							if (json.state) {
								t.remove();
								active = null;
							} else {
								ct.error(json.error);
							}
						});
					});
					return false;
				}));
			};
			var items = dialog.find('.item').click(click);
			items.eq(0).click();
            items.filter('[name="scheme"]').each(initS);
			items.filter('[name="template"]').each(initT);
			up.uploader({
				fileExt:"*.zip",
				fileDesc:'ZIP文件',
				jsonType:1,
				script:'?app=special&controller=online&action=addTemplate&contentid='+ENV.contentid,
				multi:false,
				complete:function(json, data){
					if (json) {
						if (json.state) {
							$('<div class="item" name="template" title="'+json.data.name+'" value="'+json.data.entry+'"><img src="'+json.data.thumb+'" /><span>'+json.data.name+'</span></div>')
							.insertAfter(up).click(click).each(initT).click();
						} else {
							ct.error(json.error);
						}
					} else {
						ct.error('上传失败!');
					}
				},
				error:function(data){
					ct.warn(data.file.name+'：上传失败，'+data.error.type+':'+data.error.info);
				}
			});
		}, null, null, null, function(data){
			var a = dialog.find('.item.active[name]');
			if (a.length) {
				data.push({name:a.attr('name'), value:a.attr('value')});
			}
		});
	});
	return locked;
}
function initPannel() {
	var pannel = $('#diy-pannel').append(TEMPLATE.PANNEL_CENTER),
		pannelCenter = $('#diy-center'),
		views = $('#diy-toolset>ul'),
		tabs = $('#diy-tab li'),
		toggle = $('#diy-toggle'),
		controls = $('#diy-control>span'),
		height = pannel.height(),
		cHeight = pannelCenter[0].offsetHeight,
		collapsedHeight = height - cHeight,
		oHeight = pannel[0].offsetHeight,
		oCollapsedHeight = oHeight - cHeight,
		placeHolder = $('<div class="head-placeholder"></div>').prependTo(body),
		
		backBtn = controls.filter('.back').click(function(){
			History.back();
		}),
		forwardBtn = controls.filter('.forward').click(function(){
			History.forward();
		});
	DIY.bind('hasBack',function(){
		backBtn.addClass('enable');
	}).bind('noBack',function(){
		backBtn.removeClass('enable');
	}).bind('hasForward',function(){
		forwardBtn.addClass('enable');
	}).bind('noForward',function(){
		forwardBtn.removeClass('enable');
	});
	controls.each(function(){
		var action = this.getAttribute('action');
		if (action) {
			addClass(this, action);
			$.event.add(this, 'click', function(){
				DIY[action]();
			});
		}
	});
	tabs.click(function(){
		var t = this.getAttribute('target');
		views.isnot(function(){
			return this.getAttribute('name') == t;
		},function(){
			this.style.display = 'block';
			if (! $.data(this, 'pannel-inited')) {
				initPannel[t](this);
				$.data(this, 'pannel-inited', 1);
			}
		},function(){
			this.style.display = 'none';
		});
		tabs.isnot('.active',function(){
			removeClass(this, 'active');
		});
		addClass(this, 'active');
	}).eq(0).click();
	var repos = /\-?\d+(?:px)?$/,
		supportBgPos = 'background-position-y',
		posMatch = repos.exec(body.css(supportBgPos) || body.css(supportBgPos='background-position')),
		origBgPosY = posMatch && toInt(posMatch[0]);
	DIY.preview = function(){
		if (DIY.designMode) {
			body.removeClass('design-mode');
			DIY.designMode = false;
			origBgPosY == null ||
				body.css(supportBgPos, body.css(supportBgPos).replace(repos, origBgPosY + 'px'));
			$('.diy-wrapper').each(function(){
				this.style.margin = this.getAttribute('view-margin');
			});
		} else {
			DIY.designMode = true;
			body.addClass('design-mode');
			origBgPosY == null ||
				body.css(supportBgPos, body.css(supportBgPos).replace(repos, origBgPosY + pannel[0].offsetHeight + 'px'));
			$('.diy-wrapper').each(function(){
				this.style.margin = this.getAttribute('design-margin');
			});
		}
	};
	
	function expand(flag) {
		if (flag) {
			pannel.height(height);
			pannelCenter.show();
			placeHolder.height(oHeight);
			origBgPosY == null ||
				body.css(supportBgPos, body.css(supportBgPos).replace(repos, origBgPosY + oHeight + 'px'));
			pannel.removeClass('collapsed');
		} else {
			pannel.height(collapsedHeight);
			pannelCenter.hide();
			placeHolder.height(oCollapsedHeight);
			origBgPosY == null ||
				body.css(supportBgPos, body.css(supportBgPos).replace(repos, origBgPosY + oCollapsedHeight + 'px'));
			pannel.addClass('collapsed');
		}
	}
	expand(1);
	function togglefn(){
		expand(pannel.hasClass('collapsed'));
	}
	toggle.click(togglefn);
	DIY._togglefn = togglefn;
}
$.extend(initPannel, {
	setting:function(){
		function buildSEOItem(key, text, value) {
			var li = $('<li><span class="edit"></span><label>'+text+'&nbsp;&nbsp;：</label></li>'),
			a = $('<a class="edit">'+value+'</a>').appendTo(li),
			textarea = $('<textarea cols="40" rows="2" name="'+key+'">'+value+'</textarea>').appendTo(li),
			editmode = false,
			setEdit = function(){
				if (editmode) return;
				li.addClass('active');
				textarea.focus();
				editmode = true;
			},
			exitEdit = function(){
				if (!editmode) return;
				li.removeClass('active');
				editmode = false;
			},
			editbtn = li.find('.edit').click(function(e){
				setEdit();
			});
			textarea.blur(function(){
				if (this.value != this.defaultValue) {
					var t = this, v = this.value;
					a.text(v);
					textarea.val(v);
					t.defaultValue = v;
					if (t.name == 'title') {
						document.title = v;
					} else {
						ENV.metas[key] = v;
					}
				}
				exitEdit();
			});
			li.dblclick(setEdit);
			return li;
		}
		var resEditor = {
			code:function(item){
				var url = '?app=special&controller=online&action=editRes&psn='+encodeURIComponent(item.psn);
				LANG.BUTTON_OK = '保存';
				ajaxForm('编辑文件'+item.url, url, function(json){
					ct.ok('保存成功');
				}, function(form){
					setTimeout(function(){
						form.find('textarea').editplus({
							buttons: 'save,fullscreen,wrap'
						});
					}, 0);
				});
			}
		};
		function buildRESItem(item) {
			var li = $(
			'<li>'+
				'<span class="ico '+item.ext+'"></span>'+
				'<span class="detail">'+
					'<a href="'+item.url+'" target="_blank">'+item.url+'</a>'+
					'<strong>大小：'+item.size+'</strong><strong>修改日期：'+item.updated+'</strong>'+
				'</span>'+
			'</li>');
			var ico = li.find('span.ico');
			if (/^(?:png|gif|jpeg|jpg)$/.test(item.ext)) {
				loadImage(item.url, function(){
					var $img = $(this).appendTo(ico), img = this;
					img.height > img.width
		        		? (img.height > 40 && (img.height = 40, $img.removeAttr('width')))
		        		: img.width > 40 && (img.width = 40, $img.removeAttr('height'));
				});
			}
			if (item.ext == 'css' || item.ext == 'js') {
				$('<b title="载入" class="load"></b>').click(function(){
					Load(item.url, function(){
						ct.ok('<b>'+item.url+'</b>载入完毕');
					}, true);
				}).prependTo(li);
			}
			if (item.editor && (item.editor in resEditor)) {
				var _editor = resEditor[item.editor];
				var edit = $('<b title="编辑" class="edit"></b>').prependTo(li);
				edit.click(function(){
					_editor(item);
				});
			}
			var del = $('<b title="删除" class="delete"></b>').prependTo(li);
			del.click(function(e){
				li.remove();
				var i = ENV.resources.indexOf(item);
				i != -1 && ENV.resources.splice(i, 1);
			});
			return li;
		}
		var UPLOAD_OPTIONS = {
			fileExt:'*.*',
			fileDataName:'Filedata',
			script:'?app=special&controller=online&action=addRes&pageid='+ENV.pageid,
			sizeLimit:0,
			jsonType : 1,
			multi:true
		};
		function createUploader(options){
			var up = $('<span class="button">添加</span>').appendTo(options.place);
			up.uploader($.extend({
				complete:function(json, data){
					if (json.state) {
						for (var i=0,l=json.data.length;i<l;i++) {
							options.ok(json.data[i]);
							break;
						}
					} else {
						ct.error(data.file.name+'：上传失败');
					}
				},
				allcomplete:function(data){
					ct.ok('所有文件上传完成');
				},
				error:function(data){
					ct.error(data.file.name+'：上传失败，'+data.error.type+':'+data.error.info);
				}
			}, options));
			return up;
		}
		var seoItems = {
			'title':'标题',
			'Keywords':'关键字',
			'Description':'描述',
			'Copyright':'版权信息'
		};
		return function(view){
			var boxes = $('.diy-toolbox', view);
			var seo = $('<ul class="diy-seo"></ul>').appendTo(boxes[0]);
			var res = $('<ul class="diy-res"></ul>').appendTo(boxes[1]);
			for (var k in seoItems) {
				seo.append(buildSEOItem(k, seoItems[k], (k == 'title' ? document.title : ENV.metas[k])||''));
			}
			for (var i=0,l=ENV.resources.length;i<l;i++) {
				res.append(buildRESItem(ENV.resources[i]));
			}
			var uploadOptions = $.extend({}, UPLOAD_OPTIONS);
			uploadOptions.place = boxes.eq(1).next();
			uploadOptions.ok = function(data){
				res.prepend(buildRESItem(data));
				ENV.resources.push(data);
			};
			createUploader(uploadOptions);
		};
	}(),
	frame:function(){
		var options = {
			selectArea:selectAreaByEvent,
			selectPlace:selectPlace,
			getHolder:function($h) {
				return $('<div class="diy-placeholder"></div>');
			},
			getGhost:function($h, top, left) {
				return $h.clone().css({
					position:'absolute',
					top:top,
					left:left,
					zIndex:151,
					opacity:.8,
					cursor:'move'
				}).appendTo(body);
			},
			fromGhost:function($g, found) {
				var scale = $g.attr('scale');
				$g.remove();
				if (! found) return null;
				if (scale == 'define') {
					var placeHolder = $('<div class="diy-placeholder"></div>');
					localForm({title:'输入列数',width:200},'NEW_FRAME',null,function(form){
						var frame = Gen.renderFrame(form[0].column.value);
						placeHolder.replaceWith(frame);
						initFrame(frame);
						History.log('add', [frame, frame[0].parentNode, frame.next()[0]]);
					},function(){
						placeHolder.remove();
					});
					return placeHolder;
				} else {
					var frame = Gen.renderFrame(scale.split('-'));
					setTimeout(function(){
						initFrame(frame);
						History.log('add', [frame, frame[0].parentNode, frame.next()[0]]);
					}, 0);
					return frame;
				}
			}
		};
		function buildFrameViewItem(scale, name) {
			var item = $('<div class="diy-layout diy-layout-'+scale+'" scale="'+scale+'">'+
					'<a></a>'+
					'<span>'+name+'</span>'+
				'</div>');
			new dragMent(item, options);
			return item;
		}
		return function(view){
			var box = $('.diy-toolbox', view);
			var scale = {
				'1':'100%',
				'1-1':'1:1',
				'1-1-1':'1:1:1',
				'1-1-1-1':'1:1:1:1',
				'1-2':'1:2',
				'2-1':'2:1',
				'1-3':'1:3',
				'3-1':'3:1',
				'define':'自定义'
			};
			for (var k in scale) {
				box.append(buildFrameViewItem(k, scale[k]));
			}
		};
	}(),
	widget:function(){
		var optionsEngine = {
			selectArea:selectAreaByEvent,
			selectPlace:selectPlace,
			getHolder:function($h) {
				return $('<div class="diy-placeholder"></div>');
			},
			getGhost:function($h, top, left) {
				return $h.clone().css({
					position:'absolute',
					top:top,
					left:left,
					zIndex:151,
					opacity:.8,
					cursor:'move'
				}).appendTo(body);
			},
			fromGhost:function($g, found) {
				var engine = $g.attr('engine');
				$g.remove();
				if (! found) return null;
				return DIY.addWidget(engine);
			}
		}, optionsWidget = $.extend({}, optionsEngine, {
			fromGhost:function($g, found) {
				var widgetid = $g.attr('widgetid'),
					engine = $g.attr('engine');
				$g.remove();
				if (! found) return null;
				return DIY.useWidget(engine, widgetid);
			}
		});
		function scrollData(box) {
			var showMoreLock = false, count=0,
				total=0, page = 0, lastWhere = '';
			box.mousewheel(function(e, delta){
				if (delta < 0 && !showMoreLock && count < total 
					&& box.scrollTop() + box.height() > box[0].scrollHeight - 20)
				{
					loadPage();
				}
			});
			function ajaxStart(){
				showMoreLock = true;
			}
			function ajaxEnd(){
				showMoreLock = false;
			}
			function query(where, success) {
				$.ajax({
					url:'?app=special&controller=online&action=getWidget',
					type:'POST',
					dataType:'json',
					data:where,
					beforeSend:ajaxStart,
					success:success,
					complete:ajaxEnd
				});
			}
			function pageOk(json){
				var l;
				if (json.data && (l = json.data.length)) {
					total || (total = parseInt(json.total));
					count += l;
					for (var i=0;i<l;i++) {
						box.append(buildWidgetItem(json.data[i]));
					}
				}
			}
			function queryOk(json){
				box.empty();
				total = parseInt(json.total);
				page = 1;
				count = json.data ? json.data.length : 0;
				for(var i=0;i<count;i++){
					box.append(buildWidgetItem(json.data[i]));
				}
			}
			function loadPage(){
				query(lastWhere+'&page='+(++page), pageOk);
			}
			this.query = function(where){
				lastWhere = where;
				query(where, queryOk);
			};
		}
		function buildEngineItem(item) {
			var div = $(
				'<div class="diy-module" engine="'+item.engine+'">'+
					'<img src="'+item.icon+'" />'+
					'<span>'+item.text+'</span>'+
				'</div>');
			new dragMent(div, optionsEngine);
			return div;
		}
		function buildWidgetItem(item) {
			var div = $(
				'<div class="diy-module" engine="'+item.engine+'" widgetid="'+item.widgetid+'">'+
					'<img src="'+item.icon+'" />'+
					'<span>'+item.text+'</span>'+
				'</div>');
			new dragMent(div, optionsWidget);
			$(document.createElement('b')).appendTo(div).click(function(e){
				ct.confirm('确定要取消共享模块"'+item.text+'"吗？',function(){
					var url = '?app=special&controller=online&action=unshareWidget&widgetid='+item.widgetid;
					json(url, function(json){
						if (json.state) {
							div.remove();
						} else {
							ct.error(json.error);
						}
					});
				},null,e);
			}).mousedown(function(e){
				e.stopPropagation();
				$doc.mousedown();
			});
			return div;
		}
		return function(view){
			var boxes = $('div.diy-toolbox', view),
				model = boxes.eq(0), inst = boxes.eq(1),
				scroll = new scrollData(inst),
				place = inst.next(),
				input = $('<input type="text" class="search" />').appendTo(place),
				select = $('<select><option value="">所有</option></select>').appendTo(place);
			for (var i=0,l;l=ENV.engines[i++];) {
				select.append('<option value="'+l.engine+'">'+l.text+'</option>');
				model.append(buildEngineItem(l));
			}
			scroll.query('');
			function query(){
				var where = 'keyword='+encodeURIComponent(input[0].value)+'&engine='+select[0].value;
				scroll.query(where);
			}
			DIY.bind('query-widget', query);
			input.focus(function(){
				this.style.cssText = 'background-image:none';
			}).blur(function(){
				this.value == '' && ( this.style.cssText = '');
			}).keyup(function(e){
				// DOWN UP LEFT RIGHT return
				if (e.keyCode > 36 && e.keyCode < 41) {
					return;
				}
				query();
			});
			select.change(query);
		};
	}(),
	theme:function(){
		function buildThemeItem(theme, item) {
			var div = $(
				'<div class="diy-theme" name="'+theme+'">'+
					'<img src="'+item.thumb+'" />'+
					'<span>'+item.name+'</span>'+
					'<div class="ctrl">'+
						'<i class="preview" title="预览"></i>'+
						'<i class="edit" title="编辑"></i>'+
						'<i class="delete" title="删除"></i>'+
					'</div>'+
				'</div>'),
				ival = null;
			function iuse(){
				ival = null;
				Theme.use(theme);
			}
			div.click(function(){
				ival && clearTimeout(ival);
				ival = null;
				Theme.set(theme);
				div.addClass('active').siblings('.active').removeClass('active');
			});
			div.find('.ctrl').click(function(){
				return false;
			});
			div.find('.preview').hover(function(){
				ival && clearTimeout(ival);
				ival = setTimeout(iuse, 500);
			},function(){
				ival && clearTimeout(ival);
				ival || Theme.reset();
				ival = null;
			});
			div.find('.edit').click(function(){
				DIY.setUI(theme);
			});
			var del = div.find('.delete');
			item.reserved ? del.remove() : del.click(function(e){
				ct.confirm('此操作不可恢复，删除后会影响使用此风格的页面，确定要删除风格"'+item.name+'"吗？',
				function(){
					json('?app=special&controller=online&action=delTheme&theme='+theme, function(json){
						if (json.state) {
							div.remove();
							delete ENV.themes[theme];
							Theme.name == theme && DIY.themeBox.find('[name="custom"]').click();
						} else {
							ct.error(json.error);
						}
					});
				}, null, e);
			});
			theme == Theme.name && div.addClass('active');
			return div;
		}
		return function(view) {
			var box = $('.diy-toolbox', view), btnPlace = box.next();
			for (var k in ENV.themes) {
				box[k == 'custom' ? 'prepend' : 'append'](buildThemeItem(k, ENV.themes[k]));
			}
			$('<span class="button">设置</span>').click(function(){
				Gen.setPage();
			}).prependTo(btnPlace);
			$('<span class="button">添加</span>').click(function(){
				DIY.setUI();
			}).appendTo(btnPlace);
			DIY.themeBox = box;
			DIY.addTheme = function(name, theme){
				var t = buildThemeItem(name, theme);
				box.append(t);
				return t;
			};
		}
	}()
});
var History = {
	_data:{},
	_pointer:0,
	_length:0,
	_savePoint:0,
	_unback:0,
	log:function(type, params){
		this._data[this._pointer] = {
			type:type,
			params:params
		};
		this._length = ++this._pointer;
		DIY.trigger('hasBack');
		DIY.trigger('noForward');
		this._unback || DIY.trigger('changed');
	},
	unback:function(){
		if (! this._unback) {
			this._unback = 1;
			DIY.trigger('changed');
		}
	},
	savePoint:function(){
		this._savePoint = this._pointer;
		this._unback = 0;
		DIY.trigger('unchanged');
	},
	back:function(){
		if (this._pointer < 1) return;
		DIY.trigger('hasForward');
		var h = this._data[--this._pointer];
		this._pointer < 1 && DIY.trigger('noBack');
		var undo = this.undo[h.type];
		undo && undo.apply(this, h.params);
		this._unback || DIY.trigger(this._savePoint == this._pointer ? 'unchanged' : 'changed');
	},
	forward:function(){
		if (this._pointer >= this._length) return;
		DIY.trigger('hasBack');
		var h = this._data[this._pointer++];
		this._pointer >= this._length && DIY.trigger('noForward');
		var redo = this.redo[h.type];
		redo && redo.apply(this, h.params);
		this._unback || DIY.trigger(this._savePoint == this._pointer ? 'unchanged' : 'changed');
	},
	undo:{
		remove:function(elem, area, place){
			elem.show();
			if (place && place.parentNode == area) {
				elem.insertBefore(place);
			} else {
				elem.appendTo(area);
			}
			scrollIntoView(elem);
			blink(elem);
		},
		move:function(movement, origPos, newPos) {
			if (origPos.place && origPos.place.parentNode == origPos.area) {
				movement.insertBefore(origPos.place);
			} else {
				movement.appendTo(origPos.area);
			}
			movement.hasClass('.diy-wrapper') && setColor(movement);
			return scrollIntoView(movement.show());
		},
		add:function(elem, area, place){
			elem.fadeOut('fast',function(){
				elem.appendTo(DIY.fragments);
			});
		},
		resize:function(h, p, n, origScale, newScale) {
			p.width(origScale.pw);
			n.width(origScale.nw);
			h.css('left', origScale.hl);
		}
		// resource
		// meta
		// title & frame & widget style
		// theme
	},
	redo:{
		remove:function(elem, area, place){
			elem.fadeOut('fast',function(){
				elem.appendTo(DIY.fragments);
			});
		},
		move:function(movement, origPos, newPos){
			if (newPos.place && newPos.place.parentNode == newPos.area) {
				movement.insertBefore(newPos.place);
			} else {
				movement.appendTo(newPos.area);
			}
			movement.hasClass('.diy-wrapper') && setColor(movement);
			return scrollIntoView(movement);
		},
		add:function(elem, area, place){
			elem.show();
			if (place && place.parentNode == area) {
				elem.insertBefore(place);
			} else {
				elem.appendTo(area);
			}
			scrollIntoView(elem);
			blink(elem);
		},
		resize:function(h, p, n, origScale, newScale) {
			p.width(newScale.pw);
			n.width(newScale.nw);
			h.css('left', newScale.hl);
		}
	}
};
function isCenter(style) {
	var a = {1:null};
	return ( (RE['margin-left'].exec(style) || a)[1] == 'auto'
		&& (RE['margin-right'].exec(style) || a)[1] == 'auto' )
	|| RE.center.test(style);
}
function Gen(){
	var data = [];
	data.push('"head":'+Gen.genHead());
	$('div.diy-root').each(function(){
		data.push('"'+this.id+'":'+Gen.genArea(this, true));
	});
	return '{'+data.join(',')+'}';
}
$.extend(Gen, {
	genHead:function(){
		return ['{',
			'"title":',Gen.q(document.title),
			',"theme":',Gen.q(Theme.name),
			',"meta":',Gen.genMeta(ENV.metas||{}),
			',"resource":',Gen.genRes(ENV.resources||[]),
			',"body-style":',Gen.q(body.attr('body-style')),
			',"a-style":',Gen.q(body.attr('a-style')),
		'}'].join('');
	},
	genMeta:function(o){
		var data = [];
		for (var k in o) {
			data.push(Gen.q(k)+':'+Gen.q(o[k]));
		}
		return '{'+data.join(',')+'}';
	},
	genRes:function(o){
		var data = [];
		for (var i=0,l=o.length;i<l;i++) {
			data.push(Gen.q(o[i].psn));
		}
		return '['+data.join(',')+']';
	},
	genArea:function(area, root) {
		var data = ['{',
			'"id":"', area.id, '",'
		];
		if (! root) {
			var style = ['width:'+$.curCSS(area, 'width')];
			if (children(area.parentNode, '.diy-area').length > 1 && !hasNext(area, 'diy-area'))
			{
				style.push('float:right');
			}
			data.push('"style":'+Gen.q(style.join(';'))+',');
		}
		data.push('"items":{');

		var items = [];
		each(children(area, 'div'), function(){
			hasClass(this, 'diy-wrapper')
			 ? items.push(Gen.genWrapper(this))
			 : hasClass(this, 'diy-widget')
				 && items.push('"'+this.id+'":'+Gen.genWidget(this));
		});
		return (data.join('') + items.join(',') + '}}');
	},
	genWrapper:function(wrapper) {
		var frame = children(wrapper, '.diy-frame')[0];
		var data = ['"',frame.id,'":{',
			'"id":"',frame.id,'",',
			'"theme":{',
				'"frame":',Gen.q(frame.getAttribute('frame-theme')),
				',"title":',Gen.q(frame.getAttribute('title-theme')),
			'},',
			'"style":{',
				'"frame":',Gen.q(frame.getAttribute('frame-style')),
				',"title":',Gen.q(frame.getAttribute('title-style')),
				',"title-w":',Gen.q(frame.getAttribute('title-w-style')),
				',"title-a":',Gen.q(frame.getAttribute('title-a-style')),
			'},',
			'"title":',Gen.genTitle(frame),',',
			'"items":{'
		].join('');
		var items = [];
		each(children(frame, '.diy-area'),function(){
			items.push('"'+this.id+'":'+Gen.genArea(this));
		});
		return (data + items.join(',') + '}}');
	},
	genWidget:function(widget) {
		return ['{',
			'"id":"',widget.id,'"',
			',"widgetid":"',widget.getAttribute('widgetid'),'"',
			',"theme":{',
				'"widget":',Gen.q(widget.getAttribute('widget-theme')),
				',"title":',Gen.q(widget.getAttribute('title-theme')),
				',"content":',Gen.q(widget.getAttribute('content-theme')),
			'}',
			',"style":{',
				'"widget":',Gen.q(widget.getAttribute('widget-style')),
				',"inner":',Gen.q(widget.getAttribute('inner-style')),
				',"title":',Gen.q(widget.getAttribute('title-style')),
				',"content":',Gen.q(widget.getAttribute('content-style')),
				',"inner-w":',Gen.q(widget.getAttribute('inner-w-style')),
				',"title-w":',Gen.q(widget.getAttribute('title-w-style')),
				',"content-w":',Gen.q(widget.getAttribute('content-w-style')),
				',"inner-a":',Gen.q(widget.getAttribute('inner-a-style')),
				',"title-a":',Gen.q(widget.getAttribute('title-a-style')),
				',"content-a":',Gen.q(widget.getAttribute('content-a-style')),
			'}',
			',"title":',Gen.genTitle(widget),
		'}'].join('');
	},
	genTitle:function(where) {
		var title = hasClass(where, 'diy-frame') ? children(where, '.diy-title') : $.find('.diy-title', where),
			item = [];
		title.length && $.each(children(title[0],'*'),function(){
			var a = $(this);
			item.push(['{',
				'"text":',Gen.q(a.text()),
				',"href":',Gen.q(a.attr('href')),
				',"img":',Gen.q(a.children('img').attr('src')),
				',"style":',Gen.q(a.attr('item-style')),
			'}'].join(''));
		});
		return '['+item.join(',')+']';
	},
	renderFrame:function(param){
		var frame = $('<div id="'+guid('f-')+'" class="diy-frame"></div>');
		if ($.isArray(param)) {
			var l = param.length;
			if (l < 2) {
				frame.append('<div id="'+guid('a-')+'" class="diy-area" style="width:100%"></div>');
			} else {
				var i=0, t = 0;
				for (i=0;i<l;i++) {
					t += (param[i] = toInt(param[i]));
				}
				var tp = 99.8;
				for (i=0;i<l;i++) {
					var p = tp;
					if (i < l-1) {
						p = (param[i]*100/t).toFixed(1);
						tp -= parseFloat(p);
					}
					frame.append('<div id="'+guid('a-')+'" class="diy-area" style="width:'+p+'%"></div>');
				}
			}
		} else {
			var per = toInt(param);
			if (per > 1) {
				var p = parseFloat((100/per).toFixed(1)), lp = (99.9 - (per-1)*p).toFixed(1);
				while (per--) {
					frame.append('<div id="'+guid('a-')+'" class="diy-area" style="width:'+(per ? p : lp)+'%"></div>');
				}
			} else {
				frame.append('<div id="'+guid('a-')+'" class="diy-area" style="width:100%"></div>');
			}
		}
		Theme.def.frame && frame.addClass('frame-'+Theme.def.frame);
		return frame;
	},
	renderWidget:function(engine){
		var id = guid('w-'),
		widget = $('<div id="'+id+'" class="diy-widget modified">'+
			'<div id="'+id+'-i" class="diy-inner">'+
				'<div id="'+id+'-c" class="diy-content diy-content-'+engine+'"></div>'+
			'</div>'+
		'</div>');
		
		Theme.def.widget && widget.addClass('widget-'+Theme.def.widget);
		Theme.def.content && Theme.def.content[engine]
			&& widget.find('.diy-content').addClass('content-'+engine+'-'+Theme.def.content[engine]);
		return widget;
	},
	addTitle:function(where, t){
		t || (t = $('<div id="'+where[0].id+'-t" class="diy-title"></div>'));
		if (where.hasClass('diy-frame')) {
			where.children('.diy-area').eq(0).before(t);
		} else {
			where.find('.diy-inner').prepend(t);
		}
		var theme = where.attr('title-theme');
		theme = theme
			? (theme == '(empty)' ? '' : theme)
			: Theme.def.title;
		theme && t.addClass('title-'+theme);
		return t;
	},
	useSkin:function(widget, engine, skin) {
		if (!skin) return;
		try {
			skin = (new Function('return '+skin))();
		} catch (e) {
			return;	
		}
		var id = widget[0].id,
			theme = skin.theme, style = skin.style,
			inner = widget.find('.diy-inner'),
			content = widget.find('.diy-content'),
			wTheme = '', cTheme = '';
		// load css file
		if (theme.widget) {
			if (theme.widget != '(empty)') {
				Theme.addDir('widget/'+theme.widget);
				wTheme = theme.widget;
			}
			switchClass(widget, Theme.def.widget, wTheme, 'widget-');
		}
		
		if (theme.title && theme.title != '(empty)') {
			Theme.addDir('title/'+theme.title);
		}
		
		if (theme.content) {
			if (theme.content != '(empty)') {
				Theme.addDir('content/'+engine+'/'+theme.content);
				cTheme = theme.content;
			}
			switchClass(content, (Theme.def.content && Theme.def.content[engine]), cTheme, 'content-'+engine+'-');
		}
		
		// set cssText
		setRule('#'+id, style['widget']);
		setRule('#'+id+'-i', style['inner']);
		setRule('#'+id+'-t', style['title']);
		setRule('#'+id+'-c', style['content']);
		setRule('#'+id+'-i *', style['inner-w']);
		setRule('#'+id+'-t *', style['title-w']);
		setRule('#'+id+'-c *', style['content-w']);
		setRule('#'+id+'-i a', style['inner-a']);
		setRule('#'+id+'-t a', style['title-a']);
		setRule('#'+id+'-c a', style['content-a']);
		
		// change attr
		widget.attr({
			'widget-theme':theme.widget,
			'title-theme':theme.title,
			'content-theme':theme.content,
			'widget-style':style.widget,
			'inner-style':style.inner,
			'title-style':style.title,
			'content-style':style.content,
			'inner-w-style':style['inner-w'],
			'title-w-style':style['title-w'],
			'content-w-style':style['content-w'],
			'inner-a-style':style['inner-a'],
			'title-a-style':style['title-a'],
			'content-a-style':style['content-a']
		});
		
		// render title
		if (skin.title) {
			var title = Gen.addTitle(widget);
			for (var i=0,t;t=skin.title[i++];) {
				title.append(Gen.renderSkinTitle(t));
			}
		}
	},
	renderSkinTitle:function(item){
		var text = item.img ? ('<img src="'+item.img+'"/>'+item.text) : item.text;
		if (!text) return '';
		return item.href
			? ('<a href="'+item.href+'" style="'+item.style+'" item-style="'+item.style+'">'+text+'</a>')
			: ('<span style="'+item.style+'" item-style="'+item.style+'">'+text+'</span>');
	},
	renderTitleItem:function(item, link){
		var text = item.img.value ? ('<img src="'+item.img.value+'"/>'+item.text.value) : item.text.value,
            morelist = '';
		if (!text) return '';
		var css = [], v = ival(item.offset.value), f = $(item['float']).filter(':checked').val();
		if (f == 'right') {
			css.push('float:right');
			v && css.push('margin-right:'+v);
		} else {
            if (f == 'center') {
                css.push('display:block;text-align:center');
            } else {
                v && css.push('margin-left:'+v);
            }
            if (item['morelist.add'].checked) {
                morelist = '<a href="' + link + '" item-style="float:right;margin-right:5px;" style="float:right;margin-right:5px;" target="_blank">更多</a>';
            }
        }
		v = ival(item['font-size'].value);
		v && css.push('font-size:'+v);
		v = sval(item.color.value);
		v && css.push('color:'+v);
		css = css.join(';');
		return morelist + (item.href.value
			? ('<a href="'+item.href.value+'" style="'+css+'" item-style="'+css+'">'+text+'</a>')
			: ('<span style="'+css+'" item-style="'+css+'">'+text+'</span>'));
	},
	setPage:function(){
		LANG.BUTTON_PREVIEW = '预览';
		var oStyle = {
				body:body.attr('body-style'),
				a:body.attr('a-style')
			}, recover = false;
		localForm({title:'设置页面样式', width:500, height:300}, 'SET_PAGE',
		function(form, dialog){
			var frm = form[0], css = body.attr('body-style'), acss = body.attr('a-style'), m;
			each('font-size font-weight font-style color background-color background-image background-repeat',
			function(i, s){
				if (m = RE[s].exec(css)) {
					frm[s].value = m[1];
				}
			});
			each('font-size font-weight font-style color', function(i, s){
				if (m = RE[s].exec(acss)) {
					frm['a-'+s].value = m[1];
				}
			});
			if (m = RE['background-position'].exec(css)) {
				m = m[1].split(' ');
				frm['background-position-x'].value = m[0];
				if (m[1]) {
					frm['background-position-y'].value = m[1];
				}
			}
			form.find('.color-input').colorInput();
			form.find('.image-input').imageInput(false, true);
		},function(form, flag){
			var frm = form[0], style = {
				body:[],
				a:[]
			};
			each('background-color background-image background-repeat',
			function(i, s){
				var v = sval(frm[s].value);
				if (v) {
					if (s == 'background-image' && v != 'none') {
						v = 'url('+v+')';
					}
					style.body.push(s+':'+v);
				}
			});
			each('font-size font-weight font-style color', function(i, s){
				each('body a', function(i, t){
					var v = frm[t == 'body' ? s : ('a-'+s)].value;
					v = s == 'font-size' ? ival(v) : sval(v);
					if (v) {
						style[t].push(s+':'+v);
					}
				});
			});
			var pos = [],
				b = bval(frm['background-position-x'].value);
			b && pos.push(b);
			b = bval(frm['background-position-y'].value);
			b && pos.push(b);
			pos.length && style.body.push('background-position:'+pos.join(' '));
			
			style.body = style.body.join(';');
			style.a = style.a.join(';');
			setRule('body', style.body);
			setRule('a', style.a);
			
			recover = flag == 'preview';
			// change attr
			recover || body.attr({
				'body-style':style.body,
				'a-style':style.a
			});
		},function(){
			if (recover) {
				setRule('body', oStyle.body);
				setRule('a', oStyle.a);
			}
		});
	},
	setFrameDialog:function(frame){
		LANG.BUTTON_PREVIEW = '预览';
		var id = frame[0].id,
			title = frame.find('.diy-title'),
			ofTheme = frame.attr('frame-theme'),
			otTheme = frame.attr('title-theme'),
			oStyle = {
				frame:frame.attr('frame-style'),
				title:frame.attr('title-style'),
				'title-w':frame.attr('title-w-style'),
				'title-a':frame.attr('title-a-style')
			}, recover = false;
		ofTheme = ofTheme
			? (ofTheme == '(empty)' ? '' : ofTheme)
			: Theme.def.frame;
		otTheme = otTheme
			? (otTheme == '(empty)' ? '' : otTheme)
			: Theme.def.title;
		var lfTheme = ofTheme, ltTheme = otTheme;
		localForm({title:'设置框架样式', width:500, height:500}, 'SET_FRAME',
		function(form, dialog){
			DIY.tabs(dialog, null, null, 'mouseenter');
			var frm = form[0];
			frm['frame-theme'].value = frame.attr('frame-theme')||'';
			frm['title-theme'].value = frame.attr('title-theme')||'';
			var m, style = {
				frame:frame.attr('frame-style'),
				title:frame.attr('title-style'),
				'title-w':frame.attr('title-w-style'),
				'title-a':frame.attr('title-a-style')
			};
			each('title-w title-a', function(i, t){
				var css = style[t];
				each('font-size font-weight font-style color', function(i, s){
					if (m = RE[s].exec(css)) {
						frm[t+'-'+s].value = m[1];
					}
				});
			});
			each('frame title',function(i, t){
				var css = style[t];
				each('background-color background-image background-repeat',
				function(i, s){
					if (m = RE[s].exec(css)) {
						frm[t+'-'+s].value = m[1];
					}
				});
				if (m = RE['background-position'].exec(css)) {
					m = m[1].split(' ');
					frm[t+'-background-position-x'].value = m[0];
					if (m[1]) {
						frm[t+'-background-position-y'].value = m[1];
					}
				}
			});
			each('height width', function(){
				if (m = RE[this].exec(style.frame)) {
					frm['frame-'+this].value = m[1];
				}
			});
			if (isCenter(style.frame)) {
				frm['frame-center'].checked = true;
			}
			// frame margin
			var fcss = style['frame'];
			if (m = RE['margin'].exec(fcss)) {
				frm['frame-margin-top'].value = m[1];
			} else if (RE['margin-all'].test(fcss)) {
				frm['frame-margin-all'].checked = true;
				each('top right bottom left',function(){
					var k = 'margin-'+this;
					if (m = RE[k].exec(fcss)) {
						frm['frame-'+k].value = m[1];
					}
				});
			}
			// frame border
			each('style width color', function(){
				var s = this;
				if (m = RE['border-'+s].exec(fcss)) {
					frm['frame-border-top-'+s].value = m[1];
				} else if (RE['border-'+s+'-all'].test(fcss)) {
					frm['frame-border-'+s+'-all'].checked = true;
					each('top right bottom left',function(){
						var k = 'border-'+this+'-'+s;
						if (m = RE[k].exec(fcss)) {
							frm['frame-'+k].value = m[1];
						}
					});
				}
			});
			// title padding
			var tcss = style['title'];
			if (m = RE.padding.exec(tcss)) {
				frm['title-padding-top'].value = m[1];
			} else if (RE['padding-all'].test(tcss)) {
				frm['title-padding-all'].checked = true;
				each('top right bottom left',function(){
					var k = 'padding-'+this;
					if (m = RE[k].exec(tcss)) {
						frm['title-'+k].value = m[1];
					}
				});
			}
			form.find('.item-detail').click(function(){
				if (hasClass(this, 'expanded')) {
					removeClass(this, 'expanded');
					this.nextSibling.style.display = 'none';
				} else {
					addClass(this, 'expanded');
					this.nextSibling.style.display = 'block';
					dialog[0].scrollTop = getPosition(this).top;
					if (!$.data(this, 'inited')) {
						$.data(this, 'inited', true);
						$('.image-input', this.nextSibling).imageInput(false, true);
					}
				}
			});
			form.find('.theme-input').each(function(){
				var t = this, k = t.name.split('-')[0];
				t.setAttribute('cat', k);
			}).themeInput(function(){
				dialog.dialog('option', 'position', 'center');
			});
			form.find('.color-input').colorInput();
			fold('frame-margin-all', frm);
			fold('frame-border-width-all', frm);
			fold('frame-border-style-all', frm);
			fold('frame-border-color-all', frm);
			fold('title-padding-all', frm);
		}, function(form, flag){
			var frm = form[0],
				ftv = frm['frame-theme'].value,
				ttv = frm['title-theme'].value,
				style = {
					frame:[],
					title:[],
					'title-w':[],
					'title-a':[]
				};
			each('height width', function(){
				var s = this, v = ival(frm['frame-'+s].value);
				v && style.frame.push(s+':'+v);
			});
			// frame margin
			if (frm['frame-margin-all'].checked) {
				each('top right bottom left',function(){
					var k = 'margin-'+this, v = ival(frm['frame-'+k].value);
					v && style.frame.push(k+':'+v);
				});
			} else {
				var v = ival(frm['frame-margin-top'].value);
				v && style.frame.push('margin:'+v);
			}
			if (frm['frame-center'].checked) {
				style.frame.push('margin-left:auto');
				style.frame.push('margin-right:auto');
			}
			// frame border
			each('style width color', function(){
				var s = this;
				if (frm['frame-border-'+s+'-all'].checked) {
					each('top right bottom left',function(){
						var k = 'border-'+this+'-'+s,
							v = s == 'width'
								? ival(frm['frame-'+k].value)
								: sval(frm['frame-'+k].value);
						v && style.frame.push(k+':'+v);
					});
				} else {
					var k = 'border-top-'+s, v = s == 'width'
								? ival(frm['frame-'+k].value)
								: sval(frm['frame-'+k].value);
					v && style.frame.push('border-'+s+':'+v);
				}
			});
			each('font-size font-weight font-style color', function(i, s){
				var v = frm['title-w-'+s].value;
				v = s == 'font-size' ? ival(v) : sval(v);
				if (v) {
					style.title.push(s+':'+v);
				}
				each('title-w title-a', function(i, t){
					var v = frm[t+'-'+s].value;
					v = s == 'font-size' ? ival(v) : sval(v);
					if (v) {
						style[t].push(s+':'+v);
					}
				});
			});
			// frame title
			each('frame title',function(i, t){
				each('background-color background-image background-repeat',
				function(i, s){
					var v = sval(frm[t+'-'+s].value);
					if (v) {
						if (s == 'background-image' && v != 'none') {
							v = 'url('+v+')';
						}
						style[t].push(s+':'+v);
					}
				});
				var pos = [],
					b = bval(frm[t+'-background-position-x'].value);
				b && pos.push(b);
				b = bval(frm[t+'-background-position-y'].value);
				b && pos.push(b);
				pos.length && style[t].push('background-position:'+pos.join(' '));
			});
			// title padding
			if (frm['title-padding-all'].checked) {
				each('top right bottom left',function(){
					var k = 'padding-'+this, v = ival(frm['title-'+k].value);
					v && style.title.push(k+':'+v);
				});
			} else {
				var v = ival(frm['title-padding-top'].value);
				v && style.title.push('padding:'+v);
			}
			
			each('frame title title-w title-a',function(){
				style[this] = style[this].join(';');
			});
			
			// load css file
			var fTheme = '', tTheme = '';
			if (ftv) {
				if (ftv != '(empty)') {
					Theme.addDir('frame/'+ftv);
					fTheme = ftv;
				}
			} else {
				fTheme = Theme.def.frame;
			}
			if (ttv) {
				if (ttv != '(empty)') {
					Theme.addDir('title/'+ttv);
					tTheme = ttv;
				}
			} else {
				tTheme = Theme.def.title;
			}
			// switch class
			switchClass(title, ltTheme, tTheme, 'title-');
			ltTheme = tTheme;
			
			lfTheme && frame.removeClass('frame-'+lfTheme);
			lfTheme = fTheme;
			fTheme && frame.addClass('frame-'+fTheme);
			
			setRule('#'+id, style['frame']);
			setRule('#'+id+'-t', style['title']);
			setRule('#'+id+'-t *', style['title-w']);
			setRule('#'+id+'-t a', style['title-a']);
			
			recover = flag == 'preview';
			// change attr
			frame.attr({
				'frame-theme':ftv,
				'title-theme':ttv,
				'frame-style':style['frame'],
				'title-style':style['title'],
				'title-w-style':style['title-w'],
				'title-a-style':style['title-a']
			});
			
			setFrameWrapper(frame);
		}, function(){
			if (recover) {
				lfTheme && frame.removeClass('frame-'+lfTheme);
				ofTheme && frame.addClass('frame-'+ofTheme);
				switchClass(title, ltTheme, otTheme, 'title-');
				
				setRule('#'+id, oStyle['frame']);
				setRule('#'+id+'-t', oStyle['title']);
				setRule('#'+id+'-t *', oStyle['title-w']);
				setRule('#'+id+'-t a', oStyle['title-a']);
				
				frame.attr({
					'frame-theme':ofTheme,
					'title-theme':otTheme,
					'frame-style':oStyle['frame'],
					'title-style':oStyle['title'],
					'title-w-style':oStyle['title-w'],
					'title-a-style':oStyle['title-a']
				});
				
				setFrameWrapper(frame);
			}
		});
	},
	setWidgetDialog:function(widget){
		LANG.BUTTON_PREVIEW = '预览';
		
		var id = widget[0].id,
			engine = widget.attr('engine'),
			inner = widget.find('.diy-inner'),
			title = widget.find('.diy-title'),
			content = widget.find('.diy-content'),
			owTheme = widget.attr('widget-theme'),
			otTheme = widget.attr('title-theme'),
			ocTheme = widget.attr('content-theme'),
			oStyle = {
				widget:widget.attr('widget-style'),
				inner:widget.attr('inner-style'),
				title:widget.attr('title-style'),
				content:widget.attr('content-style'),
				'inner-w':widget.attr('inner-w-style'),
				'title-w':widget.attr('title-w-style'),
				'content-w':widget.attr('content-w-style'),
				'inner-a':widget.attr('inner-a-style'),
				'title-a':widget.attr('title-a-style'),
				'content-a':widget.attr('content-a-style')
			}, recover = false;
		owTheme = owTheme
			? (owTheme == '(empty)' ? '' : owTheme)
			: Theme.def.widget;
		otTheme = otTheme
			? (otTheme == '(empty)' ? '' : otTheme)
			: Theme.def.title;
		ocTheme = ocTheme
			? (ocTheme == '(empty)' ? '' : ocTheme)
			: (Theme.def.content && Theme.def.content[engine]);
		var lwTheme = owTheme, ltTheme = otTheme, lcTheme = ocTheme;
		
		localForm({title:'设置模块样式', width:500, height:500}, 'SET_WIDGET',
		function(form, dialog){
			DIY.tabs(dialog, null, null, 'mouseenter');
			var frm = form[0];
			frm['widget-theme'].value = widget.attr('widget-theme')||'';
			frm['title-theme'].value = widget.attr('title-theme')||'';
			frm['content-theme'].value = widget.attr('content-theme')||'';
			var m,
				wStyle = widget.attr('widget-style'),
				style = {
					inner:widget.attr('inner-style'),
					title:widget.attr('title-style'),
					content:widget.attr('content-style'),
					'inner-w':widget.attr('inner-w-style'),
					'title-w':widget.attr('title-w-style'),
					'content-w':widget.attr('content-w-style'),
					'inner-a':widget.attr('inner-a-style'),
					'title-a':widget.attr('title-a-style'),
					'content-a':widget.attr('content-a-style')
				};
			if (isCenter(wStyle)) {
				frm['widget-center'].checked = true;
			}
			each('height width', function(i,s){
				if (m = RE[s].exec(wStyle)) {
					frm['widget-'+s].value = m[1];
				}
			});
			if (m = RE.height.exec(style.content)) {
				frm['content-height'].value = m[1];
			}
			if (RE['text-center'].test(style.content)) {
				frm['content-text-center'].checked = true;
			}
			if (m = RE['line-height'].exec(style.content)) {
				frm['content-line-height'].value = m[1];
			}
			each('font-size font-style font-weight color', function(i, s){
				each('inner-w title-w content-w inner-a title-a content-a',
				function(i, t){
					if (m = RE[s].exec(style[t])) {
						frm[t+'-'+s].value = m[1];
					}
				});
			});
			each('background-color background-image background-repeat', function(i, s){
				each('inner title content', function(i, t){
					if (m = RE[s].exec(style[t])) {
						frm[t+'-'+s].value = m[1];
					}
				});
			});
			each('margin padding',function(i, s){
				var css = style.inner;
				if (m = RE[s].exec(css)) {
					frm['inner-'+s+'-top'].value = m[1];
				} else if (RE[s+'-all'].test(css)) {
					frm['inner-'+s+'-all'].checked = true;
					each('top right bottom left',function(i, t){
						var k = s+'-'+t;
						if (m = RE[k].exec(css)) {
							frm['inner-'+k].value = m[1];
						}
					});
				}
			});
			
			each('style width color', function(i, s){
				var css = style.inner;
				if (m = RE['border-'+s].exec(css)) {
					frm['inner-border-top-'+s].value = m[1];
				} else if (RE['border-'+s+'-all'].test(css)) {
					frm['inner-border-'+s+'-all'].checked = true;
					each('top right bottom left',function(){
						var k = 'border-'+this+'-'+s;
						if (m = RE[k].exec(css)) {
							frm['inner-'+k].value = m[1];
						}
					});
				}
			});
			if (m = RE['background-position'].exec(style.inner)) {
				m = m[1].split(' ');
				frm['inner-background-position-x'].value = m[0];
				if (m[1]) {
					frm['inner-background-position-y'].value = m[1];
				}
			}
			
			each('title content', function(i, s){
				var css = style[s];
				if (m = RE.padding.exec(css)) {
					frm[s+'-padding-top'].value = m[1];
				} else if (RE['padding-all'].test(css)) {
					frm[s+'-padding-all'].checked = true;
					each('top right bottom left',function(){
						var k = 'padding-'+this;
						if (m = RE[k].exec(css)) {
							frm[s+'-'+k].value = m[1];
						}
					});
				}
				if (m = RE['background-position'].exec(css)) {
					m = m[1].split(' ');
					frm[s+'-background-position-x'].value = m[0];
					if (m[1]) {
						frm[s+'-background-position-y'].value = m[1];
					}
				}
			});
			
			form.find('.item-detail').click(function(){
				if (hasClass(this, 'expanded')) {
					removeClass(this, 'expanded');
					this.nextSibling.style.display = 'none';
				} else {
					addClass(this, 'expanded');
					this.nextSibling.style.display = 'block';
					dialog[0].scrollTop = getPosition(this).top;
					if (!$.data(this, 'inited')) {
						$.data(this, 'inited', true);
						$('.image-input', this.nextSibling).imageInput(false, true);
					}
				}
			});
			form.find('.theme-input').each(function(i, t){
				var k = t.name.split('-')[0];
				if (k=='content') {
					k += '/'+engine;
				}
				t.setAttribute('cat', k);
			}).themeInput(function(){
				dialog.dialog('option', 'position', 'center');
			});
			form.find('.color-input').colorInput();
			
			fold('inner-margin-all', frm);
			fold('inner-padding-all', frm);
			fold('inner-border-width-all', frm);
			fold('inner-border-style-all', frm);
			fold('inner-border-color-all', frm);
			fold('title-padding-all', frm);
			fold('content-padding-all', frm);
		}, function(form, flag){
			var frm = form[0],
				wtv = frm['widget-theme'].value,
				ttv = frm['title-theme'].value,
				ctv = frm['content-theme'].value,
				style = {
					widget:[],
					inner:[],
					title:[],
					content:[],
					'inner-w':[],
					'title-w':[],
					'content-w':[],
					'inner-a':[],
					'title-a':[],
					'content-a':[]
				};
			// widget
			if (frm['widget-center'].checked) {
				style['widget'].push('margin-left:auto');
				style['widget'].push('margin-right:auto');
			}
			if (frm['content-text-center'].checked) {
				style['content'].push('text-align:center');
			}
			each('height width', function(){
				var s = this, v = ival(frm['widget-'+s].value);
				v && style['widget'].push(s+':'+v);
			});
			var v = ival(frm['content-height'].value);
			v && style['content'].push('height:'+v);
			
			var lhv = ival(frm['content-line-height'].value);
			lhv && style.content.push('line-height:'+lhv);
			// inner
			each('margin padding',function(i, s){
				if (frm['inner-'+s+'-all'].checked) {
					each('top right bottom left',function(){
						var k = s+'-'+this, v = ival(frm['inner-'+k].value);
						v && style['inner'].push(k+':'+v);
					});
				} else {
					var v = ival(frm['inner-'+s+'-top'].value);
					v && style['inner'].push(s+':'+v);
				}
			});
			each('style width color', function(i, s){
				if (frm['inner-border-'+s+'-all'].checked) {
					each('top right bottom left',function(){
						var k = 'border-'+this+'-'+s,
							v = s == 'width'
								? ival(frm['inner-'+k].value)
								: sval(frm['inner-'+k].value);
						v && style['inner'].push(k+':'+v);
					});
				} else {
					var k = 'border-top-'+s, v = s == 'width'
								? ival(frm['inner-'+k].value)
								: sval(frm['inner-'+k].value);
					v && style['inner'].push('border-'+s+':'+v);
				}
			});
			each('font-size font-weight font-style color', function(i, s){
				each('inner title content', function(i, t){
					var v = frm[t+'-w-'+s].value;
					v = s == 'font-size' ? ival(v) : sval(v);
					if (v) {
						style[t].push(s+':'+v);
					}
				});
				each('inner-w title-w content-w inner-a title-a content-a', function(i, t){
					var v = frm[t+'-'+s].value;
					v = s == 'font-size' ? ival(v) : sval(v);
					if (v) {
						style[t].push(s+':'+v);
					}
				});
			});
			// inner title content
			each('inner title content',function(i, t){
				each('background-color background-image background-repeat',
				function(i, s){
					var v = sval(frm[t+'-'+s].value);
					if (v) {
						if (s == 'background-image' && v != 'none') {
							v = 'url('+v+')';
						}
						style[t].push(s+':'+v);
					}
				});
				var pos = [],
					b = bval(frm[t+'-background-position-x'].value);
				b && pos.push(b);
				b = bval(frm[t+'-background-position-y'].value);
				b && pos.push(b);
				pos.length && style[t].push('background-position:'+pos.join(' '));
			});
			// title content
			each('title content', function(i, t){
				if (frm[t+'-padding-all'].checked) {
					each('top right bottom left',function(){
						var k = 'padding-'+this, v = ival(frm[t+'-'+k].value);
						v && style[t].push(k+':'+v);
					});
				} else {
					var v = ival(frm[t+'-padding-top'].value);
					v && style[t].push('padding:'+v);
				}
			});
			each('widget inner title content inner-w title-w content-w inner-a title-a content-a',function(){
				style[this] = style[this].join(';');
			});
			
			// load css file
			var wTheme = '', tTheme = '', cTheme = '';
			if (wtv) {
				if (wtv != '(empty)') {
					Theme.addDir('widget/'+wtv);
					wTheme = wtv;
				}
			} else {
				wTheme = Theme.def.widget;
			}
			if (ttv) {
				if (ttv != '(empty)') {
					Theme.addDir('title/'+ttv);
					tTheme = ttv;
				}
			} else {
				tTheme = Theme.def.title;
			}
			if (ctv) {
				if (ctv != '(empty)') {
					Theme.addDir('content/'+engine+'/'+ctv);
					cTheme = ctv;
				}
			} else {
				cTheme = (Theme.def.content && Theme.def.content[engine]);
			}
			// switch class
			switchClass(widget, lwTheme, wTheme, 'widget-');
			switchClass(title, ltTheme, tTheme, 'title-');
			switchClass(content, lcTheme, cTheme, 'content-'+engine+'-');
			// record
			lwTheme = wTheme;
			ltTheme = tTheme;
			lcTheme = cTheme;
			
			// set cssText
			setRule('#'+id, style['widget']);
			setRule('#'+id+'-i', style['inner']);
			setRule('#'+id+'-t', style['title']);
			setRule('#'+id+'-c', style['content']);
			setRule('#'+id+'-i *', style['inner-w']);
			setRule('#'+id+'-t *', style['title-w']);
			setRule('#'+id+'-c *', style['content-w']);
			setRule('#'+id+'-i a', style['inner-a']);
			setRule('#'+id+'-t a', style['title-a']);
			setRule('#'+id+'-c a', style['content-a']);
			
			recover = flag == 'preview';
			// change attr
			recover || widget.attr({
				'widget-theme':wtv,
				'title-theme':ttv,
				'content-theme':ctv,
				'widget-style':style['widget'],
				'inner-style':style['inner'],
				'title-style':style['title'],
				'content-style':style['content'],
				'inner-w-style':style['inner-w'],
				'title-w-style':style['title-w'],
				'content-w-style':style['content-w'],
				'inner-a-style':style['inner-a'],
				'title-a-style':style['title-a'],
				'content-a-style':style['content-a']
			});
		}, function(){
			if (recover) {
				switchClass(widget, lwTheme, owTheme, 'widget-');
				switchClass(title, ltTheme, otTheme, 'title-');
				switchClass(content, lcTheme, ocTheme, 'content-'+engine+'-');
				
				setRule('#'+id, oStyle['widget']);
				setRule('#'+id+'-i', oStyle['inner']);
				setRule('#'+id+'-t', oStyle['title']);
				setRule('#'+id+'-c', oStyle['content']);
				setRule('#'+id+'-i *', oStyle['inner-w']);
				setRule('#'+id+'-t *', oStyle['title-w']);
				setRule('#'+id+'-c *', oStyle['content-w']);
				setRule('#'+id+'-i a', oStyle['inner-a']);
				setRule('#'+id+'-t a', oStyle['title-a']);
				setRule('#'+id+'-c a', oStyle['content-a']);
			}
		});
	},
	setTitleDialog:function(where){
		var title = where.hasClass('diy-widget')
				? where.find('.diy-title') : where.children('.diy-title'),
			origTitle = null, hasTitle = title.length, recover = false,
            widgetid = where.attr('widgetid'),
            engine = DIY._widgetEngine[where.attr('engine')],
            supportMorelist = false, morelistSetting = {},
            morelistLink = APP_URL + '?app=special&controller=morelist&action=widget&contentid='+ENV.contentid+'&widgetid='+widgetid;
		LANG.BUTTON_PREVIEW = '预览';
        function openDialog() {
            localForm('设置标题', 'SET_TITLE', function(form){
                var frm = form[0], item = title.children(morelistSetting.add ? ':last' : null),
                    morelistItems = form.find('.fn-widget-morelist');
                if (item.length) {
                    var style = item.attr('item-style');
                    frm.text.value = item.text();
                    if (item[0] && item[0].nodeName == 'A') {
                        frm.href.value = item.attr('href');
                    }
                    frm.img.value = item.children('img').attr('src')||'';
                    var m = RE['float'].exec(style);
                    if (m) {
                        if (m[1] == 'right') {
                            frm['float'][2].checked = true;
                        } else {
                            frm['float'][0].checked = true;
                        }
                    } else if (RE['text-center'].exec(style) && RE['block'].exec(style)) {
                        frm['float'][1].checked = true;
                    }
                    each('offset font-size color',function(){
                        if (m = RE[this].exec(style)) {
                            frm[this].value = m[1];
                        }
                    });
                }
                // 对齐为居中时，禁用 offset 设置项
                $(frm['float']).change(function() {
                    var self = $(this), offset = $(frm['offset']), val = this.value;
                    if (! this.checked) return;
                    if (val == 'center') {
                        offset.attr('disabled', true);
                    } else {
                        offset.removeAttr('disabled');
                    }
                    if (val == 'right') {
                        morelistItems.hide();
                    } else {
                        morelistItems.show();
                    }
                }).trigger('change');
                form.find('.color-input').colorInput();
                form.find('.image-input').imageInput(false, true);
                if (supportMorelist) {
                    var chk = frm['morelist.add'];
                    parseInt(morelistSetting.add) && (chk.checked = true);
                    form.find('a.fn-widget-morelist').click(function() {
                        frm['href'].value = morelistLink;
                        ! chk.checked && (chk.checked = true);
                    });
                } else {
                    morelistItems.hide();
                }
            }, function(form, flag){
                if (!origTitle) {
                    origTitle = title.appendTo(DIY.fragments);
                } else {
                    title && title.remove();
                }
                var item = Gen.renderTitleItem(form[0], morelistLink);
                title = item ? Gen.addTitle(where).html(item) : null;
                var callback = function() {
                    recover = flag == 'preview';
                    if (flag == 'nextstep') {
                        LANG.BUTTON_OK = '完成';
                        LANG.BUTTON_CANCEL = null;
                        DIY.setStyle(where);
                    }
                };
                if (supportMorelist) {
                    // 保存更多链接设置
                    var data = 'setting[morelist][add]=' + (form.find('[name=morelist.add]').attr('checked') ? 1 : 0);
                    json('?app=special&controller=online&action=editWidgetSetting&widgetid='+widgetid, data, function(json) {
                        if (json && json.state) {
                            callback();
                        } else {
                            ct.error(json && json.error || '区块设置修改失败');
                        }
                    });
                } else {
                    callback();
                }
            }, function(){
                if (recover) {
                    title && title.remove();
                    Gen.addTitle(where, origTitle);
                }
            });
        }
        if (engine && engine.support && $.isArray(engine.support) && $.inArray('morelist', engine.support) !== false) {
            // 异步检查当前模块的内容设置，如果是手动，则不显示列表相关的设置
            json('?app=special&controller=online&action=getOneWidget', 'widgetid='+widgetid, function(json) {
                if (json && json.state && json.widget.data && json.widget.data.method != 1) {
                    supportMorelist = true;
                    morelistSetting = json.widget.setting && json.widget.setting.morelist || {};
                }
                openDialog();
            }, openDialog, 'GET');
        } else {
            openDialog();
        }
	},
	q:function(){
		var escapable = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
			meta = {
				'\b': '\\b',
				'\t': '\\t',
				'\n': '\\n',
				'\f': '\\f',
				'\r': '\\r',
				'"' : '\\"',
				'\\': '\\\\'
			};
		return function(str) {
			str = str ? (str + '') : '';
			escapable.lastIndex = 0;
			return escapable.test(str) ?
				'"' + str.replace(escapable, function (a) {
					var c = meta[a];
					return typeof c === 'string' ? c :
					'\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
				}) + '"' :
				'"' + str + '"';
		}
	}()
});
var placeMentDragOptions = {
	selectArea:selectAreaByEvent,
	selectPlace:selectPlace,
	getHolder:function($h) {
		var h = $h[0],
		$p = $('<div class="diy-placeholder"></div>').insertAfter($h),
		height = h.offsetHeight - toInt($p.css('borderTopWidth')) - toInt($p.css('borderBottomWidth')),
		width = h.style.width,
		re = /^(?:[\d\.]+%|auto|)$/,
		margin = [
			h.style.marginTop||0,
			h.style.marginRight||0,
			h.style.marginBottom||0,
			h.style.marginLeft||0
		].join(' ');
		if (!re.test(width)) {
			width = h.offsetWidth - toInt($p.css('borderLeftWidth')) - toInt($p.css('borderRightWidth'));
		}
		$p.css({height:height, width:width, margin:margin});
		return $p;
	},
	getGhost:function($h, top, left, e) {
		var h = $h[0], ow = h.offsetWidth, oh = h.offsetHeight, nh, nw;
		$h.data('origCSSText',h.style.cssText||'').css({
			width:$h.width(),
			position:'absolute',
			top:top,
			left:left,
			margin:0,
			zIndex:151,
			opacity:.5,
			cursor:'move',
			maxHeight:100,
			maxWidth:300,
			overflow:'hidden'
		}).appendTo(body);
		nh = h.offsetHeight, nw = h.offsetWidth;
		if (nh < oh) {
			var pageY = e.clientY + $doc.scrollTop();
			if (top + nh < pageY) {
				$h.css('top', pageY - nh/2);
			}
		}
		if (nw < ow) {
			var pageX = e.clientX + $doc.scrollLeft();
			if (left + nw < pageX) {
				$h.css('left', pageX - nw/2);
			}
		}
		return $h;
	},
	fromGhost:function($g, found) {
		var cssText = $g[0].style.cssText;
		$g[0].style.cssText = $g.data('origCSSText');
		if (!found) {
			var k = $g.clone().appendTo(body);
			k[0].style.cssText = cssText;
			$g.appendTo(DIY.fragments);
			k.fadeOut('fast', function(){
				k.remove();
			});
			return null;
		}
		return $g;
	}
};
function setFrameWrapper(frame){
	var f = frame.css('margin', '')[0], w = f.parentNode,
		margins = {0:'3px', 1:'3px', 2:'5px', 3:'3px'}, designMargin = [],
		style = f.getAttribute('frame-style'),
		auto = RE.center.test(style) && 'auto',
		margin = [
			$.curCSS(f, 'margin-top'),
			auto || (RE['margin-right'].exec(style) || {1:$.curCSS(f, 'margin-right')})[1],
			$.curCSS(f, 'margin-bottom'),
			auto || (RE['margin-left'].exec(style) || {1:$.curCSS(f, 'margin-left')})[1]
		], m = RE.width.exec(style);
	for(var k in margins) {
		var x = margin[k];
		designMargin.push((x == 'auto' || toFloat(x)) ? x : margins[k]);
	}
	margin = margin.join(' ');
	designMargin = designMargin.join(' ');
	
	w.style.margin = DIY.designMode ? designMargin : margin;
	w.setAttribute('view-margin', margin);
	w.setAttribute('design-margin', designMargin);
	f.style.margin = '0';
	
	if (!m || m[1] == 'auto') {
		w.style.width = '';
	} else {
		if (RE.percent.test(m[1])) {
			w.style.width = m[1];
			f.style.width = 'auto';
		} else {
			w.style.width = f.offsetWidth + 'px';
		}
	}
}
function getColor(elem){
	if (elem.hasClass('diy-frame')) {
		elem = elem.parent();
	}
	var pw = elem.closest('.diy-frame').parent(), c = 0;
	if (pw.length) {
		var m = RE.colorful.exec(pw[0].className);
		if (m) {
			c = COLORS.indexOf(m[1]) + 1;
			if (c == COLORS.length) {
				c = 0;
			}
		}
	}
	return 'color-'+COLORS[c];
}
function setColor(wrapper){
	wrapper[0].className = wrapper[0].className.replace(RE.colorful, getColor(wrapper));
	wrapper.find('.diy-wrapper').each(function(i,t){
		t.className = t.className.replace(RE.colorful, getColor($(t)));
	});
}
function initFrame(frame){
	frame.jquery || (frame = $(frame));
	var f = frame[0], c = getColor(frame),
		areas = frame.children('.diy-area'), n = areas.length, l = 0;
	if (!f.offsetWidth) {
		throw "parse frame error";
	}
	
	var wrapper = $(
	'<div class="diy-wrapper diy-placement '+c+'">' +
		'<div class="diy-top diy-border"></div>' +
		'<div class="diy-right diy-border"></div>' +
		'<div class="diy-bottom diy-border"></div>' +
		'<div class="diy-left diy-border"></div>' +
		'<div class="diy-handle diy-border"></div>' +
	'</div>');
	
	wrapper.find('.diy-handle').hover(function(){
		wrapper.addClass('diy-over');
	},function(){
		wrapper.removeClass('diy-over');
	});
		
	frame.after(wrapper).appendTo(wrapper);
	
	setFrameWrapper(frame);
	
	var w = frame.width();
	
	areas.each(function(i){
		var t = this;
		guidStack.push(t.id);
		if (i+1==n) return;
		var p = RE.percent.exec($.curCSS(t, 'width'));
		if (p) {
			l += toFloat(p[1]);
		} else {
			l += t.offsetWidth * 100 / w;
		}
		var h = $('<div class="diy-x-resize" title="点住拖动改变宽度" style="left:'+toFixed(l)+'%"></div>')
			.insertAfter(t);
		bindResizeEvent(h, frame);
	});
	new dragMent(wrapper, placeMentDragOptions);
	bindOver(wrapper);
	bindMove(wrapper);
}
function bindOver(o) {
	o.mouseover(function(e){
		e.stopPropagation();
		var w = $(e.target).closest('.diy-placement'), m = DIY.more[0];
		m.parentNode == w[0] || w.append(m);
	});
}
function bindMove(elem) {
	var origPos = null;
	elem.bind('dragInit',function(){
		origPos = {area:elem[0].parentNode, place:elem.next()[0]};
		elem.addClass('diy-move');
	}).bind('dragEnd',function(){
		var newPos = {area:elem[0].parentNode, place:elem.next()[0]};
		if (newPos.area != origPos.area || newPos.place != origPos.place)
		{
			History.log('move', [elem, origPos, newPos]);
		}
		elem.removeClass('diy-move');
		elem.hasClass('diy-wrapper') && setColor(elem);
	}).bind('mouseup', function(){
		elem.removeClass('diy-move');
	});
}
function initWidget(widget, step) {
	widget.jquery || (widget = $(widget));
	if (!step) {
		var engine = widget.attr('engine'),
			hooks = (engine in DIY._widgetEngine) ? DIY._widgetEngine[engine] : {};
        hooks.widgetid = widget[0].id;
        hooks.content = widget.find('.diy-content');
		hooks.afterRender && hooks.afterRender.call(hooks, widget);
	}
	if (!step || step == 1) {
		guidStack.push(widget[0].id);
		widget.addClass('diy-placement');
		widget.append('<div class="diy-masker"></div>');
	}
	if (!step || step == 2) {
		new dragMent(widget, placeMentDragOptions);
		bindOver(widget);
		bindMove(widget);
		widget.dblclick(function(){
			DIY.editWidget(widget);
		});
	}
}
function microAjust(dx, p, ppa, nna, frame) {
	// Micro-adjustment change into percent format
	var fw = frame.width(), fpw = 99.9, l = 0;
	ppa.length && ppa.each(function(){
		var w = $.curCSS(this,'width'), v = RE.percent.exec(w);
		if (v) {
			l += toFloat(v[1]);
		} else {
			l += this.offsetWidth * 100 / fw;
		}
	});
	fpw -= l;
	nna.length && nna.each(function(){
		var w = $.curCSS(this,'width'), v = RE.percent.exec(w);
		if (v) {
			fpw -= toFloat(v[1]);
		} else {
			fpw -= this.offsetWidth * 100 / fw;
		}
	});
	var opw = p.width() + dx,
	npw = toFloat(toFixed(opw * 100 / fw)),
	nw = toFloat(toFixed(fpw - npw)),
	onw = toFixed(nw * fw / 100);
	return {
		pw:npw,
		nw:nw,
		opw:opw,
		onw:onw,
		hl:l + npw
	}
}
function bindResizeEvent(h, frame) {
	var p = h.prev('.diy-area'),
		n = h.next('.diy-area'),
		ph = p.prev('.diy-x-resize'),
		ppa = p.prevAll('.diy-area'),
		nna = n.nextAll('.diy-area');
	new resizeX(h, function(curX){
		return ((ph.length ? (curX < ph.position().left + ph[0].offsetWidth) : (curX < 0))
		|| (curX > n.position().left + n[0].offsetWidth - h[0].offsetWidth)) ? false : true;
	});
	var tl,tr,origScale;
	h.bind('resizeInit',function(e, o){
		h.addClass('active');
		var tips = frame.children('.diy-resize-tips');
		if (tips.length) {
			tl = tips[0];
			tr = tips[1];
		} else {
			tl = $('<div class="diy-resize-tips"></div>').appendTo(frame)[0];
			tr = $('<div class="diy-resize-tips"></div>').appendTo(frame)[0];
		}
		var l = h.position().left;
		tl.style.visibility = 'hidden';
		tl.style.display = 'block';
		var pw = p.css('width'), nw = n.css('width'),
			opw = p[0].offsetWidth, onw = n[0].offsetWidth;
		origScale = {pw:pw, nw:nw, hl:h.css('left')};
		tl.innerHTML = pw+'（'+opw+'px）';
		tr.innerHTML = nw+'（'+onw+'px）';
		tl.style.left = (l - 8 - tl.offsetWidth)+'px';
		tl.style.visibility = 'visible';
		tr.style.left = (l + 8 + h[0].offsetWidth)+'px';
		tr.style.display = 'block';
	}).bind('resizeIng',function(e, o, dx, curX){
		var v = microAjust(dx, p, ppa, nna, frame);
		tl.innerHTML = v.pw+'%（'+v.opw+'px）';
		tr.innerHTML = v.nw+'%（'+v.onw+'px）';
		tl.style.left = (curX - 8 - tl.offsetWidth)+'px';
		tr.style.left = (curX + 8 + h[0].offsetWidth)+'px';
	}).bind('resizeEnd',function(e, o, dx){
		tl.style.display = 'none';
		tr.style.display = 'none';
		h.removeClass('active');
		if (dx) {
			var v = microAjust(dx, p, ppa, nna, frame);
			var pw = toFixed(v.pw)+'%', nw = toFixed(v.nw)+'%', hl = toFixed(v.hl)+'%',
				newScale = {pw:pw,nw:nw,hl:hl};
			p.width(pw);
			n.width(nw);
			o.$handle.css('left', hl);
			History.log('resize', [h, p, n, origScale, newScale]);
			DIY.trigger('changed');
		}
	});
}

function selectAreaByEvent(e, except) {
	except.style.visibility = 'hidden';
	var el = elementFromEvent(e);
	except.style.visibility = 'visible';
	if (!el) {
		return null;
	}
	if (el.nodeType == 9 || el.nodeName == 'HTML') {
		el = document.body;
	}

	return selectArea(el, e.pageX);
}

function selectArea(node, x) {
	do {
		if (node.nodeType == 1) {
			if (hasClass(node, 'diy-area')) {
				return node;
			}
			if (node.id == 'diy-pannel') {
				return 'cancel';
			}
			if (node == document.body) {
				return null;
			}
			if (hasClass(node, 'diy-border')) {
				node == node.parentNode;
			}
			if (hasClass(node, 'diy-wrapper')) {
				return selectAreaInWrapper(node, x);
			}
			if (hasClass(node, 'diy-frame')) {
				return selectAreaInFrame(node, x);
			}
		}
	} while ((node = node.parentNode));
	return null;
}

function selectAreaInWrapper(node, x) {
	var node = node.firstChild;
	do {
		if (node.nodeType == 1 && node.offsetWidth && hasClass(node, 'diy-frame'))
		{
			return selectAreaInFrame(node, x);
		}
	} while ((node = node.nextSibling));
	return null;
}


function selectAreaInFrame(frame, x) {
	var node = frame.firstChild;
	do {
		if (node.nodeType == 1 && node.offsetWidth && hasClass(node, 'diy-area'))
		{
			var l = getOffset(node).left;
			if (l <= x && l + node.offsetWidth >= x) {
				return node;
			}
		}
	} while ((node = node.nextSibling));
	return null;
}

function selectPlace(el,y) {
	if (! (el = el.firstChild)) return null;
	var temp = null;
	do {
		if (el.nodeType == 1 && hasClass(el, 'diy-placement') && el.offsetHeight)
		{
			var t = el.offsetHeight / 2 + getOffset(el).top,
				p = $.curCSS(el, 'position'),
				abs = (p == 'absolute' || p == 'fixed');
			if (t >= y) {
				if (!abs) {
					return temp || el;
				}
				if (!temp) {
					temp = el;
				}
			} else if (!abs) {
				temp = null;
			}
		}
	} while ((el = el.nextSibling));

	return temp;
}

/**
 * Class resizeX
 */
var resizeX = function(handle,checkBound){
	var t = this;
	t.origX = null;
	t.curX = null;
	t.dX = null;
	t.$handle = handle.jquery ? handle : $(handle);
	t.handle = t.$handle[0];
	t.checkBound = checkBound;
	t.pX = 0;
	t._resizeStarted = false;
	t.$handle.mousedown(function(e){
		e.stopPropagation();
		e.preventDefault();
		$doc.mousedown();
		t.resizeInit(e);
	});
}
resizeX.prototype = {
	resizeInit:function(e){
		var t = this, h = t.handle, $h = t.$handle;
		t.origX = $h.position().left;
		t.curX = t.origX;
		t.pX = e.pageX;
		t.dX = 0;
		$.event.add(document, 'mousemove.resizeX', function(e){
			t.resizeIng(e);
		});
		$.event.add(document, 'mouseup.resizeX', function(e){
			t.resizeEnd(e);
		});
		$.event.add(document, 'selectstart.resizeX', function(){return false;});
		$.browser.mozilla && (document.body.style.MozUserSelect = 'none');
		document.body.style.cursor = 'e-resize';
		
		$h.triggerHandler('resizeInit',[t, t.origX]);
	},
	resizeIng:function(e){
		var t = this, h = t.handle;
		if (! t._resizeStarted) {
			t._resizeStarted = true;
			if (h.setCapture) {
				$.event.add(h, 'losecapture.resizeX', function(e){
					t.resizeEnd(e);
				});
				h.setCapture();
			}
			t.$handle.triggerHandler('resizeStart', [t, t.origX]);
		}
		var dX = e.pageX - t.pX, curX = t.origX + dX;
		if (t.checkBound(curX, t.$handle)) {
			t.curX = curX;
			t.dX = dX;
			t.$handle.css('left', curX);
			t.$handle.triggerHandler('resizeIng', [t, dX, curX, t.origX]);
		}
	},
	resizeEnd:function(e){
		var t = this, h = t.handle;
		$.event.remove(document, '.resizeX');
		$.browser.mozilla && (document.body.style.MozUserSelect = '');
		document.body.style.cursor = '';
		if (t._resizeStarted) {
			t._resizeStarted = false;
			$.event.remove(h, '.resizeX');
			h.releaseCapture && h.releaseCapture();
		}
		t.$handle.triggerHandler('resizeEnd', [t, t.dX, t.curX, t.origX]);
	}
};
/**
 * Class dragMent
 */
var isRightClick = function(){
	var RIGHT_KEY_VALUE = /maxthon[\/: ]2/i.test(navigator.userAgent) ? 0 : 2;
	return function(e){
		return e.button == RIGHT_KEY_VALUE;
	};
}();
var dragMent = function(elem, options){
	var t = this;
	t.origLeft = null;
	t.origTop = null;
	t.curLeft = null;
	t.curTop = null;
	t.dX = null;
	t.dY = null;
	t.clickX = 0;
	t.clickY = 0;
	t.ival = null;
	t.placeHolder = null;
	t.placeArea = null;
	t.$elem = elem.jquery ? elem : $(elem);
	t.elem = t.$elem[0];
	t.selectArea = options.selectArea;
	t.selectPlace = options.selectPlace;
	t.getGhost = options.getGhost;
	t.getHolder = options.getHolder;
	t.fromGhost = options.fromGhost;
	t.moveStarted = false;
	t.$elem.mousedown(function(e){
		if (isRightClick(e)) return;
		e.stopPropagation();
		e.preventDefault();// disable select pic
		$doc.mousedown();
		t.dragInit(e);
	});
}
dragMent.prototype = {
	dragInit:function(e){
		var t = this, $h = t.$elem;

		t.clickX = e.pageX;
		t.clickY = e.pageY;

		$.event.add(document, 'mousemove.dragment', function(e){
			t.dragIng(e);
		});
		$.event.add(document, 'mouseup.dragment', function(e){
			t.dragEnd(e);
		});
		$.event.add(document, 'selectstart.dragment', function(){return false;});
		$.browser.mozilla && (document.body.style.MozUserSelect = 'none');
		$h.triggerHandler('dragInit', []);
	},
	dragIng:function(e){
		var t = this, $g, g, $h = t.$elem;
		if (! t.moveStarted) {
			t.moveStarted = true;
			var offset = $h.offset();

			t.placeHolder = t.getHolder($h);
			t.hasPlaced = t.placeHolder.is(':visible');
			t.ghost = t.getGhost($h, offset.top, offset.left, e);
			offset = t.ghost.offset();
			t.origLeft = offset.left;
			t.origTop = offset.top;
			$g = t.ghost;
			g = $g[0];
			
			if (g.setCapture) {
				$.event.add(g, 'losecapture.dragment', function(e){
					t.dragEnd(e);
				});
				g.setCapture();
			}
		} else {
			$g = t.ghost;
			g = $g[0];
		}

		t.dX = e.pageX - t.clickX;
		t.dY = e.pageY - t.clickY;
		t.curLeft = t.origLeft + t.dX;
		t.curTop  = t.origTop + t.dY;
		
		var scrollTop = $doc.scrollTop(),
			scrollLeft = $doc.scrollLeft(),
			clientHeight = window.innerHeight || document.documentElement.clientHeight,
			clientWidth = window.innerWidth || document.documentElement.clientWidth;
		if (t.curTop + g.offsetHeight - scrollTop + 10 >= clientHeight) {
			scrollTop = scrollTop + 20;
		} else if (t.curTop < scrollTop + 10) {
			scrollTop = scrollTop - 20;
		}
		if (t.curLeft + g.offsetWidth - scrollLeft + 10 >= clientWidth) {
			scrollLeft = scrollLeft + 20;
		} else if (t.curLeft < scrollLeft + 10) {
			scrollLeft = scrollLeft - 20;
		}
		
		$g.css({
			left:t.curLeft,
			top:t.curTop
		});
		window.scrollTo(scrollLeft, scrollTop);
		
		var el = t.selectArea(e, g);
		if (el == 'cancel') {
			t.hasPlaced = false;
			t.placeHolder.hide();
		} else if (el) {
			t.hasPlaced = true;
			var where = t.selectPlace(el, e.pageY);
			where
				? t.placeHolder.insertBefore(where)
				: t.placeHolder.appendTo(el);
			t.placeHolder.show();
		}
	},
	dragEnd:function(e){
		var t = this;
		$.event.remove(document, '.dragment');
		$.browser.mozilla && (document.body.style.MozUserSelect = '');
		if (t.moveStarted) {
			t.moveStarted = false;
			var $g = t.ghost, g = t.ghost[0], $h = t.$elem;
			$.event.remove(g, '.dragment');
			g.releaseCapture && g.releaseCapture();
			var o = t.fromGhost($g, t.hasPlaced);
			if (t.hasPlaced && o) {
				t.placeHolder.replaceWith(o);
			} else {
				t.placeHolder.remove();
			}
			t.placeHolder = null;
			$h.triggerHandler('dragEnd', []);
		}
	}
};
var Hotkey = {
	_hooks:{},
	init:function() {
		$doc.keydown(function(e){
			if (READY && e.ctrlKey && e.shiftKey && e.keyCode) {
				var hooks = Hotkey._hooks[e.keyCode];
				if (hooks && hooks.length) {
					for (var i=0,fn;fn=hooks[i++];) {
						fn(e);
					}
				}
			}
		});
		return this;
	},
	bind:function(key, fn) {
		var hooks = this._hooks;
		key = key.charCodeAt(0);
		if (!(key in hooks)) {
			hooks[key] = [];
		}
		hooks[key].push(fn);
		return this;
	}
};


// run
$(function(){DIY.init()});
})(jQuery, window);