<?php 
$endtime = strtotime('2013-07-21 01:00:00');
//echo date('Y-m-d H:i:s',$endtime);
$time = $endtime - time();
echo '<div style="padding:200px; font-size:30px;">';
echo '距离 Suman到来时间 ... <br><br>';
echo '<font style="color:red">'.$time.'</font> 秒    ';
echo '<font style="color:red">'.$time/(24*60) .'</font> 小时   ';
echo '<font style="color:red">'.$time/(60*60*24).'</font> 天   <br><br>';
echo '这次不会在失信了,没有任何事情能阻挡我的信心!<br >';
echo '當你想念一個人的時候，盡情去想念吧，也許有一天，你再也不會如此想念他了</div>';
?>