<?php
class model_admin_vms
{
	public $setting, $api_url, $api_key;
	
	function __construct()
	{
		$this->setting = setting::get('video');
		$this->api_key = $this->setting['apikey'];
		$this->api_url = $this->setting['apiurl'] . (strpos($this->setting['apiurl'], '?') ? '&' : '?') . 'apikey=' . $this->api_key;
	}

    public function __call($do, $args)
    {
    	if (in_array($do, array('ls', 'delete', 'state', 'check', 'info', 'info_by_file','setinfo')))
    	{
    		$data = count($_POST) ? $_POST : $_GET;
	    	unset($data['app'], $data['controller'], $data['action']);
	    	$data['do'] = $do;
	    	$data = http_build_query($data);
	    	$url = $this->api_url;
	    	if ($data)
	    	{
	    		$url = $url . (strpos($url, '?') ? '&' : '?') . $data;
	    	}
	    	$re = request($url);
	    	if ($re['httpcode'] == 200)
	    	{
	    		return ($re['content']);
	    	}
	    	else
	    	{
	    		header('HTTP/1.1 '.$re['httpcode']);
	    		return FALSE;
	    	}
    	}
    	header('HTTP/1.1 404 Not Found');
		return FALSE;
	}
}