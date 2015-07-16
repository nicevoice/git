<?php

class router extends object 
{
	protected $_mode = 'standard';
	public $app, $controller, $action, $args;
	private static $routers;
    private static $_routers = null;
    
	function __construct($mode = 'standard', $routers = array())
	{
		$this->set_mode($mode);
		if ($this->_mode != 'standard') $this->set_routers($routers);
	}

	public static function &get_instance($mode = 'standard', $routers = array())
	{
		static $instance;
		if (!is_object($instance))
		{
			$instance = new router($mode, $routers);
		}
		return $instance;
	}
	
	public static function set_routers($routers)
	{
		if(!is_null(self::$_routers)) return ;
		self::$routers = $routers;
		foreach ($routers as $key => $val)
		{
			list($app, $controller, $action) = explode('/', $val['aca']);
			$replacement = "app=$app&controller=$controller&action=$action";
			if (strpos($key, '$') !== false)
			{
				preg_match_all("/[{][$]([a-z_][0-9a-z]*)[}]/", $key, $metches, PREG_PATTERN_ORDER);
				foreach ($metches[1] as $k => $var)
				{
					$replacement .= '&'.$var.'=$'.($k+1);
				}
				$app = $controller = $action = '[0-9a-z_]+';
				extract($val['pattern']);
				$key = str_replace(array('{', '}'), array('({', '})'), $key);
				eval("\$key = \"$key\";");
			}
			self::$_routers[$key] = $replacement;
		}
	}
	
	public function get_mode()
	{
		return $this->_mode;
	}

	public function set_mode($mode)
	{
		$this->_mode = in_array($mode, array('standard', 'querystring', 'pathinfo', 'rewrite')) ? $mode : 'standard';
	}

	public function execute()
	{
		$this->_parse();
		if (!isset($_GET['app']) || empty($_GET['app'])) $_GET['app'] = 'system';
		if (!isset($_GET['controller']) || empty($_GET['controller'])) $_GET['controller'] = 'index';
		if (!isset($_GET['action']) || empty($_GET['action'])) $_GET['action'] = 'index';
		$this->app = $_GET['app'];
		$this->controller = $_GET['controller'];
		$this->action = $_GET['action'];
		//unset($_GET['app'], $_GET['controller'], $_GET['action'], $_GET['ext']);
		$this->args = $_GET;
	}

	public function url($aca, $params = null, $is_full = false)
	{
		if ($params && !is_array($params)) parse_str($params, $params);
		foreach (self::$routers as $key=>$val)
		{
			$matched = false;
			if ($val['aca'] == $aca && count($val['pattern']) == count($params))
			{
				$matched = true;
				$pattern = &$val['pattern'];
				foreach ($params as $k => $v)
				{
					if (!isset($pattern[$k]) || !preg_match("#^".$pattern[$k]."$#", $v))
					{
						$matched = false;
						break;
					}
				}
				if ($matched) break;
			}
		}
		if ($matched)
		{
			extract($params);
			eval("\$url = \"$key\";");
		}
		elseif ($this->_mode == 'standard')
		{
			list($app, $controller, $action) = explode('/', $aca);
			$array = compact('app', 'controller', 'action');
			if ($params) $array = array_merge($array, $params);
			$url = '?'.http_build_query($array);
		}
		else
		{
			$url = '/'.$aca;
			foreach($params as $k=>$v)
			{
				$url .= '/'.urlencode($k).'/'.urlencode($v);
			}
			if ($this->_mode == 'querystring')
			{
				$url = '?'.$url;
			}
			elseif ($this->_mode == 'pathinfo')
			{
				$url = request::get_scriptname().$url;
			}
		}
        return $is_full ? request::get_base().$url : $url;
	}

	private function _parse()
	{
		if ($this->_mode == 'pathinfo')
		{
			$pathinfo = request::get_pathinfo();
		}
        elseif ($this->_mode == 'rewrite')
        {
            $pathinfo = request::get_uri();
            $pathinfo = array_shift(explode('?', $pathinfo));
        }
        elseif (!empty($_GET))
		{
			if (substr($_SERVER['QUERY_STRING'], 0, 1) != '/') return ;
			$pathinfo = $_SERVER['QUERY_STRING'];
		}
		if (isset($pathinfo))
		{
	        if (isset(self::$_routers[$pathinfo]))
			{
				$str = self::$_routers[$pathinfo];
			}
			else 
			{
				$patterns = array();
				if (is_array(self::$_routers)) foreach (self::$_routers as $pattern=>$v) $patterns[] = '#^'.$pattern.'$#';
                $str = preg_replace($patterns, self::$_routers, $pathinfo, 1);
                if ($str == $pathinfo)
				{
					$str = '';
					$args = explode('/', substr($pathinfo, 1));
					if (!empty($args)) $str .= 'app='.array_shift($args);
					if (!empty($args)) $str .= '&controller='.array_shift($args);
					if (!empty($args)) $str .= '&action='.array_shift($args);
					if (!empty($args))
					{
						$str .= '&';
						foreach ($args as $k=>$v)
						{
							$str .= $v.($k%2 == 0 ? '=' : '&');
						}
					}
				}
				
			}
			parse_str($str, $_GET);
            $_REQUEST = array_merge($_REQUEST, $_GET);
		}
	}
}