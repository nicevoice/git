var $regexs = {
	email : /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/,
	telephone : /^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/,
	mobile : /^((\(\d{2,3}\))|(\d{3}\-))?((13\d{9})|(15[389]\d{8}))$/,
	homepage : /^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"])*$/,
	qq : /^[1-9]\d{4,8}$/,
	msn : /^[1-9]\d{4,8}$/
};

function validateGuesetBook(jQ) {
	var inputs = jQ.find('input,textarea');
	var name;
	var counter = inputs.length+1;
	inputs.each(function(){
		name = this.name;
		var d = $(this);
		var value = d.val()
		if(d.hasClass('required') && value == '') {
			d.siblings('.info').html('必须填写');
			counter--;
		} else if(value != '' && $regexs[name] != undefined) {
			if(!$regexs[name].test(value)) {
				d.siblings('.info').html(name+'格式不正确');
				counter--;
			}
		} else if(name == 'seccode') {
			//验证码
			$.getJSON(
				'?app=guestbook&controller=guestbook&action=seccode&seccode='+value,
				function(json){
					if(json.state) {
						
					} else {
						d.siblings('.info').html('验证码不正确');
						counter--;
					}
				}
			); 
		}
		if(name == 'content') {
			if(parseInt($('#content_length > b').html()) > maxText) {
				$('#content_length').siblings('.info').html('留言过长');
				counter--;
			}
		}
	});
	
	if(counter < inputs.length+1) return false;
	else return true;
}