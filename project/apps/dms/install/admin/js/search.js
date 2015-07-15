(function(ct, $) {
$.extend(ct, {
    dms: {
        search: function(callback) {
            var textBox = $('.dms-search-input-text'),
                text = textBox.find('[name=keyword]'),
                type = textBox.find('[name=type]'),
                icon = $('.dms-search-input-subject-icon'),
                dropdown = $('.dms-search-input-dropdown'),
                subjects = dropdown.children(),
                dropdownOpened = false,
                updateInterval = undefined,
                self = this;

            this.init = function() {
                callback = ct.func(callback);
                text.focus(function() {
                    startUpdate();
                    icon.addClass('input');
                    if (text.val()) {
                        self.showDropdown();
                    }
                }).blur(function() {
                    icon.removeClass('input');
                    stopUpdate();
                }).keyup(function(e) {
                    if (e && e.keyCode) {
                        var code = e.keyCode,
                            currentSubject = subjects.filter('[role=' + type.val() + ']');
                        if (code == 13) // 回车搜索
                        {
                            currentSubject.trigger('click');
                            $(this).blur();
                            return false;
                        }
                        else if (code == 38) // 向下按键
                        {
                            (currentSubject.prev().size() ? currentSubject.prev() : subjects.filter(':last')).trigger('selected');
                        }
                        else if (code == 40) // 向上按键
                        {
                            (currentSubject.next().size() ? currentSubject.next() : subjects.filter(':first')).trigger('selected');
                        }
                    }
                });

                icon.click(function() {
                    dropdownOpened ? self.hideDropdown() : self.showDropdown();
                });

                subjects.each(function() {
                    var subject = $(this);
                    subject.click(function() {
                        updateType(subject);
                        self.hideDropdown();
                        callback(text.val());
                        return false;
                    });
                    subject.bind('selected', function() {
                        subjects.removeClass('current');
                        subject.addClass('current');
                        updateType(subject);
                    });
                });
            };

	        // public method
            this.showDropdown = function() {
                if (dropdownOpened) return;
                dropdown.show();
                text.addClass('select');
                if (!text.val()) {
                    icon.addClass('up');
                }
                subjects.removeClass('current').filter('[role=' + type.val() + ']').addClass('current');
                $(document).bind('click.dropdown', function(e) {
                    var target = e && e.target;
                    if (target && dropdownOpened && target != icon.get(0) && target != text.get(0) && subjects.index(target) == -1) {
                        self.hideDropdown();
                    }
                });
                dropdownOpened = true;
            };

	        // public method
            this.hideDropdown = function() {
                if (!dropdownOpened) return;
                dropdown.hide();
                text.removeClass('select');
                icon.removeClass('up');
                $.event.remove('.dropdown');
                dropdownOpened = false;
            };

	        // public method
            this.updateKeywords = function(keyword, type) {
                updateType(subjects.filter('[role=' + (type || '') + ']'));
                text.val(keyword);
            };

            function updateType(subject) {
                text.attr('placeholder', subject.attr('label'));
                type.val(subject.attr('role'));
            }

            function startUpdate() {
                updateSubjects();
                updateInterval = setInterval(function() {
                    updateSubjects();
                }, 50);
            }

            function stopUpdate() {
                clearInterval(updateInterval);
            }

            function updateSubjects() {
                var value = text.val();
                if (value) {
                    self.showDropdown();
                    subjects.each(function() {
                        var subject = $(this);
                        if (subject.attr('role')) {
                            var cuted = value.substr(0, 6);
                            subject.html(subject.attr('label') + '包含<strong>' + cuted + (cuted != value ? '...' : '') + '</strong>的项');
                        }
                    });
                } else {
                    subjects.each(function() {
                        var subject = $(this);
                        if (subject.attr('role')) {
                            subject.text('按' + subject.attr('label') + '查询');
                        }
                    });
                }
            }
        }
    }
});
})(cmstop, jQuery);
