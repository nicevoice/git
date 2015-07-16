var field = {
	getbycid: function (contentid, catid)
	{
		if(!contentid) return;
		var _this = this;
		$.getJSON('?app=field&controller=project&action=get_editfield_api&contentid='+contentid+'&catid='+catid, function(response){
			var html = _this.html(response.data);
			_this._state(response.data, html);
		});
	},

	get: function (catid)
	{
		$('#field').show();
		var _this = this;
		$.getJSON('?app=field&controller=project&action=get_field_api&catid='+catid, function(response){
//                    alert(response.ext.zoneid);
			var html = _this.html(response.data);
			_this._state(response.data, html);
                        if(response.ext.typeid>0){//有扩展属性设置
                            $('#typeid_selector').html('<input id="'+$("#typeid_init_id").val()+'" ending="1" width="'+$("#typeid_init_width").val()+'" class="selectree" name="'+$("#typeid_init_name").val()+'" initurl="?app=system&controller=property&action=name&proid=%s" url="?app=system&controller=property&action=cate&issingle=1&isonly='+response.ext.typeid+'&proid=%s" paramVal="proid" paramTxt="name" />');
                            $('#'+$("#typeid_init_id").val()).selectree();
                            $('#typeid_span').show();
                        }else{//无地区设置
                            $('#typeid_span').hide();
                        }
                        if(response.ext.subtypeid>0){//有子扩展属性设置
                            $('#subtypeid_selector').html('<input id="'+$("#subtypeid_init_id").val()+'" ending="1" width="'+$("#subtypeid_init_width").val()+'" class="selectree" name="'+$("#subtypeid_init_name").val()+'" initurl="?app=system&controller=property&action=name&proid=%s" url="?app=system&controller=property&action=cate&issingle=1&isonly='+response.ext.subtypeid+'&proid=%s" paramVal="proid" paramTxt="name" />');
                            $('#'+$("#subtypeid_init_id").val()).selectree();
                            $('#subtypeid_span').show();
                        }else{//无地区设置
                            $('#subtypeid_span').hide();
                        }                        
                        //地区已存 $("#zoneid_init_value").val()
                        if(response.ext.zoneid>0){//有地区设置
                            $('#zoneid_selector').html('<input id="'+$("#zoneid_init_id").val()+'" ending="1" width="'+$("#zoneid_init_width").val()+'" class="selectree" name="'+$("#zoneid_init_name").val()+'" initurl="?app=system&controller=property&action=name&proid=%s" url="?app=system&controller=property&action=cate&issingle=1&isonly='+response.ext.zoneid+'&proid=%s" paramVal="proid" paramTxt="name" />');
                            $('#'+$("#zoneid_init_id").val()).selectree();
                            $('#zoneid_span').show();
                        }else{//无地区设置
                            $('#zoneid_span').hide();
                        }
                        
		});
	},

	html: function(datas)
	{
		var html = '';
		if(datas && datas.length > 0)
		{
			$.each(datas, function(key, data) {
				html += '<tr id='+data.fieldid+'>\
							<th width="100">'+data.name+'：</th>\
							<td>'+data.field+'</td>\
						</tr>';
			});
		}
		return html;
	},

	expand:function(obj)
	{
		$("#"+obj+"_c").toggle();
	},

	_state:function(data, html)
	{
		if(data && data.length > 0)
		{
			$('#field').show();
			$('#field_c').show().html(html);
		}
		else
		{
			$('#field').hide();
			$('#field_c').empty().hide();
		}
	}
};