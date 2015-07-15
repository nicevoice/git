/**
 * A autoload framwork, make use everything simple
 *
 * @author     kakalong
 * @copyright  2011 (c) firebing.cn
 * @version    $Id$
 */
(function(window){

var document = window['document'],
	head = document.head || document.getElementsByTagName("head")[0] || document.documentElement,
	
	R_SPLIT = /[\s|,]+/,
	R_URL = /^(\w{3,6}:)\/\/([^?]+)/,
	R_ALIAS = /\{(\w+)\}/g,
	R_CSS = /css$/i,
	
	host = location.host,
	protocal = location.protocol,
	pathname = location.pathname,
	
	isFired = 0, isIdle = 0, fetBound = 0, fetList = [],
	
	TIMEOUT = 1e4,
	ALIAS = {}, REPOS = {};

var type = (function(){
	var CLASS2TYPE = {}, toString = Object.prototype.toString;
	for (var i=0,t='Boolean Number String Function Array Date RegExp Object'.split(' '),k;k=t[i++];)
	{
		CLASS2TYPE['[object '+k+']'] = k.toLowerCase();
	}
	return function(obj) {
		return obj == null ?
			String( obj ) :
			CLASS2TYPE[ toString.call(obj) ] || 'object';
	};
})();
function setBase(base){
	var m;
	if (base && (m = R_URL.exec(base))) {
		protocal = m[1];
		var s = m[2].split('/');
		host = s.shift();
		pathname = '/'+s.join('/');
		return true;
	}
	return false;
}
(function(){
	var elems = head.getElementsByTagName('base'),
		l = elems.length, i = 0;
	while (i < l && !setBase(elems[i++].getAttribute('href'))){}
})();
function realURL(url){
	url = url.replace(R_ALIAS, function(a,b){
		return ALIAS[b] || '';
	});
	if (R_URL.test(url)) {
		return url;
	}
	var c = url.substr(0, 1);
	if (c == '/') {
		return protocal + '//' + host + url;
	}
	var u = pathname.split('/'), l = u.length-1;
	u[l] = c == '?' ? (u[l] + url) : url;
	return protocal + '//' + host + u.join('/');
}
function each(o, fn){
	type(o) == 'array' || (o = o.split(R_SPLIT));
	var i = 0, l = o.length;
	while (i<l) {
		fn(o[i++]);
	}
}
function run(){
	var fn;
	if (fn = fetList.shift()) {
		fn(run);
	} else {
		isIdle = 1;
	}
}
function queue(stack, over, threads) {
	var fn, length = stack.length;
	if (length < 1) {
		return over();
	}
	function go(){
		length--;
		(fn = stack.shift()) ? fn(go) : (length == 0 && over());
	}
	threads || (threads = 1);
	while(threads-- > 0 && (fn = stack.shift())) {
		fn(go);
	}
}
function fireFet() {
	if (isFired) return;
	if (!document.body) {
		return setTimeout(fireFet, 1);
	}
	isFired = 1;
	isIdle = 0;
	queue(fetList, function(){
		isIdle = 1;
	});
}
function bindFet(){
	if (fetBound) {
		return;
	}
	fetBound = 1;

	if (document.readyState == 'complete') {
		return setTimeout(fireFet, 1);
	}

	if (document.addEventListener) {
		function DOMContentLoaded(){
			document.removeEventListener('DOMContentLoaded', DOMContentLoaded, false);
			fireFet();
		}
		document.addEventListener('DOMContentLoaded', DOMContentLoaded, false);
		window.addEventListener('load', fireFet, false);
	} else if (document.attachEvent) {
		function DOMContentLoaded(){
			if (document.readyState == 'complete') {
				document.detachEvent('onreadystatechange', DOMContentLoaded);
				fireFet();
			}
		}
		document.attachEvent('onreadystatechange', DOMContentLoaded);
		window.attachEvent('onload', fireFet);

		var toplevel = false;

		try {
			toplevel = window.frameElement == null;
		} catch(e) {}

		if (toplevel && document.documentElement.doScroll) {
			function doScrollCheck(){
				try {
					document.documentElement.doScroll('left');
				} catch(e) {
					setTimeout(doScrollCheck, 1);
					return;
				}
				fireFet();
			}
			doScrollCheck();
		}
	}
}

function loadAsset(url, charset, fn){
	url = realURL(url);
	loadAsset[R_CSS.test(url) ? 'link' : 'script'](url, charset, fn);
}
loadAsset.link = function(url, charset, fn){
	var link = document.createElement('link'), tval = null, clear = null;
	charset && link.setAttribute('charset', charset);
    link.rel = 'stylesheet';
    link.href = url;
    link.type = 'text/css';
    if (/msie|opera/i.test(navigator.userAgent)) {
    	clear = function(){
			tval && clearTimeout(tval);
    		link.onload = link.onreadystatechange = null;
			link = null;
    	};
		link.onload = link.onreadystatechange = function(){  
			if (!link.readyState || /loaded|complete/.test(link.readyState)) {
				clear();
				setTimeout(function(){
					fn(200);
				}, 0);
			}
		};
	} else {
		var ival = setInterval(function(){  
			try {
				link.sheet.cssRules.length;
			} catch (e) {  
				if (e.code != 1e3 && e.message != 'security' && e.message != 'denied') {
					return;
				}
			}
			clear();
			setTimeout(function(){
				fn(200);
			}, 0);
		}, 13);
		clear = function(){
			ival && clearInterval(ival);
			tval && clearTimeout(tval);
			link = null;
		};
	}
	tval = setTimeout(function(){
		clear();
		fn(404);
	}, TIMEOUT);
	head.appendChild(link);
};
loadAsset.script = function(url, charset, fn){
	var script = document.createElement('script'), tval = null,
	clear = function(){
		tval && clearTimeout(tval);
		script.onload = script.onreadystatechange = script.onerror = null;
		head && script.parentNode && head.removeChild(script);
		script = null;
	};
	charset && script.setAttribute('charset', charset);
	script.async = "async";
	script.src = url;
	script.onload = script.onreadystatechange = function(){
		if (!script.readyState || /loaded|complete/.test(script.readyState)) {
			clear();
			setTimeout(function(){
				fn(200);
			}, 0);
		}
	};
	script.onerror = function(){
		clear();
		fn(404);
	};
	tval = setTimeout(function(){
		clear();
		fn(404);
	}, TIMEOUT);
	head.insertBefore(script, head.firstChild);
};
function loadObj(o, fn){
	if (!o) return fn();
	loadDep(o.depends, function(){
		if (o.state) return fn();
		if (o.test) {
			type(o.test) == 'function'
				|| (o.test = new Function('return ('+o.test+') ? true : false;'));
			if (o.test()) {
				o.state = 200;
				return fn();
			}
		}
		o.state = 400;
		var stack = [];
		each(o.assets, function(f){
			if (!f) return;
			stack.push(function(iterator){
				loadAsset(f, o.charset, iterator);
			});
		});
		queue(stack, function(){
			o.state = 200;
			fn();
		}, 3);
	});
}
function loadDep(o, fn){
	if (!o) return fn();
	if (type(o) == 'object') {
		return loadObj(o, fn);
	}
	var stack = [];
	each(o, function(v){
		if (!v) return;
		var t = type(v);
		if (t != 'object') {
			if (t != 'string' || !(v in REPOS)) {
				throw 'component "'+v+'" is not registered';
			}
			v = REPOS[v];
		}
		stack.push(function(iterator){
			loadObj(v, iterator);
		});
	});
	queue(stack, fn);
}
function fet(component, fn) {
	fetBound || bindFet();
	var func = function(iterator){
		var t = type(component);
		if (t != 'object' && t != 'array' && t != 'string') {
			throw "argument 'component' must be object or array or string";
		}
		loadDep(component, function(){
			fn && fn();
			iterator();
		});
	};
	if (isIdle) {
		isIdle = 0;
		func(run);
	} else {
		fetList.push(func);
	}
}
fet.init = function(options) {
	for (var k in options) {
		var f = 'set'+ k.charAt(0).toUpperCase() + k.substr(1);
		(f in fet) && fet[f](options[k]);
	}
};
fet.setAlias = function(key, val) {
	if (type(key) == 'object') {
		for (var k in key) {
			ALIAS[k] = key[k];
		}
	} else {
		ALIAS[key] = val;
	}
};
fet.setBase = setBase;
fet.setTimeout = function(timeout) {
	TIMEOUT = parseInt(timeout);
};
fet.register = function(name, conf) {
	if (type(name) == 'object') {
		for (var k in name) {
			fet.register(k, name[k]);
		}
	} else {
		if (name in REPOS) {
			throw 'component name "'+name+'" is conflict in repos';
		}
		if (type(conf) != 'object') {
			throw 'argument "conf" must be object';
		}
		REPOS[name] = conf;
	}
};
window.fet = fet;
})(window);