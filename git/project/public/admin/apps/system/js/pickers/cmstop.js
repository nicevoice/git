PICKER.ready = function(form){
	function query(e){
		PICKER.query(form);
		return false;
	}
	form[0].modelid && $(form[0].modelid).modelset().bind('changed',query);
	form[0].catid && $(form[0].catid).selectree().bind('changed',query);
	form[0].keywords && $(form[0].keywords).keyup(query);
	PICKER.query(form);
};