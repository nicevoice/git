<?php
/**
 * 配置
 *
 * @author zhongshenghui
 * @copyright 2011 (c) CmsTop
 * @version $Id$
 */

define('CONFIG_PATH', ROOT_PATH.'config/');

class config
{
	/**
	 * 配置储存文件
	 *
	 * @var string
	 */
	private static $_file = null;
	
	/**
	 * 配置信息
	 *
	 * @var array
	 */
	private static $_config = array();

	/**
	 * 设置配置储存文件
	 *
	 * @param string $file
	 */
	public static function set_file($file)
	{
		if (!preg_match("/^[0-9a-z_\-]+$/i", $file)) throw new ct_exception("$file is not valid");
        self::$_file = CONFIG_PATH.$file.'.php';
        if($_ENV['extapp']){//增加项目兼容性
            $prefile = rtrim(app_dir(null),DS).DS.'config/'.$file.'.php';
            if(file_exists($prefile)){
                self::$_file = $prefile;
            }
        }
	}
	
	/**
	 * 载入文件中的配置信息
	 *
	 * @param string $file
	 * @return array 配置信息
	 */
	public static function load($file)
	{
		if (!isset(self::$_config[$file])) 
		{
			self::set_file($file);
			$config = @include(self::$_file);
			if ($config)
			{
				self::$_config[$file] = $config;
			}
			else
			{
				throw new ct_exception(self::$_file." is not exists");
			}
		}
		return self::$_config[$file];
	}
	
	/**
	 * 获取配置文件一个键的值
	 *
	 * @param string $file 配置文件
	 * @param string $key  配置键
	 * @param mixed $default 默认值
	 * @return mixed
	 */
	public static function get($file, $key = null, $default = null)
	{
		$config = self::load($file);
		return is_null($key) ? $config : (isset($config[$key]) ? $config[$key] : $default);
	}
	
	/**
	 * 以组合方式，设置配置文件的配置信息
	 *
	 * @param string $file
	 * @param array $data 配置信息
	 * @return boolean
	 */
	public static function set($file, $data = array())
	{
		$config = self::load($file);
		$config = array_merge($config, $data);
		return self::_write(self::$_file, $config);
	}

	/**
	 * 创建一个配置文件
	 *
	 * @param string $file 配置文件
	 * @param array $data 初始配置信息
	 * @return boolean
	 */
	public static function create($file, $data = array())
	{
		self::set_file($file);
		return self::_write(self::$_file, $data);
	}
	
    private static function _write($file, $array = array())
    {
    	$data = "<?php\nreturn ".var_export($array, true);
    	if (!write_file($file, $data)) throw new ct_exception("$file is not exists or not writable");
    	return true;
    }
    
    private static function _set_cache()
    {
    	$config = array();
    	$files = glob(CONFIG_PATH.'*.php');
    	foreach ($files as $path)
    	{
    		$file = basename($path, '.php');
    		$config[$file] = require($path);
    	}
    	$cache = & factory::cache();
    	$cache->set('config', $config);
    	return $config;
    }
    
    private static function _get_cache()
    {
    	$cache = & factory::cache();
    	return $cache->get('config');
    }
}