<?php

namespace Ifacesoft\Ice\Core\V2\Domain;

class ValueObject
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * Value constructor.
     * @param $value
     */
    final private function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    final protected function getValue()
    {
        return $this->value;
    }

    protected static function getInstance($value)
    {
        return new static($value);
    }

    public function printR() {
        return str_replace('Array (', '(', preg_replace('/\s{2,}/', ' ', preg_replace('/[\x00-\x1F\x7F ]/', ' ', print_r($this->value, true))));
    }
}
