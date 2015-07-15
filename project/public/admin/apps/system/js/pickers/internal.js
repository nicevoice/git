PICKER.ready = function(form){
	function query(e){
			PICKER.query(form);
			return false;
	}
	var modelid = form[0].modelid;
	modelid && modelid.nodeName == 'SELECT' && $(modelid).modelset().bind('changed',query);
	form[0].catid && $(form[0].catid).selectree().bind('changed',query);
	form[0].keywords && $(form[0].keywords).keyup(query);
	PICKER.query(form);
};