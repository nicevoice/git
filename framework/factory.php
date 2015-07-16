<?php
defined('RUN_CMSTOP') or exit('Access Denied');

class factory
{
	private static $objects;
	
	/**
	 * config 单例
	 *
	 * @return config
	 */
	public static function &config()
	{
		if (!isset(self::$objects['config']))
		{
			import('core.config');
			self::$objects['config'] = new config();
		}
		return self::$objects['config'];
	}

	/**
	 * setting 单例
	 *
	 * @return setting
	 */
	public static function &setting()
	{
		if (!isset(self::$objects['setting']))
		{
			import('core.setting');
			self::$objects['setting'] = new setting();
		}
		return self::$objects['setting'];
	}
	
	/**
	 * cache 单例
	 *
	 * @return cache
	 */
	public static function &cache()
	{
		if (!isset(self::$objects['cache']))
		{
			import('cache.cache');
			$config = config('cache');
            $config['path'] = CACHE_PATH;
			if (!empty($config['storage']))
			{
				$config[$config['storage']] = config($config['storage']);
			}
			self::$objects['cache'] = new cache($config);
		}
		return self::$objects['cache'];
	}

	/**
	 * db 工厂
	 *
	 * @param string/array $config
	 * @return db
	 */
	public static function &db($config = 'db')
	{
		$options = is_array($config) ? $config : config($config);

        // 默认数据库连接配置，不考虑其他数据库类型
		if ($config == 'db' && config('config', 'db_slaves'))
		{
            $slaves = config('db_slaves');
            import('db.db');
			return db::get_instance($options, $slaves);
		}

        $driver = $options['driver'];
        switch ($driver)
        {
            case 'mssql':
                import('db.db_mssql');
                return db_mssql::get_instance($options);
                break;
            default:
                import('db.db');
                return db::get_instance($options);
                break;
        }
	}

	/**
	 * dbkv 单例
	 *
	 * @return dbkv
	 */
	public static function &dbkv()
	{
		if (!isset(self::$objects['dbkv']))
		{
			import('dbkv.dbkv');
			$config = config('config');
			self::$objects['dbkv'] = new dbkv($config['dbkv_storage'], $config['dbkv_handler']);
		}
		return self::$objects['dbkv'];
	}
	
	/**
	 * db_cache 单例
	 *
	 * @return db_cache
	 */
	public static function &db_cache()
	{
		if (!isset(self::$objects['db_cache']))
		{
			import('db.db_cache');
			self::$objects['db_cache'] = new db_cache(CACHE_PATH.'table'.DS);
		}
		return self::$objects['db_cache'];
	}

	/**
	 * log 单例
	 *
	 * @return log
	 */
	public static function &log()
	{
		if (!isset(self::$objects['log']))
		{
			import('core.log');
			self::$objects['log'] = new log(config('log'));
		}
		return self::$objects['log'];
	}
	
	/**
	 * cookie 单例
	 *
	 * @return cookie
	 */
	public static function &cookie()
	{
		if (!isset(self::$objects['cookie']))
		{
			import('core.cookie');
			$config = config('cookie');
			self::$objects['cookie'] = new cookie($config['prefix'], $config['path'], $config['domain']);
		}
		return self::$objects['cookie'];
	}
	
	/**
	 * session 单例
	 *
	 * @return session
	 */
	public static function &session()
	{
		if (!isset(self::$objects['session']))
		{
			import('session.session');
			self::$objects['session'] = new session(config('session'));
		}
		return self::$objects['session'];
	}
	
	/**
	 * router 单例
	 *
	 * @return router
	 */
	public static function &router()
	{
		if (!isset(self::$objects['router']))
		{
			import('core.router');
			$urlmode = config('config', 'urlmode');
			$router = config('router');
			self::$objects['router'] = new router($urlmode, $router);
		}
		return self::$objects['router'];
	}
	
	/**
	 * tempate 工厂
	 *
	 * @param string $app
	 * @return template
	 */
	public static function &template($app = 'system')
	{
		if (!isset(self::$objects['template']))
		{
			import('core.view');
			import('core.template');
			$config = config('template');
			$config['app'] = $app;
			$config['dir'] = ROOT_PATH.'templates/';
			$config['compile_dir'] = CACHE_PATH.'templates/';
			self::$objects['template'] = new template($config);
		}
		return self::$objects['template'];
	}

	/**
	 * view 工厂
	 *
	 * @param string $app
	 * @return view
	 */
	public static function &view($app = 'system')
	{
		if (!isset(self::$objects['view']))
		{
			import('core.view');
			self::$objects['view'] = new view(array('dir'=>app_dir($app).'view'.DS));
		}
		return self::$objects['view'];
	}
	
	/**
	 * form 单例
	 *
	 * @return form
	 */
	public static function &form()
	{
		if (!isset(self::$objects['form']))
		{
			import('form.form');
			self::$objects['form'] = new form();
		}
		return self::$objects['form'];
	}
	
	/**
	 * element 单例
	 *
	 * @return element
	 */
	public static function &element()
	{
		if (!isset(self::$objects['element']))
		{
			import('form.form_element');
			import('form.element');
			self::$objects['element'] = new element();
		}
		return self::$objects['element'];
	}
	
	/**
	 * validator 单例
	 *
	 * @return validator
	 */
	public static function &validator()
	{
		if (!isset(self::$objects['validator']))
		{
			import('core.validator');
			self::$objects['validator'] = new validator();
		}
		return self::$objects['validator'];
	}
	
	/**
	 * json 工厂
	 *
	 * @param string $charset
	 * @return json
	 */
	public static function &json($charset = null)
	{
		if (!isset(self::$objects['json']))
		{
			import('helper.json');
			if (!$charset) $charset = config('config', 'charset');
			self::$objects['json'] = new json($charset);
		}
		return self::$objects['json'];
	}
	
	/**
	 * image 单例
	 *
	 * @return image
	 */
	public static function &image()
	{
		if (!isset(self::$objects['image']))
		{
			$setting = setting('system');
			import('helper.image');
			self::$objects['image'] = new image();
			if ($setting['thumb_enabled'])
			{
				self::$objects['image']->set_thumb($setting['thumb_width'], $setting['thumb_height'], $setting['thumb_quality']);
			}
			if ($setting['default_watermark'])
			{
				$watermark = loader::model('admin/watermark', 'system')->get($setting['default_watermark']);
				self::$objects['image']->set_watermark(UPLOAD_PATH.$watermark['image'], $watermark['minwidth'], $watermark['minheight'], $watermark['position'], $watermark['trans'], $watermark['quality']);
			}
		}
		return self::$objects['image'];
	}
	
	/**
	 * sendmail 单例
	 *
	 * @return sendmail
	 */
	public static function &sendmail()
	{
		if (!isset(self::$objects['sendmail']))
		{
			$config = setting('system','mail');
			import('helper.sendmail');
			self::$objects['sendmail'] = new sendmail($config['mailer'], $config['delimiter'], config('config', 'charset'), $config['from'], $config['sign'], $config['smtp_host'], $config['smtp_port'], $config['smtp_auth'], $config['smtp_username'], $config['smtp_password']);
		}
		return self::$objects['sendmail'];
	}

    public function &segment()
    {
        if (!isset(self::$objects['segment']))
		{
            import('helper.segment');
			$segment = new segment();
            $segment->set_charset('utf8');
			$segment->set_dict(ini_get('scws.default.fpath') . DS . 'dict.' . ini_get('scws.default.charset') . '.xdb');
			self::$objects['segment'] = $segment;
		}
		return self::$objects['segment'];
    }

    public static function &queue($engine)
    {
        import('queue.queue');
		return queue::get_instance($engine);
    }

    /**
     * 调用项目直接的app的公用对外的model
     *
     * @param $app     项目App;
     * @param $module  项目名称 ;
     * @param $dir     公用目录，默认为项目下的_common目录 ;
     */
    public static function appmodel($app,$module='',$dir='_common'){
        // 判断使用当前的module项目
        if(!$module){
            $module = $_ENV['extapp'];
        }
        $key = $module . '-' . $dir . '-' . $app ;
        if (!isset(self::$objects['appmodel'][$key])){
            $file = dirname(app_dir($app)).DS.$dir.DS.$app.'App.model.php';
            if(file_exists($file)){
                require_once $file;
                $class_name = $app . 'App' ;
                $class = new $class_name() ;
                self::$objects['appmodel'][$key] = $class ;
            }
        }
        return self::$objects['appmodel'][$key] ;
    }
}