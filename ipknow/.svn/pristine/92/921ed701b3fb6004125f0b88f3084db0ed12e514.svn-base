<?php
namespace sWechat\Core;
/**
 * Created by lixuan-it@360.cn

 */
class Auth {
    /**
     * 获取微信服务器IP列表
     */
    public static function getWeChatIPList(){
        //获取ACCESS_TOKEN
        $accessToken = AccessToken::getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token='.$accessToken;
        return Curl::callWebServer($url, '', 'GET');
    }
}