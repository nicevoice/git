var CmsTopImageDialog = {
    _imageList: undefined,

	init : function() {
		var self = this;

        tinyMCEPopup.resizeToInnerSize();

        // init tab
        var hash = window.location.hash;
        if (hash && hash.indexOf('tab=remote_tab') > -1) {
            $('#remote_tab').mousedown();
        }

        $('a,li').attr('hideFocus', true).css('outline', 'none');

        this._imageList = ct.imageList({
            uploader: $('#local_upload'),
            uploaderParams: {
                image_width: EDITOR_IMAGE_SETTINGS.image_width,
                image_height: EDITOR_IMAGE_SETTINGS.image_height
            },
            remoteButton: $('#remote_panel').find('[data-role=add]'),
            multiple: true,
            localImageList: $('#local_image_list'),
            remoteImageList: $('#remote_image_list')
        });
	},

    confirm: function(current, callback) {
        return this._imageList.confirm(current, callback);
    },

    localImageInsert: function(form) {
        var self = this,
            callback = function(items) {
                if (items) {
                    var html = '';
                    $.each(items, function(index, item) {
                        html += self._imageList.renderToEditor(item.src, item.desc);
                    });
                    tinyMCEPopup.execCommand('mceInsertContent', false, html);
                }
                tinyMCEPopup.close();
            };
        this._imageList.getLocalImageItems(form, callback, function() {
            ct.error('插入失败');
        });
        return false;
    },

    remoteImageInsert: function() {
        var self = this,
            items = this._imageList.getRemoteImageItems();
        if (items) {
            var html = '';
            $.each(items, function(index, item) {
                html += self._imageList.renderToEditor(item.src, item.desc);
            });
            tinyMCEPopup.execCommand('mceInsertContent', false, html);
        }
        tinyMCEPopup.close();
    }
};

tinyMCEPopup.onInit.add(CmsTopImageDialog.init, CmsTopImageDialog);
