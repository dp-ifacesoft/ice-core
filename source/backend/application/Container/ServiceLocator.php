<?php

namespace Ifacesoft\Ice\Core\V2\Application\Container;

use Exception;
use Ifacesoft\Ice\Core\V2\Application\Container;
use Ifacesoft\Ice\Core\V2\Application\Service;
use Ifacesoft\Ice\Core\V2\Application\SingletonContainer;
use Ifacesoft\Ice\Core\V2\Domain\Config;
use Ifacesoft\Ice\Core\V2\Domain\Dto;

final class ServiceLocator extends SingletonContainer
{
    /**
     * @param Service|string $serviceClass
     * @param Config $config
     * @param Dto $params
     * @param Container $di
     * @return mixed|null
     * @throws Exception
     */
    protected function autoCreateService($serviceClass, Config $config, Dto $params,Container $di)
    {
        $service = $this->create($serviceClass, $config, $params, $di);

        if (get_class($service) !== Container::class) {
            $this->add([$service]);
        }

        return $service;
    }
}
