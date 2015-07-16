<?php $this->display('header', 'system');?>
<!--tree-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tree/style.css"/>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.tree.js"></script>
<div class="bk_10"></div>
<div class="suggest w_650 mar_l_8">
  <h2>友情提示</h2>
  <p><b>Sitemap</b> 服务旨在使用 Feed 文件 sitemap.xml 通知 Google、Yahoo! 以及 Microsoft 等 Crawler(爬虫)网站上哪些文
件需要索引、这些文件的最后修订时间、更改频度、文件位置、相对优先索引权，这些信息将帮助他们建立索引范围和索引的行为习惯。详
细信息请查看 <a class="c_red" href="http://www.sitemaps.org" target="_blank">sitemaps.org</a> 网站上的说明。
<br/>
通过<b>Sitemap</b>，您可以获得：<br/>
1、更大的抓取范围，更新的搜索结果 – 帮助网友找到更多您的网页。<br/>
2、更为智能的抓取 – 因为我们可以得知您网页的最新修改时间或网页的更改频率。<br/>
3、详细的报告 – 详细说明 Google 如何将网友的点击指向您的网站及 Googlebot 如何看到您的网页。
</p>
<div id="testtest"></div>
</div>
<div class="bk_8"></div>
<form id="sitemaps" action="?app=system&controller=sitemaps&action=index" method="POST">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>输出XML设置</caption>  
	<tr>
		<th width="120">状态：</th>
		<td>
		<input type="radio" id="l1" name="setting[open]" value="1" class="radio" <?php if ($setting[open]) echo 'checked';?>/> 开启
		<input type="radio" id="l2" name="setting[open]" value="0" class="radio" <?php if (!$setting[open]) echo 'checked';?>> 关闭
		</td>
	</tr>  
	<tr>
		<th>Xml地址：</th>
		<td><?=element::psn('sitemapsurl','setting[url]',$setting['url'],45, 'file')?>
				<input type="button" onclick="makeSiteMaps()" value=" 立刻生成 " class="button_style_1"/>
		</td>
	</tr>  
	<tr>
		<th>Xml链接：</th>
		<td><span class="baiduurl"><a href="<?php echo $setting['Realurl']?>" target="_blank"  ><?php echo $setting['Realurl']?></a></span>
		</td>  
	</tr>
	<tr>
		<th>包含栏目：</th>
		<td valign="middle">
            <input type="hidden" id="category" name="setting[category]" value="<?=$categorys?>"
                class="placetree"
                url="?app=system&controller=category&action=cate&catid=%s"
                initUrl="?app=system&controller=category&action=name&catid=%s"
                paramVal="catid"
                paramTxt="name"
                multiple="multiple" />
		</td>
	</tr>
	<tr>
		<th>包含模型：</th>
		<td valign="middle"><?=element::model_checkbox($setting['modelid'],'setting[modelid][]')?></td>
	</tr>
	<tr>
		<th>输出条数：</th>
		<td><input type="text" name="setting[number]" value="<?=$setting['number']?>" size="5"/>   条</td>
	</tr>  
	<tr>
		<th>更新频率：</th>
		<td> <input type="text" name="setting[frequency]" value="<?=$setting['frequency']?>" size="5"/> 分钟 </td>
	</tr>
	<tr>
		<th></th>
		<td valign="middle">
		<input type="submit" id="submit" value="保存" class="button_style_2"/>
		</td>
	</tr>
</table>
</form>
<script type="text/javascript" src="apps/system/js/psn.js"></script>
<script type="text/javascript" src="apps/system/js/treeview_selector.js"></script>
<link href="<?=IMG_URL?>js/lib/treeview/treeview.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.treeview.js"></script>
<script type="text/javascript">
$(function(){
	$('#category').placetree();
	$("#sitemaps").ajaxForm('submit_ok');
	$.each(<?=$models?>, function(i, id){
		$("input[name='setting\[modelid\]\[\]'][value='"+id+"']").attr('checked', true);
	});
});
function submit_ok(data) {
	if(data.state) {
		ct.tips(data.message);
	} else {
		ct.error(data.message);
	}
}
function makeSiteMaps() {
	$.getJSON(
		"?app=system&controller=sitemaps&action=build&make=1",
		submit_ok
	);
}
</script>
<?php $this->display('footer', 'system');?>