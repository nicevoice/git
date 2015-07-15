/**
 * $Id: editor_plugin_src.js 201 2007-02-12 15:56:56Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright � 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

(function() {
	tinymce.create('tinymce.plugins.ct_sdbcPlugin', {
		box : [],
		urlbox :[],
		init : function(ed, url) {
			this.editor = ed;
			ed.onNodeChange.add(function(ed, cm, n, co) {
				cm.setDisabled('ctSdbc', co);
			});
		},
		createControl : function(n, cm) {
			var t = this;ed = t.editor;
			if(n == 'ctSdbc')
			{
				var c = cm.createMenuButton('ctSdbc',{
					title:'\u5168/\u534a\u89d2\u5207\u6362'
				});
				c.onRenderMenu.add(function(c,m){
					m.add({title : '\u534a\u89d2\u8f6c\u5168\u89d2', onclick : function() {
						var tx = ed.selection.getContent();
						if(/(<.[^<>]*?>|&[0-9a-z]{2,6};)/.test(tx)) 
						{ 
						  tx = t.output(t.sbc2dbc(t.store(tx)));
						  t.box = [];
						  t.urlbox = [];
						}
						else
						{
						   tx = t.sbc2dbc(tx);
						}
				        ed.execCommand('mceReplaceContent',false,tx);
					}});
					m.add({title : '\u5168\u89d2\u8f6c\u534a\u89d2', onclick : function() {
					    var tx = ed.selection.getContent();
				        if(/(<.[^<>]*?>|&[0-9a-z]{2,6};)/.test(tx)) 
						{ 
						  tx = t.output(t.dbc2sbc(t.store(tx)));
						  t.box = [];
						  t.urlbox = [];
						}
						else
						{
						   tx = t.dbc2sbc(tx);
						}
				        ed.execCommand('mceReplaceContent',false,tx);
					}});
				})
				return c;
			}
			return null;
		},		

		sbc2dbc: function(str) {
					var ret = [], i = 0, len = str.length, code, chr,hash = {'32' : '\u3000'};
					for (; i < len; ++i) {
						code = str.charCodeAt(i);
						chr = hash[code];
						if (!chr && code > 31 && code < 127) {
							chr = hash[code] = String.fromCharCode(code + 65248);
						}
						ret[i] = chr ? chr : str.charAt(i);
					}
					return ret.join('');
				},

	   dbc2sbc:function(str) {
					var ret = [], i = 0, len = str.length, code, chr,hash = {'12288' : ' '};
					for (; i < len; ++i) {
						code = str.charCodeAt(i);
						chr = hash[code];
						if (!chr && code > 65280 && code < 65375) {
							chr = hash[code] = String.fromCharCode(code - 65248);
						}
						ret[i] = chr ? chr : str.charAt(i);
					}
					return ret.join('');
				},
	    store: function(content)
		{
            var t = this;
		    content =content.replace(/(<a\s+[^<>]*>)([^<>]+)<\/a>?/gi,function(res,a,b){
            	b=b.replace(/http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/gi,function(ht)
            	{
            		t.urlbox[t.urlbox.length] = ht; 
            		return '\uFFFF\uFFFF\uFFFF';
            	});
            	return a+b+'</a>';
		    }).replace(/(<.[^<>]*?>|&[0-9a-z]{2,7};)/gi,function(tar,f)
		    	{
				 t.box[t.box.length] = tar;
				 return '\uFFFE\uFFFE\uFFFE';
				}
			);
			return content;
			
		},
		output: function(content)
		{
			var t = this;
			var i = 0;
			content = content.replace(/\uFFFF\uFFFF\uFFFF/g,function(){
				return t.urlbox[i++];
			});
			i = 0;
		    content = content.replace(/\uFFFE\uFFFE\uFFFE/g,function (tar){
				return t.box[i++];
			});
			return content;
		},
		getInfo : function() {
			return {
				author : 'shanhuhai'
			};
		}
	});

	tinymce.PluginManager.add('ct_sdbc', tinymce.plugins.ct_sdbcPlugin);
})();