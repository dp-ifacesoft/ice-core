<?php

namespace Ifacesoft\Ice\Core\V2\Domain;

class NullValue extends ValueObject
{
    public static function create($value = null)
    {
        return parent::getInstance(null);
    }
}