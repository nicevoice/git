<?php $this->display('header');?>
<?php $workflowid = table('category', $catid, 'workflowid');?>
  <div class="bk_8"></div>
  <div class="tag_1">
   <ul class="tag_list">
<?php 
if(priv::aca('interview', 'interview', 'view')) { 
?>
    <li><a href="?app=interview&controller=interview&action=view&contentid=<?=$contentid?>">查看</a></li>
<?php 
}
if(priv::aca('interview', 'chat', 'index')) { 
?>
    <li><a href="?app=interview&controller=chat&action=index&contentid=<?=$contentid?>">文字实录</a></li>
<?php 
}
?>
    <li><a href="?app=interview&controller=question&action=index&contentid=<?=$contentid?>" class="s_3">网友提问</a></li>
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
  <div>
    <div class="f_l w_80 tag_list_2" style="height:400px;">
      <ul>
        <li><a href="?app=interview&controller=question&action=index&contentid=<?=$contentid?>&state=1" <?php if ($state == 1) { ?>class="s_6"<?php }?> >待审</a></li>
        <li><a href="?app=interview&controller=question&action=index&contentid=<?=$contentid?>&state=2" <?php if ($state == 2) { ?>class="s_6"<?php }?> >已审</a></li>
        <li><a href="?app=interview&controller=question&action=index&contentid=<?=$contentid?>&state=3" <?php if ($state == 3) { ?>class="s_6"<?php }?> >推荐</a></li>
        <li><a href="?app=interview&controller=question&action=index&contentid=<?=$contentid?>&state=0" <?php if ($state == 0) { ?>class="s_6"<?php }?> >已删</a></li>
      </ul>
    </div>
    <div style="margin-left:100px;">
  <table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="table_list">
    <thead>
      <tr>
        <th width="30" class="t_l bdr_3"><input type="checkbox" id="check_all" /></th>
        <th>内容</th>
        <th width="100">操作</th>
        <th width="100">昵称</th>
        <th width="100">发布时间</th>
        <th width="150">IP</th>
      </tr>
    </thead>
    <tbody id="list_body">
    </tbody>
  </table>
  
<?php $this->display("state/$state");?>

</div>
<div class="clear"></div>
</div>

<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css"/>

<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.pagination.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="apps/interview/js/question.js"></script>
<script type="text/javascript">
var state = <?=$state?>;
var contentid = <?=$contentid?>;

var tableApp = new ct.table('#item_list', {
    rowIdPrefix : 'row_',
    rightMenuId : 'right_menu',
    pageField : 'page',
    pageSize : 15,
    dblclickHandler : state == 1 ? 'question.pass' : null,
    rowCallback     : 'init_row_event',
    template : row_template,
    baseUrl  : '?app=interview&controller=question&action=page&contentid='+contentid+'&state='+state
});

tableApp.load();
</script>
<?php $this->display('footer');
