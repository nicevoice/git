<?php
//namespace sWechat;
//版本号
define('LANEWECHAT_VERSION', '1.0');
define('LANEWECHAT_VERSION_DATE', '2015-06-01');
/*
 * 服务器配置，详情请参考@link http://mp.weixin.qq.com/wiki/17/2d4265491f12608cd170a95559800f2d.html#.E7.AC.AC.E4.B8.80.E6.AD.A5.EF.BC.9A.E5.A1.AB.E5.86.99.E6.9C.8D.E5.8A.A1.E5.99.A8.E9.85.8D.E7.BD.AE
 */
define("WECHAT_URL", 'http://www.iswechat.com/');
define('WECHAT_TOKEN', '556afbd29a8e35c6600d');
define('ENCODING_AES_KEY', "rpceX8JqtX3pcQJxDXfEGecKhPQadesrqsYbq79jm6i");
/*
 * 开发者配置
 */
define("WECHAT_APPID", 'wx7931259d3fbe0151');
define("WECHAT_APPSECRET", '84cbdac7f7f963217bcca877710e1475');

/**
 * 格式化打印数据
 * @param $data
 * @param bool $die
 */
function printR($data, $die = true)
{
    header("Content-type: text/html; charset=utf-8");
    echo '<pre>';
    print_r($data);
    if($die)
    {
        die();
    }
}