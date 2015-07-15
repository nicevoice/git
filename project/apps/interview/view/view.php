<?php $this->display('header');?>
<?php $workflowid = table('category', $catid, 'workflowid');?>
<script type="text/javascript" src="tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="tiny_mce/editor.js"></script>
<script type="text/javascript" src="js/related.js"></script>
<div class="bk_8"></div>
<div class="tag_1">
  <ul class="tag_list">
    <li><a href="?app=interview&controller=interview&action=view&contentid=<?=$contentid?>" class="s_3">查看</a></li>
<?php 
if(priv::aca('interview', 'chat', 'index')) { 
?>
    <li><a href="?app=interview&controller=chat&action=index&contentid=<?=$contentid?>">文字实录</a></li>
<?php 
}
if($status == 6 && priv::aca('interview', 'question', 'index')) { 
?>
    <li><a href="?app=interview&controller=question&action=index&contentid=<?=$contentid?>">网友提问</a></li>
<?php 
}
?>
  </ul>
<?php 
if(priv::aca('interview', 'interview', 'edit')) { 
?>
  <input type="button" name="edit" value="修改" class="button_style_1" onclick="content.edit(<?=$contentid?>)"/>
<?php 
}
if($status == 6 && priv::aca('interview', 'interview', 'createhtml')) { 
?>
  <input type="button" name="createhtml" value="生成" class="button_style_1" onclick="content.createhtml(<?=$contentid?>)"/>
<?php 
}
if ($status < 6 && priv::aca('interview', 'interview', 'publish')) { 
?>
  <input type="button" name="publish" value="发布" class="button_style_1" onclick="content.publish(<?=$contentid?>)"/>
<?php 
}

if ($status > 0 && priv::aca('interview', 'interview', 'remove')) { 
?>
  <input type="button" name="remove" value="删除" class="button_style_1" onclick="interview.remove(<?=$contentid?>)"/>
<?php 
}
if ($status == 0) { 
	if (priv::aca('interview', 'interview', 'delete')){
?>
  <input type="button" name="delete" value="删除" class="button_style_1" onclick="content.del(<?=$contentid?>)"/>
<?php 
    }
    if (priv::aca('interview', 'interview', 'restore')) { 
?>
  <input type="button" name="restore" value="还原" class="button_style_1" onclick="content.restore(<?=$contentid?>)"/>
<?php 
    }
}
if (($status == 1 || ($workflowid && $status == 2)) && priv::aca('interview', 'interview', 'approve')) { 
?>
  <input type="button" name="approve" value="送审" class="button_style_1" onclick="content.approve(<?=$contentid?>)"/>
<?php 
}
if ($status == 3) {
	if (priv::aca('interview', 'interview', 'pass')) { 
?>
  <input type="button" name="pass" value="通过" class="button_style_1" onclick="content.pass(<?=$contentid?>)"/>
<?php 
    }
    if (priv::aca('interview', 'interview', 'reject')) { 
?>
  <input type="button" name="reject" value="退稿" class="button_style_1" onclick="content.reject(<?=$contentid?>)"/>
<?php 
    }
}
if (priv::aca('interview', 'interview', 'move')){
?>
  <input type="button" name="move" value="移动" class="button_style_1" onclick="content.move(<?=$contentid?>)"/>
<?php 
}
if (priv::aca('interview', 'interview', 'reference')){
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
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
  <tr>
    <th width="80">标题：</th>
    <td>
    <?php if($status==6){?> <a href="<?=$url?>" target="_blank"><?=$title?></a> <?php }else{?> <?=$title?> <?php }?>
     （第<?=$number?>期）
    </td>
  </tr>
<?php if($status==6){?>
  <tr>
    <th>网址：</th>
    <td><a href="<?=$url?>" target="_blank"><?=$url?></a>
  </tr>
<?php }?>
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
	    <th>访谈状态：</th>
	    <td>
		    <table>
			    <tr>
				    <td height="25">
			        <label><input type="radio" name="state" id="state" value="0" <?php if ($state == 0) echo 'checked';?>/> 未开始</label>&emsp;
			        <label><input type="radio" name="state" id="state" value="1" <?php if ($state == 1) echo 'checked';?>/> 进行中</label>&emsp;
			        <label><input type="radio" name="state" id="state" value="2" <?php if ($state == 2) echo 'checked';?>/> 已结束</label>
				    </td>
				    <td>&nbsp;<input type="button" value="设置" class="button_style_1" onclick="interview.set_state(<?=$contentid?>, $('input[name=state]:checked').val())"></td>
			    </tr>
		    </table>
	    </td>
	  </tr>
	  <tr>
	    <th width="80">访谈<?=($mode == 'video' ? '视频' : '照片')?>：</th>
	    <td>
<?php 
if ($mode=='video' && $video) 
{
	$this->display('player'.$player, 'viedo');
}
elseif($photo)
{
?>
       <img src="<?php echo thumb($photo, 285, 240);?>" alt="<?=$title?>"/>
<?php 
}
?> 
	    </td>
	  </tr>
	  <tr>
	    <th>访谈时间：</th>
	    <td><?=$starttime ? date('Y-m-d H:i:s', $starttime) : ''?>&nbsp;~&nbsp;<?=$endtime ? date('Y-m-d H:i:s', $endtime) : ''?></td>
	  </tr>
	  <tr>
	    <th>访谈地点：</th>
	    <td><?=$address?></td>
	  </tr>
	  <tr>
	    <th>主持人：</th>
	    <td><?=$compere?></td>
	  </tr>
	  <tr>
	    <th>嘉宾：</th>
	    <td><?php foreach ($guest as $r){ echo '<font color="'.$r['color'].'">'.$r['name'].'</font>&nbsp;&nbsp;'; } ?></td>
	  </tr>
	  <tr>
	    <th>网友发言：</th>
	    <td><?=$allowchat ? "$questions 条" : '禁止'?></td>
	  </tr>
	  <tr>
	    <th>发言审核：</th>
	    <td><?=$ischeck ? '是' : '否'?></td>
	  </tr>
	  <tr>
	    <th>发言时段：</th>
	    <td><?=$startchat ? date('Y-m-d H:i:s', $startchat) : ''?> ~ <?=$endchat ? date('Y-m-d H:i:s', $endchat) : ''?></td>
	  </tr>
</table>
<?php if($description){ ?>
<div class="title mar_l_8">访谈介绍</div>
<div class="content"><?=$description?></div>
<?php } ?>

<div class="title mar_l_8">滚动公告 <a href="javascript: interview.set_notice('<?=$contentid?>');" style="color:red">设置</a></div>
<div id="notice_content" class="content"><?=$notice?></div>

<div class="title mar_l_8">精彩观点 <a href="javascript: interview.set_review('<?=$contentid?>');" style="color:red">设置</a></div>
<div id="review_content" class="content"><?=$review?></div>

<div class="title mar_l_8">图片报道</div>
<div class="content">
<form id="picture_add" method="POST" action="?app=interview&controller=interview&action=picture">
<input type="hidden" name="contentid" id="contentid" value="<?=$contentid?>" />
    <table border="0" width="100%" cellspacing="0" id="tabledata"  cellpadding="0"  class="table_form">
	  <tr>
        <th width="80">组图ID：</th>
        <td width="150">
        <input id="picture" type="text" size="15" class="c_gray" value="<?php if(isset($picture) && $picture){echo $picture;}else{echo '请输入组图ID';}?>" name="picture" onfocus="if(this.value=='请输入组图ID'){this.value='';$(this).addClass('c_gray');}else{$(this).removeClass('c_gray');}" onblur="if(this.value==''){this.value='请输入组图ID';$(this).addClass('c_gray');}else{$(this).removeClass('c_gray');if(isNaN(this.value)){ct.warn('请输入数字ID号！');this.value='请输入组图ID';}}" onchange="interview.picture_load(this.value);" /> 
        <a href="javascript:;" onclick="select_picture(3, '')" title="选择组图"><img src="images/add_1.gif" title="选择组图"/></a></td>
        <td><input type="submit"  id="submit"   name="publish" id="publish" value="保存"  class="button_style_2"/></td>
      </tr>
    </table>
</form>
</div>

<div id="picture_group" style="margin:10px">
<?php if($picture) $this->display('picture');?>
</div>
  <div class="title mar_l_8">相关</div>
    <div class="mar_l_8" style="padding-left:6px">   
            <ul class="cols_2 list_4">
  		    <?php foreach ($relateds as $k=>$r){ ?>
      			<li><a href="<?=$r['url']?>" target="_blank"><?=$r['title']?></a></li>
    		<?php } ?>    
   		   </ul>
    </div>
  </div>
  <div class="f_l w_200 box_6">
    <h3><span class="b">访谈属性</span></h3>
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

<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.jcarousellite.js"></script>
<script type="text/javascript">
$('#picture_add').ajaxForm(function (response)
{
	if (response.state)
	{
	    ct.ok('图片报道设置成功！');
	}
	else
	{
		ct.error(response.error);
	}
});

function select_picture(apiid, keywords)
{
	relateddata = related_data(apiid);
	var result = showModalDialog('?app=system&controller=related&action=picture', window, "dialogWidth:450px;dialogHeight:465px;help:0;status:0;center:yes;scroll:no");
	if(result != null)
	{
		$("#picture").val(result);
		interview.picture_load(result);
	}
}

$("#pictures").jCarouselLite({
    btnNext: ".nextimg",
    btnPrev: ".previmg",
    circular: true,
    auto: 2000,
    speed: 1000,
    scroll: 1,
    visible: 4,
    start: 0
});
</script>
<?php $this->display('footer');?>