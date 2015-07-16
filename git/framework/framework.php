<?php
//define('DS', DIRECTORY_SEPARATOR);
define('TIME', time());
defined('FW_PATH') or define('FW_PATH', dirname(__FILE__).DS);

set_include_path(FW_PATH.PATH_SEPARATOR.get_include_path());

require(FW_PATH.'loader.php');

import('core.object');
import('core.exception');
import('core.function');
import('core.config');
import('core.setting');
import('core.log');
import('core.tag');
import('core.observer');

import('core.model');
import('core.view');
import('core.controller');

import('factory');
import('env.request');
import('env.response');
import('filter.input');
import('filter.output');
import('form.form_element');
import('form.element');
import('helper.folder');

define('IP', request::get_clientip());
define('URL', request::get_url());

if (get_magic_quotes_runtime())
{
    set_magic_quotes_runtime(false);
}

function shutdown_function()
{
    $e = error_get_last();
    if(isset($e['type']))
    {
        if($e['type'] == 1 || $e['type'] == 4)
        {
            php_error_log($e['type'],$e['message'],$e['file'],$e['line'],NULL);
        }
    }
}

if(defined('LOG_ERROR') && LOG_ERROR)
{
    register_shutdown_function('shutdown_function');
    //set_error_handler('php_error_log');
}

if(function_exists('date_default_timezone_set')) date_default_timezone_set(config('config', 'timezone'));

filter::input();