<?php

namespace Ifacesoft\Ice\Core\V2\Application;

abstract class SingletonService extends Service
{
    /**
     * @var Service[]
     */
    private static $services = [];

    /**
     * @param array $options
     * @param array $params
     * @param array $services
     * @return Service|SingletonService
     * @throws \Exception
     */
    final public static function getInstance(array $options = [], array $params = [], array $services = [])
    {
        $serviceClass = static::class;

        if (isset(self::$services[$serviceClass])) {
            return self::$services[$serviceClass];
        }

        return self::$services[$serviceClass] = parent::getInstance($options, $params, $services);
    }

    final public function __clone()
    {
    }

    final public function __wakeup()
    {
    }
}