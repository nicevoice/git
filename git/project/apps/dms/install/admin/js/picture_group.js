var picture_group = {
	view : function(groupid) {
		$('#dms_pic_group_list').hide();
		$('#dms_picture_list').empty().show();
		$p = new picture_list('div#dms_picture_list', {
			'baseUrl'	: '?app=dms&controller=picture_group&action=ls&groupid=' + groupid,
			'template'	: templateP,
			'nav'		: false,
			'statusBar'	: '#dms_statusbar'
		});
		$('#dms_statusbar').hide().prev().hide().parent().append('<input type="button" value="返 回" class="button_style_1 pic-selector-home" onclick="$(\'#dms_picture_list\').hide();$(\'#dms_pic_group_list\').show();$(this).prev().show().prev().show();$(this).remove();"/>')
	},

	add : function() {
		ct.assoc.open('?app=dms&controller=picture_group&action=add', 'newtab');
	},

	edit : function() {
		//
	},

	delete : function(groupid) {
		ct.confirm('确定要删除么', function() {
			$.getJSON('?app=dms&controller=picture_group&action=del', {'id':groupid}, function(json) {
				if (json.state) {
					ct.ok('删除成功');
					$pg.reload();
				} else {
					ct.error(json.error || '删除失败');
				}
			});
		});
	},

	search : function() {
		$pg.search($('.dms_search :input').serialize());
	},

	picZoom : function(url, obj) {
		if (url == '') {
			return;
		}
		var img	= $('<img src="' + url + '" alt="" onerror="this.click();" />');
		var maxWidth	= obj.width() - 40;
		img.css('max-width', maxWidth);
		var data	= obj.html();
		obj.empty().append(img);
		img.one('click', function() {
			obj.empty().html(data);
		})
	}
}