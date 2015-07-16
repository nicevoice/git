/**
 * CmsTop 前端用户登录登出解决方案
 *
 * @author micate<root@micate.me>
 * @version $Id$
 * @depends config.js, jquery.js, dialog.js
 */

var cmstop = cmstop || {};

(function($, cmstop) {
var CALLBACKS = {
        login: [],
        logout: []
    },
    TCALLBACKS = {
        login: [],
        logout: []
    },
    lock,
    errorCount = 0;

function getCookie(name) {
    try {
        var matches = document.cookie.match(new RegExp("(^| )" + COOKIE_PRE + name + "=([^;]*)(;|$)"));
        return matches && unescape(matches[2]) || '';
    } catch(e) {}
    return '';
}

function func(ns, context) {
    if (typeof ns == 'function') {
        return ns;
    }
    if (typeof ns == 'string') {
        ns = ns.split('.');
        var o = (context || window)[ns[0]], w = null;
        if (!o) return null;
        for (var i=1,l;l=ns[i++];) {
            if (!o[l]) {
                return null;
            }
            w = o;
            o = o[l];
        }
        return o && (function(){
            return o.apply(w, arguments);
        });
    }
    return null;
}

function fireEvent(type, param) {
    var index, callback;

    if (CALLBACKS[type]) {
        for (index in CALLBACKS[type]) {
            try {
                (func(CALLBACKS[type][index]))(param);
            } catch (e) {}
        }
    }
    
    if (TCALLBACKS[type]) {
        while (callback = TCALLBACKS[type].shift()) {
            try {
                (func(callback))(param);
            } catch (e) {}
        }
    }

    if (type == 'login') {
        $('.cmstop-login-message').hide();
        $('.cmstop-logged-message').show().find('[role=username]').html(param.username);
    } else {
        $('.cmstop-login-message').show();
        $('.cmstop-logged-message').hide().find('[role=username]').html('');
    }
}

$.extend(cmstop, {
    member: {
        listen: function(login, logout) {
            login && CALLBACKS.login.push(login);
            logout && CALLBACKS.logout.push(logout);
            return this;
        },

        check: function() {
            if (getCookie('auth')) {
                fireEvent('login', {userid: getCookie('userid'), username: getCookie('username') || getCookie('rememberusername')});
                return true;
            }
            fireEvent('logout');
            return false;
        },

        login: function(callback) {
            if (lock || this.check()) return false;
            lock = true;
            setTimeout(function() {
                $.getJSON(APP_URL + '?app=member&controller=index&action=loginform&jsoncallback=?', function(html) {
                    if (html) {
                        dialog.dialog({
                            title: '登录',
                            message: html,
                            width: 410,
                            buttons: [{text: '登录', callback: function(guid, box) {
                                var username = box.find('[name=username]').val(),
                                    password = box.find('[name=password]').val(),
                                    needSeccode = box.find('#member_login_seccode_box').is(':visible'),
                                    seccode = box.find('[name=seccode]').val(),
                                    cookietime = box.find('[name=cookietime]'),
                                    remember = cookietime.is(':checked'),
                                    cookietime = cookietime.val(),
                                    param = [];

                                if (!username) {
                                    dialog.tips('请输入用户名');
                                    return false;
                                }

                                if (!password) {
                                    dialog.tips('请输入密码');
                                    return false;
                                }

                                if (needSeccode && !seccode) {
                                    dialog.tips('请输入验证码');
                                    return false;
                                }

                                param.push('username=' + encodeURIComponent(username));
                                param.push('password=' + encodeURIComponent(password));
                                needSeccode && param.push('seccode=' + encodeURIComponent(seccode));
                                remember && param.push('cookietime=' + encodeURIComponent(cookietime));
                                $.getJSON(APP_URL + '?app=member&controller=index&action=ajaxlogin&jsoncallback=?&' + param.join('&'), function(json) {
                                    if (json && json.state) {
                                        errorCount = 0;
                                        fireEvent('login', json);
                                        dialog.close(guid);
                                    } else {
                                        errorCount++;
                                        $('#member_login_seccode_box').show().find('img').trigger('click');
                                        dialog.resize(guid);
                                        dialog.error(json && json.error || '登录失败，请重新尝试');
                                    }
                                });

                                return false;
                            }}]
                        }, function(box) {
                            var seccode = box.find('[name=seccode]'),
                                img = seccode.next('img'),
                                link = img.next('a');

                            callback && TCALLBACKS.login.push(callback);

                            img.click(function() {
                                img.attr('src', APP_URL + '?app=system&controller=seccode&action=image&id=' + Math.random() * 5);
                                return false;
                            });
                            link.click(function() {
                                img.trigger('click');
                                return false;
                            });

                            seccode.is(':visible') && img.trigger('click');
                        }, function() {
                            lock = false;
                        });
                    }
                });
            }, 0);
        },
        logout: function(callback) {
            callback && TCALLBACKS.logout.push(callback);

            setTimeout(function() {
                $.getJSON(APP_URL + '?app=member&controller=index&action=ajaxlogout&jsoncallback=?', function(json) {
                    if (json && json.state) {
                        fireEvent('logout');
                    } else {
                        dialog.tips(json && json.error || '退出失败');
                    }
                });
            }, 0);
        }
    }
});
})(jQuery, cmstop);