<style type="text/css">
	td {background:#fff;}
</style>
<div class="title">
	<div class="f_r">
	<?php if ($section['published']):?>
	上次生成时间：<?=date('Y-m-d H:i:s', $section['published']);?>
	<?php else:?>
	未生成
	<?php endif;?>
	&nbsp;&nbsp;
	<input type="button" value="发布" class="button_style_1" onclick="page.publish(<?=$section['sectionid']?>); return false;" />
	</div>
	<div class="f_l"><?=$section['name']?>（编辑）</div>
</div>
<?php if ($section['description']):?>
<p><b>备注：</b><?=$section['description']?></p>
<?php endif;?>
<p class="f_r vt_m" style="height:25px;line-height:25px">
    <input name="addrow" type="button" value="新建行" class="button_style_1"/>
</p>
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="empty-cells:show;" class="table_info mar_10">
	<thead>  
		<tr>
			<th width="30"><div class="move_cursor"></div></th>
			<th width="">内容</th>
			<th width="120">操作</th>
		</tr>
	</thead>
	<tbody id="sortable">
	<?php foreach($section['data'] as $key => $line):?>
	  <tr>
	    <td class="t_c" width="30" style="cursor:move"><?=$key+1?></td>
	    <td row="<?=$key?>">
	    	<ul class="inline w_120">
				<?php foreach($line as $sort => $item):?>
	            <li col="<?=$sort ? $sort : 0?>" url="<?=$item['url']?>"><a href="<?=$item['url']?>" style="color:<?=$item['color']?>" tips="<?=$item['tips']?>" target="_blank"><?=$item['title']?></a></li>
	            <?php endforeach;?>
	        </ul>&nbsp;
	    </td>
	    <td class="t_c" width="120">
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
<form method="POST" action="?app=page&controller=section&action=edit">
<div>
    <p class="f_r vt_m" style="height:30px;line-height:30px">
        <input name="addrow" pos="last" type="button" value="新建行" class="button_style_1"/>
    </p>
    <p class="f_l vt_m" style="height:30px;line-height:30px">
	    <?php if($section['nextupdate'] < TIME):?>
	    <label style="cursor:pointer"><input class="checkbox_style" onclick="this.checked ? $('#nextupdate').show().removeAttr('disabled') : $('#nextupdate').hide().attr('disabled','disabled')" type="checkbox" /> 定时发布 </label>
	    <input type="text" id="nextupdate" class="input_calendar" name="nextupdate" style="display:none" disabled="disabled" value="<?=date('Y-m-d H:i:s', time()+3600);?>" onclick="DatePicker(this,{'format':'yyyy-MM-dd HH:mm:ss'});" />
	    <?php else:?>
	    <label style="cursor:pointer"><input class="checkbox_style" onclick="this.checked ? $('#nextupdate').show().removeAttr('disabled') : $('#nextupdate').hide().attr('disabled','disabled')" type="checkbox" checked="checked" /> 定时发布 </label>
	    <input type="text" id="nextupdate" class="input_calendar" name="nextupdate" value="<?=date('Y-m-d H:i:s', $section['nextupdate'])?>" onclick="DatePicker(this,{'format':'yyyy-MM-dd HH:mm:ss'});" />
	    <?php endif;?>
    </p>
</div>
<div class="clear"></div>
<input type="hidden" name="sectionid" value="<?=$section['sectionid'];?>" />
<div style="height:30px;line-height:30px;">
	<input type="submit" value="保存" class="button_style_1"/>
    <input onclick="page.previewSection(this.form); return false;" type="button" value="预览" class="button_style_1"/>
    <input type="button" onclick="page.unsave(<?=$section['sectionid']?>); return false;" value="退出" class="button_style_1"/>
</div>
</form>

<div id="viewholder"></div>
<div class="title mar_10">
	<div class="f_r">
		<button class="button_style_1" onclick="page.clearLog()">清空历史记录</button>
		<button class="button_style_1" onclick="page.restoreLog('orig')">恢复到原始数据</button>
	</div>
	<div class="f_l">历史记录</div>
</div>
<div class="calendar f_l" style="width:190px"></div>
<div class="logtable f_l mar_l_8">
	<?php $this->display('section/log', 'page');?>
</div>