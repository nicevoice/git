// JavaScript Document
var EDITOR_OPTIONS = {
	// General options
	mode : "exact",
	theme : "advanced",
	keep_styles:false,
	language : "ch",
	pagebreak_separator : "<!-- my page break -->",
	file_browser_callback : "ajaxfilemanager",
	convert_urls : false,
	convert_fonts_to_spans : false,
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
	// Example content CSS (should be your site CSS)
	content_css : "css/content.css",
	extended_valid_elements : "a[class|name|href|target|title|onclick|rel],script[type|src],iframe[src|style|width|height|scrolling|marginwidth|marginheight|frameborder],img[class|src|border=0|width|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|style],embed[class|allowfullscreen|quality|align|src|type|width|height|flashvars|wmode],center,$elements",
	font_size_style_values : "10px,12px,14px,16px,18px,24px,36px",
	theme_advanced_font_sizes : "10px,12px,14px,16px,18px,24px,36px",
	setup : function(ed){
        ed.onPostProcess.add(function(ed, o) {
            o.content = o.content.replace(/<br><br>/ig,'</p><p>');
        });
        ed.onMouseDown.add(function(ed, evt) {
            document.onmousedown && document.onmousedown();
            ('jQuery' in window) && $(document).trigger('mousedown');
        });
        ed.onClick.add(function(ed, evt) {
            document.onclick && document.onclick();
            ('jQuery' in window) && $(document).trigger('click');
        });
	}
};
function editor(textarea_id, type, options){
	switch(type){
	case 'complete':
		ed_plugins = "safari,pagebreak,style,advimage,advlink,preview,contextmenu,paste,template,inlinepopups,onekeyclear,fullscreen,ct_media,table,ct_sdbc,ct_moreread,ct_tag,ct_taglink,ct_vote,ct_modeswitch,ct_source,ct_save,searchreplace,ct_wdcount,ct_image";
		ed_theme_advanced_buttons1 = "undo,redo,bold,italic,underline,formatselect,fontselect,fontsizeselect,forecolor,link,unlink,justifyleft,justifycenter,justifyright,|,bullist,numlist,|,bold,italic,underline,bullist,numlist,outdent,indent";
		ed_theme_advanced_buttons2 = "tablecontrols,charmap,ctWdcount,ctSdbc,pastetext,pasteword,replace,ctImage,flash,video,ctVote,ctMoreread,ctTag,ctTaglink,onekeyclear,pagebreak,ctSource,fullscreen,ctModeswitch";
		ed_theme_advanced_buttons3 = '';
		break;
	case 'mini':
		ed_plugins = "safari,pagebreak,style,advimage,advlink,preview,contextmenu,paste,template,inlinepopups,onekeyclear,fullscreen,ct_source,ct_save,ct_image";
		ed_theme_advanced_buttons1 = "undo,bold,italic,underline,fontsizeselect,forecolor,|,link,unlink,|,justifyleft,justifycenter,justifyright,|,ctImage,onekeyclear,fullscreen,ctSource";
		ed_theme_advanced_buttons2 = '';
		ed_theme_advanced_buttons3 = '';
		break;
	default:
		ed_plugins = "safari,pagebreak,style,advimage,advlink,insertdatetime,preview,searchreplace,contextmenu,paste,template,inlinepopups,onekeyclear,fullscreen,ct_media,insertimage,ct_sdbc,ct_moreread,ct_tag,ct_vote,ct_modeswitch,ct_save,ct_wdcount,ct_image";
		ed_theme_advanced_buttons1 = "undo,bold,italic,underline,fontsizeselect,forecolor,|,link,unlink,|,justifyleft,justifycenter,justifyright,|,ctImage,video,ctVote,ctMoreread,ctTag,ctTaglink,onekeyclear,pagebreak,fullscreen,ctModeswitch";
		ed_theme_advanced_buttons2 = '';
		ed_theme_advanced_buttons3 = '';
		break;
	}
	options = $.extend({
		elements : textarea_id,
		plugins : ed_plugins,
		theme_advanced_buttons1 : ed_theme_advanced_buttons1,
		theme_advanced_buttons2 : ed_theme_advanced_buttons2,
		theme_advanced_buttons3 : ed_theme_advanced_buttons3
	}, EDITOR_OPTIONS, options||{})
	tinyMCE.init(options);
}
$.fn.editor = function(type, options)
{
    var frm = $(this[0].form);
    var textarea = this;
    var id = this[0].id;
	editorInit();
    editor(id, type, options);
    frm.submit(function(){
        textarea.val(tinyMCE.get(id).getContent());
    });
    return this;
};
function insertEditorText(editor, text) {
	tinyMCE.execInstanceCommand(editor, 'mceInsertContent', false, text);
}

function ajaxfilemanager(field_name, url, type, win) {
	var limit = '';
	switch (type) {
		case "image": limit = 'jpg,jpeg,png,gif'; break;
		case "media": limit = 'rm,rmvb,flv,swf,avi,wmv'; break;
		case "flash": limit = 'swf'; break;
		case "file":  limit = ''; break;
		default: return false;
	}
	tinyMCE.activeEditor.windowManager.open({
		url: "?app=system&controller=attachment&action=index&single=1&tinymce=true&ext_limit="+limit,
		width: 800,
		height: 530,
		inline: "yes",
		close_previous : "no"
	},{
		window : win,
		input : field_name
	});
}

var editorInit = function() {
	var insertScript =function(src) {
		var s = document.createElement('script');
		s.setAttribute('type', 'text/javascript');
		s.setAttribute('src', src);
		document.body.appendChild(s);
	}
	if (!$().imageInput) {
		insertScript('js/cmstop.filemanager.js');
	}
	if (!window.ImageEditor) {
		insertScript('imageEditor/cmstop.imageEditor.js');
	}
}