<?php

namespace Ifacesoft\Ice\Core\V2\Domain;

use Exception;

class Dto extends ArrayValue
{
    private $id = null;

    /**
     * @return null|string|int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string|int $id
     * @return Dto
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param array $data
     * @return Dto
     */
    public static function create(array $data = [])
    {
        if (static::class === self::class && !$data) {
            return EmptyDto::create();
        }

        return parent::create($data);
    }

    /**
     * @param $paramNames
     * @param array $default
     * @return mixed
     * @throws Exception
     */
    final public function get($paramNames, $default = null)
    {
        if ($paramNames === null) {
            return $this->getValue();
        }

        $required = func_num_args() === 1;

        if (get_class($this) === EmptyDto::class && $required) {
            throw new Exception('EmptyDto not can paramNames ' . ValueObject::getInstance($paramNames)->printR());
        }

        $isSingle = !is_array($paramNames) || ($required && !is_array($default));

        $isArray = $required ? is_array($paramNames) : is_array($default);

        $default = (array)$default;

        $params = [];

        foreach ((array)$paramNames as $alias => $name) {
            if (is_int($alias)) {
                $alias = $name;
            }

            if (empty($name)) {
                throw new Exception('Dto param name is empty');
            }

            if (array_key_exists($name, $this->getValue())) {
                $params[$alias] = $this->getValue()[$name];
            } else {
                $params[$alias] = $this->getValue();

                foreach (explode('/', $name) as $keyPart) {
                    if (!array_key_exists($keyPart, $params[$alias])) {
                        if ($required) {
                            throw new Exception('Required paramName ' . $name . ' in ' . get_class($this) . ':' . $this->getId());
                        }

                        if ($isSingle) {
                            $params[$alias] = $isArray ? $default : reset($default);
                        } else {
                            $params[$alias] = array_key_exists($alias, $default) ? $default[$alias] : null;
                        }

                        break;
                    }

                    $params[$alias] = $params[$alias][$keyPart];
                }
            }

            if ($isSingle) {
                if ($isArray) {
                    return (array)$params[$alias];
                }

                return is_array($params[$alias]) ? reset($params[$alias]) : $params[$alias];
            }
        }

        return $params;
    }

    /**
     * @param string $paramName
     * @return Dto
     * @throws Exception
     */
    final public function getDto($paramName = null)
    {
        $params = isset($this->getValue()[$paramName]) ? (array)$this->getValue()[$paramName] : [];

        // тут self:: Пусть будет так: почему-то работает как static:: т.е self:: возврашщает Dto, а в create static:: возвращет, например Config
        return Dto::create($params);
    }
}