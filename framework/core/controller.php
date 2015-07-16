<?php
/**
 * 控制器基类
 *
 * @author zhongshenghui
 * @copyright 2011 (c) CmsTop
 * @version $Id$
 */

abstract class controller extends object 
{	
	/**
	 * cmstop 主应用
	 *
	 * @var cmstop
	 */
	protected $app;
	
	/**
	 * 用户ID
	 *
	 * @var int
	 */
	protected $_userid;

    function __construct($app = null)
    {
        parent::__construct();

		$this->app = $app;
		$this->_userid = & $app->userid;
    }
    
    /**
	 * 是否GET方式请求而来的
	 *
	 * @return boolean
	 */
	public function is_get()
	{
		return $_SERVER['REQUEST_METHOD'] == 'GET';
	}
	
	/**
	 * 是否POST方式请求而来的
	 *
	 * @return boolean
	 */
	public function is_post()
	{
		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}
	
	/**
	 * 是否HEAD方式请求而来的
	 *
	 * @return boolean
	 */
	function is_head()
	{
		return $_SERVER['REQUEST_METHOD'] == 'HEAD';
	}
	
	/**
	 * 是否PUT方式请求而来的
	 *
	 * @return boolean
	 */
	public function is_put()
	{
		return $_SERVER['REQUEST_METHOD'] == 'PUT';
	}
	
	/**
	 * 是否DELETE方式请求而来的
	 *
	 * @return boolean
	 */
	public function is_delete()
	{
		return $_SERVER['REQUEST_METHOD'] == 'DELETE';
	}
	
	/**
	 * 是否AJAX方式请求而来的
	 *
	 * @return boolean
	 */
	public function is_ajax()
	{
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
	}
	
	/**
	 * 用session记录一个令牌token
	 *
	 */
	public function set_token()
	{
		$_SESSION['token'] = md5(microtime(true));
	}
	
	/**
	 * 验证一个令牌token
	 *
	 * @return boolean
	 */
	public function valid_token()
	{
		$return = $_REQUEST['token'] === $_SESSION['token'] ? true : false;
		self::set_token();
		return $return;
	}
	
	/**
	 * 内部分发其它位置执行
	 *
	 * @param string $app
	 * @param string $controller
	 * @param string $action
	 * @param array $args
	 * @return mixed
	 */
	public function forward($app, $controller, $action, array $args = array())
	{
		return $this->app->execute($app, $controller, $action, $args);
	}
	
	/**
	 * 通过header跳转
	 *
	 * @param string $url
	 */
	protected function redirect($url)
	{
		header("location:$url");
        exit;
	}

	/**
	 * 判断是否存在动作action
	 *
	 * @param string $action
	 * @return boolean
	 */
    protected function action_exists($action)
    {
        return (substr($action, 0, 1) !== '_' && method_exists($this, $action)) || method_exists($this, '__call');
    }
	
    /**
     * 提示信息，并halt
     *
     * @param string $message
     * @param string $url 
     * @param int $ms 延迟
     * @param boolean $success 成功与否
     */
    function showmessage($message, $url = null, $ms = 2000, $success = false)
	{
		if(!$ms) $ms = 2000;

        $accept = value($_SERVER, 'HTTP_ACCEPT', '');
        if (stripos($accept, 'application/json') !== false || stripos($accept, 'text/javascript') !== false)
        {
            $result = array('state' => $success);
            $result[$success ? 'message' : 'error'] = $message;
            if ($url) $result['url'] = $url;
            $result = $this->json->encode($result);
            echo isset($_GET['jsoncallback']) ? $_GET['jsoncallback'] . "($result);" : $result;
            exit;
        }

		if ($this->app->client === 'admin')
		{
			$handler = $this->view;
			$template = 'showmessage';
		}
		else 
		{
			$handler = $this->template;
			$template = 'system/showmessage.html';
		}
		if (is_array($message)) $message = implode('<br />', $message);
		$handler->assign('message', $message);
		$handler->assign('url', $url);
		$handler->assign('ms', $ms);
		$handler->assign('success', $success);
		$handler->display($template, 'system');
		exit;
	}
	
	/**
	 * 获取私有值
	 *
	 * @param string $key 键
	 * @param mixed $value 默认值
	 * @return mixed
	 */
	function privar($key, $value = null)
	{
		static $privar;
		static $_cache = null;
				
		if (!$this->_userid) return false;
		if (!$_cache)
		{
			$_cache = & factory::cache();
		}
		if (is_null($privar)) $privar = $_cache->get('privar_'.$this->_userid);
		if (is_null($value))
		{
			return $privar && isset($privar[$key]) ? $privar[$key] : false;
		}
		else 
		{
			if ($privar)
			{
				$privar[$key] = $value;
			}
			else
			{
				$privar = array($key=>$value);
			}
			return $_cache->set('privar_'.$this->_userid, $privar);
		}
	}
}