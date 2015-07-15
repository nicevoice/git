<?php

require_once(dirname(dirname(__FILE__)) . '/letter/lib/functions.php');
$users = online();
if (!$users)redirect('/letter/login.php?redirect='.urlencode(SILEN_URL.'letter/'));
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		 <script src="js/mui.min.js"></script>
    <link href="css/mui.min.css" rel="stylesheet"/>
    <script src="http://apps.bdimg.com/libs/zepto/1.1.4/zepto.min.js"></script>
		<title>点滴纸书</title>
		<script>
			window.page = 1;
			window.datamore = false;
			
			mui.init({
			    subpages:[{
			      url:window.location.href,//下拉刷新内容页面地址
			      id:".mui-table-view",//内容页面标志
			      styles:{
			        //top:'148',//内容页面顶部位置,需根据实际页面布局计算，若使用标准mui导航，顶部默认为48px；
			        
			      }
			    }]
			  });
			mui.init({
			  pullRefresh : {
			    container:"#refreshContainer",//下拉刷新容器标识，querySelector能定位的css选择器均可，比如：id、.class等
			    down : {
			      contentdown : "下拉可以刷新",//可选，在下拉可刷新状态时，下拉刷新控件上显示的标题内容
			      contentover : "释放立即刷新",//可选，在释放可刷新状态时，下拉刷新控件上显示的标题内容
			      contentrefresh : "正在刷新...",//可选，正在刷新状态时，下拉刷新控件上显示的标题内容
			      callback :pullfresh //必选，刷新函数，根据具体业务来编写，比如通过ajax从服务器获取新数据；
			    },
			    up : {
			      contentrefresh : "正在加载...",//可选，正在加载状态时，上拉加载控件上显示的标题内容
			      contentnomore:'没有更多数据了',//可选，请求完毕若没有更多数据时显示的提醒内容；
			      callback :pullfreshMore //必选，刷新函数，根据具体业务来编写，比如通过ajax从服务器获取新数据；
			    }
			  }
			});
			function pullfresh() {
			     getData(1, 10)
			     
			}
			function pullfreshMore() {
		     //业务逻辑代码，比如通过ajax从服务器获取新数据；
		     	getData(++window.page, 10, true,this)
		     //注意，加载完新数据后，必须执行如下代码，true表示没有更多数据了，两个注意事项：
		     //1、若为ajax请求，则需将如下代码放置在处理完ajax响应数据之后
		     //2、注意this的作用域，若存在匿名函数，需将this复制后使用，参考hello mui中的代码示例；
			}
			getData(1, 10)
			function getData(page, size, isAppend, $this)
			{
				window.page = page
				$.get('/letter/api.php', {page:page, size:size},function(json){
				var html = ''
				var data = json.data;
				
				if ($this)$this.endPullupToRefresh(json.s);
				for (i in data) {
					//html += '<li class="mui-table-view-cell"><a target="_blank" href="/wechat/post.php?id='+data[i].id+'">'+data[i].post_title+'</a></li>'
					html += '<div class="mui-col-xs-11 mui-text-center mui-content-padded"><span class="mui-badge">'+data[i].addtime+'</span></div>'+
					'<div style="" class="mui-card mui-table-view-cell mui-media mui-col-xs-11">'+
					'<a href="/letter/post.php?id='+data[i].id+'">'+
					'<img class="mui-media-object" style="margin-left:3px;width:98.5%;" src="'+data[i].thumb+'">'+
					'<div class="mui-media-body mui-text-center"><span class="mui-badge mui-badge-danger" style="float: left;">'+data[i].reply+'</span>'+data[i].title+'</div>'+
					'</a>'+
					'</div>';
				}
				if (isAppend) {
					$('.list-view').append(html)
				}else{
					$('.list-view').html(html)
				}
				mui('#refreshContainer').pullRefresh().endPulldownToRefresh();
				},'json'
				);
				
			}
		</script>
	</head>
<body>
<header class="mui-bar mui-bar-nav" >
	<h1 class="mui-title">未打开的书信</h1>
</header>

<div class="mui-content">
	<div id="refreshContainer" class="mui-content mui-scroll-wrapper"  style="padding-top: 50px;">
	  <div class="mui-scroll">
	    <!--数据列表-->
	    <div class="list-view">
	    	<div class="mui-table-view-cell mui-media mui-col-xs-12">正在刷新...</div>
	    </div>
	  </div>
	</div>
</div>
</body>
</html>
