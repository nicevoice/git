<?php $this->display('header', 'system');?>

<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.pagination.js"></script>
<script type="text/javascript" src="uploader/cmstop.uploader.js"></script>
<script type="text/javascript" src="imageEditor/cmstop.imageEditor.js"></script>
<script type="text/javascript" src="js/cmstop.filemanager.js"></script>

<div class="bk_10"></div>
<div class="tag_1">
	<ul class="tag_list">
		<li class="s_3"><a href="?app=member&controller=index&action=profile&userid=<?=$member['userid']?>">用户资料</a></li>
		<?php if($member['groupid'] == 1) { ?>
	<li><a href="?app=system&controller=administrator&action=stat&userid=<?=$member['userid']?>">工作报表</a></li>
	<li><a href="?app=system&controller=score&action=view&userid=<?=$member['userid']?>">评分记录</a></li>
	<li><a href="?app=system&controller=administrator&action=priv&userid=<?=$member['userid']?>">权限</a></li>
		<?php } ?>
	</ul>
	<input type="button" value="修改资料" class="button_style_1" onclick="member.edit(<?=$member['userid']?>)"/>
	<input type="button" value="修改密码" class="button_style_1" onclick="member.password(<?=$member['userid']?>)"/>
	<input type="button" value="修改头像" class="button_style_1" onclick="member.avatar(<?=$member['userid']?>)"/>
	<input type="button" value="删除" class="button_style_1" onclick="member.del(<?=$member['userid']?>);"/>
	<input name="back" type="button" value="返回" class="button_style_1" onclick="javascript:history.go(-1);"/>
</div>
<table width="790" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
  <tr>
	<td width="110" rowspan="4" align="center"><img src="<?=$member['photo']?>" alt="用户头像" height="96" width="96"/></td>
	<th width="70">姓名：</th>
	<td width="130"><?=$member['name']?></td>
	<th width="80">性别：</th>
	<td width="150"><?php if($member['sex'] == 1) echo '男';else echo '女';?></td>
	<th width="80">生日：</th>
	<td><?php if(strtotime($member['birthday'])) echo $member['birthday'];?></td>
  </tr>
  <tr>
	<th>职业：</th>
	<td></td>
	<th>手机：</th>
	<td><?=$member['mobile']?></td>
	<th>电话：</th>
	<td><?=$member['telephone']?></td>
  </tr>
  <tr>
	<th>E-mail：</th><td><?=$member['email']?></td>
	<th>QQ：</th><td><?=$member['qq']?></td> 
	<th>MSN：</th><td><?=$member['msn']?></td>   
  </tr>
  <tr>
	<th>地址：</th><td colspan="3"><?=$member['address']?></td>
	<th>邮编：</th><td><?=$member['zipcode']?></td>   
  </tr>
  <tr>
	<td align="center"><?=$member['username']?>（<?=$member['group']?>）</td>
	<th>注册时间：</th>
	<td><?=$member['regtime']?></td>
	<th>上次登录：</th>
	<td><?=$member['lastlogintime']?> (<?=$member['location']?>)</td>
	<th>登录次数：</th>
	<td><?=$member['logintimes']?></td>
  </tr>
  <?php if($space) { ?>
	<td></td>
	<th>个人专栏：</th>
	<td colspan="3"><a href="<?php echo SPACE_URL.$space['alias'];?>" target="_blank"><?php echo $space['name'];?></a></td>
	<th>投稿数：</th>
	<td><?php echo intval($space['posts']);?></td>
  <?php } ?>
  <?php if($member['groupid'] == 1) { ?>
  <tr>
	<td></td>
	<th>部门：</th>
	<td><?=$member['departmentid']?></td>
	<th>角色：</th>
	<td><?=$member['roleid']?></td>
	<th>发稿数：</th>
	<td><?=$member['posts']?></td>
  </tr>
  <?php } ?>
</table>
<div class="bk_10"></div>
<div class="tag_list_1 pad_8 layout mar_l_8" id="bytime_list">
	<a href="javascript:tablePost.load('published_min=');" id="all" class="s_5">全部</a>
	<a href="javascript:tablePost.load('published_min=<?=date('Y-m-d', TIME)?>');">今日</a>
	<a href="javascript:tablePost.load('published_min=<?=date('Y-m-d', strtotime('yesterday'))?>&published_max=<?=date('Y-m-d', strtotime('yesterday'))?>');">昨日</a>
	<a href="javascript:tablePost.load('published_min=<?=date('Y-m-d', strtotime('last monday'))?>');">本周</a>
	<a href="javascript:tablePost.load('published_min=<?=date('Y-m-d', strtotime('last month'))?>');">本月</a>
	<div class="clear"></div>
</div>
<div class="bk_8"></div>
<table id="item_list" width="99%" cellpadding="0" cellspacing="0" class="tablesorter table_list mar_l_8">
	<thead><tr>
		<th class="bdr_3">标题</th>
		<th width="80">栏目</th>
		<th width="60">类型</th>
		<th width="60">状态</th>
		<th width="60">点击</th>
		<th width="60">评论</th>
		<th width="120">发布时间</th>
	</tr></thead>
	<tbody id="list_body"></tbody>
</table>
<?php if($member['groupid'] == 1) {?>
<div class="title mar_l_8"><span class="f_l">操作记录</span> <a href="?app=system&controller=content_log&action=index&createdby=<?=$userid?>" class="f_r c_blue">查看全部</a> </div>
<table width="790" border="0" cellspacing="0" cellpadding="0" class="table_text mar_5 mar_l_8">
	<?php if(is_array($log)) {foreach($log as $v) {?>
	  <tr>
		<td width="20%" class="t_c"><?php echo $v['created']?></td>
		<td width="80%"><?php echo $v['action_name']?> <a href="javascript:void(0);" onclick="openUrl('?app=<?=$v['model']?>&controller=<?=$v['model']?>&action=view&contentid=<?=$v['contentid']?>')"><?php echo $v['title']?></a></td>
	  </tr>
	<?php } } ?>
</table>
<?php } ?>

<script type="text/javascript">
$.validate.setConfigs({
	xmlPath:'/apps/member/validators/'
});
var row_template = '<tr id="row_{contentid}">';
	row_template +='	<td><a href="javascript:void(0);" onclick="openUrl(\'?app=article&controller=article&action=view&contentid={contentid}\')" tips="ID：{contentid}<br />点击：{pv}次<br />评论：{comments}条<br />Tags：{tags}<br />创建：{createdbyname}（{created}）<br />修改：{modifiedbyname}（{modified}）<br />审核：{checkedbyname}（{checked}）" class="title_list">{title}</a></td>';
	row_template +='	<td class="t_c">{catname}</td>';
	row_template +='	<td class="t_c">{modelname}</td>';
	row_template +='	<td class="t_c">{status}</td>';
	row_template +='	<td class="t_c">{pv}</td>';
	row_template +='	<td class="t_c">{comments}</td>';
	row_template +='	<td class="t_c">{modified}</td>';
	row_template +='</tr>';
var tablePost = new ct.table('#item_list', {
		rowIdPrefix : 'row_',
		pageSize : 15,
		rowCallback: 'init_row_event',
		jsonLoaded : 'json_loaded',
		template : row_template,
		baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=contribute&createdby=<?=$member['userid']?>'
});
function init_row_event(id, tr) {
	tr.unbind('click');
	tr.find('a.title_list').attrTips('tips');
}

function json_loaded(json) {
	$('#contributeTotal').html(json.total);
}
function openUrl(url) {
	ct.assoc.open(url,'newtab');
}
$(function() {
	tablePost.load();
	$('#pagesize').val(tablePost.getPageSize());
	$('#pagesize').blur(function(){
		var p = $(this).val();
		tablePost.setPageSize(p);
		tablePost.load();
	});
	$('#bytime_list > a').click(function(){
		$('#bytime_list > a.s_5').removeClass('s_5');
		$(this).addClass('s_5');
	}).focus(function(){
		this.blur();
	});

});
</script>
<script type="text/javascript" src="apps/member/js/member.js"></script>
<?php $this->display('footer','system');?>