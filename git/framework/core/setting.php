<?php
define('SETTING_PATH', CACHE_PATH.'setting'.DS);

class setting extends object 
{
	private $db, $table;
	private static $objInstance; 
	function __construct()
	{
		$this->db = & factory::db();
		$this->table = '#table_setting'; //在db类的dbh()方法中被替换成['prefix']，即前缀cmstop
	}
	
	static function getInstance() {
		if(!self::$objInstance){
			$objInstance = new setting();
		}
		return $objInstance;
	}

	static function get($app, $var = null)
	{
		static $settings;
		$cache	= & factory::cache();
		if (!isset($settings[$app]))
		{
			if(!$settings[$app] = $cache->get('setting_'.$app))
			{
				$settings[$app] = self::getInstance()->cache($app);
			}
		}
		return is_null($var) ? $settings[$app] : (isset($settings[$app][$var]) ? $settings[$app][$var] : null);
	}
	
	function set($app, $var, $value)
	{
		if (is_array($value)) $value = var_export($value, true);
		$db = $this->db->prepare("REPLACE INTO `$this->table`(`app`, `var`, `value`) VALUES(?,?,?)");
		return $db->execute(array($app, $var, $value));
	}
	
    function set_array($app, $data)
    {
    	if (!is_array($data)) return false;
    	foreach ($data as $key => $value)
		{
			$this->set($app, $key, $value);
		}
		$this->cache($app);
		return true;
    }
	
	function cache($app = null)
	{
		if (is_null($app))
		{
			$arrapp = table('app');
			$apps = array_keys($arrapp);
			return array_map(array($this, 'cache'), $apps);
		}
		else 
		{
			$setting = array();
		    $data = $this->db->select("SELECT `var`,`value` FROM `$this->table` WHERE `app`=?", array($app));
                    if($data){
		    foreach ($data as $r)
		    {
		    	if (substr($r['value'], 0, 5) === 'array')
		    	{
		    		eval("\$value = {$r['value']};");
		    	    $setting[$r['var']] = $value;
		    	}
		    	else 
		    	{
		    		$setting[$r['var']] = $r['value'];
		    	}
		    }
                    }
			$cache	= & factory::cache();
			$cache->set('setting_'.$app, $setting);
            return $setting;
		}
	}
}