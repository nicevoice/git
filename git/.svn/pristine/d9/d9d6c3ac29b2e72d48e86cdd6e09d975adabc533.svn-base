<?php $this->display('header');?>
<?php $workflowid = table('category', $catid, 'workflowid');?>
<div class="bk_10"></div>
<div class="tag_1">
  <ul class="tag_list">
    <li class="s_3"><a href="?app=video&controller=video&action=view&contentid=<?=$contentid?>" class="s_3">查看</a></li>
  </ul>
<?php 
if(priv::aca('video', 'video', 'edit')) { 
?>
  <input type="button" name="edit" value="修改" class="button_style_1" onclick="content.edit(<?=$contentid?>)"/>
<?php 
}
if($status == 6 && priv::aca('video', 'video', 'createhtml')) { 
?>
  <input type="button" name="createhtml" value="生成" class="button_style_1" onclick="content.createhtml(<?=$contentid?>)"/>
<?php 
}
if ($status < 6 && priv::aca('video', 'video', 'publish')) { 
?>
  <input type="button" name="publish" value="发布" class="button_style_1" onclick="content.publish(<?=$contentid?>)"/>
<?php 
}
if ($status > 0 && priv::aca('video', 'video', 'remove')) { 
?>
  <input type="button" name="remove" value="删除" class="button_style_1" onclick="content.remove(<?=$contentid?>)"/>
<?php 
}
if ($status == 0) { 
	if (priv::aca('video', 'video', 'delete')){
?>
  <input type="button" name="delete" value="删除" class="button_style_1" onclick="content.del(<?=$contentid?>)"/>
<?php 
    }
    if (priv::aca('video', 'video', 'restore')) { 
?>
  <input type="button" name="restore" value="还原" class="button_style_1" onclick="content.restore(<?=$contentid?>)"/>
<?php 
    }
}
if (($status == 1 || ($workflowid && $status == 2)) && priv::aca('video', 'video', 'approve')) { 
?>
  <input type="button" name="approve" value="送审" class="button_style_1" onclick="content.approve(<?=$contentid?>)"/>
<?php 
}
if ($status == 3) {
	if (priv::aca('video', 'video', 'pass')) { 
?>
  <input type="button" name="pass" value="通过" class="button_style_1" onclick="content.pass(<?=$contentid?>)"/>
<?php 
    }
    if (priv::aca('video', 'video', 'reject')) { 
?>
  <input type="button" name="reject" value="退稿" class="button_style_1" onclick="content.reject(<?=$contentid?>)"/>
<?php 
    }
}
if (priv::aca('video', 'video', 'move')){
?>
  <input type="button" name="move" value="移动" class="button_style_1" onclick="content.move(<?=$contentid?>)"/>
<?php 
}
if (priv::aca('video', 'video', 'reference')){
?>
  <input type="button" name="reference" value="引用" class="button_style_1" onclick="content.reference(<?=$contentid?>)"/>
<?php 
}
?>
  <input type="button" name="note" value="批注" class="button_style_1" onclick="content.note(<?=$contentid?>)"/>
  <input type="button" name="version" value="版本" class="button_style_1" onclick="content.version(<?=$contentid?>)"/>
  <input type="button" name="log" value="日志" class="button_style_1" onclick="content.log(<?=$contentid?>)"/>
</div>
<div class="bg_2">
  <div class="f_l w_600 mar_r_8">
    <table width="500" border="0" cellspacing="0" cellpadding="0" class="table_form mar_t_10 mar_l_8">
      <tr>
        <th width="60">标题：</th>
        <td><a href="<?=$url?>" target="_blank" style="color:<?=$color?>"><?=$title?></a></td>
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
    </table>

    <div class="title mar_l_8">视频</div>
    <div class="content">
    <p style="text-align:left"><?php include ROOT_PATH.'apps/video/view/player/'.$player.'.php';?></p>
    </div>
    <?php if($description){ ?>
    <div class="title mar_l_8">视频介绍</div>
    <div class="content"><?=$description?></div>
    <?php } ?>
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
    <h3><span class="b">视频属性</span></h3>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_form">
      <tr>
        <th>ID：</th>
        <td><?=$contentid?></td>
      </tr>
      <tr>
        <th>栏目：</th>
        <td><a href="<?=$caturl?>" target="_blank"><?=$catname?></a></td>
      </tr>
      <tr>
        <th>来源：</th>
        <td><a href="<?=$source_url?>" target="_blank"><?=$source_name?></a> &nbsp;</td>
      </tr>
      <tr>
        <th>作者：</th>
        <td><a href="<?=$author_url?>" target="_blank"><?=$author_name?></a>&nbsp;</td>
      </tr>
     <tr>
        <th>时长：</th>
        <td><?=$playtime?>分钟&nbsp;</td>
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
        <th>编辑：</th>
        <td><?=$editor?></td>
      </tr>
      <tr>
        <th>录入：</th>
        <td><a href="javascript: url.member(<?=$createdby?>);"><?=$createdbyname?></a></td>
      </tr>
      <tr>
        <th>录入时间：</th>
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
</div>

<?php $this->display('footer', 'system');