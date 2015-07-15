<div class="bk_8"></div>
<form id="guestbook_reply" method="POST" class="validator" action="?app=guestbook&controller=guestbook&action=replyed&gid=<?php echo $data['gid'];?>">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
	<tr>
		<th width="80" class="t_r">留言者信息:</th>
		<td width="100">姓名：<span class="c_gray"><?php echo $data['username'];?><span></td>
	</tr>
	<tr>
		<th></th>
		<td>IP：<span class="c_gray"><?php echo $data['ip'];?>(<?php echo $data['location']; ?>)<span></td>
	</tr>
	<tr>
		<th></th>
		<td>留言时间：<span class="c_gray"><?php echo $data['addtime'];?><span></td>
	</tr>
	<?php if($data['gender']){ ?>
	<tr>
		<th></th>
		<td>性别：<span class="c_gray"><?php echo $data['gender'];?><span></td>
	</tr>
	<?php } ?>
	<?php if($data['qq']){ ?>
	<tr>
		<th></th>
		<td>QQ ：<span class="c_gray"><?php echo $data['qq'];?></span></td>
	<tr>
	<?php } ?>
	<?php if($data['msn']){ ?>
	<tr>
		<th></th>
		<td> MSN ：<span class="c_gray"><?php echo $data['msn'];?></span></td>
	</tr>
	<?php } ?>
	<?php if($data['homepage']){ ?>
	<tr>
		<th></th>
		<td>个人主页：<span class="c_gray"><?php echo $data['homepage'];?></span></td>
	</tr>
	<?php } ?>
	<?php if($data['email']){ ?>
	<tr>
		<th></th>
		<td>Email：<span class="c_gray"><?php echo $data['email'] ?></span></td> 
	</tr>
	<?php } ?>
	<tr>
		<th width="80" class="t_r">标题:</th>
		<td><span class="c_gray"><?php echo $data['title'];?></span>  </td>
	</tr>
	<tr>
		<th>内容:</th>
		<td><div  class="c_gray" ondblclick="setTextArea(this,<?php echo $data['gid'];?>)" id="content<?php echo $data['gid'];?>" style="height:auto;border :1px solid #94C5E5;line-height:20px;padding:3px;overflow:hidden;margin-right:5px"><?php echo $data['content']; ?></div> </td>
	</tr>
	<tr><th>回复:</th>
		<td align="center"><br/>
		<textarea name="reply" id="textarea" cols="40" rows="9"><?php echo  $data['reply'] ? $data['reply'] : '';?></textarea>
		</td>
	</tr>
</table>
</form>
<script type="text/javascript">
var setStateId = 0;
function setTextArea(obj,gid) {
	if($("#content"+setStateId+">textarea").val() != undefined) {
		$.post('?app=guestbook&controller=guestbook&action=edit_content',{gid:gid,content:$("#content"+setStateId+">textarea").val()},function(data){$("#content"+setStateId).html(data.conentet);
		},'json');
	} else {
		obj.innerHTML = '<textarea cols="" onblur="setTextArea($(\'#content'+gid+'\'),'+gid+')" rows="5" style="width:100%;">'+obj.innerHTML.replace(/<[\/]?span[^>]*>/ig,'')+'</textarea>';
		$("#content"+gid+">textarea").focus();
		setStateId = gid;
	}
}
</script>