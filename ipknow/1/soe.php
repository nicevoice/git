<?php 
$endtime = strtotime('2013-07-21 01:00:00');
//echo date('Y-m-d H:i:s',$endtime);
$time = $endtime - time();
echo '<div style="padding:200px; font-size:30px;">';
echo '���� Suman����ʱ�� ... <br><br>';
echo '<font style="color:red">'.$time.'</font> ��    ';
echo '<font style="color:red">'.$time/(24*60) .'</font> Сʱ   ';
echo '<font style="color:red">'.$time/(60*60*24).'</font> ��   <br><br>';
echo '��β�����ʧ����,û���κ��������赲�ҵ�����!<br >';
echo '��������һ���˵ĕr�򣬱M��ȥ����ɣ�Ҳ�S��һ�죬����Ҳ���������������</div>';
?>