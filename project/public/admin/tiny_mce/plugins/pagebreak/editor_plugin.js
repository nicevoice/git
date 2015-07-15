/**
 * $Id: editor_plugin_src.js 201 2007-02-12 15:56:56Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright � 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

(function() {
	tinymce.create('tinymce.plugins.PageBreakPlugin', {
		init : function(ed, url) {
			//var pb = '<img src="' + url + '/img/trans.gif" class="mcePageBreak mceItemNoResize" />', cls = 'mcePageBreak', sep = ed.getParam('pagebreak_separator', '<!-- pagebreak -->'), pbRE;
            var pb = '<p class="mcePageBreak"></p>', 
				cls = 'mcePageBreak', 
				sep = ed.getParam('pagebreak_separator', '<p class="pagebreak"></p>'), 
				pbRE, 
				preTitle = '#';
			pbRE = new RegExp(sep.replace(/[\?\.\*\[\]\(\)\{\}\+\^\$\:]/g, function(a) {return '\\' + a;}), 'g');

            function focusP(p) {
                p.focus();
                p.innerHTML = '&nbsp;';
                ed.selection.select(p.firstChild);
                p.innerHTML = '';
            }

			// Register commands
			ed.addCommand('mcePageBreak', function() {
                var isIE = /MSIE\s/.test(navigator.userAgent), blankLine = '<p>&nbsp;</p>';
				ed.execCommand('mceInsertContent', 0, pb);
                isIE && ed.execCommand('mceInsertContent', 0, blankLine);
				
				if(ed.dom.select('p.' + cls).length == 1){
					var html_content = ed.getContent();
					html_content += '<p><br /></p>';
					html_content = pb + (isIE ? blankLine : '') + html_content;
					ed.setContent(html_content);
                    focusP(ed.dom.select('p.'+cls)[0]);
				} else {
                    var allP = ed.dom.select('p.'+cls), lastP = allP[allP.length - 1];
                    focusP(lastP);
                }
			});

			// Register buttons
			ed.addButton('pagebreak', {title : 'pagebreak.desc', cmd : cls});

			ed.onInit.add(function() {
				if (ed.settings.content_css !== false)
					ed.dom.loadCSS(url + "/css/content.css");

				if (ed.theme.onResolveName) {
					ed.theme.onResolveName.add(function(th, o) {
						if (o.node.nodeName == 'IMG' && ed.dom.hasClass(o.node, cls))
							o.name = 'pagebreak';
					});
				}
			});

			ed.onClick.add(function(ed, e) {
				e = e.target;

				if (e.nodeName === 'IMG' && ed.dom.hasClass(e, cls))
					ed.selection.select(e);
			});

			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('pagebreak', n.nodeName === 'IMG' && ed.dom.hasClass(n, cls));
			});

			ed.onBeforeSetContent.add(function(ed, o) {
				o.content = o.content.replace(pbRE, pb);
			});

			ed.onPostProcess.add(function(ed, o) {
				if (o.get)
					o.content = o.content.replace(/<img[^>]+>/g, function(im) {
						if (im.indexOf('class="mcePageBreak') !== -1)
							im = sep;

						return im;
					});
			});
		},

		getInfo : function() {
			return {
				longname : 'PageBreak',
				author : 'Moxiecode Systems AB',
				authorurl : 'http://tinymce.moxiecode.com',
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/pagebreak',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('pagebreak', tinymce.plugins.PageBreakPlugin);
})();