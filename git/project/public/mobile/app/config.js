 /**
  * $.GlobalConfig
  * @param {Object} $
  */
 ( function ( $ ) {
 	
	$.GC = $.GlobalConfig = {};
	$.GC.BaseUrl = 'http://'+ location.hostname +'/';
	$.GC.DataUrl = $.GC.BaseUrl + 'index.php';
	$.GC.AddScreenIco = $.GC.BaseUrl+'/ui/css/ui/addscreen.png';
	$.GC.LoadingText = '数据加载中...'; 	
	
 })( Zepto );
 
 
