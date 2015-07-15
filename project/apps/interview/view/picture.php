<?php 
$picture = isset($picture) ? $picture : $_GET['contentid'];
$pictures = loader::model('admin/picture_group', 'picture')->ls($picture);
?>
    <div class="f_l"><a href="#" onclick="return false;"><img class="previmg" src="<?=IMG_URL?>images/pic_pre.gif" height="72" width="16" alt="上一张"/></a></div>
    <div class="f_r"><a href="#" onclick="return false;"><img class="nextimg" src="<?=IMG_URL?>images/pic_next.gif" height="72" width="16" alt="下一张"/></a></div>
	<div id="pictures">
	    <ul class="inline w_141">
		 <?php foreach ($pictures as $pic) { ?>
		     <li style="margin:8px"><a href="<?=$pic['url']?>" target="_blank"><img src="<?=thumb($pic['image'], 115, 90)?>" alt="<?=$pic['note']?>"/></a></li>
		 <?php } ?>	
	    </ul>
	</div>