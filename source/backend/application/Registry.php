<?php

namespace Ifacesoft\Ice\Core\V2\Application;

use Exception;
use Ifacesoft\Ice\Core\V2\Domain\Entity;

final class Registry extends Service
{
    /** @var Entity */
    private $data = null;

    /**
     * @return Registry|Service
     */
    protected function init()
    {
        $this->data = Entity::create();

        return parent::init();
    }

    /**
     * @param Service|string $serviceClass
     * @param string|array $paramNames
     * @param mixed $default
     * @return mixed
     * @throws Exception
     */
    public function get($serviceClass, $paramNames, $default = null)
    {
        return func_num_args() === 1
            ? $this->getServiceData($serviceClass)->get($paramNames)
            : $this->getServiceData($serviceClass)->get($paramNames, $default);
    }

    /**
     * @param $serviceClass
     * @param $params
     * @return Registry
     * @throws Exception
     */
    public function set($serviceClass, $params)
    {
        try {
            $serviceData = $this->getServiceData($serviceClass);
        } catch (Exception $e) {
            $this->data->set([$serviceClass => $serviceData = Entity::create()]);
        }

        $serviceData->set($params);

        return $this;
    }

    /**
     * @param $serviceClass
     * @return Entity
     * @throws Exception
     */
    private function getServiceData($serviceClass)
    {
        return $this->data->get($serviceClass);
    }
}
