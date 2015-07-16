(function(){
var replace = function() {
		this.find('object,embed').each(function(){
            var self = $(this),
	            width = parseInt(self.attr('width')) || self.width(),
	            height = parseInt(self.attr('height')) || self.height(),
                parent = self.parent();
            self.remove();
            parent.append('<img src="'+IMG_URL+'apps/special/widget/live/live.jpg" width="' + width + '" height="' + height + '" style="border:none;"/>');
        });
	};
DIY.registerEngine('live', {
    dialogWidth:400,
    addFormReady:function(form, dialog) {
    },
    editFormReady:function(form, dialog) {
    },
    afterRender: function(widget) {
        replace.apply(this.content);
    },
    beforeSubmit:function(form, dialog) {},
    afterSubmit:function(form, dialog) {}
});
})();