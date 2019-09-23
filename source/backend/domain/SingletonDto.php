<?php

namespace Ifacesoft\Ice\Core\V2\Domain;

class SingletonDto extends Dto
{
    private static $instance;

    protected static function getInstance($value)
    {
        if (isset(static::$instance)) {
            return static::$instance;
        }

        return static::$instance = new static($value);
    }

    final public function __clone()
    {
    }

    final public function __wakeup()
    {
    }
}