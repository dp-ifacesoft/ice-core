<?php

namespace Ifacesoft\Ice\Core\V2\Domain;

final class EmptyDto extends SingletonDto
{
    private static $emptyDto = null;

    protected static function getInstance($value)
    {
        if (self::$emptyDto) {
            return self::$emptyDto;
        }

        return self::$emptyDto = parent::getInstance([]);
    }
}