
var share = {
	title : document.title,
	url : document.location.href,
	copyToClipboard : function(txt) {
		if(window.clipboardData) {
			window.clipboardData.clearData();
			window.clipboardData.setData("Text", txt);
			alert("复制链接成功！");
		} else if(navigator.userAgent.indexOf("Opera") != -1) {
			window.location = txt;
		} else if (window.netscape) {
			try {
				netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
			} catch (e) {
				alert(" 被浏览器拒绝！\n请在浏览器地址栏输入'about:config'并回车\n然后将 'signed.applets.codebase_principal_support'设置为'true'");
			}
			var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);
			if (!clip)
			return;
			var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);
			if (!trans)
			return;
			trans.addDataFlavor('text/unicode');
			var str = new Object();
			var len = new Object();
			var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);
			var copytext = txt;
			str.data = copytext;
			trans.setTransferData("text/unicode",str,copytext.length*2);
			var clipid = Components.interfaces.nsIClipboard;
			if (!clip)
			return false;
			clip.setData(trans,null,clipid.kGlobalClipboard);
			alert("复制链接成功！");
		}
	},
	website : {
		tsina : 'http://service.t.sina.com.cn/share/share.php?url={url}&title={title}',
		qq : 'http://v.t.qq.com/share/share.php?url={url}&title={title}',
		baidu : 'http://cang.baidu.com/do/add?it={title}&iu={url}&dc=&fr=ien',
		qzone : 'http://shuqian.qq.com/post?from=3&title={title}&uri={url}&jumpback=2&noui=1',
		kaixin001 : 'http://www.kaixin001.com/~repaste/repaste.php?&rurl={url}&rtitle={title}&rcontent=',
		douban : 'http://www.douban.com/recommend/?url={url}&title={title}',
		msn : '',
		renren : 'http://share.renren.com/share/buttonshare.do?link={url}&title={title}',
		buzz : 'http://www.google.com/buzz/post?url={url}',
		sohu : 'http://bai.sohu.com/share/blank/add.do?link={url}',
		m139 : 'http://auth.shequ.10086.cn/login/index.php?tourl={url}',
		taobao : '',
		tsohu : 'http://t.sohu.com/third/post.jsp?url={url}&title={title}&content='
	},
	styles : {
		
	},
	open : function(select,width){
		$(select).stop().animate({right: '0'}, "slow");
	},
	sendto : function(site) {
		if(site == 'copy') {
			this.copyToClipboard(this.url);
			return true;
		} else if (site == 'fav') {
			window.external.AddFavorite(this.url, this.title);
		} else {
			var href = this.website[site];
			if(!href) {
				alert('未定义的分享规则');
				return false;
			} else {
				var item = {
					url : encodeURIComponent(this.url),
					title : encodeURIComponent(this.title)
				};
				for (var key in item) {
					href = href.replace(new RegExp('{'+key+'}',"gm"), item[key]);
				}
				window.open(href);
			}
		}
	}
}