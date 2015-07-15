(function(){

function setPoint(x, y) {
	$('#map_x').val(x);
	$('#map_y').val(y);
}
function setZoom(zoom) {
    $('#map_zoom').val(zoom);
}
DIY.registerEngine('map', {
	dialogWidth : 600,
	addFormReady:function(form, dialog) {
        var lng = 116.404;
        var lat = 39.915;
        var zoom = 12;
        setPoint(lng, lat);
        setZoom(zoom);
        fet('net.BMap', function(){
        	var map = new BMap.Map("map_container"),
	            point = new BMap.Point(lng, lat),
	            marker = new BMap.Marker(point);
			map.enableScrollWheelZoom();
			map.addControl(new BMap.NavigationControl());
			map.addEventListener('zoomend', function(){
			   setZoom(map.getZoom()); 
			});
			marker.enableDragging();
			marker.addEventListener('dragend', function(e){
			    var p = e.point;
			    setPoint(p.lng, p.lat);
				map.panTo(p);
			});
			map.addOverlay(marker);
			map.addEventListener('click', function(e){
			    var p = e.point;
				setPoint(p.lng, p.lat);
				marker.setPoint(p);
				map.panTo(p);
			});
			setTimeout(function(){
				map.centerAndZoom(point, zoom);
			}, 200);
        });
	},
	editFormReady:function(form, dialog) {
		var lng = $('#map_x').val() || 116.404;
		var lat = $('#map_y').val() || 39.915;
		var zoom = $('#map_zoom').val() || 12;
		fet('net.BMap', function(){
			var map = new BMap.Map("map_container"),
	            point = new BMap.Point(lng, lat),
	            marker = new BMap.Marker(point);
			map.enableScrollWheelZoom();
			map.addControl(new BMap.NavigationControl());
			marker.enableDragging();
			map.addEventListener('zoomend', function(){
			   setZoom(map.getZoom()); 
			});
			marker.addEventListener('dragend', function(e){
			    var p = e.point;
			    setPoint(p.lng, p.lat);
			    map.panTo(p);
			});
			map.addOverlay(marker);
			map.addEventListener('click', function(e){
			    var p = e.point;
				setPoint(p.lng, p.lat);
				marker.setPoint(p);
				map.panTo(p);
			});
			setTimeout(function(){
				map.centerAndZoom(point, zoom);
			}, 200);
		});
	},
	afterRender: function(widget) {},
	beforeSubmit:function(form, dialog){},
	afterSubmit:function(form, dialog){}
});


})()