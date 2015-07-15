<?php $this->display('header', 'system');?>
<link href="<?=IMG_URL?>js/lib/tree/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.tree.js"></script>

<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.progressbar.js"></script>
<!-- 时间选择器 -->
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.datepicker.js"></script>
<link href="<?=IMG_URL?>js/lib/datepicker/style.css" rel="stylesheet" type="text/css" />

<div class="mar_t_10 mar_l_8">
	<input name="" type="button" value="生成滚动页" class="button_style_1" style="width:80px" onclick="html.roll();"/> 
    <input name="" type="button" value="生成排行榜" class="button_style_1" style="width:80px" onclick="html.rank();" /> 
    <input name="" type="button" value="生成热门标签" class="button_style_1" style="width:90px" onclick="html.tags();" /> 
    <input name="" type="button" value="生成网站地图" class="button_style_1" style="width:90px" onclick="html.map();" /> 
</div>
  
<div class="bk_10 box_11"></div>
<div class="bg_1" id="tree_in">
  <div class="w_160 box_6 mar_r_8 f_l" style="position:relative;height:740px;width:155px;">
    <h3><span class="dis_b b" onclick="">栏目列表</span></h3>
    
    <div id="category_" style="position:absolute;z-index:3;"></div>
    
  </div>
  <div class="f_l w_600">
    <div class="bk_10"></div>    
    <div id="subcategory" class="tag_1">
      <ul class="tag_list">
        <li><a href="javascript: html.ls();" id="ls" class="s_3">生成列表页</a></li>
        <li><a href="javascript: html.show();" id="show">生成内容页</a></li>
      </ul>
      <div id="category_buttons" class="pad_8 f_l">
        <input type="button" id="createindex" value="生成首页" class="button_style_1" onclick="html.category()"/>
        <input type="button" id="visit" value="查看栏目页" class="button_style_1" onclick="window.open($('#category_'+current_catid).attr('href'));"/>
      </div>
    </div>

    <div id="ls_form" class="clear">
<form id="html_ls" method="POST" action="?app=system&controller=html&action=ls">
<input name="catid" type="hidden" value="<?=$catid?>"/>
<table border="0" cellspacing="0" cellpadding="0" class="table_form" width="100%">
  <tr>
    <th width="120">生成前：</th>
    <td><input type="text" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" size="5"/> 页（留空则表示生成全部列表页）</td>
  </tr>
  <tr>
    <th>同时生成子栏目页：</th>
    <td>
        <label><input type="radio" name="child" value="2" class="radio_style" checked /> 是</label>
        <label><input type="radio" name="child" value="1" class="radio_style"> 否</label>
    </td>
  </tr>
  <tr>
  	<th></th>
  	<td><input type="submit" value="生成Html" class="button_style_1"/><span id="ls_progressbar"></span></td>
  </tr>
</table>
</form>
    </div>
    
    <div id="show_form" style="display: none">
<form id="html_show" method="POST" action="?app=system&controller=html&action=show">
<input name="catid" type="hidden" value="<?=$catid?>"/>
<table border="0" cellspacing="0" cellpadding="0" class="table_form yyss">
  <tr>
    <th width="120">最新内容：</th>
    <td><input type="text" name="number" id="number" value="<?=$number?>" size="5"/> 条（留空则表示生成全部列表页）</td>
  </tr>
  <tr>
    <th>发布时间范围：</th>
    <td><input type="text" name="published_start" onclick="DatePicker(this,{'format':'yyyy-MM-dd'});" id="published_start" class="input_calendar" value="<?=$published_start?>" size="10"/> - <input type="text" name="published_end"  onclick="DatePicker(this,{'format':'yyyy-MM-dd HH:mm:ss'});" id="published_end" class="input_calendar" value="<?=$published_end?>" size="10"/></td>
  </tr>
  <tr>
    <th>ID范围：</th>
    <td><input type="text" name="contentid_start" id="contentid_start" value="<?=$contentid_start?>" size="5"/> - <input type="text" name="contentid_end" id="contentid_end" value="<?=$contentid_end?>" size="5"/></td>
  </tr>
  <tr>
    <th>指定ID：</th>
    <td><input type="text" name="contentids" id="contentids" value="<?=$contentids?>" size="60"/> 多个ID请用英文逗号隔开</td>
  </tr>
  <tr>
    <th>每轮生成：</th>
    <td><input type="text" name="limit" id="limit" value="<?=$limit?>" size="5"/> 条</td>
  </tr>
  <tr>
  	<th></th>
  	<td><input type="submit" value="生成Html" class="button_style_1"/><div id="show_progressbar"></div></td>
  </tr>
</table>
</form>
    </div>

  </div>
  <div class="clear"></div>
</div>
<script type="text/javascript" src="apps/system/js/html.js"></script>
<script type="text/javascript">
var current_catid = '<?=$current_catid?>';

$('#category_').tree({
	url:"?app=system&controller=html&action=cate&catid=%s",
	paramId : 'catid',
	paramHaschild:"hasChildren",
	renderTxt:function(div, id, item){
		return $('<span>'+item.name+'</span>');
	},
	active : function(div, id, item){
		$('#createindex')[id == 'all' ? 'show' : 'hide']();
		$('input[name=catid]').val(id);
		current_catid = id;
		if (current_catid > 0) $('#category_buttons').show();
		else $('#category_buttons').hide();
	},
	prepared:function(){
		var t = this;
		$.getJSON('?app=system&controller=category&action=path&catid='+catid, function(path){
			t.open(path);
		});
	}
});

// $('#category_'+current_catid).find('>span').click();

html.init();
</script>
<?php $this->display('footer', 'system');