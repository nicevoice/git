<?php
return array(
'/' => array('aca'=>'content/content/index', 'pattern'=>array()),
'/category/{$catid}/' => array('aca'=>'content/content/category', 'pattern'=>array('catid'=>'[0-9]+')),
'/show/{$date}/{$id}.html' => array('aca'=>'content/content/show', 'pattern'=>array('id'=>'[0-9]+','date'=>'[0-9]{4}\-[0-9]{2}\-[0-9]{2}')),
'/comment/{$contentid}.html' => array('aca' =>'comment/comment/index','pattern' => array('contentid' =>'[0-9]+'))
);