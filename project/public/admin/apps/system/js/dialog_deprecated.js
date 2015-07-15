// JavaScript Document
function dialog(dialog_type, settings, callback_ok, callback_cancel){
	dialog_id = settings.dialog_id == null ? 'dialog' : settings.dialog_id;
	type = settings.type == null ? 'html' : settings.type;
	s_width = settings.width == null ? 400 : settings.width;
	s_height = settings.height == null ? 350 : settings.height;
	s_maxHeight = settings.maxHeight == null ? 600 : settings.maxHeight;
	s_maxWidth = settings.maxWidth == null ? 600 : settings.maxWidth;
	s_modal = !!settings.modal;
	s_autoOpen = settings.autoOpen == null ? false : settings.autoOpen;
	
	//绑定dialog的onresize事件
	_onresize = settings.onresize ? settings.onresize: function() {};
	if (dialog_type == 'iframe' && settings.full)
	{
		_onresize = function()
		{
			$("#"+dialog_id).find('iframe').height($("#"+dialog_id).height());
		}
	}
	ok_text = settings.ok_text || '确定';
	cancel_text = settings.cancel_text || '取消';
	
	//ok button callback function
	var cbfunc_ok = (typeof callback_ok == "function")?callback_ok:eval('('+callback_ok+')');

	if($("#"+dialog_id).length == 0){
		//crete the dialog div if it not exist
		$("<div id='"+dialog_id+"' title=''></div>").appendTo('body');
	}
	
	if(dialog_type == 'choose'){
		var cbfunc_cancel = (typeof callback_cancel == "function")? callback_cancel:eval('('+callback_cancel+')');//cancel button callback function
		buttons = {};
		buttons[ok_text] = function(){dialog_obj = $(this); cbfunc_ok(); };
		buttons[cancel_text] = function(){cbfunc_cancel($(this));$(this).dialog("close");};
		$("#"+dialog_id).dialog({
			autoOpen: s_autoOpen,
			bgiframe: true,
			closeOnEscape:true,
			draggable: true,
			maxHeight: s_maxHeight,
			maxWidth: s_maxWidth,
			width : s_width,
			height: s_height,
			modal: s_modal,
			stack: true,
			close: cbfunc_cancel,
			resize:_onresize,
			title:settings.title,
			buttons: buttons
		});	
	} else if(dialog_type == 'iframe'){
		$("#"+dialog_id).dialog({
			autoOpen: s_autoOpen,
			bgiframe: true,
			closeOnEscape:true,
			draggable: true,
			//resizable : false,
			resize:_onresize,
			resizable:settings.resizable?settings.resizable:true,
			maxHeight: s_maxHeight,
			maxWidth: s_maxWidth,
			width : s_width,
			height: s_height,
			modal: s_modal,
			stack: true,
			title:settings.title,
			buttons: {}
		});	
	}else {
		buttons = {};
		buttons[ok_text] = function(){$(this).dialog("close");cbfunc_ok(dialog_id); };
		$("#"+dialog_id).dialog({
			autoOpen: s_autoOpen,
			bgiframe: true,
			closeOnEscape:true,
			draggable: true,
			maxHeight: s_maxHeight,
			maxWidth: s_maxWidth,
			width : s_width,
			height: s_height,
			modal: s_modal,
			resize:_onresize,
			stack: true,
			title:settings.title,
			buttons: buttons
		});	
		$("#"+dialog_id).css('overflow', 'auto');
		$("#"+dialog_id).css('overflow-y','auto');
		$("#"+dialog_id).css('overflow-x','hidden');
	}
	
	if(type == 'html') {
		$("#"+dialog_id).html('<div class="bk_5"></div>'+settings.content);
		var inputs = $("#"+dialog_id+" input[type='text']");
		if(inputs.length){
			$(inputs[0]).focus();
		}
	} else if(type == 'ajax'){
		$.get(settings.content, function(data){
			$("#"+dialog_id).html(data);
			var inputs = $("#"+dialog_id+" input[type='text']");
			if(inputs.length){
				//setTimeout('', 500);
				$(inputs[0]).focus(function(){this.focus();return true;});//fixed ie bug
				setTimeout('', 0);//fixed ie bug
				$(inputs[0]).focus();
				vali_form();
				//alert($(inputs[0]).attr('name'));
			}
		});
	} else if(type == 'iframe'){
		
		$("#"+dialog_id).html('<iframe frameborder="0" scrolling="auto" src="'+settings.content+'" width="100%" height="'+(settings.height-30)+'" ></iframe>');
	}
	
	$("#"+dialog_id).dialog('open');
	$("#"+dialog_id).css('padding', '4px').css('margin-top', '-9px');
	if(type == 'iframe')
	{
		$("#"+dialog_id).css('padding', '0px');
		if(dialog_id == 'template_select')
			$("#template_select").css('width', '600px');
		if (settings.full) $("#"+dialog_id).find('iframe').height($("#"+dialog_id).height());
	}
	return false;
}

function ct_confirm(msg, cb_ok, cb_cancel,dialog_id){
	if (!dialog_id) dialog_id = 'ct_dialog_confirm';
	if($("#"+dialog_id).length == 0){
		$("<div id='"+dialog_id+"' title=''></div>").appendTo('body');
	}
	var ret = '';
	typeof cb_ok == 'function' || (cb_ok = window[cb_ok]);
	typeof cb_cancel == 'function' || (cb_cancel = window[cb_cancel]);
	
	$("#"+dialog_id).dialog({
		autoOpen: false,
		bgiframe: true,
		closeOnEscape:true,
		draggable: true,
		width : 320,
		//height: 250,
		modal: false,
		stack: true,
		title:'提示信息',
		buttons: { "确定": function() { $(this).dialog("close");cb_ok(1111);return true; },'取消':function(){$(this).dialog("close"); cb_cancel(); return false;} }
	});	
	$("#"+dialog_id).html('<div class="bk_5"></div><p class="ct_confirm"><sup></sup>'+msg+'</p>');
	$("#"+dialog_id).dialog('open');
}


function ct_confirm_picture(msg, cb_ok, cb_cancel,dialog_id){
	if (!dialog_id) dialog_id = 'ct_dialog_confirm_picture';
	if($("#"+dialog_id).length == 0){
		$("<div id='"+dialog_id+"' title=''></div>").appendTo('body');
	}
	
	var ret = '';
	var cb_ok = eval('('+cb_ok+')');
	var cb_cancel = eval('('+cb_cancel+')');
	
	$("#"+dialog_id).dialog({
		autoOpen: false,
		bgiframe: true,
		closeOnEscape:true,
		draggable: true,
		width : 420,
		//height: 250,
		modal: true,
		stack: true,
		title:'提示信息',
		buttons: { "发布新组图": function() { $(this).dialog("close");cb_ok();return true; },'退出发布界面':function(){$("#ct_dialog_confirm_picture").dialog("close");cb_cancel(); return false;} }
	});	
	$("#"+dialog_id).html('<div class="bk_5"></div><p class="ct_ok"><sup></sup>'+msg+'</p>');
	$("#"+dialog_id).dialog('open');
}

function ct_confirm_interview(msg, cb_ok, cb_cancel,dialog_id){
	if (!dialog_id) dialog_id = 'ct_dialog_confirm_picture';
	if($("#"+dialog_id).length == 0){
		$("<div id='"+dialog_id+"' title=''></div>").appendTo('body');
	}
	
	var ret = '';
	var cb_ok = eval('('+cb_ok+')');
	var cb_cancel = eval('('+cb_cancel+')');
	
	$("#"+dialog_id).dialog({
		autoOpen: false,
		bgiframe: true,
		closeOnEscape:true,
		draggable: true,
		width : 420,
		//height: 250,
		modal: true,
		stack: true,
		title:'提示信息',
		buttons: { "发布新访谈": function() { $(this).dialog("close");cb_ok();return true; },'退出发布界面':function(){$("#ct_dialog_confirm_picture").dialog("close");cb_cancel(); return false;} }
	});	
	$("#"+dialog_id).html('<div class="bk_5"></div><p class="ct_ok"><sup></sup>'+msg+'</p>');
	$("#"+dialog_id).dialog('open');
}



function ct_alert(msg, type,modal){
	type = 'ct_'+(type || 'ok');
	//alert(type);
	dialog_id = 'ct_dialog_alert';
	if($("#"+dialog_id).length == 0){
		//crete the dialog div if it not exist
		$("<div id='"+dialog_id+"' title=''></div>").appendTo('body');
	}
	
	var ret = '';
	$("#"+dialog_id).dialog({
		autoOpen: false,
		bgiframe: true,
		closeOnEscape:true,
		draggable: true,
		width : 320,
		//height: 100,
		modal: (modal == undefined)?true:modal,
		stack: true,
		title:'提示信息',
		buttons: { "确定": function() { $(this).dialog("close");return true; } }
	});	
	
	$("#"+dialog_id).html('<div class="bk_5"></div><p class="'+type+'"><sup></sup>'+msg+'</p>');
	$("#"+dialog_id).dialog('open');
}
function ct_info(msg, type, callback)
{
    type = 'ct_'+(type || 'ok');
	//alert(type);
	dialog_id = 'ct_dialog_info';
	if($("#"+dialog_id).length == 0){
		//crete the dialog div if it not exist
		$("<div id='"+dialog_id+"' title=''></div>").appendTo('body');
	}
	
	var ret = '';
	$("#"+dialog_id).dialog({
		autoOpen: false,
		bgiframe: true,
		closeOnEscape:true,
		draggable: true,
		width : 320,
		//height: 100,
		modal: true,
		stack: true,
		close: callback,
		title:'提示信息',
		buttons: { "确定": function() { $(this).dialog("close");return true; } }
	});	
	
	$("#"+dialog_id).html('<div class="bk_5"></div><p class="'+type+'"><sup></sup>'+msg+'</p>');
	$("#"+dialog_id).dialog('open');
}

function ct_view(url,title)
{
    var dialog = $('#ct_dialog_view');
	if(dialog.length){
		dialog.empty().load(url);
		dialog.dialog('open');
		return;
	}
	var dialog = $("<div id='ct_dialog_view'></div>").appendTo('body');
	dialog.dialog({
		autoOpen: false,
		bgiframe: true,
		closeOnEscape:true,
		draggable: true,
		width : 400,
		height: 480,
		modal: false,
		stack: true,
		title:title||'查看',
		buttons: { "确定": function() { $(this).dialog("close");return true; } }
	});
	dialog.load(url).dialog('open');
}