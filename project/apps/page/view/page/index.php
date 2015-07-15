<?php $this->display('header', 'system');?>
<!--tree table-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/treetable/style.css"/>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.treetable.js"></script>
<!--contextmenu-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.contextMenu.js"></script>

<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/autocomplete/style.css" />
<script src="<?=IMG_URL?>js/lib/cmstop.autocomplete.js" type="text/javascript"></script>
<script type="text/javascript" src="apps/system/js/psn.js"></script>
<style type="text/css">
.overlay {
	position:fixed;top:0;left:0;
	z-index:9998;
	height:100%;
	width:100%;
	background-color:black;
	opacity:0.5;
	filter:Alpha(Opacity=50);
}
.test-box {
	position:fixed;
	width:700px;
	height:100px;
	top:50%;
	left:50%;
	margin-left:-370px;
	margin-top:-80px;
	padding:20px;
	background-color:#fff;
	z-index:9999;
	border:1px solid #ccc;
	border-radius:5px;
	text-align:center;
	*filter:progid:DXImageTransform.Microsoft.Glow(color=#000000,strength=6);
	box-shadow:3px 3px 5px #000;
}
.test-box.haserror {
	height:250px;
	margin-top:-155px;
}
.test-box .progress-control:after {
	content: ".";
	display: block;
	height: 0;
	font-size: 0;
	clear: both;
	visibility: hidden;
}
.test-box .progress-control {
	width:100px;
	margin:0 auto;
}
.test-box .progress-control.wide {
	width:650px;
}
.test-box .control {
	cursor:pointer;
	font-size:16px; font-family:Arial, Verdana, "宋体";
	font-weight:bold;
	padding:14px;
	border:1px solid #C3E1F0;
	background-color:#F2F8FD;
	border-radius:5px;
	text-align:center;
	width:80px;
	height:30px;
	line-height:30px;
	float:right;
}
.test-box .control:hover {
	border-color:#FDBD77;
	background-color:#FFFDD7;
	color: #FF3300;
}
.test-box .current {
	margin:5px auto;
	text-align:left;
	font-size:14px; font-family:Arial, Verdana, "宋体";
	width:650px;
	height:20px;
	line-height:20px;
}
.test-box .output {
	width:640px;
	height:150px;
	margin:0 auto;
	overflow-y:auto;
	padding:5px;
	text-align:left;
	background:#000;
	display:none;
}
.test-box.haserror .output {
	display:block;
}
.test-box .output p {
	font-size:12px; font-family:Arial, Verdana, "宋体";
	color:#fff;
}
.test-box .output .err {
	color:red;
}
.test-box .output .ok {
	color:green;
}
.test-box .close {
	width:30px;
	height:20px;
	font-size:18px;
	line-height:20px;
	text-align:center;
	right:0;
	top:0;
	position:absolute;
	background-color:#ccc;
	cursor:pointer;
}
.test-box .progress {
	font-size:12px; font-family:Arial, Verdana, "宋体";
	padding:14px;
	border:1px solid #C3E1F0;
	background-color:#F2F8FD;
	border-radius:5px;
	text-align:center;
	width:500px;
	float:left;
}
.test-box .progress .bar {
	height:28px;
	border:1px solid #ccc;
	background:#fff;text-align:left;position:relative;
}
.test-box .progress .bar .percent {
	position:absolute; top:0; left:50%;
	width:80px;
	height:28px;
	margin-left:-40px;
	line-height:28px;text-align:center;
	font-family:Tahoma, Verdana, Arial; font-size:14px;
	font-weight:bold; color:#f60;
}
.test-box .progress .bar .indicator {
	background-color:#DDEEF1;
	height:28px;width:10%;
	border-right:1px solid #9FCCE9;
}
</style>
<div class="bk_8"></div>
<div class="table_head">
	<button onclick="app.addRootPage()" class="button_style_4 f_l" type="button">添加页面</button>
	<button onclick="app.recoverPage(this)" class="button_style_4 f_l" type="button">恢复页面</button>
	<button onclick="app.test()" class="button_style_4 f_l" type="button">模版检测</button>
</div>
<div class="bk_8"></div>
<table width="98%" id="treeTable" class="table_list treeTable mar_l_8" cellpadding="0" cellspacing="0">
  <thead>
    <tr>
      <th class="t_c bdr_3">页面名称</th>
      <th class="t_c" width="250">管理员</th>
      <th class="t_c" width="80">更新频率</th>
      <th class="t_c" width="130">生成时间</th>
      <th class="t_c" width="60">页面大小</th>
      <th class="t_c" width="180">管理操作</th>
    </tr>
  </thead>
  <tbody>
  </tbody>
</table>
<ul id="right_menu" class="contextMenu">
   <li class="edit"><a href="#edit">编辑</a></li>
   <li class="new"><a href="#addPage">添加子页面</a></li>
   <li><a href="#publishPage">生成</a></li>
   <li class="delete"><a href="#del">删除</a></li>
</ul>
<script type="text/javascript">
function floatBox(html, pos, afterHtml) {
	$('div.floatbox').remove();
	var div = $('<div class="floatbox" style="position:fixed;visibility:hidden"></div>')
		.appendTo(document.body);
	var img = $('<img src="images/close.gif" />').css({
		position:'absolute',
		top:1,
		right:2,
		cursor:'pointer'
	}).click(function(){
		div.remove();
	});
	
	div.html(img).append(html);
	typeof afterHtml == 'function' && afterHtml(div);
	var doc = $(document);
	var style = cmstop.pos(pos, div.outerWidth(true), div.outerHeight(true));
	style.visibility = 'visible';
	div.css(style);
	return div;
}
(function(){
var rowTemplate = '\
<tr id="row_{pageid}">\
	<td><a class="edit" href="javascript:;">{name}</a></td>\
	<td>{admin}</td>\
	<td class="t_r">{frequency}（秒）</td>\
	<td class="t_r">{published}</td>\
	<td class="t_r">{size}</td>\
	<td class="t_c">\
		<a href="{url}" target="_blank" title="查看">\
			<img class="hand" height="16" width="16" src="images/view.gif" alt="查看" />\
		</a>\
		<img class="hand" func="addPage" height="16" width="16" src="images/add_1.gif" title="添加子页" alt="添加子页" />\
		<img class="hand" func="publishPage" width="16" height="16" src="images/refresh.gif" title="生成" alt="生成" />\
		<img class="hand" func="edit" height="16" width="16" src="images/edit.gif" title="编辑" alt="编辑" />\
		<img class="hand" func="visualedit" height="16" width="16" src="images/visualedit.gif" title="可视编辑" alt="可视编辑" />\
		<img class="hand" func="del" height="16" width="16" src="images/delete.gif" title="删除" alt="删除" />\
		<img class="hand" func="bakup" height="16" width="16" src="images/backup.gif" title="备份" alt="备份" />\
		<img class="hand" func="recover" height="16" width="16" src="images/recover.gif" title="恢复" alt="恢复" />\
	</td>\
</tr>';
var returnFalse = function(){return false};
var tree = new ct.treeTable('#treeTable',{
    idField:'pageid',
	treeCellIndex:0,
	template:rowTemplate,
	baseUrl:'?app=page&controller=page&action=tree',
	rowReady:function(id,tr,json)
	{
		tr.find('img.hand').click(function(){
			var a = this.getAttribute('func');
			a && func[a](id,tr,json,this);
		}).dblclick(returnFalse);
		tr.dblclick(function(){
			func.edit(id,tr,json);
		});
		tr.find('a.edit').click(function(){
			func.edit(id,tr,json);
		});
		tr.contextMenu('#right_menu', function(action) {
			func[action](id, tr, json, this);
		});
	}
});
var func = {
	visualedit:function(pageid, tr) {
		window.open('?app=page&controller=page&action=visualedit&pageid='+pageid, '_blank');
	},
	edit:function(pageid, tr, json){
		ct.assoc.open('?app=page&controller=page&action=view&pageid='+pageid, 'newtab', json.clickpath.split(','));
	},
	editTpl:function(pageid, sectionid){
		ct.assoc.open('?app=page&controller=page&action=view&pageid='+pageid+'&sectionid='+sectionid+'&editTemplate=1', 'newtab');
	},
	addPage:function(pageid){
		ct.form('添加页面', '?app=page&controller=page&action=add&parentid='+pageid,
		400, 230, function(json){
    	    if (json.state)
    	    {
    	    	var d = json.data;
    	    	ct.assoc.call('refresh');
    	    	tree.addRow(d);
    	    	ct.tips('添加页面成功，2秒后进入该页面','success');
    	    	setTimeout(function(){
    	    		ct.assoc.open('?app=page&controller=page&action=view&pageid='+d.pageid, 'current', d.clickpath.split(','));
    	    	}, 2000);
    	    	return true;
        	}
    	});
	},
	addRootPage:function(){
		ct.form('添加页面', '?app=page&controller=page&action=add&parentid=0', 400, 230,
        function(json){
    	    if (json.state)
    	    {
    	    	var d = json.data;
    	    	ct.assoc.call('refresh');
    	    	tree.addRow(d);
    	    	ct.tips('添加页面成功，2秒后进入该页面','success');
    	    	setTimeout(function(){
    	    		ct.assoc.open('?app=page&controller=page&action=view&pageid='+d.pageid, 'current', d.clickpath.split(','));
    	    	}, 2000);
        	}
    	});
	},
	publishPage:function(pageid){
		$.post('?app=page&controller=page&action=publish','pageid='+pageid,
		function(json){
			if(json.state){
				ct.ok('生成成功');
				tree.updateRow(pageid, json.page);
			}else{
				ct.error(json.error);
			}
		},'json');
	},
	del:function(pageid){
		ct.confirm('此操作不可恢复，确认删除此页面吗？',function(){
    	    $.post('?app=page&controller=page&action=delete', 'pageid='+pageid,
    		function(json){
    			if(json.state){
    				tree.deleteRow(pageid);
    				ct.assoc.call('refresh');
    				ct.ok('页面已删除');
    			}else{
    				ct.error(json.error);
    			}
    		},'json');
    	});
	},
	bakup:function(pageid,tr){
		$.getJSON('?app=page&controller=page&action=bakup&pageid='+pageid,function(json){
			if (json.state) {
				ct.tips(json.info,'success');
			} else {
				ct.tips(json.error,'error');
			}
		});
	},
	recoverPage:function(o){
		floatBox('\
    	<input type="text" class="bdr_6" size="30"\
    		url="?app=page&controller=page&action=baksuggest&keyword=%s"\
    	/>', o,
    	function(box){
    		box.find('input').autocomplete({
    			itemSelected:function(a, item){
    				box.remove();
    				ct.confirm('此操作可能有意外，确定使用备份文件<b class="c_red">'+item.text+'</b>吗？',function(){	
	    				$.post('?app=page&controller=page&action=recover',
	    				'bakfile='+encodeURIComponent(item.text),function(json){
	    					if (json.state) {
	    						tree.addRow(json.data);
	    						ct.tips(json.info,'success');
	    					} else {
	    						ct.tips(json.error,'error');
	    					}
	    				},'json');
    				});
    			}
    		});
    	});
	},
	recover:function(pageid,tr,json,o){
		floatBox('\
    	<input type="text" class="bdr_6" size="30"\
    		url="?app=page&controller=page&action=baksuggest&pageid='+pageid+'&keyword=%s"\
    	/>', o,
    	function(box){
    		box.find('input').autocomplete({
    			itemSelected:function(a, item){
    				box.remove();
    				ct.confirm('此操作可能有意外，确定使用备份文件<b class="c_red">'+item.text+'</b>恢复<b class="c_red">'+json.name+'</b>吗？',function(){	
	    				$.post('?app=page&controller=page&action=recover&pageid='+pageid,
	    				'bakfile='+encodeURIComponent(item.text),function(json){
	    					if (json.state) {
	    						tree.addRow(json.data);
	    						ct.tips(json.info,'success');
	    					} else {
	    						ct.tips(json.error,'error');
	    					}
	    				},'json');
    				});
    			}
    		});
    	});
	},
	test:function(){
		var overlay = $('<div class="overlay"></div>').appendTo(document.body);
    	var testbox = $(
		'<div class="test-box">'+
			'<div class="close">&#x2716;</div>'+
			'<div class="progress-control">'+
				'<div class="control">开始检测</div>'+
				'<div class="progress">'+
					'<div class="bar">'+
						'<div class="percent">0%</div>'+
						'<div class="indicator"></div>'+
					'</div>'+
				'</div>'+
			'</div>'+
			'<div class="current"></div>'+
			'<div class="output"></div>'+
		'</div>').appendTo(document.body);
		var close, progressControl, progress, percent, indicator, control, state, output;
		testbox.find('div').each(function(){
			switch(this.className){
			case 'close': close = $(this); break;
			case 'progress-control': progressControl = $(this); break;
			case 'progress': progress = $(this); break;
			case 'percent': percent = $(this); break;
			case 'indicator': indicator = $(this); break;
			case 'control': control = $(this); break;
			case 'current': current = $(this); break;
			case 'output': output = $(this); break;
			}
		});
		var running = false, proceed = 0, errors=0, ival = null, inPing = true, xhr;
		progress.hide();
		function stop(clear){
			running = false;
			progress.hide();
			progressControl.removeClass('wide');
			xhr && xhr.abort();
			control.html('重新检测');
			current.html('检测完毕 <b>共检测:'+proceed+'</b> <b style="color:red">问题数:'+errors+'</b>');
			ival && clearTimeout(ival);
			$.getJSON('?app=page&controller=page&action=stopTest&clear='+(clear||0));
			ct.endLoading();
		}
		function start(){
			if (inPing) return;
			running = true;
			proceed = 0;
			errors = 0;
			percent.html('0%');
			indicator.width('0%');
			progressControl.addClass('wide');
			testbox.removeClass('haserror');
			progress.show();
			control.html('终止检测');
			current.empty();
			output.empty();
			xhr = $.ajax({
				dataType:'json',
				url:'?app=page&controller=page&action=test'
			});
			ct.endLoading();
			ival = setTimeout(ping, 50);
		}
		function update(json){
			inPing = false;
			if (!running) return;
			if (json.state) {
				xhr.abort();
				var p = Math.floor(json.percent * 100)+'%';
				percent.html(p);
				indicator.width(p);
				proceed = json.proceed;
				current.html('正在检测:'+json.current);
				if (json.results && json.results.length) {
					errors || testbox.addClass('haserror');
					errors += json.results.length;
					$.each(json.results, function(){
						output.append('<p>'+this+'</p>');
					});
					output.scrollTop(10000);
				}
				if (json.percent == 1 || json.total == 0) {
					return stop(1);
				}
			} else if (proceed > 0) {
				return stop();
			}
			ival = setTimeout(ping, 20);
		}
		function ping(){
			inPing = true;
			$.getJSON('?app=page&controller=page&action=pingTest&proceed='+proceed, update);
			ct.endLoading();
		}
		control.click(function(){
			running ? stop(1) : start();
		});
		close.click(function(){
			running = false;
			xhr && xhr.abort();
			ival && clearTimeout(ival);
			testbox.remove();
			overlay.remove();
		});
		$.getJSON('?app=page&controller=page&action=pingTest', function(json){
			inPing = false;
			json.state && start();
		});
	}
};
window.app = func;
tree.load();
})();
</script>
</body>
</html>