<?php

include 'sWechat/swechat.php';

use sWechat\Core\Wechat;
//初始化微信类
$wechat = new WeChat(WECHAT_TOKEN, TRUE);
echo $wechat->run();

//$wechat->checkSignature();
exit;
