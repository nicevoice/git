// JavaScript Document
var EDITOR_OPTIONS = {
	// General options
	mode : "exact",
	theme : "advanced",
	keep_styles:false,
	language : "ch",
	pagebreak_separator : "<!-- my page break -->",
	convert_urls : false,
	convert_fonts_to_spans : false,
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : false,
	// Example content CSS (should be your site CSS)
	content_css : "css/content.css",
	extended_valid_elements : "a[class|name|href|target|title|onclick|rel],script[type|src],iframe[src|style|width|height|scrolling|marginwidth|marginheight|frameborder],img[class|src|border=0|width|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],center,$elements",
	font_size_style_values : "10px,12px,14px,16px,18px,24px,36px",
	theme_advanced_font_sizes : "10px,12px,14px,16px,18px,24px,36px",
	setup : function(ed){
			 ed.onPostProcess.add(function(ed, o) {
			 	o.content = o.content.replace(/<br><br>/ig,'</p><p>');
			 });
	}
};
function editor(textarea_id, options){
	ed_plugins = "safari,style,advimage,advlink,preview,searchreplace,contextmenu,paste,template,inlinepopups,onekeyclear,fullscreen";
	ed_theme_advanced_buttons1 = "undo,bold,italic,underline,fontsizeselect,forecolor,|,link,unlink,|,justifyleft,justifycenter,justifyright,|,image,onekeyclear,fullscreen,ctModeswitch";
	ed_theme_advanced_buttons2 = "";
	ed_theme_advanced_buttons3 = '';
	options = $.extend({
		elements : textarea_id,
		plugins : ed_plugins,
		theme_advanced_buttons1 : ed_theme_advanced_buttons1,
		theme_advanced_buttons2 : ed_theme_advanced_buttons2,
		theme_advanced_buttons3 : ed_theme_advanced_buttons3
	}, EDITOR_OPTIONS, options||{});
	tinymce.EditorManager.init(options);
}
$.fn.editor = function(options)
{
    var frm = $(this[0].form);
    var textarea = this;
    var id = this[0].id;
    editor(id, options);
  	frm.submit(function(){
        textarea.val(tinyMCE.get(id).getContent());
    });
    return this;
};
function insertEditorText(editor, text) {
	tinyMCE.execInstanceCommand(editor, 'mceInsertContent', false, text);
}
