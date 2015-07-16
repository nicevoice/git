<div class="bk_8"></div>
  <div class="f_l w_670">
    <div class="tag_list_1 layout mar_t_10" style="padding-right: 1px;"><div class="f_14 b f_l">最近操作记录</div>
    	<div class="f_r days">
        	<?php foreach($days as $key=> $value):?>
    		<a href="#<?=($key+1)?>" from="<?=($key+1)?>"  <? if($_GET['from'] == ($key + 1)){?>class="s_5"<? }?>><?=$value['name']?></a>
            <?php endforeach;?>
        </div>
        
      <div class="clear"></div>
    </div>
    <div id="log_container" style="height:200px; overflow:auto;"> 
    <table width="96%" border="0" cellspacing="0" cellpadding="0" class="table_text mar_5">
     <tbody id="logs">
     <?=$this->display('page/log');?>
     </tbody>
    </table>
    </div>
    <div class="pagination" id="pagination"></div>
    <div class="clear"></div>
    <table cellpadding="0" cellspacing="0" border="0" class="table_form mar_t_10" width="100%">
    <caption>页面属性</caption>
   <tr>
    <th class="t_r">名称：</th>
    <td><?=$page['name']?></td>
   </tr>
   <tr>
     <th width="100" class="t_r">网址：</th>
     <td><a href="<?=$page['url']?>" target="_blank"><?=$page['url']?></a></td>
   </tr>
   <tr>
     <th width="100" class="t_r">编辑：</th>
     <td>
<?php
foreach ($adminids as $userid)
{
     echo '<a href="javascript: url.member('.$userid.');">'.username($userid).'</a> ';
}
?>
     </td>
   </tr>
  <tr>
    <th class="t_r">模板：</th>
    <td><?=$page['template'];?></td>
  </tr>
  <tr>
  	<th class="t_r">更新频率：</th>
    <td>
    	<?=$page['frequency']?$page['frequency'].'秒':'手动'?>
    </td>
  </tr>
  <?php if($page['published']):?>
  <tr>
  	<th class="t_r">上次更新：</th>
    <td><?=date('Y-m-d H:i:s',$page['published']);?><?php if($page['updatedby']):?> （<?=username($page['updatedby'])?>）<?php endif;?></td>
  </tr>
  <?php endif;?>
  <?php if($page['nextpublish']):?>
  <tr>
  	<th class="t_r">下次更新：</th>
    <td><?=date('Y-m-d H:i:s',$page['nextpublish']);?></td>
  </tr>
  <?php endif;?>
  <tr>
  	<th class="t_r">创建者：</th>
    <td><a href="javascript:url.member(<?=$page['createdby']?>);"><?=username($page['createdby']);?></a> （<?=date('Y-m-d H:i:s',$page['created']);?>）</td>
  </tr>
  <tr>
    <th class="t_r">区块数：</th>
    <td id="section_num"><script type="text/javascript">$('#section_num').text($('#section_list>li').length);</script></td>
  </tr>
  <tr>
    <th class="t_r">页面大小：</th>
    <td><?=$page['filesize']?>kb</td>
  </tr>
</table>
</div>