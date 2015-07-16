<?php

function dms_log($action, $data = NULL)
{
    static $logger = NULL;
    static $models = NULL;

    if (NULL === $logger)
    {
        $logger = loader::model('admin/dms_log', 'dms');
    }

    $appid = value($_GET, 'appid', 0);
    $operator = value($_GET, 'operator', NULL);

    $model_alias = value($_GET, 'controller', '');
    if ($model_alias)
    {
        if (NULL === $models)
        {
            $models = array();
            foreach (table('dms_model') as $model)
            {
                $models[$model['alias']] = $model['modelid'];
            }
        }
    }
    $modelid = $models && isset($models[$model_alias]) ? $models[$model_alias] : 0;

    $target = value($_GET, 'id', 0);

    return $logger->add(compact($appid, $operator, $modelid, $target, $action, $data, TIME, IP));
}

function get_setting($var)
{
	$setting = setting::get('dms');
	return $setting[$var];
}

function input()
{
	return json_decode(file_get_contents('php://input'), true);
}

function get_url($serverid, $path)
{
	$db		= & factory::db();
	$server	= $db->get("SELECT `name`, `url` FROM ".$db->options['prefix']."dms_server WHERE `serverid` = $serverid LIMIT 1");
	$url	= $server['url'].$path;
	return $url;
}

function get_model($id = null, $name = null)
{
	foreach (table('dms_model') as $model)
	{
		if (($model['modelid'] == $id) || ($model['alias'] == $name))
		{
			return $model;
		}
	}
	return false;
}