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
		var _this = this;
		$.getJSON('?app=field&controller=project&action=get_field_api&catid='+catid, function(response){
			var html = _this.html(response.data);
			_this._state(response.data, html);
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