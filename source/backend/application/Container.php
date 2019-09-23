<?php

namespace Ifacesoft\Ice\Core\V2\Application;

use Exception;
use Ifacesoft\Ice\Core\V2\Application\Container\ServiceLocator;
use Ifacesoft\Ice\Core\V2\Domain\Config;
use Ifacesoft\Ice\Core\V2\Domain\Dto;
use Ifacesoft\Ice\Core\V2\Infrastructure\Repository\Configuration;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Container extends Service implements ContainerInterface
{
    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string|array $id Identifier of the entry to look for.
     *
     * @return Service
     *
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws Exception
     */
    final public function get($id)
    {
        if (!$id) {
            throw new \RuntimeException('Service id is empty in ' . get_class($this));
        }

        if (!is_array($id)) {
            return $this->getParam($id);
        }

        /**
         * @var Service|string $serviceClass
         * @var array $options
         * @var array $params
         * @var array $services
         */
        list($serviceClass, $options, $params, $services) = array_pad($id, 4, []);

        $configuration = Configuration::getInstance();

        $options = array_merge_recursive(
            $options,
            $serviceClass::config()
        );

        $configId = $serviceClass . '/' . md5(json_encode($options));

        /** @var Config $config */
        try {
            $config = $configuration->get($configId);
        } catch (Exception $e) {
            $config = Config::create($options)->setId($configId);

            $configuration->add([$config]);
        }

        $di = $configuration->getDi($config, $services);

        $params = Dto::create($params);

        try {
            return $this->getParam($serviceClass::serviceId($serviceClass::generateId($config, $params, $di))); // todo: научиться в опциях Dtp::get указывать возможность бросать исключения (ам нужно бросать что-то типа Сервис не найден и именно его отлавливать)
        } catch (Exception $e) {
            if (get_class($this) === ServiceLocator::class) {
                if ($service = $this->autoCreateService($serviceClass, $config, $params, $di)) {
                    return $service;
                }
            }

            throw $e;
        }
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     *
     * @throws Exception
     *
     * @deprecated Use Container::get(id)
     *
     */
    final public function has($id)
    {
        return (bool)$this->get($id);
    }

    /**
     * @param array $services
     *
     * @return Container
     *
     * @throws Exception
     */
    final public function add(array $services)
    {
        $this->getParam()->set(
            $services,
            [
                'callbacks' => static function ($alias, $service) {
                    /** @var Service $service */
                    return [$service->getServiceId(), $service];
                }
            ]
        );

        return $this;
    }

    /**
     * @param array $services
     *
     * @return Container
     *
     * @throws Exception
     */
    final public function remove(array $services)
    {
        $this->getParam()->delete($services);

        return $this;
    }
}