/**
 * CmsTop 专题组件（widget）助手文件
 *
 * 提供专题 widget 的常见操作，如拖动排序，上移下移等操作
 *
 * @depends jquery.js cmstop.js config.js
 */

window.cmstop || (cmstop = {});
cmstop.widget || (cmstop.widget = {});

/**
 * 专题列表组件拖动排序
 *
 * 仅使用于如下的结构：
 * @code
 * <div class="list-area">
 *     <div class="list-sepr">...</div>
 *     <div class="list-item">...</div>
 *     <div class="list-sepr">...</div>
 *     <div class="list-item">...</div>
 *     ...
 * </div>
 * @endcode
 *
 * @depends jquery.js, jquery.ui.js
 */
(function($, ct) {
$.extend(ct.widget, {
    dragSort: function() {
        var OPTIONS = {
                'handle': '.list-ctrl',
                'number': 'span.num',
                'child-class': 'list-item',
                'sepr-class': 'list-sepr'
            },
            DOT = '.',
            HELPER = 'drag-sort-helper',
            inited = false,
            seprPrev = undefined,
            from = undefined,
            to = undefined;
        return function(elem, options) {
            var o = $.extend({}, OPTIONS, options || {}),
                childClass = DOT + o['child-class'],
                seprClass = DOT + o['sepr-class'];
            if (inited) {
                elem.sortable('refresh');
            } else {
                elem.sortable({
                    'axis': 'y',
                    'handle': o['handle'],
                    'items': childClass,
                    'cancel': seprClass,
                    'helper': 'clone',
                    'placeholder': o['child-class'] + ' ' + HELPER,
                    'opacity': 0.6,
                    create: function() {
                        inited = true;
                    },
                    start: function(ev, ui) {
                        from = ui.item.parent().children(childClass).index(ui.item[0]);
                        seprPrev = ui.item.prev(seprClass).hide();
                        elem.find(DOT + HELPER).css('height', ui.item.height());
                    },
                    stop: function(ev, ui) {
                        var nextIsSepr = ui.item.next(':first').is(seprClass),
                            prevIsSepr = ui.item.prev(':first').is(seprClass),
                            items = ui.item.parent().children(childClass);
                        to = items.index(ui.item[0]);
                        if (prevIsSepr || from > to && ! nextIsSepr) {
                            seprPrev.insertAfter(ui.item);
                        }
                        if (nextIsSepr || from < to && ! prevIsSepr) {
                            seprPrev.insertBefore(ui.item);
                        }
                        seprPrev.show();
                        seprPrev = undefined;

                        items.each(function(index) {
                            $(this).find(o.number).text(index + 1);
                        });
                    }
                });
            }
        };
    }()
});
})(jQuery, cmstop);