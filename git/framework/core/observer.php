<?php

class observer implements SplObserver 
{
	private $config = array(), $dir = null;
	static private $plugin;
	
	public function __construct($dir)
	{
		$this->dir = $dir;
		if(file_exists($dir.'config.php'))
		{
			$config = include($dir.'config.php');
			foreach ($config as $file=>$events)
			{
				foreach ($events as $event)
				{
					$this->config[$event][] = $file; 
				}
			}
		}
	}
	
	public function update(SplSubject $subject)
	{
		$event = $subject->event;
		if(empty($this->config[$event]))
		{
			return FALSE;
		}
		foreach ($this->config[$event] as $file)
		{
			if (!isset(self::$plugin[$file]))
			{
				require_once($this->dir.$file.'.php');
				$class = 'plugin_'.$file;
				self::$plugin[$file] = new $class($subject);
			}
			self::$plugin[$file]->$event();
		}
	}	
}