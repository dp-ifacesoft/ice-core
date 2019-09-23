<?php

namespace Ifacesoft\Ice\Core\V2\Domain;

class StringValue extends ValueObject
{
    public static function create($value = '')
    {
        return parent::getInstance($value);
    }
}