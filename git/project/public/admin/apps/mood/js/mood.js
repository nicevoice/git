var baseUrl = '?app=mood&controller=mood';
var mood = {
	view : function (modelid, contentid) {
		ct.assoc.open(baseUrl+'&action=view&modelid='+modelid+'&contentid='+contentid, 'newtab');
	},
	add : function() {
		ct.form(
			'添加心情方案',
			baseUrl+'&action=add',
			360,
			180,
			function(json){
				tableApp.addRow(json.data);
				return true;
			},
			function(){return true;}
		);
	},
	edit : function(moodid) {
		ct.form(
			'编辑方案',
			baseUrl+'&action=edit&moodid='+moodid,
			360,
			180,
			function(json){
				json.state
				 ? (ct.ok('修改成功'), tableApp.updateRow(moodid,json.data))
				 : ct.error(json.error);
				return true;
			},
			function(){return true;}
		);
	},
	del : function(moodid) {
		ct.confirm('确认删除？',function(){
			$.post(baseUrl+'&action=delete',{moodid:moodid},function(json){
				json.state
				 ? (ct.ok('删除完毕'), tableApp.deleteRow(moodid))
				 : ct.error(json.error);
			},'json');
		})
	},
	sort_up : function sort_up(moodid){
		 $.post(
			baseUrl+'&action=sort',
			{moodid:moodid,sort:'up'},
			function(data){
				if (data.state){
					tableApp.load();
				}
				else ct.error(data.error);
			}, 
			'json'
		);
	},
	sort_down : function(moodid){
		 $.post(
			baseUrl+'&action=sort',
			{moodid:moodid,sort:'down'},
			function(data){
				if (data.state){
					tableApp.load();
				}
				else ct.error(data.error);
			},
			'json'
		);
	}
}