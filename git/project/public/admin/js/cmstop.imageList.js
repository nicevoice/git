/**
 * CmsTop 图片选择和上传组件
 *
 * 注意，目前该文件并未彻底抽象，仅适用于组图和编辑器场合，在其他场合使用暂未验证
 */
(function($, ct) {
var OPTIONS = {
    uploader: undefined,
    uploaderParams: {
        thumb_width: 213,
        thumb_height: 160
    },
    multiple: false,
    dragsort: false,
    deleteLocalImage: true,
    updateLocalImageDesc: true,
    localImageList: undefined,
    remoteImageList: false,
    remoteButton: undefined
};
var imageList = function(option) {
    this.options = $.extend({}, OPTIONS, option);
};
imageList.prototype = {
    dragsortInited: false,
    editLock: [],

	init: function() {
		var self = this,
            o = this.options;

        if (o.uploader) {
            o.uploader.uploader({
                script       : '?app=system&controller=image&action=upload',
                fileDataName : 'ctimg',
                fileDesc	 : '图像',
                fileExt		 : '*.jpg;*.jpeg;*.gif;*.png;*.bmp;',
                multi        : o.multiple ? 1 : 0,
                params       : o.uploaderParams,
                jsonType     : 1,
                complete     : function(response, data) {
                    var aid, thumb, file, desc;
                    if (Object.prototype.toString.call(response) == '[object Object]') {
                        aid = response.aid;
                        thumb = response.thumb;
                        file = response.file;
                        desc = data.file.name.indexOf('.') > -1 ? data.file.name.substr(0, data.file.name.lastIndexOf('.')) : data.file.name;
                    } else {
                        aid = 0;
                        thumb = file = response;
                        desc = '';
                    }
                    self.addLocalImage({
                        aid: aid,
                        thumb: thumb,
                        image: file,
                        desc: desc
                    });
                }
            });
        }

        if (o.localImageList !== false) {
            this.localImageList = o.localImageList;
            this.initLocalImagePanel();
        }

        if (o.remoteImageList !== false) {
            this.remoteImageList = o.remoteImageList;
            this.initRemoteImagePanel();
        }

        return this;
	},

    htmlspecialchars: (function() {
        var maps = {
            "'": '&#39;',
            '"': '&quot;',
            '<': '&lt;',
            '>': '&gt;'
        };
        return function(html, decode) {
            if (! html) return html;
            for (var key in maps) {
                if (maps.hasOwnProperty(key)) {
                    html = html.replace(new RegExp((decode ? maps[key] : key), 'gim'), (decode ? key : maps[key]));
                }
            }
            return html;
        }
    })(),

    confirm: function(current, callback) {
        var self = this;
        callback = ct.func(callback) || function() {};
        if (! current && this.itemChanged) {
            ct.confirm('继续操作将丢失现有的修改，确定要继续吗？', function() {
                self.itemChanged = false;
                callback();
            }, function() {

            });
        } else {
            callback();
        }
    },

    imageUrl: function() {
        var re_pre_upurl = new RegExp('^'+UPLOAD_URL);
        return function(f, abs) {
            return abs ? (re_pre_upurl.test(f) ? f : (UPLOAD_URL+f)) : f.replace(re_pre_upurl, '');
        };
    }(),

    scrollHeight: function(elem) {
        return elem.scrollHeight
            ? elem.scrollHeight
            : Math.max(elem.offsetHeight, elem.clientHeight);
    },

    getThumb: function(image, callback) {
        if (! image) return;
        var options = {
            width: 213,
            height: 160,
            abs: 1,
            file: image
        };
        $.getJSON('?app=system&controller=image&action=thumb', options, function(json) {
            if (json && json.state) {
                (ct.func(callback) || function() {})(json.thumb);
            } else {
                ct.error('获取缩略图失败');
            }
        });
    },

    loadImage: function(url, callback) {
        $('<img />').load(function() {
            callback(url);
            $(this).remove();
        }).attr('src', url);
    },

    updateDesc: function(aid, desc, callback) {
        if (! aid || ! desc) {
            ct.error('请输入描述信息');
            return false;
        }
        var options = {
            aid: aid,
            newname: this.htmlspecialchars(desc)
        };
        this.itemChanged = true;
        $.post('?app=system&controller=attachment&action=rename_file', options, function(json) {
            if (json && json.state) {
                (ct.func(callback) || function() {})();
            } else {
                ct.error(json && json.error || '编辑失败');
            }
        }, 'json');
    },

    // 本地图片

    initLocalImagePanel: function() {
        this.detectIfEmtpy();

        if (this.options.dragsort) {
            this.localImageList.addClass('image-list-sortable');
        }
    },

    addLocalImage: function(data, moreToAdd) {
        if (! this.localImageList || ! this.localImageList.length || ! data || ! data.image) return;

        if (this.localImageList.find('li.image-thumb-item').filter(function(index) {
            return $(this).attr('aid') == data.aid;
        }).length) {
            ct.error('图片 ' + (data.desc && this.htmlspecialchars(data.desc) || data.aid) + ' 已添加过');
            return false;
        }

        var o = this.options,
            self = this,
            item = this.renderLocalImage(data),
            actionArea = item.find('.image-thumb-item-action').hide(),
            editButton = actionArea.find('.image-thumb-item-action-edit'),
            deleteButton = actionArea.find('.image-thumb-item-action-delete'),
            descArea = item.find('.image-thumb-item-desc'),
            messageArea = item.find('.image-thumb-item-message');

        ! o.multiple && this.clearLocalImages();

        if (data.replace && data.replace.jquery) {
            data.replace.replaceWith(item);
        } else {
            item.appendTo(this.localImageList.find('> ul'));
        }

        // 隐藏与显示
        item.hover(function() {
            actionArea.show();
        }, function() {
            if (! self.editLock || ! self.editLock.length || self.editLock[0].get(0) != item.get(0)) {
                actionArea.hide();
            }
        });

        // 修改
        editButton.click(function(e) {
            e.stopPropagation();

            var win = window,
                imageField = item.find('[data-role=image]'),
                src = imageField.val(),
                afterEdit = function(json) {
                    imageField.val(self.imageUrl(json.file, 1));
                    self.getThumb(json.file, function(thumb) {
                        thumb = self.imageUrl(thumb, true);
                        item.find('[data-role=thumb]').val(thumb);
                        self.updateThumbShow(item.find('[data-role=thumb-show]'), thumb);
                    });
                };

            if ($(win).width() < 750 || $(win).height() < 500) {
                if (win.parent) win = window.parent;
            }

            if (! win['ImageEditor']) {
                ct.warn('<h3><strong>加载图片编辑器时遇到问题</strong></h3>如果当前页面的大小低于图片编辑器所需大小，<br />建议您按下 F11 键进入全屏模式后重新尝试。<br />', 'center', 1000);
                return false;
            }

            if (win['cmstop'] && win.cmstop.editImage) {
                win.cmstop.editImage(src, afterEdit);
            } else {
                var inst = win['ImageEditor'].open(src);
                inst.bind("saved", afterEdit);
                return inst;
            }
        });

        // 删除
        deleteButton.click(function(e) {
            var callback = function() {
                item.remove();
                self.detectIfEmtpy();
                self.sortLocalImageItems();
            };
            e.stopPropagation();
            ct.confirm('确定要删除图片吗？', function() {
                var aid = item.find('[data-role=aid]').val();
                if (o.deleteLocalImage && aid) {
                    self.deleteLocalImage(aid, callback);
                } else {
                    callback();
                }
            });
        });

        // 编辑描述
        descArea.bind('click', function(e) {
            e.stopPropagation();
            if (self.editLock.length) {
                if (self.editLock[0].get(0) == item.get(0)) return;
                self.saveEditDesc();
            }
            self.editLock.unshift(item);
            var descField = item.find('[data-role=desc]'),
                desc = descField.val();
            descArea.empty().append([
                '<textarea rows="2">', self.htmlspecialchars(desc), '</textarea>'
            ].join(''));
            descArea.attr('data-title', descArea.attr('title')).removeAttr('title');

            var textarea = descArea.find('textarea'),
                cancelUpdate = function() {
                    var callback = function() {
                        descArea.removeClass('image-thumb-item-desc-edit').addClass('image-thumb-item-desc');
                        descArea.attr('title', descArea.attr('data-title'));
                        descArea.removeAttr('data-title');
                        descArea.empty();
                        descArea.html(self.renderLocalImageDesc(desc));
                        actionArea && actionArea.hide();
                        messageArea.text('').hide();
                        var index = -1;
                        $.each(self.editLock, function(i, lock) {
                            if (lock[0] == item[0]) {
                                index = i;
                                return true;
                            }
                        });
                        self.editLock[index] && self.editLock.splice(index, 1);
                    };
                    return function(confirm) {
                        if (confirm && textarea.val() != desc) {
                            ct.confirm('图片信息已修改，是否需要保存？', function() {
                                item.trigger('saveEditDesc');
                            }, function() {
                                callback();
                            });
                        } else {
                            callback();
                        }
                    };
                }();
            messageArea.text('编辑中').show();
            descArea.removeClass('image-thumb-item-desc').addClass('image-thumb-item-desc-edit');
            textarea.get(0).select();
            item.bind('saveEditDesc', function(e) {
                e.stopPropagation();
                if (textarea.val() == desc) {
                    cancelUpdate();
                    return;
                }
                desc = self.htmlspecialchars(textarea.val());
                var aid = item.find('[data-role=aid]').val();
                if (o.updateLocalImageDesc && aid) {
                    self.updateDesc(aid, desc, function() {
                        cancelUpdate();
                        descField.val(desc);
                    });
                } else {
                    cancelUpdate();
                    descField.val(desc);
                }
            });
            item.bind('cancelEditDesc', function(e) {
                e.stopPropagation();
                cancelUpdate();
            });
            $(document).bind('click.editDesc', function(e) {
                if (e.target != textarea.get(0) && $(e.target).parents().index(item.get(0)) == -1) {
                    item.trigger('saveEditDesc');
                }
            });
        });

        this.itemChanged = true;
        // 当还有更多条目添加时，暂时不初始化排序、拖拽
        if (moreToAdd) return;
        this.detectIfEmtpy();;
        this.sortLocalImageItems();
        this.localImageList.parent().scrollTop(this.scrollHeight(this.localImageList.get(0)));
        if (o.dragsort) {
            if (this.dragsortInited) {
                this.localImageList.sortable('refresh');
            } else {
                this.localImageList.sortable({
                    'handle': o['handle'],
                    'items': '.image-thumb-item',
                    'helper': 'clone',
                    'placeholder': 'image-thumb-item-helper',
                    'opacity': 0.6,
                    create: function() {
                        self.dragsortInited = true;
                    },
                    start: function(ev, ui) {
                        self.localImageList.find('.image-thumb-item-helper').css('height', ui.item.height());
                    },
                    stop: function(ev, ui) {
                        self.sortLocalImageItems();
                    }
                });
            }
        }
    },

    renderLocalImage: function(data) {
        var self = this;
        data.aid = data.aid || 0;
        var item = $([
            '<li class="image-thumb-item" aid="', data.aid, '">',
                '<input type="hidden" data-role="aid" name="post[', data.aid, '][aid]" value="', data.aid, '" />',
                '<input type="hidden" data-role="thumb" name="post[', data.aid, '][thumb]" value="', this.imageUrl(data.thumb, true) || '', '" />',
                '<input type="hidden" data-role="image" name="post[', data.aid, '][url]" value="', data.image, '" />',
                '<input type="hidden" data-role="desc" name="post[', data.aid, '][title]" value="', this.htmlspecialchars(data.desc || ''), '" />',
                '<input type="hidden" data-role="sort" name="post[', data.aid, '][sort]" value="', data.sort || '', '" />',
                '<a data-role="thumb-show" class="image-thumb-item-a" href="javascript:;"></a>',
                '<span class="image-thumb-item-action">',
                    '<span class="image-thumb-item-message"></span>',
                    '<a class="image-thumb-item-action-edit" href="javascript:;" title="编辑"><img src="images/edit.gif" alt="" /></a>',
                    '<a class="image-thumb-item-action-delete" href="javascript:;" title="删除"><img src="images/del.gif" alt="" /></a>',
                '</span>',
                '<span class="image-thumb-item-desc" title="点击以编辑">',
                    '<textarea rows="2">', this.htmlspecialchars(data.desc || ''), '</textarea>',
                '</span>',
            '</li>'
        ].join(''));
        if (! data.thumb) {
            this.getThumb(data.image, function(thumb) {
                thumb = self.imageUrl(thumb, true);
                item.find('[data-role=thumb]').val(thumb);
                self.updateThumbShow(item.find('[data-role=thumb-show]'), thumb);
            });
        } else {
            this.updateThumbShow(item.find('[data-role=thumb-show]'), data.thumb);
        }
        return item;
    },

    updateThumbShow: function(item, thumb) {
        this.loadImage(thumb + '?' + Math.random(), function(url) {
            item.css('background-image', 'url(' + url + ')');
        });
    },

    renderLocalImageDesc: function(desc) {
        return [
            '<textarea rows="2">', this.htmlspecialchars(desc) || '', '</textarea>'
        ].join('');
    },

    deleteLocalImage: function(aid, callback) {
        var self = this;
        callback = ct.func(callback) || function() {};
        if (! aid) {
            callback();
            return;
        }
        this.itemChanged = true;
        $.post('?app=system&controller=attachment&action=delete_file', {aids:aid}, function(json) {
            if (json && ! json.error) {
                ct.ok('删除成功');
                callback(json);
            } else {
                ct.ok('删除失败 ' + (json.error || ''));
            }
        }, 'json');
    },

    getLocalImageItemsLength: function() {
        return this.localImageList.find('li.image-thumb-item').length;
    },

    detectIfEmtpy: function() {
        var empty = this.localImageList.find('li.image-thumb-empty');
        if (! this.getLocalImageItemsLength()) {
            empty.show();
        } else {
            empty.hide();
        }
    },

    saveEditDesc: function() {
        if (this.editLock && this.editLock.length) {
            $.each(this.editLock, function() {
                $(this).trigger('saveEditDesc');
            });
            this.editLock = [];
        }
    },

    sortLocalImageItems: function() {
        this.localImageList.find('li.image-thumb-item').each(function(index, li) {
            $(this).find('[data-role=sort-label]').text('#' + (index + 1));
            $(this).find('[data-role=sort]').val(index + 1);
        });
    },

    getLocalImageItems: function(form, callback, error) {
        var self = this;
        this.saveEditDesc();
        $(form).ajaxSubmit({
            success: function(json) {
                if (! json || ! json.state) {
                    ct.error(json.error || '获取图片失败');
                    return false;
                }
                var items = [];
                self.localImageList.find('li.image-thumb-item').each(function(index, li) {
                    var row = $(li),
                        aid = row.find('[data-role=aid]').val(),
                        thumb = row.find('[data-role=thumb]').val(),
                        src = row.find('[data-role=image]').val(),
                        desc = row.find('[data-role=desc]').val(),
                        sort = row.find('[data-role=sort]').val();
                    if (src) {
                        items.push({
                            aid: aid,
                            thumb: thumb,
                            src: src,
                            desc: self.htmlspecialchars(desc),
                            sort: sort
                        });
                    }
                });
                (ct.func(callback) || function() {})(items);
            },
            error: function() {
                (ct.func(error) || function() {})();
            }, 
            dataType: 'json'
        });
        return false;
    },

    clearLocalImages: function() {
        this.localImageList.find('li.image-thumb-item').remove();
        this.detectIfEmtpy();
    },

    // 网络图片

    initRemoteImagePanel: function() {
        var self = this,
            addButton = this.options.remoteButton;
        addButton.click(function() {
            self.addRemoteImage();
        });
        for (var i = 0; i < 2; i++) {
            this.addRemoteImage();
        }
        this.itemChanged = false;
    },

    addRemoteImage: function(url, desc) {
        if (! this.remoteImageList || ! this.remoteImageList.length) return;
        var self = this,
            html = this.renderRemoteImage(url, desc),
            item = $(html).appendTo(this.remoteImageList.find('> table > tbody')),
            deleteButton = item.find('[data-role=delete]');
        this.sortRemoteImage();
        this.itemChanged = true;
        deleteButton.click(function() {
            self.deleteRemoteImage(item);
        });
        this.remoteImageList.parent().scrollTop(this.scrollHeight(this.remoteImageList.get(0)));
    },

    sortRemoteImage: function() {
        this.itemChanged = true;
        $.each(this.remoteImageList.find('> table > tbody > tr'), function(index, tr) {
            $(tr).find('[data-role=sort]').text(index + 1);
        });
    },

    renderRemoteImage: function() {
        var sort = 1;
        return function(url, desc) {
            return [
                '<tr>',
                    '<td width="30" class="t_c bdr_3" data-role="sort">', sort++, '</td>',
                    '<td width="290">',
                        '<input data-role="src" type="text" value="', url || '', '" placeholder="网址" />',
                    '</td>',
                    '<td>',
                        '<input data-role="desc" type="text" value="', this.htmlspecialchars(desc || ''), '" placeholder="简介" />',
                    '</td>',
                    '<td width="30" class="t_c">',
                        '<a data-role="delete" href="javascript:;" title="删除"><img src="images/del.gif" alt="" /></a>',
                    '</td>',
                '</tr>'
            ].join('');
        };
    }(),

    deleteRemoteImage: function(elem) {
        elem.remove();
        this.sortRemoteImage();
        this.itemChanged = true;
    },

    getRemoteImageItems: function() {
        var self = this, items = [];
        this.remoteImageList.find('tr').each(function(index, tr) {
            var row = $(tr),
                src = row.find('[data-role=src]').val(),
                desc = row.find('[data-role=desc]').val();
            if (src) {
                items.push({
                    src: src,
                    desc: self.htmlspecialchars(desc)
                });
            }
        });
        return items;
    },

    renderToEditor: function(src, desc) {
        if (! src) {
            return '';
        }
        var html = '<p data-mce-style="text-align: center;" style="text-align:center;"><img src="' + src + '" /></p>';
        desc = this.htmlspecialchars(desc);
        desc && (html += '<p data-mce-style="text-align: center; font-size: 12px; text-indent: 0;" style="text-align: center; font-size: 12px; text-indent: 0;">'+desc+'</p>');
        return html;
    }
};
ct.imageList = function(option) {
    return new imageList(option).init();
};
})(jQuery, cmstop);