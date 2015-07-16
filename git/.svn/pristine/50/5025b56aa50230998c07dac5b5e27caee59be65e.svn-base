<?php $this->display('header');?>
<?php $workflowid = table('category', $catid, 'workflowid');?>
<div class="bk_10"></div>
<div class="tag_1">
   <ul class="tag_list">
    <li class="s_3"><a href="?app=picture&controller=picture&action=view&contentid=<?=$contentid?>" class="s_3">查看</a></li>
   </ul>
<?php 
if(priv::aca('picture', 'picture', 'edit')) { 
?>
  <input type="button" name="edit" value="修改" class="button_style_1" onclick="content.edit(<?=$contentid?>)"/>
<?php 
}
if($status == 6 && priv::aca('picture', 'picture', 'createhtml')) { 
?>
  <input type="button" name="createhtml" value="生成" class="button_style_1" onclick="content.createhtml(<?=$contentid?>)"/>
<?php 
}
if ($status < 6 && priv::aca('picture', 'picture', 'publish')) { 
?>
  <input type="button" name="publish" value="发布" class="button_style_1" onclick="content.publish(<?=$contentid?>)"/>
<?php 
}
if ($status > 0 && priv::aca('picture', 'picture', 'remove')) { 
?>
  <input type="button" name="remove" value="删除" class="button_style_1" onclick="content.remove(<?=$contentid?>)"/>
<?php 
}
if ($status == 0) { 
	if (priv::aca('picture', 'picture', 'delete')){
?>
  <input type="button" name="delete" value="删除" class="button_style_1" onclick="content.del(<?=$contentid?>)"/>
<?php 
    }
    if (priv::aca('picture', 'picture', 'restore')) { 
?>
  <input type="button" name="restore" value="还原" class="button_style_1" onclick="content.restore(<?=$contentid?>)"/>
<?php 
    }
}
if (($status == 1 || ($workflowid && $status == 2)) && priv::aca('picture', 'picture', 'approve')) { 
?>
  <input type="button" name="approve" value="送审" class="button_style_1" onclick="content.approve(<?=$contentid?>)"/>
<?php 
}
if ($status == 3) {
	if (priv::aca('picture', 'picture', 'pass')) { 
?>
  <input type="button" name="pass" value="通过" class="button_style_1" onclick="content.pass(<?=$contentid?>)"/>
<?php 
    }
    if (priv::aca('picture', 'picture', 'reject')) { 
?>
  <input type="button" name="reject" value="退稿" class="button_style_1" onclick="content.reject(<?=$contentid?>)"/>
<?php 
    }
}
if (priv::aca('picture', 'picture', 'move')){
?>
  <input type="button" name="move" value="移动" class="button_style_1" onclick="content.move(<?=$contentid?>)"/>
<?php
}
if (priv::aca('picture', 'picture', 'reference')){
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
    <td><a href="<?php echo UPLOAD_URL.$thumb?>" target="_blank"><img  src="<?php echo thumb($thumb, 100, 100);?>"></a></td>
  </tr>
<?php } ?>
	  <?php $this->display('workflow/view_inc', 'system');?>
</table>
<?php if($description){ ?>
<div class="title mar_l_8">摘要</div>
<div class="content"><?=$description?></div>
<?php } ?>
<div class="title mar_l_8">组图(<?=$total?>)</div>
   <div class="expand mar_l_8">
    <ul class="inline" id="pictureList">
      <?php foreach ($pictures as $key=>$pic) :?>
      <li id="imgfloat_<?=$pic['pictureid'];?>" style="width:116px; height: 120px">
	      <div><a href="<?php echo UPLOAD_URL;?><?=$pic['image'];?>"><img src="<?php echo thumb($pic['image'], 100, 100);?>" tips="<?=$pic['note'];?>" /></a></div>
	      <div style="line-height: 150%"><?=str_cut($pic['note'], 14);?></div>
      </li>
      <?php endforeach;?>
    </ul>
    <div class="clear"></div>
  </div>
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
    <div class="f_l w_200 box_6"><h3><span class="b">组图属性</span></h3>
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
<link rel="stylesheet" type="text/css" href="css/imagesbox.css" media="screen" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.lightbox.js"></script>
<script type="text/javascript">
$('#pictureList a').lightBox();
$("#pictureList img").attrTips('tips');
</script>
<?php $this->display('footer');