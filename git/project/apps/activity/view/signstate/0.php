<div id="created_x" class="th_pop" style="display:none;width:150px">
     <div>
        <a href="javascript: tableApp.load('created_min=<?=date('Y-m-d', TIME)?>');">今日</a> |
        <a href="javascript: tableApp.load('created_min=<?=date('Y-m-d', strtotime('yesterday'))?>&created_max=<?=date('Y-m-d', strtotime('yesterday'))?>');">昨日</a> | 
        <a href="javascript: tableApp.load('created_min=<?=date('Y-m-d', strtotime('last monday'))?>');">本周</a> | 
        <a href="javascript: tableApp.load('created_min=<?=date('Y-m-01', strtotime('this month'))?>');">本月</a>
     </div>
     <ul>
       <?php 
       for ($i=2; $i<=7; $i++) { 
       	  $createdate = date('Y-m-d', strtotime("-$i day"));
       ?>
        <li><a href="javascript: tableApp.load('created_min=<?=$createdate?>&created_max=<?=$createdate?>');"><?=$createdate?></a></li>
       <?php } ?>
     </ul>
  </div>  
  <div style="margin-left:100px;">
  <table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="table_list">
    <thead>
      <tr>
        <th width="30" class="t_l bdr_3"><input type="checkbox" id="check_all" /></th>
         <?php if($fields['name']) {?> <th id="name" width="100">名字</th> <?php };?>
        <?php if($fields['sex']) {?>  <th id="sex">性别</th> <?php };?> 
        <?php if($fields['photo']){?> <th id="photo">照片</th> <?php };?> 
        <?php if($fields['identity']){?>  <th id="identity">身份证号</th> <?php };?> 
        <?php if($fields['company']){?> <th id="company">所在单位</th> <?php };?> 
        <?php if($fields['job']){?> <th id="job">工作</th>  <?php };?> 
       <?php if($fields['telephone']){?> <th id="telephone">电话</th> <?php };?> 
       <?php if($fields['mobile']){?><th id="mobile">手机</th><?php };?> 
       <?php if($fields['email']){?> <th id="email">电子邮箱</th> <?php };?> 
        <?php if($fields['qq']){?> <th id="qq">QQ</th> <?php };?> 
        <?php if($fields['msn']){?> <th id="msn">MSN</th> <?php };?> 
       <?php if($fields['site']){?>  <th id="site">主页</th> <?php };?> 
        <?php if($fields['address']){?> <th id="address">地址</th>  <?php };?>       
       <?php if($fields['zipcode']){?> <th id="zipcode">邮编</th>  <?php };?>
       <?php if($fields['aid']){?> <th id="aid">附件</th> <?php };?>
       <?php if($fields['note']){?> <th id="note">留言</th>  <?php };?>    
        <th  class="ajaxer" width="180"><em class="more_pop" name="created_x"></em><div name="created">报名时间</div></th>
        <th width="80">管理操作</th>
      </tr>
    </thead> 
    <tbody id="list_body">
    </tbody>
  </table>
  <div class="table_foot">
    <div id="pagination" class="pagination f_r"></div>
    <p class="f_l">
      <input type="button" name="pass" onclick="sign.pass();" value="通过" class="button_style_1"/>
      <input type="button" name="remove" onClick="sign.del();" value="删除" class="button_style_1"/>
    </p>
  </div>
<div class="clear"></div>
</div>
  
  <!--右键菜单-->
  <ul id="right_menu" class="contextMenu">
    <li class="view"><a href="#sign.view">查看</a></li>
    <li class="remove"><a href="#sign.pass">通过</a></li>
    <li class="edit"><a href="#sign.edit">编辑</a></li>
    <li class="remove"><a href="#sign.del">删除</a></li>
 </ul>
  
<script type="text/javascript">
var row_template = '<tr id="row_{signid}">\
                	    <td><input type="checkbox" class="checkbox_style" name="chk_row" id="chk_row_{signid}" value="{signid}" /></td>\
	                <?php if($fields['name']) {?><td><a href="javascript:sign.view({signid});"  tips="ID：{signid}<br />报名时间：{created}）<br />审核：{checkedby}（{checked}）" class="title_list">{name}</a></td>\<?php };?>
	                	<?php if($fields['sex']) {?><td class="t_c">{sex}</td>\<?php };?>
	                	<?php if($fields['photo']) {?><td class="t_c"><a  class="photo" target="_blank" tips="{phototips}" href="'+UPLOAD_URL+'{photo}">{photo}</a></td>\<?php };?>
	                	<?php if($fields['identity']) {?><td class="t_c">{identity}</td>\<?php };?>
	                	<?php if($fields['company']) {?><td class="t_c">{company}</td>\<?php };?>
	                	<?php if($fields['job']) {?><td class="t_c">{job}</td>\<?php };?>
	                	<?php if($fields['telephone']) {?><td class="t_c">{telephone}</td>\<?php };?>
	                	<?php if($fields['mobile']) {?><td class="t_c">{mobile}</td>\<?php };?>
	                	<?php if($fields['email']) {?><td class="t_c">{email}</td>\<?php };?>
	                	<?php if($fields['qq']) {?><td class="t_c">{qq}</td>\<?php };?>
	                	<?php if($fields['msn']) {?><td class="t_c">{msn}</td>\<?php };?>
	                	<?php if($fields['site']) {?><td class="t_c">{site}</td>\<?php };?>
	                	<?php if($fields['address']) {?><td class="t_c">{address}</td>\<?php };?>
	                	<?php if($fields['zipcode']) {?><td class="t_c">{zipcode}</td>\<?php };?>
	                	<?php if($fields['aid']) {?><td class="t_c"><a target="_blank" href="{aid}">下载</a></td>\<?php };?>
	                	<?php if($fields['note']) {?><td class="t_c">{note}</td>\<?php };?>
	                	<td class="t_c">{created}</td>\
	                	<td class="t_c"><img src="images/view.gif" alt="访问" width="16" height="16" class="hand view" /> &nbsp;<img src="images/edit.gif" alt="编辑" width="16" height="16" class="hand edit"/> &nbsp;<img src="images/sh.gif" title="通过" width="16" height="16" class="hand pass" /></td>\
	                </tr>';
</script>