<?php

namespace Ifacesoft\Ice\Core\V2\Domain;

use ArrayObject;
use Iterator;

class ArrayValue extends ValueObject implements Iterator
{
    public static function create(array $data = [])
    {
        return parent::getInstance(new ArrayObject($data));
    }

    /**
     * Return the current element
     * @link https://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     * @throws Exception
     */
    final public function current()
    {
        return $this->getValue()[$this->key()];
    }

    /**
     * Move forward to next element
     * @link https://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    final public function next()
    {
        $array = $this->getValue();

        next($array);
    }

    /**
     * Return the key of the current element
     * @link https://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    final public function key()
    {
        return key($this->getValue());
    }

    /**
     * Checks if current position is valid
     * @link https://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    final public function valid()
    {
        $key = $this->key();

        return ($key !== null && $key !== false);
    }

    /**
     * Rewind the Iterator to the first element
     * @link https://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    final public function rewind()
    {
        $array = $this->getValue();

        reset($array);
    }
}