(function(){
var form;
var testRule = function(data){
	$.post('?app=spider&controller=manager&action=testRule',data,
	function(json){
		if (json.state) {
			ct.ok(json.info);
		} else {
			ct.error(json.error);
		}
	},'json');
};
var App = {
	init:function(){
		form = document.getElementById('ruleForm');
		$(form.listType).click(function(){
			var m = this.value == 1 ? 'hide' : 'show';
			$(this).parents('tbody:first').next('tbody')[m]();
			$(form.contentUrl).next('button')[m]();
		});
		var $replacement = $('#replacement');
		$('#replaceAdder').click(function(){
			var _tr = $replacement.find('>tr:last');
			var tr = _tr.clone();
			tr.find('th').html('&nbsp;');
			tr.find('textarea').val('');
			var del = $('<a href="javascript:;">删除</a>').click(function(){
				if (_tr == tr) _tr = tr.prev();
				tr.remove();
			});
			tr.find('td:last').html(del);
			_tr.after(tr);
			_tr = tr;
		});
		$(form).ajaxForm(function(json){
			if (json.state) {
				ct.confirm(json.info+'，继续添加规则？',null,function(){
					ct.assoc.close();
				});
			} else {
				ct.error(json.error);
			}
		});
	},
	addSite:function(){
		ct.form('添加网站','?app=spider&controller=manager&action=addSite',
		280,100,
		function(json){
			if (json.state)
			{
				var item = new Option(json.data.name, json.data.siteid);
				form.siteid.options.add(item);
				item.selected = true;
				return true;
			}
		});
	},
	testEnterRule:function(){
		var data = [
			'url='+encodeURIComponent(form.listUrl.value),
			'pattern='+encodeURIComponent(form.enterPattern.value)
		].join('&');
		testRule(data);
	},
	testListRule:function(){
		var data = [
			'url='+encodeURIComponent(form.contentUrl.value),
			'pattern='+encodeURIComponent(form.urlPattern.value)
		].join('&');
		testRule(data);
	},
	testGetList:function(){
		var data = [
			'charset='+$(form.charset).filter(':checked').val(),
			'url='+encodeURIComponent(form.listUrl.value),
			'listType='+$(form.listType).filter(':checked').val(),
			'listStart='+encodeURIComponent(form.listStart.value),
			'listEnd='+encodeURIComponent(form.listEnd.value),
			'urlPattern='+encodeURIComponent(form.urlPattern.value),
			'listLimitLength='+encodeURIComponent(form.listLimitLength.value),
			'listNextPage='+encodeURIComponent(form.listNextPage.value)
		].join('&');
		$.post('?app=spider&controller=manager&action=testGetList',data,
		function(html){
			var d = $('<div></div>').html(html||'').dialog({
				autoOpen: true,
				bgiframe: true,
				modal : false,
				height:500,
				width:500,
				title : '获取列表测试',
				close: function(){
					d.dialog('destroy').remove();
				}
			});
		});
	},
	testGetDetail:function(){
		var data = [
			'charset='+$(form.charset).filter(':checked').val(),
			'url='+encodeURIComponent(form.contentUrl.value),
			'rangeStart='+encodeURIComponent(form.rangeStart.value),
			'rangeEnd='+encodeURIComponent(form.rangeEnd.value),
			'titleStart='+encodeURIComponent(form.titleStart.value),
			'titleEnd='+encodeURIComponent(form.titleEnd.value),
			'contentStart='+encodeURIComponent(form.contentStart.value),
			'contentEnd='+encodeURIComponent(form.contentEnd.value),
			'nextPage='+encodeURIComponent(form.nextPage.value),
			'authorStart='+encodeURIComponent(form.authorStart.value),
			'authorEnd='+encodeURIComponent(form.authorEnd.value),
			'sourceStart='+encodeURIComponent(form.sourceStart.value),
			'sourceEnd='+encodeURIComponent(form.sourceEnd.value),
			'pubdateStart='+encodeURIComponent(form.pubdateStart.value),
			'pubdateEnd='+encodeURIComponent(form.pubdateEnd.value),
			'allowTags='+encodeURIComponent(form.allowTags.value),
			'saveRemoteImg='+form.saveRemoteImg.checked
		];
		var s = form['replacement[source][]'],
			t = form['replacement[target][]'];
		if (s.nodeType) {
			data.push('replacement[source][]='+encodeURIComponent(s.value));
			data.push('replacement[target][]='+encodeURIComponent(t.value));
		} else {
			for (var i=0,l=s.length;i<l;i++) {
				data.push('replacement[source][]='+encodeURIComponent(s[i].value));
				data.push('replacement[target][]='+encodeURIComponent(t[i].value));
			}
		}
		$.post('?app=spider&controller=manager&action=testGetDetail',data.join('&'),
		function(html){
			var d = $('<div></div>').html(html||'').dialog({
				autoOpen: true,
				bgiframe: true,
				modal : false,
				title : '获取内容测试',
				height:500,
				width:500,
				close: function(){
					d.dialog('destroy').remove();
				}
			});
		});
	}
};
window.App = App;
})();