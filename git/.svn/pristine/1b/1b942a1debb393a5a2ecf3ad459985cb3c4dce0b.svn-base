<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$CONFIG['charset']?>" />
<title>关键词链接</title>

<!--gloabl-->
<link rel="stylesheet" type="text/css" href="css/admin.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/validator/style.css"/>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/config.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/cmstop.js"></script>
<!--global -->
<!--tinymce-->
<script type="text/javascript" src="<?=ADMIN_URL?>tiny_mce/tiny_mce_popup.js"></script>
<script type="text/javascript">
var ed = tinyMCEPopup.editor;
</script>
<!--tinymce-->

<style  type="text/css">
body{background-color:#FFFFFF}
fieldset{ margin:0px; padding:4px;}
.btn_float{float:right; margin-right:0; margin-left:3px;}
input,select{ font-size:12px;}
.delimg{cursor:pointer;margin-bottom:2px;display:none}
.hidden{display:none}
</style>
</head>
<body>
<table id="matchtag" style="padding-left:5px;padding-bottom:3px" width="490" border="0" cellspacing="0"  cellpadding="0" class="table_form">
     <tr>
		<td style="padding-bottom:6px"><strong>关键词</strong></td>
		<td></td>
		<td style="padding-bottom:6px"><strong>网址</strong></td>
		<td></td>
	</tr>
	<tr>
		<td><input type="text" name="t1" id="t1" size="10" /></td>
		<td>&nbsp;&nbsp;</td>
		<td><input type="text" name="c1" id="c1" size="55" value="http://"/></td>
		<td><img src="images/del.gif" class="delimg hidden" /></td>
	</tr>
	<tr>
		<td><input type="text" name="t2" id="t2" size="10" /></td>
		<td>&nbsp;&nbsp;</td>
		<td><input type="text" name="c2" id="c2" size="55" value="http://"/></td>
		<td><img src="images/del.gif" class="delimg hidden" /></td>
	</tr><tr>
		<td><input type="text" name="t3" id="t3" size="10"/></td>
		<td>&nbsp;&nbsp;</td>
		<td><input type="text" name="c3" id="c3" size="55" value="http://"/></td>
		<td><img src="images/del.gif" class="delimg hidden" /></td>
	</tr><tr>
		<td><input type="text" name="t4" id="t4" size="10"/></td>
		<td>&nbsp;&nbsp;</td>
		<td><input type="text" name="c4" id="c4" size="55" value="http://"/></td>
		<td><img src="images/del.gif" class="delimg hidden" /></td>
	</tr><tr>
		<td><input type="text" name="t5" id="t5" size="10"/></td>
		<td>&nbsp;&nbsp;</td>
		<td><input type="text" name="c5" id="c5" size="55" value="http://"/></td>
		<td><img src="images/del.gif" class="delimg hidden" /></td>
	</tr>
	<tr><td colspan="4"><div align="right">
	<input name="add_line" type="button" value="增加行" class="hand button_style" style="margin-right:20px" onclick="tagLink.addLine()" />
	</tr>
	
</table>	

<div class="mceActionPanel" align="center" >
    <input type="button"  name="cancel" class="button_style_1 btn_float" value="取消" onClick="tinyMCEPopup.close();" />
    <input type="button" onclick="tagLink.insertLink()" name="insert" id="insertlink" class="button_style_1 btn_float" value="替换" />
</div>

<script type="text/javascript">
var tagLink = {
	num : 5,
	max : 5,
	oldTagmap: {},
	tagmap : {},
	links : [],
	addLine : function()
	{
		var t=this,table = $('#matchtag');
		t._appendTr(table);
		$('.delimg').show();
	},
	
	deleteLine : function(obj)
	{
		var t = this;trs = $('#matchtag tr');
		if(trs.length>7)
		{
			$(obj).parents('tr').remove();
			t.num -=1;
		}
		if(trs.length == 8)
		{
			$('.delimg').hide();
		}	
	},
	
	insertLink : function()
	{
		var t = this,
			ed = tinyMCE.activeEditor,
		    edcontent = ed.getContent(),
		    trs = $("#matchtag").find('tr'),
		    titlearr = [],
		    doit = false;
		trs.each(function(){
			var title = $(this).find('input[id^="t"]').val();
			var link = $(this).find('input[id^="c"]').val();
			doit = doit?doit:title;
			if(title && link)
			{
				titlearr.push(title);
				t.tagmap[title] = link;
			}
		});
		if(!doit) return tinyMCEPopup.close();
		edcontent = edcontent.replace(/<a\s[^>]+>[^<]+<\/a>/ig,function(a){
			t.links[t.links.length] = a;
			return '__ct__';
		});
		var regExp = new RegExp(titlearr.join('|'),'g');
		edcontent = edcontent.replace(regExp,function(a,b){
			return '<a href='+t.tagmap[a]+' target="_blank">'+a+'</a>';
		});
		
		var i = 0;
		edcontent = edcontent.replace(/__ct__/g,function(){
			return t.links[i++];
		});
		
		ed.getBody().innerHTML = edcontent;
		
		this._update();
		
	},
	
	_appendTr : function(table)
	{
		var t = this;

		table.find('tr:eq('+t.num+')').after('<tr>\
			<td><input type="text" name="t'+(t.max+1)+'" id="t'+(t.max+1)+'" size="10"/></td>\
			<td>&nbsp;&nbsp;</td>\
			<td><input type="text" name="c'+(t.max+1)+'" id="c'+(t.max+1)+'" size="55" value="http://"/></td>\
		    <td><img src="images/del.gif" class="delimg"></td>\
		    </tr>');
		t.num += 1;
		t.max += 1;
	},
	
	_update : function()
	{
		var t=this,diff = [];
		for(var i in t.tagmap)
		{
			if(typeof t.oldTagmap[i] === 'undefined'){
				diff.push({name:i,value:t.tagmap[i]});
			}
			else if(t.oldTagmap[i]!= t.tagmap[i]){
				diff.push({name:i+'_ctup_',value:t.tagmap[i]});
			}
		}
		if(diff[0])
		{
		$.post('?app=editor&controller=taglink&action=update',diff,function(){        	
			tinyMCEPopup.close();
		});
		}
		else
		{
			tinyMCEPopup.close();
		}
			
	}
	
}

$(function(){
	 $('#t1').focus();
	//即时查询
	var cache = '';
	$('input[name^="t"]').live('keyup',function(){
		var title = this.value;
		var olink= $('#c'+this.id.substr(1));
		if(title!=cache)
		{
			$.get('?app=editor&controller=taglink&action=getlink',{title:title},function(data){
				if(data)
				{
					olink.val(data);
					tagLink.oldTagmap[title] = data;
				}
			})
		}
	})
	$('.delimg').live('click',function(){ 
		tagLink.deleteLine(this);
	});
})


</script>
</body></html>