//code by shanhuhai
(function($)
{
	$.fn.category = function(){
		var catobj = $(this),
		cs_box = catobj.next().next(),
		cs_sb = cs_box.find('.cs_sb'),
		cateseek = cs_sb.find('input'),
		cs_load = catobj.next(),
		cs_mitem = cs_box.find('.cs_mitem'),
		cate = {
			scache :{},
			mcache :{},
			kcache :'',
			load :function(e)
			{
				$(this).css('visibility','hidden');
				var position= $(this).position();
				cs_box.show().css({'left':position.left,'top':position.top-4});
				if(!cate.mcache[0]) $.getJSON('?app=system&controller=category&action=dropcate',{},function(data){
					if(data){ cate._binditem(data, false); cate.mcache = data;}
				});
				else cate._binditem(cate.mcache, false);
				cateseek.focus();
				e.stopPropagation();
			},
			search : function(e){
				if(e.keyCode ==38 || e.keyCode ==40 ||e.keyCode ==13) return;
				if(e.keyCode ==37 || e.keyCode ==39) {cate.select($('.over'));return}
				var value = this.value;
				cate.kcache = value;
				if(value)
				{
					if(cate.scache[value]==undefined)  $.getJSON('?app=system&controller=category&action=search',{keyword:value},function(data){
						cate._binditem(data, true);
						cate.scache[value] = data;
					});
					else cate._binditem(cate.scache[value],true);
				}
				else
				{
				 	cate.mcache[0] && cate._binditem(cate.mcache, false);
				}
				return false;
			},
			hotkey : function(e){
				var over = $('.over');
				if(e.keyCode ==38 || e.keyCode ==40)
				{
					if(over[0] == undefined)
					{
						$('.cs_mlist:first').addClass('over');
					}
					else
					{
						over.removeClass('over');
						var prev = over.prev()[0]?over.prev():$('.cs_mlist:last');
						var next = over.next()[0]?over.next():$('.cs_mlist:first');
						e.keyCode == 40 && next.addClass('over');
						e.keyCode == 38 && prev.addClass('over');
					}
				}
				if(e.keyCode == 13)
				{
					cate.select($('.over'));
				}
				e.stopPropagation();
			},
			select : function(obj)
			{
				var target = obj[0]?obj:$(this),
				    catid = target.attr('catid'),
					name = target.attr('name');
				if(target[0] !=document) {
					catobj.val(catid);
					cs_load.html(name);
					$.get('?app=system&controller=category&action=note',{catid:catobj.val(),name:target.attr('name')});
					var keyexist = false;
					for(var i in cate.mcache){
						if(catid == cate.mcache[i]['catid']){
							keyexist = true;
							cate.mcache.splice(i,1);
							cate.mcache.unshift({'catid':catid,'name':name});
							break;
						}
					}
					if(!keyexist){
						cate.mcache.unshift({'catid':catid,'name':name});
						cate.mcache.pop();
					}
				}
				cs_box.hide();
				cs_mitem.html('');
				cs_load.css('visibility','visible');
			},
			_binditem : function(data, isearch)
			{
				cs_mitem.html('');
				var bname = '';
				var dl = data.length;
				for(var i=0;i<dl;i++)
				{
					if(isearch) bname = data[i].name.replace(cate.kcache,'<b style="color:#F00">'+cate.kcache+'</b>');
					cs_mitem.append($('<div class="cs_mlist '+((i==0&&isearch)?'over':'')+'" catid="'+data[i].catid+'" name="'+data[i].name+'" >'+(isearch?bname:data[i].name)+'</div>').hover(function(){$(this).addClass('over')},function(){$(this).removeClass('over')}).bind('click',cate.select));
				}
			}
		}
		$(document).click(cate.select);
		cs_load.click(cate.load);
		cs_sb.click(function(e){e.stopPropagation();});
		cateseek.keyup(cate.search);
		cs_box.keydown(cate.hotkey);
	}
})(jQuery);


