  <div id="published_x" class="th_pop" style="display:none;width:150px">
     <div>
        <a href="javascript: tableApp.load('published_min=<?=date('Y-m-d', TIME)?>');">今日</a> |
        <a href="javascript: tableApp.load('published_min=<?=date('Y-m-d', strtotime('yesterday'))?>&published_max=<?=date('Y-m-d', strtotime('yesterday'))?>');">昨日</a> | 
        <a href="javascript: tableApp.load('published_min=<?=date('Y-m-d', strtotime('last monday'))?>');">本周</a> | 
        <a href="javascript: tableApp.load('published_min=<?=date('Y-m-01', strtotime('this month'))?>');">本月</a>
     </div>
     <ul>
       <?php 
       for ($i=2; $i<=7; $i++) { 
       	  $publishdate = date('Y-m-d', strtotime("-$i day"));
       ?>
        <li><a href="javascript: tableApp.load('published_min=<?=$publishdate?>&published_max=<?=$publishdate?>');"><?=$publishdate?></a></li>
       <?php } ?>
     </ul>
  </div>
  <div id="weight_x" class="th_pop" style="display:none;width:60px">
     <ul>
        <li><a href="javascript: tableApp.load('weight_min=0&weight_max=29');">0-29</a></li>
        <li><a href="javascript: tableApp.load('weight_min=30&weight_max=49');">30-49</a></li>
        <li><a href="javascript: tableApp.load('weight_min=50&weight_max=59');">50-59</a></li>
        <li><a href="javascript: tableApp.load('weight_min=60&weight_max=69');">60-69</a></li>
        <li><a href="javascript: tableApp.load('weight_min=70&weight_max=79');">70-79</a></li>
        <li><a href="javascript: tableApp.load('weight_min=80&weight_max=89');">80-89</a></li>
        <li><a href="javascript: tableApp.load('weight_min=90&weight_max=100');">90-100</a></li>
     </ul>
  </div>
  <div id="publishedby_x" class="th_pop" style="display:none;width:150px">
  <table>
    <tr>
       <td><input type="text" id="publishedbyname" size="12" /></td><td><input type="button" value="查询" class="button_style_1" onclick="tableApp.load('publishedbyname='+$('#publishedbyname').val());"/></td>
    </tr>
  </table>
  </div>
  <table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8">
    <thead>
      <tr>
        <th width="30" class="t_l bdr_3"><input type="checkbox" id="check_all" /></th>
        <th>标题</th>
        <th width="60">管理操作</th>
        <th width="60" class="ajaxer"><div name="pv">点击</div></th>
        <th width="60" class="ajaxer"><em class="more_pop" name="weight_x"></em><div name="weight">权重</div></th>
        <th width="80" class="ajaxer"><em class="more_pop" name="publishedby_x"></em><div name="publishedby">发布人</div></th>
        <th width="120" class="ajaxer"><em class="more_pop" name="published_x"></em><div name="published">发布时间</div></th>
      </tr>
    </thead>
    <tbody id="list_body">
    </tbody>
  </table>
  <div class="table_foot">
    <div id="pagination" class="pagination f_r"></div>
    <p class="f_l">
<?php 
if (priv::aca('interview', 'interview', 'remove')){
?>
      <input type="button" name="remove" onclick="interview.remove();" value="删除" class="button_style_1"/>
<?php 
}
if (priv::aca('interview', 'interview', 'unpublish')){
?>
      <input type="button" name="unpublish" onclick="interview.unpublish();" value="撤稿" class="button_style_1"/>
<?php 
}
if (priv::aca('interview', 'interview', 'move')){
?>
      <input type="button" name="move" onclick="interview.move();" value="移动" class="button_style_1"/>
<?php 
}

if (priv::aca('interview', 'interview', 'reference')){
?>
      <input type="button" name="reference" onclick="interview.reference();" value="引用" class="button_style_1"/>
<?php 
}
if (priv::aca('interview', 'interview', 'createhtml')){
?>
      <input type="button" name="createhtml" onclick="interview.createhtml();" value="生成" class="button_style_1"/>
<?php 
}
?>    </p>
  </div>
  <!--右键菜单-->
  <ul id="right_menu" class="contextMenu">
<?php 
if (priv::aca('interview', 'interview', 'view')){
?>
    <li class="view"><a href="#interview.view">查看</a></li>
<?php 
}
if (priv::aca('interview', 'chat', 'index')){
?>
    <li class="chat"><a href="#interview.chat">文字实录</a></li>
<?php 
}
if (priv::aca('interview', 'question', 'index')){
?>
    <li class="question"><a href="#interview.question">网友提问</a></li>
<?php 
}
if (priv::aca('interview', 'interview', 'edit')){
?>
    <li class="edit"><a href="#interview.edit">编辑</a></li>
<?php 
}
if (priv::aca('interview', 'interview', 'remove')){
?>
    <li class="remove"><a href="#interview.remove">删除</a></li>
<?php 
}
if (priv::aca('interview', 'interview', 'unpublish')){
?>
    <li class="unpublish"><a href="#interview.unpublish">撤稿</a></li>
<?php 
}
if (priv::aca('interview', 'interview', 'createhtml')){
?>
    <li class="createhtml"><a href="#interview.createhtml">生成</a></li>
<?php 
}
if (priv::aca('interview', 'interview', 'move')){
?>
    <li class="move separator"><a href="#interview.move">移动</a></li>
<?php 
}

if (priv::aca('interview', 'interview', 'reference')){
?>
    <li class="reference"><a href="#interview.reference">引用</a></li>
<?php 
}
?>
  </ul>
  
<script type="text/javascript">
var action = '';
<?php 
if (priv::aca('interview', 'interview', 'edit')){
?>
action += '<img src="images/edit.gif" alt="编辑" width="16" height="16" class="hand edit"/> &nbsp;';
<?php 
}
if (priv::aca('interview', 'interview', 'remove')){
?>
action += '<img src="images/delete.gif" alt="删除" width="16" height="16" class="hand delete" />';
<?php 
}
?>
var row_template = '<tr id="row_{contentid}">\
                	    <td><input type="checkbox" class="checkbox_style" name="chk_row" id="chk_row_{contentid}" value="{contentid}" /></td>\
	                	<td>{thumb_icon}<a href="javascript: interview.view({contentid});" style="color:{color}" tips="ID：{contentid}<br />点击：{pv}次<br />评论：{comments}条<br />来源：{source_name}<br />Tags：{tags}<br />创建：{createdbyname}（{created}）<br />修改：{modifiedbyname}（{modified}）<br />审核：{checkedbyname}（{checked}）" class="title_list">{title}</a> {note} {lock}</td>\
	                 	<td class="t_c"><a href="{url}" target="_blank"><img src="images/view.gif" alt="访问" width="16" height="16" class="hand view" /></a> &nbsp;'+action+'</td>\
	                	<td class="t_r">{pv}</td>\
	                	<td class="t_r">{weight}</td>\
	                	<td class="t_c"><a href="javascript: url.member({publishedby});">{publishedbyname}</a></td>\
	                	<td class="t_c">{published}</td>\
	                </tr>';
</script>