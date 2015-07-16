<?php

class view extends object 
{
    public $dir, 
           $file, 
           $ext = '.php';
           
    protected $_vars;

    function __construct(array $config = null)
    {
        if (is_array($config))
        {
            foreach ($config as $key => $value)
            {
                $this->{$key} = $value;
            }
        }
        $this->clean_vars();
    }

    function set_view($view, $app = null)
    {
        $this->file = is_null($app) ? $this->dir.$view.$this->ext : app_dir($app).'view'.DS.$view.$this->ext;
        return $this;
    }

    function set_dir($dir)
    {
        $this->dir = $dir;
        return $this;
    }
    
    function assign($key, $data = null)
    {
        if (is_array($key))
        {
            $this->_vars = array_merge($this->_vars, $key);
        }
        elseif (is_object($key))
        {
        	$this->_vars = array_merge($this->_vars, (array)$key);
        }
        else
        {
            $this->_vars[$key] = $data;
        }
        return $this;
    }
    
	function clean_vars()
    {
        $this->_vars = array();
        return $this;
    }
    
    function display($view, $app = null)
    {
        echo $this->fetch($view, $app);
    }

    function fetch($view, $app = null)
    {
    	$this->set_view($view, $app);
        $this->_before_render($view);
        if ($_REQUEST) extract($_REQUEST);
        if ($this->_vars) extract($this->_vars);
        ob_start();
        try {
        	include $this->_file();
        } catch (Exception $e) {
        	ob_end_clean();
        	throw $e;
        }
        $output = ob_get_clean();
        $this->_after_render($output);
        return $output;
    }
    
    protected function _before_render($view) {}
    
    protected function _after_render(& $output) {}
    
    protected function _file()
    {
    	return $this->file;
    }
}