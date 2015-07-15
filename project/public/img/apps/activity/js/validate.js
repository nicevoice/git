var _regexp = {
		email: /^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/,
		any:/(.*)/,
		identity:/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}[\d|x]$/,
		url:/http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/,
		username: /^[a-z]\w{3,19}$/i,
		password:/^[^\s\$]{6,20}$/,
		telephone: /^(86)?(\d{3,4}-)?(\d{7,8})$/,
		mobile: /^1\d{10}$/,
		ip: /^((2[0-4]\d|25[0-5]|[01]?\d\d?)\.){3}(2[0-4]\d|25[0-5]|[01]?\d\d?)$/,
		id:/^(?:\d{14}|\d{17})[\dxX]$/,
		qq: /^[1-9]\d{4,20}$/,
		date:/^(\d{4})(-|\/)(\d{1,2})\2(\d{1,2})$/,
		datetime:/^(\d{4})(-|\/)(\d{1,2})\2(\d{1,2})\s(\d{1,2}):(\d{1,2}):(\d{1,2})$/,
		zipcode: /^[0-9]\d{5}$/,
		currency: /^\d+(\.\d+)?$/,
		number: /^\d+$/,
		english: /^[A-Za-z]+$/,
		chinese: /^[\u4e00-\u9fa5]+$/,
		integer: /^[-\+]?\d+$/,
		'float': /^[-\+]?\d+(\.\d+)?$/
};
$(function(){
$('#signform').submit(function(){
	if(!validate(_regexp.any,$('#name'),50)) return false;
	if(!validate(_regexp.any,$('#man'))) return false;
	if(!validate(_regexp.any,$('#photo'))) return false;
	if(!validate(_regexp.email,$('#email'))) return false;
	if(!validate(_regexp.any,$('#address'),100)) return false;
	if(!validate(_regexp.telephone,$('#telephone'))) return false;
	if(!validate(_regexp.mobile,$('#mobile'))) return false;
	if(!validate(_regexp.zipcode,$('#zipcode'))) return false;
	if(!validate(_regexp.qq,$('#qq'))) return false;
	if(!validate(_regexp.email,$('#msn'))) return false;
	if(!validate(_regexp.url,$('#site'))) return false;
	if(!validate(_regexp.any,$('#job'),50)) return false;
	if(!validate(_regexp.identity,$('#identity'))) return false;
	if(!validate(_regexp.any,$('#company'),100)) return false;
	if(!validate(_regexp.any,$('#aid'))) return false;
	if(!validate(_regexp.any,$('#note'))) return false;
});
});
function validate(reg,obj,maxlength){   
	if(typeof obj[0] == 'undefined') return true;
	var oparent  = obj.parent();
	var keyword = oparent.prev().find('label').html();
	if(obj.is(':radio') && oparent.children('input:radio:checked')[0] == undefined)
	{ 
		alert(keyword+'必填');
		obj.focus();
        return false;
	}
	if(obj.val() == '' && oparent.hasClass('need')) 
	{
		alert(keyword+'不得为空');
        obj.focus();
        return false;
	}
	if(arguments[2])
	{
	   if(obj.val().length>maxlength) 
	   {
		 alert(keyword+'不得超过'+maxlength+'个字符');
		 obj.focus();
		 return false;
	   }
	}
	if(obj.val())
	{
		if(!reg.test(obj.val()))
		{
			alert(keyword+'格式不正确');
			obj.focus();
			return false;
		}
	}
	return true;
}
