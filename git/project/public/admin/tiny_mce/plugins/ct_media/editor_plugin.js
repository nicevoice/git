(function() {
	var each = tinymce.each;

	tinymce.create('tinymce.plugins.ctMediaPlugin', {
		init : function(ed) {
			var t = this;

			cls = 'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000';
			codebase = 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0';
			type = 'application/x-shockwave-flash';

			function replaceObject2Img(content) {
                var html = content.replace(/[\r\n]/g,'').replace(/(?:<p[^>]*>(?:\s|&nbsp;)*<\/p>)?(<(embed|object|script)(?:[^>]*?(?:class|data-mce-role)\s*=\s*['"]?(?:.*\s)?cmstop(Flash|Video)(?:.*?)?['"].*?)<?\/(?:embed|object|script)?>)(?:<p[^>]*>(?:\s|&nbsp;)*<\/p>)?/img, function(context, object, elm, type) {
					if (elm.toLowerCase() == 'object') {
						object += '</object>';
					}
					var width = /width\s*=\s*['"]?(\w*)['"]?/.test(context) ? /width\s*=\s*['"]?(\w*)['"]?/.exec(context)[1] : 450;
					var height = /height\s*=\s*['"]?(\w*)['"]?/.test(context) ? /height\s*=\s*['"]?(\w*)['"]?/.exec(context)[1] : 400;
					return '<img src="/tiny_mce/plugins/ct_media/img/trans.gif" data-mce-src="/tiny_mce/plugins/ct_media/img/trans.gif" class="cmstop'+type+'" width="'+width+'" height="'+height+'" data-mce-data="'+encodeURI(object)+'" />';
				});
				return html;
            }

			function replaceImg2Object(content) {
				var content = content.replace(/(?:<p[^>]*>)?<img[^>]*?class\s*=\s*['"]?cmstop(?:Flash|Video)['"].*?>(?:<\/p>)?/img, function(context) {
					return decodeURI(/data-mce-data="(.*?)"\s/.exec(context)[1]);
				});
				return content;
			}

			// Register commands
			ed.addCommand('ctFlash', function() {
				var params = null,catobj = $('#catid');
				params =catobj[0].tagName == 'INPUT'?[catobj.val(),catobj.next().html()]:catobj.val();
				ed.windowManager.open({
					file : '?app=editor&controller=video&action=flash',
					width : 450,
					height : 355,
					inline : 1
				});
			});

			ed.addCommand('ctVideo', function() {
				var params = null,catobj = $('#catid');
				params =catobj[0].tagName == 'INPUT'?[catobj.val(),catobj.next().html()]:catobj.val();
				ed.windowManager.open({
					file : '?app=editor&controller=video&action=video',
					width : 450 ,
					height : 420,
					inline : 1
				}, {
					plugin_url : url,
					catid : params
				});
			});
			ed.addButton('flash', {title : '插入/编辑flash', cmd : 'ctFlash'});
			ed.addButton('video', {title : '插入/编辑视频', cmd : 'ctVideo'});

			ed.onInit.add(function() {
				ed.setContent(replaceObject2Img(ed.getContent()),{format: 'raw'});
				if (ed.settings.content_css !== false) {
					ed.dom.loadCSS("tiny_mce/plugins/ct_media/css/content.css");
				}
				ed.onGetContent.add(function(ed, o) {
					var context = o.content;
					o.content = replaceImg2Object(context);
					ed.setContent(context,{format: 'raw'});
				});
				ed.onBeforeExecCommand.add(function(ed, cmd, ui, val) {
					if (cmd == 'mceFullScreen') {
						try {
							tinymce.get(ed.getParam('fullscreen_editor_id')).setContent(replaceObject2Img(ed.getContent()));
							tinymce.mediaFlag = 1;
						} catch (e) {}
					}
					if (cmd == 'mceSource') {
						try {
							if (tinymce.activeEditor.plugins.ct_source.isSource) {
		                        ed.setContent(replaceObject2Img($(tinymce.activeEditor.plugins.ct_source.ta).val()), {format: 'raw'});
								tinymce.mediaFlag = 1;
							}
						} catch (e) {}
					}
				});
			});
		}
	});

	// Register plugin
	tinymce.PluginManager.add('ct_media', tinymce.plugins.ctMediaPlugin);
})();