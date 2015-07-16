(function(){

var Weather = {
	
	level : 0,
	data: {},
	depth: 4,
	
	init: function(areaid){
		this.level = 0;
		this.data = {};
		this.depth= 4;
		this.groups = new Array();
		Weather.build();
		Weather.loadS(areaid,0);
	},
	
	loadS: function(key,level){
		var data = weather_citys[key];
		if(!data) return;
		Weather.data=data.split(",");
		var cities=new Array();
		for(var i=0;i <Weather.data.length;i++){
			cities.push({id:Weather.data[i].split("|")[0],name:Weather.data[i].split("|")[1]});
		}
		
		while(Weather.groups[level].firstChild){
			Weather.groups[level].removeChild(Weather.groups[level].firstChild);
		}
		
		for(var i=0;i<cities.length;i++){
			var node = document.createElement('option');
			node.value = cities[i].id;
			node.innerHTML = cities[i].name;
			$(Weather.groups[level]).append(node);
		}
		if(level<Weather.depth-1){
			Weather.loadS(cities[0].id,level+1);
		}
		
	},
	
	regAction: function(node,level){
		$(node).change(function(){
			Weather.loadS(node.value,level+1);
		});
	},
	
	groups: new Array(),
	
	build: function(){
		for(var i=0;i<Weather.depth;i++){
			var flag=document.createElement("select");
			flag.className="city"+(i+1);
			flag.name="data[city"+(i+1)+"]";
			Weather.regAction(flag,i);
			flag.style.width="70px";
			flag.style.height="23px";
			flag.style.margin="0 5px 0 0";
			Weather.groups.push(flag);
			$("#city").append(flag);
		}
		Weather.groups[Weather.depth-1].style.display="none";
	}
}
function weather_makecode(form) {
	var style = form.find("input[name='data[style]']:checked").val();
	var width = form.find("input[name='data[width]']").val();
	var height = form.find("input[name='data[height]']").val();
	var citycode = form.find("select[name='data[city4]']").text();
	var citycodehtml = '';
	if(citycode !=='0') citycodehtml = '?id='+citycode+'T';
	var str = '<iframe src="http://m.weather.com.cn/m/'+style+'/weather.htm'+citycodehtml+'" width="'+width+'" height="'+height+'" marginwidth="0" marginheight="0" hspace="0" vspace="0" frameborder="0" scrolling="no"></iframe>';
	form.find('textarea').val(str);
}

DIY.registerEngine('weather', {
	//dialogWidth : 600,
	addFormReady:function(form, dialog) {
		Weather.init("00");
		form.find('input,select').change(function(){
			weather_makecode(form);
		});
		var width = form.find("input[name='data[width]']");
		var height = form.find("input[name='data[height]']");
		width.val('100%');
		height.val(20);
		form.find("input[name='data[style]']").click(function(){
			var type = form.find("input[name='data[style]']:checked").val();
			if(type == 'pn7') {
				width.val('100%');
				height.val(20);
			} else if (type == 'pn12'){
				width.val('100%');
				height.val(110);
			}
			weather_makecode(form);
		});
		weather_makecode(form);
	},
	editFormReady:function(form, dialog) {
		Weather.init("00");
		//获取到配置的城市code
		var city1 = form.find("input[name='city1']").val();
		var city2 = form.find("input[name='city2']").val();
		var city3 = form.find("input[name='city3']").val();
		$(".city1").val(city1).change();
		$(".city2").val(city2).change();
		$(".city3").val(city3).change();
		form.find('input,select').change(function(){
			weather_makecode(form);
		});
		form.find("input[name='data[style]']").click(function(){
			var type = form.find("input[name='data[style]']:checked").val();
			var width = form.find("input[name='data[width]']");
			var height = form.find("input[name='data[height]']");
			if(type == 'pn7') {
				width.val('100%');
				height.val(20);
			} else if (type == 'pn12'){
				width.val('100%');
				height.val(110);
			}
			weather_makecode(form);
		});
		weather_makecode(form);
	},
	afterRender: function(widget) { 
		
	},
	beforeSubmit:function(form, dialog){
		
	},
	afterSubmit:function(form, dialog){}
});

})()