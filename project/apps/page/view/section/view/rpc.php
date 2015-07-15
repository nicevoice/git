<div class="title">
	<div class="f_r">
		<?php if ($section['published']):?>
		上次抓取时间：<?=date('Y-m-d H:i:s', $section['published']);?>
		<?php else:?>
		未抓取
		<?php endif;?>
		&nbsp;&nbsp;
		<input type="button" value="抓取" class="button_style_1" onclick="page.grapSection(<?=$section['sectionid']?>); return false;" /> 
	</div>
	<div class="f_l"><?=$section['name']?>（预览）</div>
</div>
<div class="bk_5"></div>
<div id="html_content" style="border:1px solid #9C6; padding:8px;<?=($section['width'] > 0)?'width:'.($section['width']+16).'px;':''?>"<?=$section['width']?' title="区块宽度'.$section['width'].'px"' : ''?>>
</div>

<div class="bk_10"></div>
<input type="button" value="立即抓取" class="button_style_1" onclick="page.grapSection(<?=$section['sectionid']?>); return false;" />
<?php if ($section['nextupdate']>TIME):?>
<span>下次更新时间：<?=date('Y-m-d H:i:s', $section['nextupdate'])?></span>
<?php endif;?>
<?php if($lockedid && $lockedid != $_userid): ?>
	<span>当前<b><?=username($lockedid)?></b>在编辑</span>
	<input type="button" onclick="page.unlock(<?=$section['sectionid']?>,this); return false;" value="解除锁定" class="button_style_1" />
<?php endif;?>

<div class="bk_10"></div>
<div class="title">区块属性</div>
<table cellpadding="0" cellspacing="0" border="0" class="table_form mar_t_10" width="500">
  <tr>
    <th width="90">ID：</th>
    <td><?=$section['sectionid']?></td>
  </tr>
  <tr>
    <th>类型：</th>
    <td>RPC</td>
  </tr>
  <tr>
    <th>管理：</th>
    <td><a href="javascript:;" onclick="page.setProperty(<?=$section['sectionid']?>); return false;" >[设置]</a>　<a href="javascript: sectionpriv.set(<?=$section['sectionid']?>);">[权限]</a></td>
  </tr>
   <tr>
     <th>编辑：</th>
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
  	<th>引用代码：</th>
    <td><input type="text" size="50" value="&lt;!--#include virtual=&quot;/section/<?=$section['sectionid']?>.html&quot;--&gt;&lt;!--<?=htmlspecialchars(table('page', intval($section['pageid']), 'name'))?> <?=htmlspecialchars($section['name'])?>--&gt;" onfocus="this.select();" /></td>
  </tr>
  <tr>
  	<th>更新频率：</th>
    <td><?=$section['frequency']?></td>
  </tr>
  <tr>
  	<th>RPC 源：</th>
    <td><?=$section['url']?></td>
  </tr>
  <tr>
  	<th>调用接口：</th>
    <td><?=$section['method']?></td>
  </tr>
  <tr>
  	<th>参数：</th>
    <td><?=$section['args']?></td>
  </tr>
  <tr>
  	<th>备注：</th>
  	<td><?=$section['description']?></td>
  </tr>
</table>