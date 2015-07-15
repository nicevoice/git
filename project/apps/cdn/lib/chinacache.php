<?php
class Chinacache
{
	function __construct()
	{
		
	}

	function run($para)
	{
		if (!$para['user'] || !$para['pswd'])
		{
			return array('state'=>0, 'error'=>'缺少参数');
		}
		$path	= $_SERVER['path'];
		$path	= str_replace('@', '%0D%0A', $path);
		unset($_SERVER['path']);
		$url	= "http://ccms.chinacache.com/index.jsp?user=$para[user]&pswd=$para[pswd]&ok=ok&urls=$path";
		$retList = explode("\r\n", $this->getHttp($url));
		foreach ($retList as $s)
		{
			if (strpos($s, "whatsup: content=\"succeed\"") !== false)
			{
				return array('state'=>1);
			}
			if (strpos($s, "urlexceed: ") !== false)
			{
				return array('state'=>0, 'error'=>'url提交超量');
			}
			if (strpos($s, "direxceed: ") !== false)
			{
				return array('state'=>0, 'error'=>'目录提交超量');
			}
		}
	}

	private function getHttp($url)
	{
		$userAgent = 'Mozilla/4.0+(compatible;+MSIE+6.0;+Windows+NT+5.1;+SV1)';
		$referer = $url;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);			//设置访问的url地址
		curl_setopt($ch, CURLOPT_HEADER, 1);				//设置返回头部，用于内容编码判断
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);				//设置超时
		curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);	//用户访问代理 User-Agent
		curl_setopt($ch, CURLOPT_REFERER, $referer);		//设置 referer
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip');			//设置客户端是否支持 gzip压缩
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);		//跟踪301,已关闭
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		//返回结果
		if(stripos($this->url,'https:') !== false){				//加载SSL公共证书，请求HTTPS访问
			# Below two option will enable the HTTPS option.
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		}
		$html = curl_exec($ch);
		curl_close($ch);
		return $html;
	}
}
