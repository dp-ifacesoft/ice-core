<?php

namespace Ifacesoft\Ice\Core\V2\Infrastructure\Repository;

use Exception;
use Ifacesoft\Ice\Core\V2\Application\Container;
use Ifacesoft\Ice\Core\V2\Application\Container\ServiceLocator;
use Ifacesoft\Ice\Core\V2\Application\EmptyContainer;
use Ifacesoft\Ice\Core\V2\Application\Service;
use Ifacesoft\Ice\Core\V2\Domain\Config;
use Ifacesoft\Ice\Core\V2\Domain\Dto;
use Ifacesoft\Ice\Core\V2\Infrastructure\SingletonRepository;

class Configuration extends SingletonRepository
{
    /**
     * @param Config $config
     * @param Service[] $services
     * @return Container|Service
     * @throws Exception
     */
    public function getDi(Config $config, array $services)
    {
        $serviceLocator = ServiceLocator::getInstance();

        foreach ($config->get('services', []) as $serviceAlias => $serviceData) {
            $serviceDto = Dto::create($serviceData);

            $services += [$serviceAlias => $serviceLocator->get([$serviceDto->get('class')])];
        }

        return $services
            ? Container::getInstance([], $services)
            : EmptyContainer::getInstance();
    }
}