(function($){
function insertText(o, textValue, sel, offset, length){
    o.focus();
	var selection = document.selection;
	if (offset == undefined) {
		offset = 0;
	}
	if (selection && selection.createRange) {
		if (!sel) {
			sel = selection.createRange();
		}
		sel.text = textValue;
		var l = textValue.replace(/\r\n/g, '\n').length;
		sel.moveStart('character', offset-l);
		if (length != undefined) {
			sel.moveEnd('character', length-(l-offset));
		}
		sel.select();
	}
	else {
		var st = o.scrollTop, sl = o.scrollLeft;
		if (length == undefined) {
			length = textValue.replace(/\r\n/g, '\n').length - offset;
		}
		if (typeof o.selectionStart != 'undefined') {
			var opn = o.selectionStart + 0;
			o.value = o.value.substring(0, o.selectionStart)+textValue+o.value.substr(o.selectionEnd);
			o.selectionStart = opn + offset;
			o.selectionEnd = o.selectionStart + length;
		} else {
			o.value += textValue;
			o.focus();
			o.selectionStart = offset;
			o.selectionEnd = o.selectionStart + length;
		}
		o.scrollTop = st;
		o.scrollLeft = sl;
	}
	return this;
}
function _dsnready(dialog){
	var frm = dialog.find('form');
    $([frm[0].dsnid, frm[0].table]).change(function(){
        var data = 'dsnid='+frm[0].dsnid.value+'&table='+(this.name == 'table' ? encodeURIComponent(frm[0].table.value) : '');
        dialog.load('?app=system&controller=template&action=tag_db',data,function(){
            _dsnready(dialog);
        });
    });
}
var _refield = /\*/g;
$.editplus.setPlugin('db',function(a,sel){
	var editEl = this.textarea;
	var url = '?app=system&controller=template&action=tag_db';
    var _dsnD = ct.ajaxDialog({
		title:'数据库调用标签',
		width:650
	}, url, _dsnready,
    function(dialog){
        var t = '{db';
        var frm = dialog.find('form')[0];
        // dsnid dsnname
        if (frm.dsnid.value != '0') {
            t += ' dsn="'+frm.dsnid.options[frm.dsnid.selectedIndex].text+'"';
        }
        var fields = [];
        var allfields = [];
        var where = [];
        var order = [];
        var rows = dialog.find('table.table_list>tbody>tr').each(function(){
            var r = $(this);
            var n = '`'+this.cells[0].innerHTML+'`';
            allfields.push(n);
            r.find('input:checked').length && fields.push(n);
            var condition = r.find('select[name=condition]').val();
            var val = r.find('input:text').val();
            if (condition == 'IS NULL' || condition == 'IS NOT NULL')
            {
                where.push(n+' '+condition);
            } else if(val && val.length) {
                if (_refield.test(condition)) {
                    where.push(n+' '+condition.replace(_refield,val));
                } else {
                    where.push(n+' '+condition+' \''+val+'\'');
                }
            }
            var sort = r.find('select[name=sort]').val();
            if (sort == 'asc' || sort == 'desc')
            {
                order.push(n+' '+sort);
            }
        });
        fields.length && (allfields = fields);
        var usableFields = [];
        for (var i=0,f;f=allfields[i++];usableFields.push('{$r['+f.replace(/`/g,'')+']}')){}
        usableFields = usableFields.join(', ');

        var sql = 'SELECT '+(fields.length ? fields.join(',') : '*')
            +' FROM `'+frm.table.value+'`';
        where.length && (sql += ' WHERE '+where.join(' AND '));
        order.length && (sql += ' ORDER BY '+order.join(','));
        t += ' sql="'+sql+'"';
        var size = parseInt(frm.size.value);
        if (size) {
            t += ' size="'+size+'"';
        }

        t += '}\n'+usableFields+'\n'+sel.text+'\n{/db}';
        insertText(editEl[0], t, sel.selection);
        dialog.dialog('close');
    },function(dialog){dialog.dialog('close');});
},{
	desc:'数据源'
}).setPlugin('content',function(a,sel){
	var editEl = this.textarea,
		url = '?app=system&controller=template&action=tag_content',
		frm = null,
    	_contentD = ct.ajaxDialog('内容模型调用标签', url,
    function(dialog){
    	frm = dialog.find('form:first')[0];
    	dialog.find('.modelset').modelset();
    	dialog.find('.selectree').selectree();
    	dialog.find('.suggest').suggest();
    },
    function(dialog){
    	if (!frm) return;
        var t = '{content';
        var catid = $(frm.category).val();
        catid && (t += ' catid="'+catid+'"');
        var proid = $(frm.proid).val();
        proid && (t += ' proid="'+proid+'"');
        var modelid = $(frm.model).val();
        modelid && (t += ' modelid="'+modelid+'"');
        var sourceid = $(frm.source).val();
        sourceid && (t += ' sourceid="'+sourceid+'"');
        var createdby = $(frm.createdby).val();
        createdby && (t += ' createdby="'+createdby+'"');

        var weights = frm.weight;
        var weight = weights[0].value || '';
        var subWeight = weights[1].value || '';
        if (frm.weight_range.checked && (weight.length||subWeight.length)) {
            weight += ','+subWeight;
        }
        weight.length && (t += ' weight="'+weight+'"');

        if (frm.published.value && frm.published.value != '0') {
        	t += ' published="'+frm.published.value+'"';
        }

        var tag = frm.tag.value;
        tag && tag.length && (t+=' tags="'+tag+'"');

        var orderby = [];
        $('#orderby>div:visible').each(function(){
            orderby.push($('>select',this).val() +' '+ ($('input:checkbox',this)[0].checked ? 'desc' : 'asc'));
        });
        orderby.length && (t += ' orderby="'+orderby+'"');

        var size = parseInt(frm.size.value);
        if (size) {
            t += ' size="'+size+'"';
        }
		var usableFields = '{$r[title]},{$r[color]},{$r[thumb]},{$r[url]},{date("Y-m-d H:i", $r[published])}';
        t += '}\n'+usableFields+'\n'+sel.text+'\n{/content}';
        insertText(editEl[0], t, sel.selection);
        dialog.dialog('close');
    },function(dialog){dialog.dialog('close');});
},{
	desc:'内容'
}).setPlugin('discuz',function(a,sel){
	var editEl = this.textarea;
	var url = '?app=system&controller=template&action=tag_discuz';
    var _contentD = ct.ajaxDialog('Discuz!调用标签', url,
    function(dialog){
    	var frm = dialog.find('form')[0];
    	var tbody = dialog.find('tbody:last');
    	var dsn = $(frm.dsnid).change(function(){
    		$.getJSON('?app=system&controller=template&action=load_discuz&dsnid='+this.value,
    		function(json){
    			if (json.state) {
    				tbody.html(json.html);
    				// bind event
    				tbody.find('input.suggest').suggest();
    				tbody.find('img.tips').attrTips('tips', 'tips_green', 200, 'top');
    			}
    			else {
    				tbody.html('<tr><td colspan="2">'+json.error+'</td></tr>');
    			}
    		});
    		$.post('?app=system&controller=template&action=memdiscuzdsn','dsnid='+this.value);
    		
    	});
    	
    	if (frm.dsnid.value && frm.dsnid.value != '0') {
    		dsn.change();
    	}
    }, function(dialog){
    	var frm = dialog.find('form')[0];
    	var t = '{discuz';
    	// dsnid dsnname
        if (frm.dsnid.value && frm.dsnid.value != '0') {
            t += ' dsn="'+frm.dsnid.options[frm.dsnid.selectedIndex].text+'"';
        }
        if (frm.discuzX.value == '1') {
        	t += ' discuzX="1"';
        }
        t += ' prefix="'+frm.prefix.value+'"';
        t += ' filter="'+$(frm.filter).filter(':checked').val()+'"';
        var specials = [];
        $(frm.special).each(function(){
        	this.checked && specials.push(this.value);
        });
        if (specials.length) {
        	t += ' special="'+specials+'"';
        }
        if (frm.published.value && frm.published.value != '0') {
        	t += ' published="'+frm.published.value+'"';
        }
        t += ' orderby="'+frm.orderby.value+' '+$(frm.ascdesc).filter(':checked').val()+'"';
        var fids = $(frm.fid).val();
        if (fids && fids.length && fids.indexOf('all')==-1) {
        	t += ' fid="'+fids+'"';
        }
        if (frm.author.value) {
        	t += ' uid="'+frm.author.value+'"';
        }
        if (frm.keywords.value) {
        	t += ' keywords="'+frm.keywords.value+'"';
        }
        var size = parseInt(frm.size.value);
        if (size) {
            t += ' size="'+size+'"';
        }

        t += '}\n'+sel.text+'\n{/discuz}';
        insertText(editEl[0], t, sel.selection);
        dialog.dialog('close');
    }, function(dialog){dialog.dialog('close');});
},{
	text:'discuz!',
	desc:'discuz!论坛主题'
}).setPlugin('phpwind',function(a,sel){
	var editEl = this.textarea;
	var url = '?app=system&controller=template&action=tag_phpwind';
    var _contentD = ct.ajaxDialog('phpwind调用标签', url,
    function(dialog){
    	var frm = dialog.find('form')[0];
    	var tbody = dialog.find('tbody:last');
    	var dsn = $(frm.dsnid).change(function(){
    		$.getJSON('?app=system&controller=template&action=load_phpwind&dsnid='+this.value,
    		function(json){
    			if (json.state) {
    				tbody.html(json.html);
    				// bind event
    				tbody.find('input.suggest').suggest();
    				tbody.find('img.tips').attrTips('tips', 'tips_green', 200, 'top');
    			}
    			else {
    				tbody.html('<tr><td colspan="2">'+json.error+'</td></tr>');
    			}
    		});
    		$.post('?app=system&controller=template&action=memphpwinddsn','dsnid='+this.value);
    	});
    	if (frm.dsnid.value && frm.dsnid.value != '0') {
    		dsn.change();
    	}
    }, function(dialog){
    	var frm = dialog.find('form')[0];
    	var t = '{phpwind';
    	// dsnid dsnname
        if (frm.dsnid.value && frm.dsnid.value != '0') {
            t += ' dsn="'+frm.dsnid.options[frm.dsnid.selectedIndex].text+'"';
        }
        t += ' prefix="'+frm.prefix.value+'"';
        t += ' filter="'+$(frm.filter).filter(':checked').val()+'"';
        var specials = [];
        $(frm.special).each(function(){
        	this.checked && specials.push(this.value);
        });
        if (specials.length) {
        	t += ' special="'+specials+'"';
        }
        if (frm.published.value && frm.published.value != '0') {
        	t += ' published="'+frm.published.value+'"';
        }
        t += ' orderby="'+frm.orderby.value+' '+$(frm.ascdesc).filter(':checked').val()+'"';
        var fids = $(frm.fid).val();
        if (fids && fids.length && fids.indexOf('all')==-1) {
        	t += ' fid="'+fids+'"';
        }
        if (frm.author.value) {
        	t += ' uid="'+frm.author.value+'"';
        }
        if (frm.keywords.value) {
        	t += ' keywords="'+frm.keywords.value+'"';
        }
        var size = parseInt(frm.size.value);
        if (size) {
            t += ' size="'+size+'"';
        }

        t += '}\n'+sel.text+'\n{/phpwind}';
        insertText(editEl[0], t, sel.selection);
        dialog.dialog('close');
    }, function(dialog){dialog.dialog('close');});
},{
	text:'phpwind',
	desc:'phpwind论坛主题'
}).setPlugin('shopex',function(a,sel){
	var editEl = this.textarea;
	var url = '?app=system&controller=template&action=tag_shopex';
    var _contentD = ct.ajaxDialog('shopex调用标签', url,
    function(dialog){
    	var frm = dialog.find('form')[0];
    	var tbody = dialog.find('tbody:last');
    	var dsn = $(frm.dsnid).change(function(){
    		$.getJSON('?app=system&controller=template&action=load_shopex&dsnid='+this.value,
    		function(json){
    			if (json.state) {
    				tbody.html(json.html);
    				// bind event
    				tbody.find('input.suggest').suggest();
    				tbody.find('img.tips').attrTips('tips', 'tips_green', 200, 'top');
    			}
    			else {
    				tbody.html('<tr><td colspan="2">'+json.error+'</td></tr>');
    			}
    		});
    		$.post('?app=system&controller=template&action=memshopexdsn','dsnid='+this.value);
    	});
    	if (frm.dsnid.value && frm.dsnid.value != '0') {
    		dsn.change();
    	}
    }, function(dialog){
    	var frm = dialog.find('form')[0];
    	var t = '{shopex';
    	// dsnid dsnname
        if (frm.dsnid.value && frm.dsnid.value != '0') {
            t += ' dsn="'+frm.dsnid.options[frm.dsnid.selectedIndex].text+'"';
        }
        t += ' prefix="'+frm.prefix.value+'"';
        var tagids = [];
        $(frm.tagid).each(function(){
        	this.checked && tagids.push(this.value);
        });
        if (tagids.length) {
        	t += ' tagid="'+tagids+'"';
        }
        if (frm.published.value && frm.published.value != '0') {
        	t += ' published="'+frm.published.value+'"';
        }
        t += ' orderby="'+frm.orderby.value+' '+$(frm.ascdesc).filter(':checked').val()+'"';
        var catids = $(frm.catid).val();
        if (catids && catids.length && catids.indexOf('all')==-1) {
        	t += ' catid="'+catids+'"';
        }
        if (frm.brand.value) {
        	t += ' bid="'+frm.brand.value+'"';
        }
        if (frm.keywords.value) {
        	t += ' keywords="'+frm.keywords.value+'"';
        }
        var size = parseInt(frm.size.value);
        if (size) {
            t += ' size="'+size+'"';
        }

        t += '}\n'+sel.text+'\n{/shopex}';
        insertText(editEl[0], t, sel.selection);
        dialog.dialog('close');
    }, function(dialog){dialog.dialog('close');});
},{
	text:'shopex',
	desc:'shopex'
}).setPlugin('loop',function(a,sel){
	insertText(this.textarea[0], '{loop $array $k $v}\n'+sel.text+'\n{/loop}', sel.selection);
},{
	desc:'循环'
}).setPlugin('ifelse',function(a,sel){
	insertText(this.textarea[0], '{if condition}\n'+sel.text+'\n{else}\n\t\n{/if}', sel.selection);
},{
	text:'if else',
	desc:'分支语句'
}).setPlugin('elseif',function(a,sel){
	insertText(this.textarea[0], '{elseif condition}\n'+sel.text, sel.selection);
},{
	text:'elseif',
	desc:'分支语句'
}).setPlugin('preview',function(a,sel){
    ct.ajaxDialog('预览', '?app=system&controller=template&action=preview&code='+encodeURIComponent(sel.text)).css('padding',10);
},{
	text:'预览选中',
	desc:'预览选中部分'
});
})(jQuery);