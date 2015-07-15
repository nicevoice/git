ed = tinyMCEPopup.editor;
// 插入视频
var insertVideo = function(f) {
	var fe, h;
	if($('input:radio:checked').val())
	{
		$.get('?app=editor&controller=video&action=getVideocode&contentid='+$('input:radio:checked').val(),{},function(text){
			var width = /width\s*=\s*['"]?(\w*)['"]?/.test(text) ? /width\s*=\s*['"]?(\w*)['"]?/.exec(text)[1] : 450;
			var height = /height\s*=\s*['"]?(\w*)['"]?/.test(text) ? /height\s*=\s*['"]?(\w*)['"]?/.exec(text)[1] : 400;
			var text = '<img src="/tiny_mce/plugins/ct_media/img/trans.gif" data-mce-src="/tiny_mce/plugins/ct_media/img/trans.gif" class="cmstopVideo" width="'+(width || 500)+'" height="'+(height || 430)+'" data-mce-data="'+encodeURI(text)+'" />';
			ed.execCommand('mceInsertContent', false, text);
			tinyMCEPopup.close();
		});
		return false;
	}
}
