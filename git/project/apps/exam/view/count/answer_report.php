
<div class="layerBoxCon">
    <div class="title" style="margin: 0;"><h3>模考练习：<?=$exams['title']?></h3><a href="javascript:" class="close popup_close_btn" onclick="$('#popup_login').addClass('fn-dn')"></a></div>
    <div class="infoForm">
        <?php $answertime = $exams['examtime']*60-$answer['examtime'];?>
<div>交卷时间：<?php echo time_format($answer['created'])?></div>
<div class="mt20">答题时间：<?php echo $answertime?>秒</div>
<div>
<?php $_key = 1; foreach ($exams['question'] as $val): ?>
    <div class="tk-tooltitle mt20 mb20">
        <?= $propertys[$val['qid']]['name'] ?>
    </div>
    <div class="tk-lidef clearfix">
        <ul>
            <?php foreach ($val['question'] as $_val):
                $_class = $answer['option'][$_val['questionid']]['wrong'] == 1 ? 'greenli' : 'redli';
                ?>
                <li  class="<?=$_class?>"><a><?=$_key?></a></li>
            <?php $_key++;endforeach; ?>
        </ul>
    </div>

<?php endforeach; ?>
</div>
    </div>
</div>