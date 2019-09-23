<?php

namespace Ifacesoft\Ice\Core\V2\Infrastructure;

use Exception;
use Ifacesoft\Ice\Core\V2\Application\Service;

abstract class SingletonRepository extends Repository
{
    /**
     * @var Repository[]
     */
    private static $repositories = [];

    /**
     * @param array $options
     * @param array $params
     * @param array $services
     * @return Repository|Service
     * @throws Exception
     */
    final public static function getInstance(array $options = [], array $params = [], array $services = [])
    {
        if (isset(self::$repositories[static::class])) {
            return self::$repositories[static::class];
        }

        return self::$repositories[static::class] = parent::getInstance($options, $params, $services);
    }

    final public function __clone()
    {
    }

    final public function __wakeup()
    {
    }
}
