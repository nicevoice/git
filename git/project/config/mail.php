<?php
return array(
    'sites' => array(
        'sina.com' => 'http://mail.sina.com.cn/',
        'vip.sina.com' => 'http://mail.sina.com.cn/',
        'sina.cn' => 'http://mail.sina.com.cn/',
        '3g.sina.cn' => 'http://weibo.com/',

        '189.cn' => 'http://mail.189.cn/',
        'wo.com.cn' => 'http://mail.wo.com.cn',
        '139.com' => 'http://mail.139.com/',

        'yahoo.cn' => 'http://mail.yahoo.cn/',
        'sohu.com' => 'http://mail.sohu.com/',
        'gmail.com' => 'http://mail.google.com/',
        'hotmail.com' => 'http://www.hotmail.com/',
        '126.com' => 'http://www.126.com/',
        '163.com' => 'http://mail.163.com/',
    ),

    'interval' => 20, // 每隔多久查询一次（以无限循环方式运行时有效）
    'interval_size' => 50, // 每次获取（执行）多少记录
    'queue_max_times' => 3 // 每条记录最多执行次数
);