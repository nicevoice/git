var scrollList = function(elem, targetUl, countSource, countTarget)
{
	var ul = $('<ul class="txt_list"></ul>');
	elem.append(ul);
	var selectedIds = [];
	var bind_target_li_event = function(li, id)
	{
		li.find('img.del').click(function(){
			var i = selectedIds.indexOf(id);
			if (i!=-1)
			{
				selectedIds.splice(i, 1);
			}
			li.remove();
			ul.find('>li[id$='+id+']').trigger('unCheck');
			countTarget(targetUl.find('>li').length);
		});
	};
	var build_target_li = function(txt, id)
	{
		var li = $('<li id="checked_'+id+'"><span class="f_r"><img class="hand del" height="16" width="16" title="取消" alt="取消" src="images/del.gif" /></span><span class="f_l">'+txt+'</span></li>');
		bind_target_li_event(li, id);
		targetUl.append(li);
		return li;
	};
	// init scan target
	targetUl.find('>li').each(function(){
		var id = this.id.split('_').pop();
		selectedIds.push(id);
		bind_target_li_event($(this), id);
	});
	countTarget(selectedIds.length);
	var buildLi = function(item)
	{
	    var id = item.sectionid;
		var li = $('<li id="item_'+id+'"><input class="checkbox_style" type="checkbox" /><span>'+item.pagename+'</span>：<span>'+item.name+'</span></li>');
		var checkbox = li.find('input');
		li.bind('check',function(){
	        // toggle seleted
	        (li.addClass('checked'), (checkbox[0].checked = true));
	    }).bind('unCheck',function(){
	        (li.removeClass('checked'), (checkbox[0].checked = false));
	    });
		var togglechk = function(e){
	        // toggle seleted
	        e.stopPropagation();
	        var flag = checkbox[0].checked;
	        e.target == checkbox[0] && (flag = !flag);
	        if (flag)
	        {
	        	var i = selectedIds.indexOf(id);
				if (i!=-1)
				{
					selectedIds.splice(i, 1);
				}
	        	targetUl.find('>li[id$='+id+']').remove();
	        	li.trigger('unCheck');
	        }
	        else
	        {
	        	selectedIds.push(id);
	        	build_target_li(item.name, id);
	        	li.trigger('check');
	        }
	        countTarget(targetUl.find('>li').length);
	    };
	    li.click(togglechk);
        checkbox.click(togglechk);
		if (selectedIds.indexOf(id)!=-1)
		{
			li.trigger('check');
		}
		ul.append(li);
	};
	var baseUrl = '?app=page&controller=section&action=search&pagesize=20';
	var _oldwhere = '';
	var count = 0;
	var page = 1;
	var total = 0;
	var show_more_lock = false;
	this.load = function(where)
	{
		where.nodeType && (where = $(where));
		where && (_oldwhere = where.jquery ? where.serialize() : where);
		$.post(baseUrl, _oldwhere, function(json){
			total = json.total;
			page = 1;
			count = json.data.length;
			countSource(count + ' / '+ total);
			ul.empty();
			for (var i=0;i<count;i++)
			{
				buildLi(json.data[i]);
			}
		}, 'json');
	};
	elem.scroll(function(){
		if (!show_more_lock && count < total 
			&& elem.scrollTop() + elem.height() > elem[0].scrollHeight - 90)
		{
			show_more_lock = true;
			$.post(baseUrl+'&page='+(++page), _oldwhere, function(json){
				var l = json.data.length;
				count += l;
				countSource(count + ' / '+ total);
				for (var i=0;i<l;i++)
				{
					buildLi(json.data[i]);
				}
				show_more_lock = false;
			}, 'json');
		}
	});
}
function section_select(){
	ct.ajax('选择推送到的区块','?app=page&controller=section&action=search', 500,390,
	function(dialog){
		var _html = [];
		$('#section_data>li').each(function(){
			var id = this.id.split('_').pop();
			var txt = $(this).html();
			_html.push('<li id="checked_'+id+'"><span class="f_r"><img class="hand del" height="16" width="16" title="取消" alt="取消" src="images/del.gif" /></span><span class="f_l">'+txt+'</span></li>');
		});
		var targetUl = dialog.find('ul#section_selected');
		targetUl.append(_html.join(''));
		var o = new scrollList(dialog.find('div#scroll_div'), targetUl,
		function(c){
			$('#count').text(c);
		},function(c){
			$('#checked_count').text(c);
		});
		dialog.find('form input:submit').click(function(){
			o.load(this.form);
			return false;
		}).click();
	}, function(dialog){
		var selected_section = [];
		var section_html = '';
		dialog.find('ul#section_selected>li').each(function(){
			var span = $('>span:last', this);
			var id = this.id.split('_').pop();
			selected_section.push(id);
			section_html += '<li id="section_'+id+'" class="pad_r_8">'
			  +span.html()+'</li>';
		});
		$('#sectionids').val(selected_section);
		$('#section_data').html(section_html);

		return true;
	}, function(){
		return true;
	});
}