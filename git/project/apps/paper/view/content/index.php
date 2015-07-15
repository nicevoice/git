<?$this->display('header'); ?>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.tree.js"></script>
<link rel="stylesheet" href="<?=IMG_URL?>js/lib/tree/style.css" type="text/css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/imageareaselect/jquery.imgareaselect.js"></script>
<script type="text/javascript" src="apps/paper/js/imgmap.js"></script>
<link rel="stylesheet" href="<?=IMG_URL?>js/lib/imageareaselect/deprecated.css" type="text/css"/>
<div id="mapContainer">
    <div id="imgMain" class="box_6">
        <h3 class="title">
        	<?=$title?>
	         <img align="absmiddle" src="images/question.gif" class="tips" tips="
				说明：<br/>
				1. 添加选区：在左侧报纸图片上划动选择范围，在弹出文章框里查找，选择关联文章<br/>
				2. 修改关联文章：双击选区<br/>
			"/>
        </h3>
       	<img id="pic" width="400" src="<?=UPLOAD_URL.$image?>"/>
    </div>
    <div id="area" class="f_l box_6 w_200"><!--style="width: 80%"-->
		<h3 class="title">
			热区 <img src="images/add.gif" id="addArea" alt="添加热区" class="hide" title="添加热区"/>
		</h3>
		<form id="mapform" method="post" action="javascript:;">
			<div id="storeDiv">
				<ul id="store">
					<!-- 存储缩略图信息 -->
				</ul>
			</div>
		</form>
		<div id="data">
			<h2>
				当前位置：<br/>
				x1：<span>0</span> 
				　y1：<span>0</span> 
				　x2：<span>0</span> 
				　y2：<span>0</span> 
			</h2>
		</div><br/>
		<input type="button" id="prevView" value="预览" class="button_style_1"/>
		<input type="button" id="return" value="返回" class="button_style_1"/>
    </div>
</div>
<ul id="rmenu">
	<li class="mark">标 注</li>
	<li class="remove">移 除</li>
</ul>
<div id="tips">这儿是标题这儿是标题这儿是标题</div>
<script type="text/javascript">
var ias = {};
var pageid = "<?=$_GET['id']?>";
var eid = "<?=$editionid?>";
var coords = <?=$coords?>;
$(function () {
	$('#imgMain').width($('#pic').width());
	$('#addArea, #add').click(area.add);
	$('#publish').click(area.publish);
	$('#prevView').click(area.prevView);
	$('#return').click(function (){
		location.href = '?app=paper&controller=page&action=index&id=<?=$editionid?>';
	});
	$('.tips').attrTips('tips');
	
	ct.nav([
		{text: '扩展'},
		{text: '报纸', url: '?app=paper&controller=paper&action=index'},
		{text: '<?=$paperName?>', url: '?app=paper&controller=edition&action=index&id=<?=$paperid?>'},
		{text: '总第<?=$total_number?>期', url: '?app=paper&controller=page&action=index&id=<?=$editionid?>'},
		{text: '<?=$name?>'}
	]);
	
	//初始化拖拽插件
	var op = {
		instance: true,
		//minHeight: 20,
		//minWidth: 20,
		show: false,
		onSelectStart: area.start,
		onSelectEnd: area.end,
		onSelectChange: area.change,
		onInit: area.init,
		keys: true
	};
	area.ias = $('#pic').imgAreaSelect(op);
	$('div.loading').hide();
});
</script>