/**
 * navigator base on jQuery 1.2+ for cmstop directory
 *
 * @author     kakalong
 * @copyright  2010 (c) cmstop.com
 * @version    $Id: cmstop.navigator.js 1350 2010-10-21 02:52:18Z root $
 * @depends
 *		jquery
 */
(function($){
var CLASSES = {
	navigator:'navigator',
	container:'container',
	leftbtn:'leftbtn',
	rightbtn:'rightbtn',
	refresh:'refresh',
    dirbtn:'dirbtn',
    direct:'direct',
    layer:'layer',
    item:'item',
    focus:'focus',
    hover:'hover'
},
OPTIONS = {
	dirUrl:'/',
	dirVar:'dir',
	refreshButton:1
},
substr = function(str, len) {
	var l = 0;
	for (var i=0; i<str.length; i++) {
		l += str.charCodeAt(i) > 255 ? 2 : 1;
		if (l > len) {
			break;
		}
	}
	if (l <= len) {
		return str;
	}
	len = len - 3;
	while (i--) {
		l -= str.charCodeAt(i) > 255 ? 2 : 1;
		if (l <= len) {
			break;
		}
	}
	return str.substr(0,i+1) + '...';
},
doc = $();
$.fn.navigator = function(options){
	options = $.extend({},OPTIONS,options||{});
    var jq = this.addClass(CLASSES.navigator).css('overflow','hidden');
    var _active = 0;
    
    var _container = $('<ul class="'+CLASSES.container+'" />').css({
        width:'auto',
        position:'absolute',
        left:0
    });
    
    var _tempContainer = $('<ul/>');
	var _stepstop = 0;
	var dequeue = function(step)
	{
		var _queue = function(){
			if (_stepstop) {
				return;
			}
			var l = step.pop();
			l === undefined || _container.animate({left:l},'fast',_queue);
		};
		_queue();
	};
	var mrdown = 0, mldown = 0, rfdown = 0;
    // add move right
    var mright = $('<div class="'+CLASSES.leftbtn+'" />')
	.mousedown(function(){
		_container.stop(1);
		_stepstop = 0;
		mrdown = 1;
		mright.addClass(CLASSES.focus);
        var posLeft = _container.position().left;
        var offsetLeft = mright.outerWidth();
        if (posLeft >= offsetLeft) return;
        var childs = _container.children('.'+CLASSES.dirbtn);
		var step = [];
        for (var i=0,j=childs.length;i<j;i++)
        {
            var l = $(childs[i]).position().left;
            if (l + posLeft < offsetLeft)
            {
                step.push(offsetLeft - l)
            }
            else {
                break;
            }
        }
		dequeue(step);
    }).mouseup(function(){
        mrdown && (
			(_stepstop = 1),
			mright.removeClass(CLASSES.focus),
			(mrdown = 0)
		);
    }).hover(function(){
        mright.addClass(CLASSES.hover);
    },function(){
		mright.removeClass(CLASSES.hover);
        mrdown && (
			(_stepstop = 1),
			mright.removeClass(CLASSES.focus),
			(mrdown = 0)
		);
    });
    // add move left
    var mleft = $('<div class="'+CLASSES.rightbtn+'" />').mousedown(function(){
        _container.stop(1);
		_stepstop = 0;
		mldown = 1;
		mleft.addClass(CLASSES.focus);
		var posLeft = _container.position().left;
        var offsetLeft = mleft.position().left;
        if (offsetLeft >= _container.outerWidth() + posLeft) return;
        var childs = _container.children('.'+CLASSES.direct);
		var step = [];
        for (var i=childs.length;i-->0;)
        {
			var b = $(childs[i]);
            var l = b.position().left;
            if (l + b.outerWidth() + posLeft  > offsetLeft)
            {
                step.push(offsetLeft - l - b.outerWidth());
            }
            else {
                break;
            }
        }
		dequeue(step);
    }).mouseup(function(){
        mldown && (
			(_stepstop = 1),
			mleft.removeClass(CLASSES.focus),
			(mldown = 0)
		);
    }).hover(function(){
        mleft.addClass(CLASSES.hover);
    },function(){
        mleft.removeClass(CLASSES.hover);
        mldown && (
			(_stepstop = 1),
			mleft.removeClass(CLASSES.focus),
			(mldown = 0)
		);
    });
    jq.append(mright).append(_container).append(mleft);

    var ref = null;
    if (options.refreshButton) {
        // add refresh
        
        ref = $('<div class="'+CLASSES.refresh+'" />').mousedown(function(){
            rfdown = 1;
    		ref.addClass(CLASSES.focus);
        }).mouseup(function(){
            rfdown && (
    			ref.removeClass(CLASSES.focus),
    			(rfdown = 0),
    			jq.trigger('cd', [_container.find('li.dirbtn:last').attr('path')])
    		);
        }).hover(function(){
            ref.addClass(CLASSES.hover);
        },function(){
            ref.removeClass(CLASSES.hover);
    		rfdown && (
    			ref.removeClass(CLASSES.focus),
    			(rfdown = 0)
    		);
        });
    	jq.append(ref);
    	mleft.css('right',ref.outerWidth());
    } else {
        mleft.css('right',0);
    }


	var _dirbtnStack = {}, _btnStack = {};
    /**
     * {name:,path:,}
     * return <jQuery>
     */
    var _createItem = function(json,layer)
    {
        var li = $('<li class="'+CLASSES.item+'"><span>'+substr(json.name,20)+'</span></li>')
        .click(function(){
        	jq.trigger('cd',[json.path]);
            layer.data('btn').blur();
        }).hover(function(){
            li.addClass(CLASSES.hover);
        },function(){
            li.removeClass(CLASSES.hover);
        });
        layer.append(li);
    };
    /**
     * return <jQuery> 
     */
    var _createDirect = function(dirbtn)
    {
        var visable = 0,
        ivalshow, ivalhide,
        clearIshow = function(){
            ivalshow && (clearTimeout(ivalshow), (ivalshow = null));
        },
        clearIhide = function(){
            ivalhide && (clearTimeout(ivalhide) , (ivalhide = null));
        },
        ishow = function(){
            clearIhide();
            visable || ivalshow || (ivalshow = setTimeout(show, 1792));
        },
        ihide = function(){
        	clearIshow();
            visable && ivalhide || (ivalhide = setTimeout(hide, 6000));
        },
        path = dirbtn.attr('path'),
        layerlock = 0,
        show = function(){
            clearIhide();
            clearIshow();
            if (visable) {
                return;
            }
            // set active
            _active = 1;
            visable = 1;
            btn.data('visable',1);
            // set btn style
            btn.addClass(CLASSES.focus);
            // set dirbtn style
            dirbtn.focus();
            
            var offset = btn.offset();
            layer.css({
                top:(offset.top + parseInt(btn.outerHeight())),
                left:offset.left - 9,
                display:'block'
            });
            
            // bind mousedown event to document
            // hack avoid close when open in mozilla
            setTimeout(function(){
                doc.mousedown(blur);
            }, 0);
            // if not expired
            if (layerlock) {
                return;
            }
            layerlock = 1;
            // else ajax get 
            $.ajax({
                dataType:'json',
                url:options.dirUrl,
                type:'POST',
                data:options.dirVar+'='+path,
                success:function(json){
                    var i=0,l=json.length;
                    if (l>0) {
                        layer.empty();
                        for (;i<l;i++)
                        {
                            _createItem(json[i],layer);
                        }
                    } else {
                        layer.html('<li class="'+CLASSES.item+'">无子目录</li>');
                    }
                },complete:function(){
                    layerlock = 0;
                }
            });
        },
        hide = function(){
			// avoid lazy unbind
			setTimeout(function(){
				doc.unbind('mousedown',blur);
			}, 0);
            clearIhide();
            clearIshow();
            if (!visable) {
                return;
            }
            _active = 0;
            visable = 0;
            btn.data('visable',0);
            // set btn style
            btn.removeClass(CLASSES.focus);
            // set dirbtn style
            dirbtn.blur();
            // hide layer
            layer.hide();
        },
        blur = function(e){
            e.target == layer[0] ||
            layer.find(e.target.nodeName).index(e.target) != -1 ||
            hide();
        },
        btn = $('<li class="'+CLASSES.direct+'"></li>').mousedown(function(){
            visable ? hide() : show();
        }).hover(function(){
            // hover this
            btn.addClass(CLASSES.hover);
            if (_active && !visable) {
                // if navigator active, showLayer()
                // hide other
                for (var p in _btnStack)
                {
                    p == path || _btnStack[p].data('visable') && _btnStack[p].blur();
                }
                // show this
                show();
            } else {
                ishow();
            }
        },function(){
            btn.removeClass(CLASSES.hover);
            ihide();
        }).bind('blur',hide).bind('focus',show),
        layer = $('<ul class="'+CLASSES.layer+'" />')
            .data('btn',btn)
			.hover(clearIhide,ihide).css('display','none').appendTo(document.body);
        _btnStack[path] = btn;
        dirbtn.after(btn);
        return btn;
    };
    var _createDirbtn = function(path,name)
    {
        var dirbtn = $('<li class="'+CLASSES.dirbtn+'" path="'+path+'">'+substr(name,20)+'</li>').appendTo(_container),
        	btn = _createDirect(dirbtn);
        _dirbtnStack[path] = dirbtn;
        if (!path) {
        	// dirbtn[0].style.cssText = 'padding:0;margin:0;border:none';
        	return;
        }
        var _down = 0;
        dirbtn.mousedown(function(){
            _down = 1;
            dirbtn.addClass(CLASSES.focus);
        }).mouseup(function(){
            _down && (
                dirbtn.removeClass(CLASSES.focus),
                jq.trigger('cd',[path]),
                (_down = 0)
            );
        }).hover(function(){
            dirbtn.addClass(CLASSES.hover);
            if (_active && !btn.data('visable')) {
                for (var p in _btnStack)
                {
                    p == path || _btnStack[p].data('visable') && _btnStack[p].blur();
                }
                // show this
                btn.focus();
            }
        },function(){
            dirbtn.removeClass(CLASSES.hover);
            _down && (
				dirbtn.removeClass(CLASSES.focus),
				(_down = 0)
			);
        }).focus(function(){
        	dirbtn.addClass(CLASSES.focus);
        }).blur(function(){
        	dirbtn.removeClass(CLASSES.focus);
        });
    };
    
    /**
     * [
     *  {path,name}
     *  {path,name}
     * ]
     */
    jq.bind('setNav', function(e,path,alias){
        // move orig btns to temp container
        _container.children().appendTo(_tempContainer);

        $.isArray(path) || (path = path.split('/'));
		alias ? $.isArray(alias) || (alias = alias.split('/')) : (alias = []);
		var p = path[0],n=alias[0]||path[0];

		_dirbtnStack[p] ? (
			_container.append(_dirbtnStack[p]),
			_container.append(_btnStack[p])
		) : (
			_createDirbtn(p,n)
		);
		for (var i=1,l=path.length;i<l;i++){
			p += '/'+path[i];
			n = alias[i]||path[i];
			_dirbtnStack[p] ? (
				_container.append(_dirbtnStack[p]),
				_container.append(_btnStack[p])
			) : (
				_createDirbtn(p,n)
			);
        }

		var width = 0;
		_container.children().each(function(){
			width += $.css(this,'width',false,'border');
		});
		_container.css('width',width);
        // width of container
        width = _container.outerWidth();
        // width of bar
        
		jq.innerWidth() - (ref ? ref.outerWidth() : 0) > width ? (
			mleft.hide(), mright.hide(),
			_container.animate({left:0},'fast')
		) : (
			mleft.show(), mright.show(),
			_container.animate({left:mleft.position().left - width},'fast')
		);
    });
    return this;
};
})(jQuery);