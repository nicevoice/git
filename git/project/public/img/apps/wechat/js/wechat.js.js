(function($){
var types = ['DOMMouseScroll', 'mousewheel'];
$.fn.mousewheel = function(fn) {
            return fn ? this.bind("mousewheel", fn) : this.trigger("mousewheel");
};
$.event.special.mousewheel = {
            setup: function() {
                        if ( this.addEventListener )
                                   for ( var i=types.length; i; )
                                               this.addEventListener( types[--i], handler, false );
                        else
                                   this.onmousewheel = handler;
            },
 
            teardown: function() {
                        if ( this.removeEventListener )
                                   for ( var i=types.length; i; )
                                               this.removeEventListener( types[--i], handler, false );
                        else
                                   this.onmousewheel = null;
            }
};
function handler(event) {
            var args = [].slice.call( arguments, 1 ), delta = 0, returnValue = true;
 
            event = $.event.fix(event || window.event);
            event.type = "mousewheel";
 
            if ( event.wheelDelta ) delta = event.wheelDelta/120;
            if ( event.detail ) delta = -event.detail/3;
 
            // Add events and delta to the front of the arguments
            args.unshift(event, delta);
 
            return $.event.handle.apply(this, args);
}
})(jQuery);
 
(function($){
 
var frm = null,
            btn = null,
            ul = null,
            more = null,
            lock = false,
            page = 0,
            win = $(window),
            isunbind = false;
 
function add(e){
            var f = this;
            e.preventDefault();
            e.stopPropagation();
 
            var data = frm.serialize();
            btn.attr('disabled', true);
            $.ajax({
                        url:'?app=book&controller=index&action=add',
                        dataType:'json',
                        data:data,
                        type:'POST',
                        success:function(json){
                                   if (json.state) {
                                               var li = createRow(json.data);
                                               ul.prepend(li.hide());
                                               li.slideDown();
                                   } else {
                                               if(json.error){
                                                           alert(json.error);
                                               }else{
                                                           alert('添加失败');
                                               }
                                   }
                        },
                        error:function(){
                                   alert('请求异常');
                        },
                        complete:function(){
                                   btn.attr('disabled', false).removeAttr('disabled');
                        }
            });
}
 
function createRow(row) {
            var li = '<li>'+
                        '<strong>'+(row.username||'网友')+'</strong>'+
                        '<em>（'+row.email+'）</em>'+
                        '<span>'+row.addtime+'</span>'+
                        '<p>'+row.content+'</p>';
            if(row.reply){
                        li = li + '<p class="reply"><b>回复：</b>' + row.reply + '</p>';
            }
            li = li + '</li>';
            return $(li);
}
 
function spage(e, data){
            if (data < 0 && document.documentElement.scrollTop + win.height() > document.documentElement.scrollHeight - 50)
            {
                        query(page+1);
            }
}
function query(p){
            if (lock) {
                        return;
            }
            lock = true;
            more.addClass('loading');
            $.ajax({
                        url:'?app=book&controller=index&action=page&page='+p,
                        dataType:'json',
                        type:'GET',
                        success: function(json){
                                   for (var i=0, t; t = json[i++];) {
                                               ul.append(createRow(t));
                                   }
                                   if (p <= 1) {
                                               win.mousewheel(spage);
                                   }
                                   if (!isunbind && p > 4) {
                                               win.unbind('mousewheel', spage);
                                               isunbind = true;
                                   }
                                   if (json.length) {
                                               page = p;
                                   } else if (!isunbind) {
                                               win.unbind('mousewheel', spage);
                                               isunbind = true;
                                   }
                        },
                        complete:function(){
                                   lock = false;
                                   more.removeClass('loading');
                        }
            });
}
 
window.init = function(){
            frm = $('#book_add').bind('submit', add);
            btn = $('#submit');
            ul = $('#list');
            more = $('#more');
            query(1);
};
})(jQuery);