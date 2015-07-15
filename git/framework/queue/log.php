<?php
class queue_log
{
    protected static $_db;
    protected static $_table;

    protected static $_inited;

    static function init()
    {
        if (self::$_inited) return true;

        self::$_db = & factory::db();
        self::$_table = '#table_queue_log';
        self::$_inited = true;

        return true;
    }

    static function log($queueid, $action, $arguments, $result, $message = '')
    {
        if (!self::$_inited) self::init();
        $result = (NULL === $result || !$result) ? '' : $result;
        $arguments = is_array($arguments) ? json_encode($arguments) : $arguments;
        $params = compact('queueid', 'action', 'arguments', 'result', 'message');
        return self::$_db->insert("INSERT INTO `" . self::$_table . "` (`queueid`, `action`, `arguments`, `result`, `message`) VALUES (:queueid, :action, :arguments, :result, :message)", $params);
    }
}