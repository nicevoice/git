<?php $this->display('header','system');?>
<div class="bk_10"></div>
<div class="tag_1">
	<ul class="tag_list" id="datatype" style="margin-left:145px">
		<li><a href="?app=system&controller=rank&action=index" class="s_3">综合排行</a></li>
	    <li><a href="?app=system&controller=rank&action=rank_pv">点击排行</a></li>
	    <li><a href="?app=system&controller=rank&action=rank_comments">评论排行</a></li>
	    <li><a href="?app=system&controller=rank&action=rank_digg">Digg排行</a></li>
	    <li><a href="?app=system&controller=rank&action=rank_mood">心情排行</a></li>
	</ul>
</div>
<div>
 <div id="proids">
 <dl class="stat_channel" id="channel"><dt class="f_l">频道：</dt>
 	<dd class="f_l">
 <?php 
 $channels = channel();
 foreach ($channels as $v):?>
 <a href="#channel_<?=$v['catid']?>"><?=$v['name']?></a>
 <?php endforeach;?>
 </dd></dl>
 </div>
</div>
<div class="stat_channelInner">
<?php 
$content = loader::model('admin/content');
$db = factory::db();
foreach ($channels as $va):
?>
 	<div class="head"><h3 class="b f_l"><?=$va['name']?></h3><a name="channel_<?=$va['catid']?>"></a></div>
	
	<!-- 点击排行 -->
	<div class="mod statMod">
		<div class="inner">
			<div class="hd">
				<h3>点击排行</h3>
			</div>
			<div class="bd">
				<ul>
				
	<?php $data = $content->select("status=6 AND catid IN($va[childids])", "*", 'comments DESC', 10);?>
		<?php foreach ($data as $i => $d): ?>
		<li<?php if($i<3) echo ' class="front"';?>><em><?=$i+1?></em><a href="<?=$d['url']?>" target="_blank"><?=$d['title']?></a></li>
		<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
	
	<!-- 评论排行  -->
	<div class="mod statMod">
		<div class="inner">
			<div class="hd">
				<h3>评论排行</h3>
			</div>
			<div class="bd">
				<ul>
	<?php $data = $content->select("status=6 AND catid IN($va[childids])", "*", 'pv DESC', 10);?>
		<?php foreach ($data as $i=>$d): ?>
		<li<?php if($i<3) echo ' class="front"';?>><em><?=$i+1?></em><a href="<?=$d['url']?>" target="_blank"><?=$d['title']?></a></li>
		<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
	
	<!-- Digg排行  -->
	<div class="mod statMod">
		<div class="inner">
			<div class="hd">
				<h3>DIGG排行</h3>
			</div>
			<div class="bd">
				<ul>
	<?php $data = $db->select("SELECT * FROM #table_content AS c,#table_digg AS d WHERE c.status=6 AND c.contentid=d.contentid AND c.catid IN($va[childids]) ORDER BY d.supports DESC LIMIT 10")?>
		<?php foreach ($data as $i=>$d): ?>
		<li<?php if($i<3) echo ' class="front"';?>><em><?=$i+1?></em><a href="<?=$d['url']?>" target="_blank"><?=$d['title']?></a></li>
		<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
	
	
	<div class="clear"></div>
 <?php endforeach;?>
</div>

<?php $this->display('footer');?>