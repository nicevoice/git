<?php
abstract class fieldEngine
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

	public static function getInstance($engine)
	{
		if (isset(self::$_instance[$engine]))
		{
			return self::$_instance[$engine];
		}
		loader::import("lib.fieldEngine.$engine");
		$class = "fieldEngine_$engine";
		if (!class_exists($class))
		{
			throw new Exception("Field Engine '$engine' not exists.");
		}
		return self::$_instance[$engine] = new $class();
	}

	public static function render($engine, $field)
	{
		return self::getInstance($engine)->_render($field);
	}

	public static function genData($engine, $setting, $fid)
	{
		return self::getInstance($engine)->_genData($setting, $fid);
	}

	public function genEditData($engine, $field, $value)
	{
		return self::getInstance($engine)->_genEditData($field, $value);
	}

	public static function addView($engine, $pid)
	{
		return self::getInstance($engine)->_addView($pid);
	}

	public static function addPost($engine, $post)
	{
		return self::getInstance($engine)->_addPost($post);
	}

	public static function editPost($engine, $fid, $post)
	{
		return self::getInstance($engine)->_editPost($fid, $post);
	}

	public static function editView($engine, $fid)
	{
		return self::getInstance($engine)->_editView($fid);
	}

	final public function _addPost($post)
	{
		$post['setting'] = serialize($post['setting']);
		return $this->field->insert($post);
	}

	final public function _editPost($fid, $post)
	{
		$post['setting'] = serialize($post['setting']);
		return $this->field->update($post, $fid) ? $fid : false;
	}

	protected function _genHtml($data)
	{
		$file = 'field/'.$data['field'].'/view';
		$html = $this->view->assign($data)->fetch($file);
		return $html;
	}
	abstract public function _render($field);
	abstract public function _addView($pid);
	abstract public function _editView($fid);
	abstract public function _genData($setting, $fid);
	abstract public function _genEditData($field, $value);
}