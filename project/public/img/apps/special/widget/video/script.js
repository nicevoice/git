(function(){

function _init(form, dialog) {
	var $video_input = $('#data_video');
	$('#filebtn_flash').click(function(){
		ct.fileManager(function(at){
			var file = at.src;
			$video_input.val(UPLOAD_URL+file);
		}, 'swf');
	});
	$('#video_select').click(function(){
		$.datapicker({
			multiple:false,
			picked:function(items){
				var contentid = items[0].contentid;
				$.getJSON(
					'?app=special&controller=online&action=video_getvideo',
					{'contentid':contentid},
					function(json) {
						if(json.state) {
							$video_input.val(json.data);
						} else {
							ct.error(json.error);
						}
					}
				);
			},
			url:'?app=system&controller=picker&action=pick&pickid=internal&modelid=4'
		});
	});
}
var replace = function() {
		this.find('object,embed,#ctvideo').each(function(){
            var self = $(this),
	            width = parseInt(self.attr('width')) || self.width(),
	            height = parseInt(self.attr('height')) || self.height(),
                parent = self.parent();
            self.remove();
            parent.append('<img src="'+IMG_URL+'apps/special/widget/video/video.jpg" width="' + width + '" height="' + height + '" style="border:none;"/>');
        });
	};
DIY.registerEngine('video', {
	//dialogWidth : 540,
	addFormReady:function(form, dialog) { _init(form, dialog);},
	editFormReady:function(form, dialog) { _init(form, dialog);},
	afterRender: function(widget) {
        var content = this.content,
            html = content.html();
		// 如果是 script document write 的话，此处内容可能为空，假定脚本会自动正确初始化
		if (! html || html.indexOf('<script>') > -1) {
			content.unbind('change.ctVideoServer').bind('change.ctVideoServer', function() {
				replace.apply(content);
			});
		} else {
            replace.apply(content);
        }
	},
	beforeSubmit:function(form, dialog){
		var content = this.content,
            video = form.find('[name=data\[video\]]').val(),
            manual;
		if (video.indexOf('[ctvideo]') === 0 && content && content.length) {
            manual = form.find('[name=data\[manual\]]');
            ! manual.length && (manual = $('<input type="hidden" name="data[manual]" value="" />').appendTo(form));
            manual.val(encodeURIComponent(content.attr('id')));
		}
    },
	afterSubmit:function(form, dialog){}
});

})();