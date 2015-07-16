window.fet && fet.register({
	'admin.editor':{
		assets:'tiny_mce/tiny_mce.js tiny_mce/editor.js',
		test:'window.tinyMCE',
		depends:'lib.jQuery admin.ImageEditor'
	},
    'admin.ImageEditor':{
        assets:'imageEditor/cmstop.imageEditor.js',
        test:'window.ImageEditor'
    },
	'admin.uploader':{
		assets:'uploader/cmstop.uploader.js',
		test:'$.uploader',
		depends:'lib.jQuery'
	},
	'admin.tabview':{
		assets:'js/cmstop.tabview.js',
		test:'$.tabview',
		depends:'lib.jQuery'
	},
	'admin.tabnav':{
		assets:'js/cmstop.tabnav.js',
		test:'$.fn.tabnav',
		depends:'lib.jQuery'
	},
	'admin.superAssoc':{
		assets:'css/backend.css js/cmstop.superassoc.js',
		test:'window.superAssoc',
		depends:'lib.jQuery lib.tree admin.tabview'
	},
	'admin.fileManager':{
		assets:'js/cmstop.filemanager.js',
		depends:'lib.jQuery lib.dialog cmstop admin.ImageEditor admin.uploader'
	},
	'admin.datapicker':{
		assets:'js/datepicker/WdatePicker.js'
	}
});