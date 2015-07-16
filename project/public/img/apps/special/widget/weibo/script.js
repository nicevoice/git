(function(){

function __replace_tags(template,value) {
	for (var key in value) {
		template = template.replace(new RegExp('{'+key+'}',"gm"), value[key]);
	}
	return template;
}

var func = {
    sina_tags: function() {
        var code = 'http://v.t.sina.com.cn/widget/widget_topic_publish.php?tags={tags}&skin={skin}&isShowPic={isShowPic}&width={width}&height={height}&wordLength={wordLength}&mblogNum={mblogNum}';
        var option = {
            tags : encodeURI($("input[name='sina[tags]']").val()),
            skin : $("input[name='sina[skin]']").val() || 1,
            isShowPic : $("input[name='sina[isShowPic]']").val() || 1,
            width : $("input[name='sina[width]']").val() || '100%',
            height : $("input[name='sina[height]']").val() || 500,
            wordLength : $("input[name='sina[wordLength]']").val(),
            mblogNum : $("input[name='sina[mblogNum]']").val() || 10
        };
        var url = __replace_tags(code,option);
        var iframe_height = 250+parseInt(option.height);
        $("textarea[name='data[code]']").val('<iframe frameborder="0" width="'+option.width+'" height="'+iframe_height+'"scrolling="no" style="border: 0px none;" src="'+url+'"></iframe>');
    },
    sina_show: function() {
        $("textarea[name='data[code]']").val('');
    },
    qq_tags: function() {
        $("textarea[name='data[code]']").val('');
    },
    qq_show: function() {
        $("textarea[name='data[code]']").val('');
    }
};

function __get_url(type, provider) {
	var __url = {
		live : {
			sina : 'http://open.t.sina.com.cn/wiki/index.php/%E5%BE%AE%E5%8D%9Awidget',
			qq : 'http://open.t.qq.com/apps/wall/explain.php'
		},
		show : {
			sina : 'http://t.sina.com.cn/plugins/WeiboShow.php',
			qq : 'http://open.t.qq.com/apps/show/explain2.php'
		}
	};
	var weibo_args = $('#weibo_args');
    var show_args = $('#show_args');
    var current_args = type == 'live' ? weibo_args : show_args;
    var current_func = provider + '_' + (type == 'live' ? 'tags' : 'show');
    if (type == 'live') {
        current_args = weibo_args;
        show_args.hide();
    } else {
        current_args = show_args;
        weibo_args.hide();
    }
	if(provider == 'sina') {
    //新浪微博直播的生成
        current_args.show();
        current_args.find("tbody[rel!='sina']").hide();
        current_args.find("tbody[rel='sina']").show();
    } else if (provider == 'qq') {
        //腾讯直播
        current_args.show();
        current_args.find("tbody[rel!='qq']").hide();
        current_args.find("tbody[rel='qq']").show();
    }
    func[current_func] && func[current_func]();
	$('#weibo_generator').html('<a href="'+__url[type][provider]+'" target="_blank">'+__url[type][provider].substr(0,50)+'...</a>');
}

function __init(form,dialog) {
	var type = form.find("input[name='data[type]']");
	var provider = form.find("select[name='data\[provider\]']");
	type.click(function(){
		__get_url(this.value,provider.val());
	});
	provider.change(function(){
		__get_url(type.filter(':checked').val(),provider.val());
	});
	form.find("input[name^='sina']").keyup(function(){
		func.sina_tags();
	});
	__get_url(type.filter(':checked').val(),provider.val());
}



DIY.registerEngine('weibo', {
	//dialogWidth : 600,
	addFormReady:function(form, dialog) {
		__init(form,dialog);
	},
	editFormReady:function(form, dialog) { 
		__init(form,dialog);
	},
	afterRender: function(widget) { },
	beforeSubmit:function(form, dialog){
		if(form.find('textarea').val() == '') {
			ct.error('请输入调用代码');
			return false;
		}
	},
	afterSubmit:function(form, dialog){}
});

})();