var i = 0;

var guest =
{
	init: function ()
	{
		$('#guests>div').find("ul>li").show();
		$('#guests>div:first-child').find("ul>li:nth-child(2)").hide();
		$('#guests>div:last-child').find("ul>li:nth-child(3)").hide();
	},
	
	add: function (name, color, photo, aid, url, resume, id)
	{
		i++;
		if (typeof name === 'undefined') name = '';
		if (typeof color === 'undefined') color = '';
		if (typeof photo === 'undefined' || photo=='')
		{
			photo = '';
			image = 'images/guest.gif';
		}
		else
		{
			image = UPLOAD_URL+photo;
		}
		if (typeof aid === 'undefined') aid = '';
		if (typeof url === 'undefined') url = '';
		if (typeof resume === 'undefined') resume = '';
		if (typeof id === 'undefined') id = '';
		
		var html = '<div>\
			         <table id="guest_'+i+'" width="100%" border="0" cellspacing="0" cellpadding="0">\
			           <tr>\
			             <th border="0" cellspacing="0" cellpadding="0" valign="top"><input type="hidden" name="guest['+i+'][guestid]" id="guestid_'+i+'" value="'+id+'"/></th>\
			             <td>\
			          <table border="0" cellspacing="0" cellpadding="0" style="margin-top:7px;">\
			            <tr>\
			              <td width="120" valign="top" >\
			              <div style="position:relative">\
			              <div style="position:absolute;background:#000000;opacity:0.5;filter:alpha(opacity=50);width:112px;height:20px;left:1px;top:92px;">\
			              <span id="uploadphoto'+i+'" style="display:inline-block;color:#FFFFFF;opacity:1;text-align:center;width:55px;float:left">上传</span><span style="color:#FFFFFF;opacity:1;text-align:center;width:60px;cursor:pointer;display:'+(photo?'':'none')+';width:55px;float:right" onclick="guest.photo_edit('+i+');">裁剪</span>\
			              </div>\
			              <a href="'+image+'" id="thinkbox_'+i+'" '+(photo?'':'onclick="return false;"')+' title="image" class="imagebox" target="_blank"><img src="'+image+'" width="110" height="107" style="margin-top:2px" class="pic" id="head_'+i+'" /></a>\
			              </div>\
			              <input type="hidden" name="guest['+i+'][photo]" id="photo_'+i+'" value="'+photo+'"/><input type="hidden" name="guest['+i+'][aid]" id="aid_'+i+'" value="'+aid+'"/></td>\
			              <td width="520px">\
			                  <table border="0" cellspacing="0" cellpadding="0">\
				                 <tr>\
				                     <td>\
					                     <table border="0" cellspacing="0" cellpadding="0">\
						                 <td width="150" height="25"><input type="text" name="guest['+i+'][name]" id="name_'+i+'" value="'+name+'" size="15" maxlength="30" title="嘉宾姓名" placeholder="嘉宾姓名" style="color:'+color+'" uncount="1"/> <input type="hidden" name="guest['+i+'][color]" id="color_'+i+'" value="'+color+'"/><img src="images/color.gif" id="title_cp'+i+'" class="hand" onclick="$(this).colorPicker({setColor:\'#name_'+i+'\', setValue:\'#color_'+i+'\'});" align="absmiddle"/></td>\
					                     </table>\
					                 </td>\
					             </tr>\
				                 <tr height="25">\
				                	<td><input type="text" name="guest['+i+'][url]" id="url_'+i+'" value="'+url+'" maxlength="80" title="嘉宾介绍网址" placeholder="嘉宾介绍网址" style="width:500px;" uncount="1"/></td>\
				                 </tr>\
				                 <tr>\
				                 	<td><textarea name="guest['+i+'][resume]" id="resume_'+i+'" cols="80" rows="4" class="c_gray mar_5" style="width:500px;height:50px" title="嘉宾介绍" placeholder="嘉宾介绍">'+resume+'</textarea></td>\
				                 </tr>\
			                 </table>\
			                 </td>\
			              <td>\
			                <ul>\
			                  <li style="width:18px"><span onclick="guest.remove('+i+')" style="cursor:pointer"><img src="images/del.gif" width="16" height="16" alt="删除" /></span></li>\
			                  <li style="width:18px"><span onclick="guest.up('+i+')" style="cursor:pointer"><img src="images/up.gif" width="16" height="16" alt="向上" /></span></li>\
			                  <li style="width:18px"><span onclick="guest.down('+i+')" style="cursor:pointer"><img src="images/down.gif" width="16" height="16" alt="向下"  /></span></li>\
			                </ul>\
			              </td>\
			            </tr>\
			           </table>\
			          </td>\
			         </tr>\
			       </table>\
		          </div>';
		$('#guests').append(html);
		guest.init();	    
		guest.photo_upload(i);
		photo && $('#thinkbox_'+i).lightBox();
	},
	
	up: function (i)
	{
		var obj = $('#guest_'+i).parent();
		if (obj.prev().is('div'))
		{
			obj.insertBefore(obj.prev());
			guest.init();
		}
	},
	
	down: function (i)
	{
		var obj = $('#guest_'+i).parent();
		if (obj.next().is('div'))
		{
			obj.insertAfter(obj.next());
			guest.init();
		}
	},
	
	remove: function (i)
	{
		$('#guest_'+i).parent().remove();
		guest.init();
	},

	photo_upload: function (n)
	{
		var t = this;

		$("#uploadphoto"+n).uploader({
			script         : '?app=interview&controller=interview&action=upload',
			fileDesc		 : '图片',
			fileExt		 : '*.jpg;*.jpeg;*.gif;*.png;',
			multi          : false,
			complete:function(response,data){
				if(response != 0) {
					var img = response.split('|');
					var aid = img[0];
					var img = img[1];
					$("#head_"+n).attr('src', UPLOAD_URL+img);
					$("#thinkbox_"+n).attr('href', UPLOAD_URL+img).lightBox();
					$("#photo_"+n).val(img);
					$("#aid_"+n).val(aid);
					$("#uploadphoto"+n).next().show();
				} else {
					ct.error('对不起！您上传文件过大而失败!');
				}
			},
			error:function(data) {
				alert(data.error.type);
			}
		}).next().andSelf().hover(function(){
			this.style.color = '#FFFF00';
			this.style.textDecoration = 'underline';
		},function(){this.style.color ='#FFFFFF';this.style.textDecoration = 'none'});
		return;
	},
	
	photo_edit : function(n)
	{
		ct.editImage($("#photo_"+n).val(),function(json){
			$("#photo_"+n).val(json.file);
			$("#head_"+n).attr('src', UPLOAD_URL+json.file+'?'+Math.random());
			$("#thinkbox_"+n).attr('href', UPLOAD_URL+json.file+'?'+Math.random());
		})
	}
}