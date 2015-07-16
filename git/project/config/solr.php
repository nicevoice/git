<?php
return array(
    /**
     *  域名
     */
    "domain"=>'.p.kuaiji.com' ,

    /**
     * solr的配置
     */
    "solr" => array(
        'host' => '192.168.1.12',
        'port' => '8090',
        'path' => '/solr/',
        'core' => 'kuaiji',
    ),

    /**
     * 索引对应产品标识
     */
    "product" => array(
        'cms' => 1,
        'fagui' => 2,
        'wenda' => 3,
        'wenku' => 4,
        'bbs' => 5,
        'baike' => 6,
        'video' => 7,
        'so_history' => 8,
        'tag' => 9,
        'course' => 10,
        'material' => 11,
        'activity' => 12,
        'agency_news' => 13,
        'video_cmstop' => 14,
        'cms_news'=>15,//资讯
        'video_album'=>16,//视频专辑
        'cms_fagui'=>17,//法规
    ),

    /**
     * 数据库配置
     */
    "db" => array(

        "cms" => array(
            'dbhost' => '192.168.1.12:13360',
            'dbuser' => 'kj_cms_v',
            'dbpw' => 'kj_cms_v',
            'dbname' => 'cms',
            'dbcharset' => 'utf8',
            'dbpre' => 'cms_',
            'dbdebug' => true,
            'dbpconnect' => 0,
        ),

        "fagui" => array(
            'dbhost' => '192.168.1.12:13360',
            'dbuser' => 'kj_cms_v',
            'dbpw' => 'kj_cms_v',
            'dbname' => 'cms',
            'dbcharset' => 'utf8',
            'dbpre' => 'cms_',
            'dbdebug' => true,
            'dbpconnect' => 0,
        ),

        "wenda" => array(
            'dbhost' => '192.168.1.12:13360',
            'dbuser' => 'kuaiji_wenda',
            'dbpw' => 'mRz5RC2qBveyfMrK',
            'dbname' => 'kuaiji_wenda',
            'dbcharset' => 'utf8',
            'dbpre' => 'dev_answers_',
            'dbdebug' => true,
            'dbpconnect' => 0,
        ),

        "wenku" => array(
            'dbhost' => '192.168.1.12:13360',
            'dbuser' => 'kuaiji_wenku',
            'dbpw' => 'atazSDH6p7beaPKX',
            'dbname' => 'kuaiji_wenku',
            'dbcharset' => 'utf8',
            'dbpre' => 'dev_',
            'dbdebug' => true,
            'dbpconnect' => 0,
        ),

        "bbs" => array(
            'dbhost' => '192.168.1.12:13360',
            'dbuser' => 'kuaiji_bbs',
            'dbpw' => 'Y44hEpHZmpxcjXcY',
            'dbname' => 'kuaiji_bbs',
            'dbcharset' => 'utf8',
            'dbpre' => 'pre_',
            'dbdebug' => true,
            'dbpconnect' => 0,
        ),

        "baike" => array(
            'dbhost' => '192.168.1.12:13360',
            'dbuser' => 'kuaiji_baike_v1',
            'dbpw' => 'kuaiji_baike_v1',
            'dbname' => 'kuaiji_baike_v1',
            'dbcharset' => 'utf8',
            'dbpre' => 'kuaijibaike_',
            'dbdebug' => true,
            'dbpconnect' => 0,
        ),

        "video" => array(
            'dbhost' => '192.168.1.12:13360',
            'dbuser' => 'kuaiji_video_v1',
            'dbpw' => 'kuaiji_video_v1',
            'dbname' => 'kuaiji_video_v1',
            'dbcharset' => 'utf8',
            'dbpre' => 'kuaijivideo_',
            'dbdebug' => true,
            'dbpconnect' => 0,
        ),

        "so_history" => array(
            'dbhost' => '192.168.1.12:13360',
            'dbuser' => 'kj_cms_v',
            'dbpw' => 'kj_cms_v',
            'dbname' => 'cms',
            'dbcharset' => 'utf8',
            'dbpre' => 'cms_',
            'dbdebug' => true,
            'dbpconnect' => 0,
        ),

        "tag" => array(
            'dbhost' => '192.168.1.12:13360',
            'dbuser' => 'kj_cms_v',
            'dbpw' => 'kj_cms_v',
            'dbname' => 'cms',
            'dbcharset' => 'utf8',
            'dbpre' => 'cms_',
            'dbdebug' => true,
            'dbpconnect' => 0,
        ),

        "course" => array(
            'dbhost' => '192.168.1.12:13360',
            'dbuser' => 'kuaiji',
            'dbpw' => 'kuaiji.com',
            'dbname' => 'kj_brand',
            'dbcharset' => 'utf8',
            'dbpre' => 'kj_',
            'dbdebug' => true,
            'dbpconnect' => 0,
        ),

        "material" => array(
            'dbhost' => '192.168.1.12:13360',
            'dbuser' => 'kuaiji',
            'dbpw' => 'kuaiji.com',
            'dbname' => 'kj_brand',
            'dbcharset' => 'utf8',
            'dbpre' => 'kj_',
            'dbdebug' => true,
            'dbpconnect' => 0,
        ),

        "activity" => array(
            'dbhost' => '192.168.1.12:13360',
            'dbuser' => 'kuaiji',
            'dbpw' => 'kuaiji.com',
            'dbname' => 'kj_brand',
            'dbcharset' => 'utf8',
            'dbpre' => 'kj_',
            'dbdebug' => true,
            'dbpconnect' => 0,
        ),

        "agency_news" => array(
            'dbhost' => '192.168.1.12:13360',
            'dbuser' => 'kuaiji',
            'dbpw' => 'kuaiji.com',
            'dbname' => 'kj_brand',
            'dbcharset' => 'utf8',
            'dbpre' => 'kj_',
            'dbdebug' => true,
            'dbpconnect' => 0,
        ),

        "video_cmstop" => array(
            'dbhost' => '192.168.1.12:13360',
            'dbuser' => 'kuaiji',
            'dbpw' => 'kuaiji.com',
            'dbname' => 'kj_brand',
            'dbcharset' => 'utf8',
            'dbpre' => 'kj_',
            'dbdebug' => true,
            'dbpconnect' => 0,
        ),

        "cms_news" => array(
            'dbhost' => '192.168.1.12:13360',
            'dbuser' => 'kuaiji',
            'dbpw' => 'kuaiji.com',
            'dbname' => 'kj_cms',
            'dbcharset' => 'utf8',
            'dbpre' => 'cms_',
            'dbdebug' => true,
            'dbpconnect' => 0,
        ),

        "video_album" => array(
            'dbhost' => '192.168.1.12:13360',
            'dbuser' => 'kuaiji',
            'dbpw' => 'kuaiji.com',
            'dbname' => 'kj_cms',
            'dbcharset' => 'utf8',
            'dbpre' => 'cms_',
            'dbdebug' => true,
            'dbpconnect' => 0,
        ),

        "cms_fagui" => array(
            'dbhost' => '192.168.1.12:13360',
            'dbuser' => 'kuaiji',
            'dbpw' => 'kuaiji.com',
            'dbname' => 'kj_cms',
            'dbcharset' => 'utf8',
            'dbpre' => 'cms_',
            'dbdebug' => true,
            'dbpconnect' => 0,
        ),
    ),
);
