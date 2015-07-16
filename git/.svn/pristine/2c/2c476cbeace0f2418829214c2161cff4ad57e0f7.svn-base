<?php
defined('DS') or define('DS', '/');
define('APPS_DIR', ROOT_PATH.'apps/');

abstract class loader
{
	/**
	 * 当前应用标识
	 *
	 * @var string
	 */
	private static $app;
	
	/**
	 * 当期APP应用路径
	 *
	 * @var string
	 */
	private static $app_path;
	
	/**
	 * 载入文件寻找路径栈
	 *
	 * @var array
	 */
	private static $paths = array();
	
	/**
	 * 已载入的类文件栈
	 *
	 * @var array
	 */
	private static $classes = array();
	
	/**
	 * 实例后的对象栈
	 *
	 * @var array
	 */
	private static $instances = array();
	
	/**
	 * 设置APP标识 路径
	 *
	 * @param sting $app 路径名称
	 */
	static function set_app($app)
	{
		self::$app = $app;
		self::$app_path = app_dir($app);
	}
	
	/**
	 * 载入一个类文件
	 *
	 * @param string $filepath 文件名
	 * @param string $base 基础路径
	 * @param string $key 和文件名连接用于记录是否已载入
	 * @return boolean
	 */
	static function import($filepath, $base = null, $key = null)
	{
		$keypath = $key ? $key.$filepath : $filepath;
		if (!isset(self::$paths[$keypath]))
		{
			if (is_null($base)) $base = self::$app_path;
			$parts = explode('.', $filepath);
			$classname = array_pop($parts);
			$path  = str_replace('.', DS, $filepath);
			self::$paths[$keypath] = include $base.$path.'.php';
		}
		return self::$paths[$keypath];
	}

	/**
	 * 注册类名称对应的文件
	 *
	 * @param string $class 类名称
	 * @param string $file 文件位置
	 */
	static function register($class = null, $file = null)
	{
		if($class && is_file($file))
		{
			$class = strtolower($class);
			self::$classes[$class] = $file;
		}
	}

	/**
	 * 用类名称载入类文件
	 *
	 * @param string $class 类名称
	 * @return boolean
	 */
	static function load($class)
	{
		$class = strtolower($class);
		if(class_exists($class)) return;
		return isset(self::$classes[$class]) ? include(self::$classes[$class]) : false;
	}
	
	/**
	 * 载入控制器并实例化
	 *
	 * @param string $controller
	 * @param string $app
	 * @return controller
	 */
	static function controller($controller, $app = null)
	{
		if (!is_null($app)) self::set_app($app);
		return self::_load_class($controller, 'controller');
	}
	
	/**
	 * 载入模型并实例化
	 *
	 * @param string $model
	 * @param string $app
	 * @return model
	 */
	static function model($model, $app = null)
	{
        return self::_load_class($model, 'model', $app);
	}
	
	/**
	 * 载入第三方帮助类库并实例化
	 *
	 * @param string $helper
	 * @param string $app
	 * @return unknown
	 */
	static function helper($helper, $app = null)
	{
		return self::_load_class($helper, 'helper', $app);
	}
	
	/**
	 * 载入类库并实例化
	 *
	 * @param string $lib
	 * @param string $app
	 * @return unknown
	 */
	static function lib($lib, $app = null)
	{
		return self::_load_class($lib, 'lib', $app);
	}
	
	/**
	 * 载入文件
	 *
	 * @param string $path
	 * @param string $dir
	 * @return unknown
	 */
	static function _load($path, $dir = 'view')
	{
		return include(self::_file($path, $dir));
	}
	
	/**
	 * 载入类文件并实例化
	 *
	 * @param string $path
	 * @param string $dir
	 * @param string $app
	 * @return unknown
	 */
	static function _load_class($path, $dir = 'model', $app = null)
	{
		$key = $app.$dir.$path;
		if (!isset(self::$instances[$key]))
		{
			$class = (in_array($dir, array('controller', 'model')) ? $dir.'_' : '').str_replace('/', '_', $path);
			require_once self::_file($path, $dir, $app);
			self::$instances[$key] = new $class;
			if (self::$instances[$key] instanceof SplSubject)
			{
				$plugin_dir = (is_null($app) ? self::$app_path : app_dir($app)).'plugin'.DS.$class.DS;
				self::$instances[$key]->attach(new observer($plugin_dir));
			}
		}
		return self::$instances[$key];
	}
	
	/**
	 * 载入文件
	 *
	 * @param string $path
	 * @param string $dir
	 * @param string $app
	 * @return string
	 */
	static function _file($path, $dir = 'view', $app = null)
	{
		$app_path = is_null($app) ? self::$app_path : app_dir($app);
		$file = $app_path.$dir.DS.$path.'.php';
		if (!file_exists($file)) throw new ct_exception("file $file is not exists");
		return $file;
	}
}

/**
 * 注册一个自动载入处理函数
 */
spl_autoload_register(array('loader', 'load'));

/**
 * 载入一个文件, loader::import短用法
 *
 * @param string $path
 * @return boolean
 */
function import($path)
{
	return loader::import($path, FW_PATH);
}