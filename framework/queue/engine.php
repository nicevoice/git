<?php
abstract class queue_engine
{
    protected $_error;

    abstract function execute(array $params);

    function error()
    {
        return $this->_error;
    }
}