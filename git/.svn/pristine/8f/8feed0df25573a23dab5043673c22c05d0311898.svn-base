//修改导航,data是对象数组,格式[{url: url, text: text}]
ct.nav = function (data)
{
	if(top.$('#position').length < 1) return false;
	top.$('#position').empty();
	var action = function ()
	{
		var url = $(this).attr('url');
		if(url != 'javascript:;' && url) {
			top.superAssoc.open(url);
		}
	}
	for(k in data)
	{
		if(!data[k].url) data[k].url = '';
		if(!data[k].text) continue;
		top.$('#position').append('<a href="javascript:;">' + data[k].text + '</a>');
		top.$('#position a:last').attr('url', data[k].url).click(action);
	}
	top.$('#position a:last').css({background: 'none', color: '#000', textDecoration: 'none', cursor: 'text'});
}