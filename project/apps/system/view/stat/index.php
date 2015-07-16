<?php $this->display('header','system');?>
<link href="<?=IMG_URL?>js/lib/tree/style.css" rel="stylesheet" type="text/css"/>
<script src="<?=IMG_URL?>js/lib/cmstop.tree.js" type="text/javascript"></script>
<div class="bg_1" id="tree_in" style="margin-top:10px;width:860px">
  <div class="w_160 box_6 mar_r_8 f_l" style="position:relative;width:155px;height:740px;background: #F9FCFD;border-top:1px solid #00C2E6">
    <h3><span class="dis_b b">栏目列表</span></h3>
    <div id="category_" style="position:absolute;z-index:3;"></div>
    
  </div>
  <div class="f_l" style="padding-left:10px;width:680px">
    <div class="bk_10"></div>
    <div style="text-align:right;margin-bottom:5px;width:100%">统计时间：<span id="last_stat_time"></span>&nbsp;&nbsp;<span><a href="javascript:;" onclick="load(1)">重新统计</a></span></div>
    <div id="stat">
	<div id="proids" style="margin-right:0px">
    <dl class="proids" id="year"><dt>年份：</dt><dd>
	 <a href="0">全部</a>
	 <?php 
	 $year = intval(date('Y'));
	 for($y = $year; $y > $year-10; $y--):?>
  		  <a href="<?=$y?>"<?php if($y == $year):?> class="checked"<?php endif;?>><?=$y?>年</a>
  	<?php endfor;?>

	</dd></dl>
	<dl class="proids" id="month"><dt>月份：</dt><dd>
     <a href="0">全部</a>
	 <?php for ($m = 1; $m <= 12; $m++): ?>
  		  <a href="<?=$m?>"<?php if($m == intval(date('n'))):?> class="checked""<?php endif;?>><?=$m?>月</a>
  	<?php endfor;?></dd></dl>
  	
  	<dl class="proids" id="model"><dt>模型：</dt><dd>
  	 <a href="0" class="checked">全部</a>
	 <?php 
	 $model = table('model');
	 foreach ($model as $v): ?>
  		  <a href="<?=$v['modelid']?>"><?=$v['name']?></a>
  	<?php endforeach;?></dd></dl>
  	<dl class="proids" id="viewtype"><dt>查看方式：</dt>
  	<dd><a href="0" class="checked">日期</a><a href="category">栏目</a></dd>
  	</dl>    
 </div>
	
	</div>
	<div id="data" style="width:99%;;margin-left:1%">
	<div style="background-color: #FFFDD7;border: 1px solid #FDBD77;height:20px;padding:10px 0px 10px 5px;margin:5px 0px;">统计概况：发稿量(<span id="total_posts" style="color:red"></span>)&nbsp;&nbsp;评论量(<span id="total_comments" style="color:red"></span>)&nbsp;&nbsp;浏览量(<span id="total_pv" style="color:red"></span>)</div>
	<div id="datatype" style="margin:10px auto">
		<label><input type="radio" name="datatype" value="posts" checked="checked">发稿量</label>
		<label><input type="radio" name="datatype" value="comments">评论数</label>
		<label><input type="radio" name="datatype" value="pv">访问量</label>
    </div>
	<div id="chart" style="height:270px;"></div>
    <div id="datalist">
    	
<table width="100%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list" style="margin-top:10px;">
	<thead>
    	<tr>
		<th width="100">日期</th>
		<th class="sorter"><div>发稿量</div></th>
		<th class="sorter"><div>评论数</div></th>
		<th class="sorter"><div>访问量</div></th>
		</tr>
    </thead>
	<tbody>
    </tbody>
</table>
    
    </div>
	</div>
  </div>
  <div class="clear"></div>
</div>
<script type="text/javascript" src="chart/FusionCharts.js"></script>
<script type="text/javascript">
var catid = 0, year = 0, month = 0, modelid = 0, viewtype = 0, datatype='posts';
$('#category_').tree({
	url:"?app=system&controller=stat&action=cate&catid=%s",
	paramId : 'catid',
	paramHaschild:"hasChildren",
	renderTxt:function(div, id, item){
		return $('<span>'+item.name+'</span>');
	},
	active : function(div, id, item){
		catid = id;
		load();
	},
	prepared:function(){
		var t = this;
		$.getJSON('?app=system&controller=stat&action=category_path&catid=<?=$catid?>', function(path){
			t.open(path);
		});
	}
});
$(function(){
	$('#year,#month,#model,#viewtype').each(function(){
		$(this).find('a').click(function(){
			$(this).siblings().removeClass('checked').end().addClass('checked');
			load();
			return false;
		})
	});
	$('#datatype').find('input').click(function(){
		load();
	});
	load();
})

function load(){
	year =  $('#year a.checked').attr('href');
	month = $('#month a.checked').attr('href');
	modelid = $('#model a.checked').attr('href');
	viewtype = $('#viewtype a.checked').attr('href');
	datatype = $('#datatype input:checked').val();
	var url = '?app=system&controller=stat&action=query';
	if(catid) url+=('&catid='+catid);
	if(modelid)  url+='&modelid='+modelid;
	if(year) url+='&year='+year;
	if(month) url+='&month='+month;
	if(viewtype)  url+='&viewtype='+viewtype;
	if(datatype) url+='&datatype='+datatype;
	if(arguments[0]) url+='&restat=1';
	$.getJSON(url, function(html){
		eval(html.js);
		$('#viewtype').css('display',html.viewtype?'block':'none');
		$('#last_stat_time').html(html.last_stat_time);
		var data = html.datastack.data, itemlist = [];
		$('#item_list thead th:eq(0)').html(html.datastack.groupby=='category'?'栏目':'日期');
		var total_posts=0,total_comments=0,total_pv=0;
		for(var i in data)
		{
			var posts = data[i].posts==null?0:data[i].posts;
			var comments = data[i].comments==null?0:data[i].comments;
			var pv = data[i].pv==null?0:data[i].pv;
			total_posts+=parseInt(posts);
			total_comments+=parseInt(comments);
			total_pv+=parseInt(pv);
			itemlist.push('<tr><td>'+i+'</td><td class="t_c">'+posts+'</td><td class="t_c">'+comments+'</td><td class="t_c">'+pv+'</td></tr>');
		}
		$('#total_posts').html(total_posts);
		$('#total_comments').html(total_comments);
		$('#total_pv').html(total_pv);
		$('#item_list tbody').html(itemlist.join(''));
	});
}

</script>
<?php $this->display('footer');?>