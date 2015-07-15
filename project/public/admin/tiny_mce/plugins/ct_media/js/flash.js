cls = 'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000';
codebase = 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0';
type = 'application/x-shockwave-flash';
ed = tinyMCEPopup.editor;

// 插入flash
var insertFlash = function(form) {
	var text,data,src,width,height;
	src = form.src.value || '';
	width = form.width.value || 400;
	height = form.height.value || 300;
	data = {
		'width'		: width,
		'height'	: height,
		'src'		: src,
		'type'		: type
	}
	data = new Array('<object class="cmstopFlash" width="'+data.width+'" height="'+data.height+'" data="'+data.src+'" type="'+data.type+'">',
				'<param name="data" value="'+data.src+'" />',
				'<param name="src" value="'+data.src+'" />',
			'</object>').join('\r\n');
	text = '<img src="/tiny_mce/plugins/ct_media/img/trans.gif" data-mce-src="/tiny_mce/plugins/ct_media/img/trans.gif" class="cmstopFlash" width="'+width+'" height="'+height+'" data-mce-data="'+encodeURI(data)+'" />';
	ed.execCommand('mceInsertContent', false, text);
	tinyMCEPopup.close();
}

// 预览flash
var generatePreview = function() {
	var f = document.forms[0], p = document.getElementById('prev'), h = '', cls, pl, n, type, codebase, wp, hp, nw, nh;

	nw = parseInt(f.width.value);
	nh = parseInt(f.height.value);

	if (f.width.value != "" && f.height.value != "") {
		if (f.constrain.checked) {
			if (c == 'width' && oldWidth != 0) {
				wp = nw / oldWidth;
				nh = Math.round(wp * nh);
				f.height.value = nh;
			} else if (c == 'height' && oldHeight != 0) {
				hp = nh / oldHeight;
				nw = Math.round(hp * nw);
				f.width.value = nw;
			}
		}
	}

	oldWidth = f.width.value || '';
    oldHeight = f.height.value || '';

	pl = _serializeParameters();

	if (pl == {}) {
		p.innerHTML = '';
		return;
	}

	if (!pl.src) {
		p.innerHTML = '';
		return;
	}

	pl.width = !pl.width ? '' : pl.width;
	pl.height = !pl.height ? '' : pl.height;
	pl.id = !pl.id ? 'obj' : pl.id;
	pl.name = !pl.name ? 'eobj' : pl.name;
	pl.align = !pl.align ? '' : pl.align;

	// Avoid annoying warning about insecure items
	if (!tinymce.isIE || document.location.protocol != 'https:') {
		h += '<object classid="' + cls + '" codebase="' + codebase + '" width="' + pl.width + '" height="' + pl.height + '" id="' + pl.id + '" name="' + pl.name + '" align="' + pl.align + '">';

		for (n in pl) {
			h += '<param name="' + n + '" value="' + pl[n] + '">';

			// Add extra url parameter if it's an absolute URL
			if (n == 'src' && pl[n].indexOf('://') != -1)
				h += '<param name="url" value="' + pl[n] + '" />';
		}
	}

	h += '<embed type="' + type + '" ';

	for (n in pl)
		h += n + '="' + pl[n] + '" ';

	h += '></embed>';

	// Avoid annoying warning about insecure items
	if (!tinymce.isIE || document.location.protocol != 'https:')
		h += '</object>';
	p.innerHTML = h;
}


var _serializeParameters = function() {
	var d = document, f = d.forms[0], pl = {};
	pl.src = f.src.value || '';
	pl.width = f.width.value || 400;
	pl.height = f.height.value || 300;
	pl.align = 'center';
	return pl;
}

// 初始化回调
tinyMCEPopup.onInit.add(function() {
	document.getElementById('filebrowsercontainer').innerHTML = getBrowserHTML('filebrowser','src','flash','media');
	$('#uploader').uploader({
		script         : '?app=editor&controller=video&action=upload',
		fileDataName   : 'ctvideo',
		fileDesc		 : '视频',
		fileExt		 : '*.swf;',
		multi : false,
		progress:function(data)
		{
		},
		complete:function(response,data)
		{
			$('#src').val(response);
			generatePreview();
		}
	});
});