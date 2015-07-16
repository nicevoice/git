window.fet && fet.register({
	'lib.jQuery':{
		assets:'{IMG_URL}js/lib/jquery.js',
		test:'window.jQuery'
	},
	cmstop:{
		assets:'{IMG_URL}js/cmstop/style.css {IMG_URL}js/cmstop.js {IMG_URL}js/config.js',
		depends:'lib.jQuery'
	},
	'lib.autocomplete':{
		assets:'{IMG_URL}js/autocomplete/style.css {IMG_URL}js/cmstop.autocomplete.js',
		depends:'lib.jQuery'
	},
	'net.BMap':{
		assets:'http://api.map.baidu.com/res/11/bmap.css http://api.map.baidu.com/getscript?v=1.1&services=true&js',
		test:'window.BMap'
	}
});