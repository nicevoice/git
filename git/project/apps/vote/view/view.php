<?php $this->display('header');?>
<?php $workflowid = table('category', $catid, 'workflowid');?>
<div class="bk_10"></div>
<div class="tag_1">
	<ul class="tag_list">
		<li class="s_3"><a href="?app=<?=$app?>&controller=<?=$controller?>&action=<?=$action?>&contentid=<?=$contentid?>" class="s_3">查看</a></li>
	</ul>
<?php 
if(priv::aca('vote', 'vote', 'code')) { 
?>
	<input type="button" name="code" value="调用代码" class="button_style_1" onclick="vote.code(<?=$contentid?>)"/>
<?php 
}
if(priv::aca('vote', 'vote', 'edit')) { 
?>
  <input type="button" name="edit" value="修改" class="button_style_1" onclick="content.edit(<?=$contentid?>)"/>
<?php 
}
if($status == 6 && priv::aca('vote', 'vote', 'createhtml')) { 
?>
  <input type="button" name="createhtml" value="生成" class="button_style_1" onclick="content.createhtml(<?=$contentid?>)"/>
<?php 
}
if ($status < 6 && priv::aca('vote', 'vote', 'publish')) { 
?>
  <input type="button" name="publish" value="发布" class="button_style_1" onclick="content.publish(<?=$contentid?>)"/>
<?php 
}
if ($status > 0 && priv::aca('vote', 'vote', 'remove')) { 
?>
  <input type="button" name="remove" value="删除" class="button_style_1" onclick="content.remove(<?=$contentid?>)"/>
<?php 
}
if ($status == 0) { 
	if (priv::aca('vote', 'vote', 'delete')){
?>
  <input type="button" name="delete" value="删除" class="button_style_1" onclick="content.del(<?=$contentid?>)"/>
<?php 
    }
    if (priv::aca('vote', 'vote', 'restore')) { 
?>
  <input type="button" name="restore" value="还原" class="button_style_1" onclick="content.restore(<?=$contentid?>)"/>
<?php 
    }
}
if (($status == 1 || ($workflowid && $status == 2)) && priv::aca('vote', 'vote', 'approve')) { 
?>
  <input type="button" name="approve" value="送审" class="button_style_1" onclick="content.approve(<?=$contentid?>)"/>
<?php 
}
if ($status == 3) {
	if (priv::aca('vote', 'vote', 'pass')) { 
?>
  <input type="button" name="pass" value="通过" class="button_style_1" onclick="content.pass(<?=$contentid?>)"/>
<?php 
    }
    if (priv::aca('vote', 'vote', 'reject')) { 
?>
  <input type="button" name="reject" value="退稿" class="button_style_1" onclick="content.reject(<?=$contentid?>)"/>
<?php 
    }
}
if (priv::aca('vote', 'vote', 'move')){
?>
  <input type="button" name="move" value="移动" class="button_style_1" onclick="content.move(<?=$contentid?>)"/>
<?php 
}

if (priv::aca('vote', 'vote', 'reference')){
?>
  <input type="button" name="reference" value="引用" class="button_style_1" onclick="content.reference(<?=$contentid?>)"/>
<?php 
}
?>	<input type="button" name="note" value="批注" class="button_style_1" onclick="content.note(<?=$contentid?>)"/>
	<input type="button" name="version" value="版本" class="button_style_1" onclick="content.version(<?=$contentid?>)"/>
	<input type="button" name="log" value="日志" class="button_style_1" onclick="content.log(<?=$contentid?>)"/>
</div>
<div class="bg_2">
	<div class="f_l w_600 mar_r_8">
		<table width="500" border="0" cellspacing="0" cellpadding="0" class="table_form mar_t_10 mar_l_8">
			<tr>
				<th>标题：</th>
				<td><a href="<?=$url?>" target="_blank" style="color:<?=$color?>"><?=$title?></a> (<? if($type == 'checkbox') { ?>多选<?php if ($maxoptions){?>，最多可同时选<?=$maxoptions?>项<? } } else {?>单选<? } ?>)</td>
			</tr>
			<tr>
				<th>网址：</th>
				<td><a href="<?=$url?>" target="_blank"><?=$url?></a></td>
			</tr>
			<tr>
				<th>Tags：</th>
				<td><?=$tags_view?></td>
			</tr>
      <?php if($thumb){ ?>
      <tr>
        <th>缩略图：</th>
        <td><a href="<?php echo (strpos($thumb,'://') ? $thumb : UPLOAD_URL .$thumb); ?>" target="_blank"><img src="<?php echo (strpos($thumb,'://') ? $thumb : UPLOAD_URL .$thumb); ?>" width="300" height="250"></a></td>
      </tr>
      <?php } ?>
      <?php include ROOT_PATH.'apps/system/view/workflow/view_inc.php';?>
			<tr>
				<th>总票数：</th>
				<td><?=$total?> 票　<a href="javascript:;" onclick="vote.vote_log(<?=$contentid?>)">查看记录&gt;&gt;</a></td>
			</tr>
			<tr>
				<th>开始：</th>
				<td><?=$starttime ? date('Y-m-d H:i:s', $starttime) : ''?>&nbsp;</td>
			</tr>
			<tr>
				<th>结束：</th>
				<td><?=$endtime ? date('Y-m-d H:i:s', $endtime) : ''?>&nbsp;</td>
			</tr>
			<tr>
				<th>防刷：</th>
				<td><? if($mininterval) { ?>同IP <?=$mininterval?> 小时内不得重复投票<? } else { ?>不限<? } ?></td>
			</tr>
		</table>
		<div class="title mar_l_8">简介</div>
		<div class="content"><?=$description?></div>
		<div class="title mar_l_8">投票结果</div>
		<dl class="list_2">
		<?php 
		foreach ($option as $k=>$r) { 
		?>
			<dd><?=($k+1)?> . <?=$r["name"]?> </dd>
			<dd><a href="javascript:;" class="f_r" onclick="vote.log_data(<?=$r["optionid"]?>)">查看记录</a><div class="bar-chart" style="width:400px"><span><img src="images/space.gif" height="16" width="<? if($r["percent"]) {?><?php echo 4*$r["percent"]?><? }else{ ?>1<? } ?>" alt="<?=$r["name"]?>"/></span></div>&nbsp;<?=$r["percent"]?> (<?=$r["votes"]?>)</dd>
		<?php } ?>
		</dl>
		<?php if (!empty($related)) { ?>
		 <div class="title mar_l_8">相关</div>
        	<div class="mar_l_8" style="padding-left:6px">   
            <ul class="cols_2 list_4">
  		    <?php foreach ($relateds as $k=>$r){ ?>
      			<li><a href="<?=$r['url']?>" target="_blank"><?=$r['title']?></a></li>
    		<?php } ?>    
   		   </ul>
   		 </div>
   		 <?php } ?>
	</div>
	<div class="f_l w_200 box_6">
		<h3><span class="b">投票属性</span></h3>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_form">
			<tr>
				<th width="70">ID：</th>
				<td><?=$contentid?></td>
			</tr>
			<tr>
				<th>栏目：</th>
				<td><a href="<?=$caturl?>" target="_blank"><?=$catname?></a></td>
			</tr>
			<tr>
				<th>上线：</th>
				<td><?=$published?>&nbsp;</td>
			</tr>
			<tr>
				<th>下线：</th>
				<td><?=$unpublished?>&nbsp;</td>
			</tr>
			<tr>
				<th>权重：</th>
				<td><?=$weight?></td>
			</tr>
			<tr>
				<th>录入：</th>
				<td><a href="javascript: url.member(<?=$createdby?>);"><?=$createdbyname?></a></td>
			</tr>
			<tr>
				<th>时间：</th>
				<td><?=$created?></td>
			</tr>
	      <tr>
	        <th>修改：</th>
	        <td><a href="javascript: url.member(<?=$modifiedby?>);"><?=$modifiedbyname?></a>&nbsp;</td>
	      </tr>
	      <tr>
	        <th>修改时间：</th>
	        <td><?=$modified?>&nbsp;</td>
	      </tr>
	      <tr>
	        <th>审核：</th>
	        <td><a href="javascript: url.member(<?=$checkedby?>);"><?=$checkedbyname?></a>&nbsp;</td>
	      </tr>
	      <tr>
	        <th>审核时间：</th>
	        <td><?=$checked?>&nbsp;</td>
	      </tr>
	      <tr>
	        <th>锁定：</th>
	        <td><a href="javascript: url.member(<?=$lockedby?>);"><?=$lockedbyname?></a>&nbsp;</td>
	      </tr>
	      <tr>
	        <th>锁定时间：</th>
	        <td><?=$locked?>&nbsp;</td>
	      </tr>
			<tr>
				<th>状态：</th>
				<td><?=table('status', $status, 'name')?></td>
			</tr>
	      <tr>
	        <th width="35%">浏览量：</th>
	        <td width="65%"><?=$pv?></td>
	      </tr>
	      <tr>
	        <th>评论：</th>
	        <td><?=($allowcomment ? $comments : '禁止')?></td>
	      </tr>
		</table>
	</div>
	<div class="clear"></div>
</div>
<?php $this->display('footer', 'system');