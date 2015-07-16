<style type="text/css">
    .dialog-box td {background: #fff;}
    .dialog-box p {padding:0;}
    .move_cursor {width:16px; height:16px; background: url('css/images/move.png'); margin: 0 auto;}
</style>
<div class="bk_10"></div>
<p class="f_r vt_m" style="height:25px;line-height:25px;margin-right:16px;*margin-right:8px;padding:0 0 10px;">
    <input name="addrow" type="button" value="新建行" class="button_style_1"/>
</p>
<table width="95%" border="0" cellspacing="0" cellpadding="0" class="table_info mar_l_8">
  <thead>
	<tr>
		<th width="20" class="t_c"><div class="move_cursor"></div></th>
		<th class="t_c">内容</th>
		<th width="90" class="t_c">操作</th>
	</tr>
  </thead>
  <tbody id="sortable">
	<?php foreach($section['data'] as $key => $line):?>
	<tr>
		<td class="t_c" style="cursor: move;" width="30"><?=$key+1?></td>
		<td row="<?=$key?>">
			<ul class="inline w_400">
				<?php foreach($line as $sort => $item):?>
		        <li col="<?=$sort ? $sort : 0?>" url="<?=$item['url']?>">
		        	<a href="<?=$item['url']?>" tips="<?=$item['tips']?>" target="_blank"><?=$item['title']?></a>
		        </li>
		        <?php endforeach;?>
		    </ul>
		</td>
		<td class="t_c" width="100">
		    <img src="images/add_1.gif" alt="增加" height="16" width="16" action="additem" class="hand" />
		    <img src="images/up.gif" alt="上移" title="上移"  width="16" height="16" action="uprow" class="hand"/>
		    <img src="images/down.gif" alt="下移" title="下移"  width="16" height="16" action="downrow" class="hand"/>
		    <img src="images/del.gif" height="16" width="16" alt="删除" title="删除" action="delrow" class="hand"/>
		</td>
	</tr>
	<?php endforeach;?>
  </tbody>
</table>
<div class="bk_5"></div>
<div style="margin:0 8px;">
	<p class="f_r vt_m" style="height:30px;line-height:30px;margin-right:7px;*margin-right:0;padding:0;">
        <input name="addrow" pos="last" type="button" value="新建行" class="button_style_1"/>
    </p>
    <p class="f_l vt_m" style="height:30px;line-height:30px;">
	    <?php if($section['nextupdate'] < TIME):?>
	    <label><input name="commit_publish" class="checkbox_style" onclick="this.checked ? $('#nextupdate').show() : $('#nextupdate').hide()" type="checkbox" /> 定时发布</label>
	    <input type="text" id="nextupdate" class="input_calendar" name="nextupdate" style="display:none" value="<?=date('Y-m-d H:i:s', time()+3600);?>" onclick="DatePicker(this,{'format':'yyyy-MM-dd HH:mm:ss'});" />
	    <?php else:?>
	    <label><input name="commit_publish" class="checkbox_style" onclick="this.checked ? $('#nextupdate').show() : $('#nextupdate').hide()" type="checkbox" checked="checked" /> 定时发布</label>
	    <input type="text" id="nextupdate" class="input_calendar" name="nextupdate" value="<?=date('Y-m-d H:i:s', $section['nextupdate'])?>" onclick="DatePicker(this,{'format':'yyyy-MM-dd HH:mm:ss'});" />
	    <?php endif;?>
	</p>
</div>
<div class="clear"></div>
<div class="bk_10"></div>