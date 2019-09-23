<?php

namespace Ifacesoft\Ice\Core\V2\Application;

use Exception;

abstract class SingletonContainer extends Container
{
    /**
     * @var Container[]
     */
    private static $containers = [];

    /**
     * @param array $options
     * @param array $params
     * @param array $services
     * @return Container|Service
     * @throws Exception
     */
    final public static function getInstance(array $options = [], array $params = [], array $services = [])
    {
        if (isset(self::$containers[static::class])) {
            return self::$containers[static::class];
        }

        return self::$containers[static::class] = parent::getInstance($options, $params, $services);
    }

    final public function __clone()
    {
    }

    final public function __wakeup()
    {
    }
}
