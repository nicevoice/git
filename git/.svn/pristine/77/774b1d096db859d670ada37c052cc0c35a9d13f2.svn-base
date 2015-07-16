<?php
$isdev = file_exists(__DIR__.'/define.local.php');
if($isdev){
    require_once __DIR__.'/define.local.php';
}else{
    define('IS_DEBUG',false);
    ini_set('error_reporting', E_ERROR | E_PARSE);
//ini_set('error_reporting', E_ALL );

// 是否开启错误日志显示
    ini_set('display_errors', 0);
// 是否记录致命错误
    define('LOG_ERROR', true);

// 网站域名
    define('BASE_DOMAIN', '.s.com');

// 动态访问网址
    define('APP_URL', 'http://app' . BASE_DOMAIN . '/');
// 图片库的网址
    define('PIC_URL', 'http://img' . BASE_DOMAIN . '/');
// 公共图片、JS、CSS网址
    define('STYLE_URL', 'http://static' . BASE_DOMAIN . '/');
// JS的域名地址
    define('JS_URL', STYLE_URL . 'js/');
// CSS的域名地址
    define('CSS_URL', STYLE_URL . 'css/');
// 公用images的域名地址
    define('IMG_URL', STYLE_URL . '');
// 广告位地址
    define('POSTER_URL', JS_URL . 'poster/');
//套页模板URL
    define('COVER_URL', STYLE_URL . 'cover/') ;

// 附件网址
    define('FILES_URL', 'http://file' . BASE_DOMAIN . '/');
// 附件网址
    define('UPLOAD_URL', 'http://img' . BASE_DOMAIN . '/');
// 机构后台
    define('BRAND_URL', 'http://brand' . BASE_DOMAIN . '/');
// 主站域名
    define('WWW_URL', 'http://www' . BASE_DOMAIN . '/');
// 机构管理域名
    define('MANAGER_URL', 'http://manager' . BASE_DOMAIN . '/');
// 新机构管理域名
    define('MALL_URL', 'http://e' . BASE_DOMAIN . '/');
// 通行证域名
    define('PASSPORT_URL', 'http://passport' . BASE_DOMAIN . '/');
    define('REGISTER_URL', 'http://passport' . BASE_DOMAIN . '/register');
// 个人中心域名
    define('MY_URL', 'http://my' . BASE_DOMAIN . '/');
// 购物车域名
    define('CART_URL', 'http://cart' . BASE_DOMAIN . '/');
// 订单域名
    define('ORDER_URL', 'http://order' . BASE_DOMAIN . '/');
// 书城域名
    define('BOOK_URL', WWW_URL . 'book/');
// 后台域名

    if($_SERVER['HTTP_HOSTGAGA']=="http://sys.kuaiji.com") define('ADMIN_URL', 'http://sys.kuaiji.com:3345/kjloger');
    else define('ADMIN_URL', 'http://admin' . BASE_DOMAIN . '/');

// 产品详情域名
    define('ITEM_URL', 'http://item' . BASE_DOMAIN . '/');
// 站点公用动作处理的域名
    define('ACTION_URL', 'http://action' . BASE_DOMAIN . '/');
// 资讯域名
    define('NEWS_URL', 'http://news' . BASE_DOMAIN . '/');
// 视频域名
    define('VIDEO_URL', 'http://video' . BASE_DOMAIN . '/');
// 视频附件域名
    define('V_FILE_URL', 'http://v4.file' . BASE_DOMAIN . '/');
// 个人专栏
    define('SPACE_URL',  WWW_URL . 'author' . DS);
    define('EXAM_URL',  WWW_URL . 'exam' . DS);

// 论坛域名
    define('BBS_URL', 'http://bbs' . BASE_DOMAIN . '/');
//搜索域名
    define('SO_URL', 'http://so' . BASE_DOMAIN . '/');
// UC域名
    define('UC_URL', 'http://uc' . BASE_DOMAIN . '/');
// wap域名
    if($_SERVER['HTTP_HOST']=="3g.kuaiji.com") define('WAP_URL', 'http://3g' . BASE_DOMAIN . '/');
    else define('WAP_URL', 'http://m' . BASE_DOMAIN . '/');
    define('M_URL', WAP_URL);

// 上传图片的存放目录
    define('PIC_PATH', ROOT_PATH . 'upload' . DS . 'images' . DS);
// 上传附件的存放目录
    define('FILES_PATH', ROOT_PATH . 'upload' . DS . 'files' . DS);
// 上传视频的存放目录
    define('VIDEO_PATH', ROOT_PATH . 'upload' . DS . 'video' . DS);

    define('FW_PATH', ROOT_PATH . 'framework' . DS);
    define('CACHE_PATH', ROOT_PATH . 'data' . DS . 'cache' . DS);
    define('PUBLIC_PATH', ROOT_PATH . 'public' . DS);
    define('WWW_PATH', PUBLIC_PATH . 'html' . DS . 'web' . DS);
    define('PC_PATH_NGCACHE', PUBLIC_PATH . 'html' . DS . 'web' . DS. '_NGCACHE_' .DS);
    define('UPLOAD_PATH', ROOT_PATH . 'upload' . DS . 'images' . DS);
    define('VIDEO_PATH', ROOT_PATH . 'upload' . DS . 'video' . DS);

    define('WAP_PATH', PUBLIC_PATH . 'html' . DS . 'mobile' . DS);
    define('IMG_PATH', PUBLIC_PATH . 'img' . DS);
//套页模板路径
    define('COVER_PATH', PUBLIC_PATH . 'img' . DS . 'cover' . DS);
    define('STATIC_PATH', PUBLIC_PATH . 'static' . DS);
    define('BRANDS_CACHE_PATH', ROOT_PATH . 'data' . DS . 'brands' . DS);
    define('CACHE_COMMON_PATH', BRANDS_CACHE_PATH . 'common' . DS);

    /* 允许上传的文件类型 ，开始和末尾不加|，*代表任意格式  */
    define('UPLOAD_FILE_EXTS', '*');
    define('VIDEO_MAX_FILE_SIZE', 512000000);

//constants
    define('MALE', 1);
    define('FEMALE', 2);

//message
    define('MESSAGE_NEW', 1);
    define('MESSAGE_READ', 2);
    define('MESSAGE_REPLIED', 3);
    define('MESSAGE_DELETE', 4);

//page
    define('FRAG_AUTO', 1);
    define('FRAG_FEED', 2);
    define('FRAG_MANUL', 3);
    define('FRAG_HTML', 4);

//page state
    define('PAGE_LOCK', 1);
    define('PAGE_UNLOCK', 1);
    define('PAGE_DELETE', 1);
    define('SECTION_PUBLISHED', 1);

//状态 status
    define('STATUS_PUBLISH', 1);
    define('STATUS_UNPUBLISH', 2);
    define('STATUS_DOWN', 3);

// 订单状态
    define('TICKET_MAX_DAYS', 7);
    define('ORDER_MAX_DAYS', 7);
    define('ORDER_SUBMIT_DAYS', 15);
    define('ORDER_CART_DAYS' , 14);

// 默认发送的邮箱地址
    define('KUAIJI_EMAIL', 'noreply@kuaiji.com');


//定义最大的递归分类层级，主要是来算根类别catid
    define('MAX_CATID_LEVEL',6);

//打开中间层模式，命名即为中间层名称
    define('BIZ_NAME','商铺ID');


//为shtml的后缀选择更加便利的方式
    define('SHTML','.html');

//会计网品牌id
    define('KUAIJI_BRAND_ID',100000);
//会计网机构id
    define('KUAIJI_AGENCY_ID',100000);

//DSN
    define('BRAND_DSN', 3);
    define('UC_DSN', 4);

    define('VIDEO_MAX_FILE_SIZE', 1024000);

    define('BAIDU_AK', '2134432c54199d194c59729e0423cea9');

    //百度站长
    define('BAIDU_ZZ', '0kzv2vBR5UoqFUmR');

    define('KJ_AUTH_KEY', 'DxoWFokhCLPuJpLt6PGP');
    define('SOLR_DIC_PATH', '/data/wwwroot/so/solr/data/');
}
