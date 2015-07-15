<?php
abstract class widgetEngine
{
	private static $_instance = array();
	private static $_ENV = null;

	public function __get($key)
	{
		return self::$_ENV[$key];
	}
	
	public static function setEnv(array $env)
	{
		self::$_ENV = $env;
	}
	/**
	 * getInstance用来返回子类的对象
	 */
	public static function getInstance($engine)
	{
		if (isset(self::$_instance[$engine]))
		{
			return self::$_instance[$engine];
		}
		loader::import("lib.widgetEngine.$engine");
		$class = "widgetEngine_$engine";
		if (!class_exists($class, false))
		{
			throw new Exception("Widget Engine '$engine' not exists.");
		}
		return self::$_instance[$engine] = new $class();
	}

	public static function render($engine, $widget)
	{
		return self::getInstance($engine)->_render($widget);
	}
	public static function addView($engine)
	{
		return self::getInstance($engine)->_addView();
	}
	public static function addPost($engine, $post)
	{
		return self::getInstance($engine)->_addPost($post);
	}
	public static function editView($engine, $widget)
	{
		return self::getInstance($engine)->_editView($widget);
	}
	public static function editPost($engine, $widget, $post)
	{
		return self::getInstance($engine)->_editPost($widget, $post);
	}
	public static function copy($widget)
	{
		return self::getInstance($widget['engine'])->_copy($widget);
	}
	public static function genData($engine, $post, $widget = null)
	{
		return self::getInstance($engine)->_genData($post, $widget);
	}
	public static function dispath($engine, $action)
	{
		return self::getInstance($engine)->$action();
	}
	final public function _addPost($post)
	{
		$widgetid = $this->widget->insert(array(
			'engine'=>strtolower(substr(get_class($this), 13)),
			'data'=>$this->_genData($post)
		));
		$this->_afterPost($widgetid, $post);
		return $widgetid;
	}
	final public function _editPost($widget, $post)
	{
		$rs = $this->widget->update(array(
			'data'=>$this->_genData($post, $widget)
		), $widget['widgetid']);
		$this->_afterPost($widget['widgetid'], $post);
		return $rs;
	}
	final public function _copy($widget)
	{
		$widgetid = $this->widget->insert(array(
			'engine'=>$widget['engine'],
			'data'=>$widget['data']
		));
		$this->_afterCopy($widgetid, $widget);
		return $widgetid;
	}
	public function _afterPost($widgetid, $post)
	{}
	public function _afterCopy($widgetid, $widget)
	{}
	public function _genData($post, $widget = null)
	{
		return encodeData($post['data']);
	}
	protected function _genHtml($template, $data)
	{
		if (! $template)
		{
			$dir = APPS_DIR.'special/view/widgets/';
			$file = strtolower(substr(get_class($this), 13)).'/template.html';
		}
		else
		{
			$dir = CACHE_PATH;
			$file = 'widget/'.md5($template).'.php';
			$sfile = $dir.$file;
			if (! is_file($sfile))
			{
				write_file($sfile, $template);
			}
		}
		$orig_dir = $this->template->dir;
		$this->template->set_dir($dir);
		$html = $this->template->assign($data)->fetch($file);
		$this->template->set_dir($orig_dir);
		return $html;
	}
	abstract public function _addView();
	abstract public function _editView($widget);
	abstract public function _render($widget);
}