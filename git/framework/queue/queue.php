<?php
loader::register('queue_log', dirname(__FILE__).DS.'log.php');
loader::register('queue_engine', dirname(__FILE__).DS.'engine.php');
loader::register('queue_exception', dirname(__FILE__).DS.'exception.php');

class queue
{
    private $_db;
    private $_table;

    private $_error;

    private $_engine;
    private $_engine_name;

    private $_userid;

    function __construct($engine)
    {
        $this->_db = & factory::db();
        $this->_table = '#table_queue';
        
        $this->_engine_name = $engine;
        $class_name = "queue_" . $this->_engine_name;
        loader::register($class_name, dirname(__FILE__) . DS . 'engine' . DS . $this->_engine_name . '.php');
        $this->_engine = new $class_name;
        if (! $this->_engine instanceof queue_engine)
        {
            throw new queue_exception('incorrect queue engine type');
        }

        $user_info = online();
        if ($user_info)
        {
            $this->_userid = $user_info['userid'];
        }
    }

    static function &get_instance($engine)
    {
        static $instance;
		if (!isset($instance[$engine]))
		{
			$instance[$engine] = new queue($engine);
		}
		return $instance[$engine];
    }

    function add($data, $delete = true)
    {
        $params = array(
            'engine' => $this->_engine_name,
            'arguments' => json_encode($data),
            'nextrun' => time(),
            'created' => time(),
            'createdby' => $this->_userid ? $this->_userid : 0,
            'status' => 1,
            'delete' => $delete ? 1 : 0
        );
        $result = $this->_db->insert("INSERT INTO `{$this->_table}` (`engine`, `arguments`, `nextrun`, `created`, `createdby`, `status`, `delete`) VALUES (:engine, :arguments, :nextrun, :created, :createdby, :status, :delete)", $params);
        queue_log::log($result, __FUNCTION__, $params, $result);
        return $result;
    }

    function start($queueid)
    {
        $params = array(
            'started' => time(),
            'ended' => 0,
            'status' => 2,
            'queueid' => $queueid
        );
        $result = $this->_db->update("UPDATE `{$this->_table}` SET `started` = :started, `ended` = :ended, `times` = `times` + 1, `status` = :status WHERE `queueid` = :queueid AND `status` <> 2", $params);
        queue_log::log($queueid, __FUNCTION__, $params, $result);
        return $result;
    }

    function end($queueid, $result, $status = 3)
    {
        $params = array(
            'ended' => time(),
            'result' => json_encode($result),
            'status' => $status,
            'queueid' => $queueid
        );
        $addon = '';

        // need run again
        if (!$result && $status != 3)
        {
            $params['nextrun'] = time() + mt_rand(1, 10) * 60;
            $addon = '`nextrun` = :nextrun,';
        }

        $result = $this->_db->update("UPDATE `{$this->_table}` SET $addon `ended` = :ended, `result` = :result, `status` = :status WHERE `queueid` = :queueid AND `status` <> 3", $params);
        queue_log::log($queueid, __FUNCTION__, $params, $result);
        return $result;
    }

    function reset($queueid)
    {
        $params = array(
            'nextrun' => time(),
            'started' => 0,
            'ended' => 0,
            'status' => 1,
            'queueid' => $queueid
        );
        $result = $this->_db->update("UPDATE `{$this->_table}` SET `nextrun` = :nextrun, `started` = :started, `ended` = :ended, `status` = :status WHERE `queueid` = :queueid", $params);
        queue_log::log($queueid, __FUNCTION__, $params, $result);
        return $result;
    }

    function delete($queueid)
    {
        $params = array(
            'queueid' => $queueid
        );
        $result = $this->_db->delete("DELETE FROM `{$this->_table}` WHERE `queueid` = :queueid AND `status` <> 0", $params);
        queue_log::log($queueid, __FUNCTION__, $params, $result);
        return $result;
    }

    function execute($queueid)
    {
        $queue = $this->_db->get("SELECT * FROM `{$this->_table}` WHERE `queueid` = ?", array($queueid));

        // queue not exists
        if (!$queue)
        {
            $this->_error = 'queue not exists';
            return false;
        }

        // queue not done
        if ($queue['status'] === 2 || $queue['started'] > $queue['ended'])
        {
            $this->_error = 'queue not done';
            return false;
        }

        // queue reached max try times limit
        $queue_max_times = intval(config('mail', 'queue_max_times'));
        if ($queue_max_times && $queue['times'] >= $queue_max_times)
        {
            $this->_error = 'queue reached max try times limit';
            return false;
        }

        // execute it
        $this->start($queueid);
        $arguments = (array) json_decode($queue['arguments'], true);
        $result = $this->_engine->execute($arguments);
        if (!$result) $this->_error = $this->_engine->error();
        queue_log::log($queueid, __FUNCTION__, $arguments, $result, $this->_error);
        $this->end($queueid, $result, ($result || $queue['times'] + 1 >= $queue_max_times) ? 3 : 1);

        // *require delete* after success executed or reached max try times limit
        if ($queue['delete'] && ($result || ($queue_max_times && ($queue['times'] + 1) >= $queue_max_times)))
        {
            $this->delete($queueid);
        }

        return $result;
    }

    function interval($size)
    {
        $result = "start: " . date('Y-m-d H:i:s', time()) . PHP_EOL;

        if ($size)
        {
            $queues = $this->_db->select("SELECT `queueid` FROM `{$this->_table}` WHERE `engine` = '{$this->_engine_name}' AND `status` = 1 AND `nextrun` <= " . time() . " ORDER BY `nextrun` ASC LIMIT 0, {$size}");
            $results = array('total' => 0, 'success' => 0, 'error' => 0);
            if ($queues)
            {
                $results['total'] = count($queues);
                foreach ($queues as $queue)
                {
                    $results[$this->execute($queue['queueid']) ? 'success' : 'error']++;
                }
            }
            $result .= "total: {$results['total']}, success: {$results['success']}, error: {$results['error']}" . PHP_EOL;
        }
        else
        {
            $result .= "size incorrect" . PHP_EOL;
        }

        $result .= "  end: " . date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL;
        return $result;
    }

    function error()
    {
        return $this->_error;
    }
}