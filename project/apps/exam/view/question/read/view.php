
<?php

$qs = loader::model('admin/question','exam')->select(array('bandid'=>$questionid));
?>
<dl class="list_2">
<dt class="c_blue b f_14"><span><?=$sort?></span>. <?=$subject?></dt>
<dd><?=$description?></dd>
<dd><?php
    foreach($qs as $q):

    ?>
        <li><?=$q['subject']?></li>
    <?php endforeach;?>
</dd>
</dl>