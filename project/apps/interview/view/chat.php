<?php $this->display('header');?>
<?php $workflowid = table('category', $catid, 'workflowid');?>
  <div class="bk_8"></div>
  <div class="tag_1">
   <ul class="tag_list">
<?php 
if(priv::aca('interview', 'interview', 'view')) { 
?>
    <li><a href="?app=interview&controller=interview&action=view&contentid=<?=$contentid?>" >查看</a></li>
<?php 
}
?>
    <li><a href="?app=interview&controller=chat&action=index&contentid=<?=$contentid?>" class="s_3">文字实录</a></li>
<?php 
if(priv::aca('interview', 'question', 'index')) { 
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
  <input type="button" name="remove" value="删除" class="button_style_1" onclick="content.remove(<?=$contentid?>)"/>
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
if (priv::aca('interview', 'interview', 'copy')){
?>
  <input type="button" name="copy" value="复制" class="button_style_1" onclick="content.copy(<?=$contentid?>)"/>
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
  
<div class="bk_5"></div>
<div id="chat_scroll" class="w_650 h_350 box_6 box_8 mar_l_8" style="border:4px solid #E3F0F4;">
  <ul id="chat">
  </ul>
</div>
<div class="bk_5"></div>
<div class="w_650 interview">
<form id="chat_add" action="?app=interview&controller=chat&action=add" method="POST">
  <ul>
    <li>
      <p id="guests">
        <a href="javascript:chat.guestid()" class="s_5" id="guest_">主持人</a>
        <?php foreach ($guest as $r) { ?>
        <a href="javascript:chat.guestid(<?=$r['guestid']?>)" id="guest_<?=$r['guestid']?>" style="color:<?php echo $r['color'];?>"><?=$r['name']?></a>
        <?php } ?>
      </p>
      <input type="hidden" name="guestid" id="guestid" value="">
      <input type="hidden" name="contentid" id="contentid" value="<?=$contentid?>">
      <textarea name="content" id="content" cols="120" rows="5"  style="width:635px;" class="c_gray h_80"></textarea>
    </li>
    <li><input type="submit" name="submit" id="submit" value="发送" class="button_style_2"/><span class="c_green">按 <strong>Ctrl+Enter</strong> 键即可发送</span></li>
  </ul>
</form>
</div>
<div class="clear"></div>
<script type="text/javascript" src="apps/interview/js/chat.js"></script>
<script type="text/javascript">
var data = <?=$data?>;
chat.load(data);
chat.reset();

$('#chat_add').ajaxForm('chat.add_submit');

$(document).keydown(function(event){
	event = event || window.event;
	if(event.ctrlKey && event.keyCode == 13) {
        $('#chat_add').submit();
    }
    else if(event.ctrlKey && event.keyCode == 37)
    {
    	var previd = $('.s_5').prev().is('a') ? $('.s_5').prev().attr('id') : $('#guests > a:last').attr('id');
    	var guestid = previd.substring(6);
    	chat.guestid(guestid);
    }
    else if(event.ctrlKey && event.keyCode == 39)
    {
    	var nextid = $('.s_5').next().is('a') ? $('.s_5').next().attr('id') : $('#guests > a:first').attr('id');
    	var guestid = nextid.substring(6);
    	chat.guestid(guestid);
    }
});
</script>
<?php $this->display('footer');