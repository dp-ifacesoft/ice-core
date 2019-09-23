<?php

namespace Ifacesoft\Ice\Core\V2\Application;

use Exception;
use Ifacesoft\Ice\Core\V2\Application\Container\ServiceLocator;
use Ifacesoft\Ice\Core\V2\Domain\Config;
use Ifacesoft\Ice\Core\V2\Domain\Dto;
use Ifacesoft\Ice\Core\V2\Domain\EmptyDto;
use Ifacesoft\Ice\Core\V2\Domain\Entity;
use Ifacesoft\Ice\Core\V2\Infrastructure\Repository\Configuration;
use RuntimeException;

abstract class Service
{
    /**
     * @var string
     */
    private $id = null;

    /**
     * @var Dto
     */
    private $params = null;

    /**
     * @var Container
     */
    private $di = null;

    /**
     * @var Config
     */
    private $config = null;

    /**
     *  return array_merge_recursive(
     *      [
     *          'params' => []
     *      ],
     *      parent::config()
     *  );
     *
     * @return array
     */
    protected static function config()
    {
        return [];
    }

    /**
     * Service constructor.
     * @param Config $config
     * @param Dto $params
     * @param Container $di
     * @throws Exception
     */
    final private function __construct(Config $config = null, Dto $params = null, Container $di = null)
    {
        if (get_class($this) === EmptyContainer::class) {
            $di = $this;
        }

        $this->config = $config ? $config : Config::create();
        $this->params = $params ? $params : EmptyDto::create();
        $this->di = $di ? $di : EmptyContainer::getInstance();

        $this->init();
    }

    /**
     * @return string
     */
    final public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    private function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param null $paramNames
     * @param $options
     * @return Dto
     * @throws Exception
     */
    final public function getParam($paramNames = null, $options = null)
    {
        switch (func_num_args()) {
            case 0:
                return $this->params;
            case 1:
                return $this->params->get($paramNames);
            default:
                return $this->params->get($paramNames, $options);
        }
    }

    /**
     * @return Service
     */
    protected function init()
    {
        $this->id = self::generateId($this->config, $this->params, $this->di);

        $this->config->setId($this->getServiceId());

        if (get_class($this->params) !== EmptyDto::class) {
            $this->params->setId($this->getServiceId());
        }

        if (get_class($this->di) !== EmptyContainer::class) {
            $this->di->setId($this->getServiceId());
        }

        return $this;
    }

    /**
     * @param $id
     * @return Service
     * @throws Exception
     */
    public function getService($id) {
        return $this->di->get($id);
    }

    /**
     * @param Config $config
     * @param Dto $params
     * @param Container $di
     * @return string
     * @todo частое использование при получении и созжании сервиса
     */
    protected static function generateId(Config $config, Dto $params, Container $di)
    {
//        $idData = [
//            spl_object_hash($config),
//            spl_object_hash($params),
//            spl_object_hash($di)
//        ];

        $idData = [
            $config,
            $params,
            $di
        ];

        return md5(json_encode($idData));
    }

    final public function getServiceId()
    {
        return static::serviceId($this->getId());
    }

    final public static function serviceId($id)
    {
        return static::class . '/' . $id;
    }

    /**
     * @param Service|string $serviceClass
     * @param Config $config
     * @param Dto $params
     * @param Container $di
     * @return Service
     */
    final protected function create($serviceClass, Config $config, Dto $params, Container $di)
    {
        if (get_class($this) !== ServiceLocator::class) {
            throw new RuntimeException('Only ServiceLocator can create Service');
        }

        return new $serviceClass($config, $params, $di);
    }

    /**
     * @param array $options
     * @param array $params
     * @param Service[] $services
     * @return Service
     * @throws Exception
     */
    public static function getInstance(array $options = [], array $params = [], array $services = [])
    {
        if (static::class === ServiceLocator::class) {
            $serviceLocator = new ServiceLocator(null, Entity::create());

            $serviceLocator->add([$serviceLocator]);

            $serviceLocator->add([(Environment::getInstance())]);
            $serviceLocator->add([(Configuration::getInstance())]);

            return $serviceLocator;
        }

        if (static::class === EmptyContainer::class) {
            return new EmptyContainer();
        }

        if (static::class === Environment::class) {
            return new Environment();
        }

        if (static::class === Configuration::class) {
            return new Configuration(null, Entity::create());
        }

        return ServiceLocator::getInstance()->get([static::class, $options, $params, $services]);

//
//        try {
//            /** @var ConfigRepository $configRepository */
//            $configRepository = $serviceLocator->get(ConfigRepository::class);
//        } catch (\Exception $e) {
//            $configRepository = new ConfigRepository(null);
//
//        }
//
//
//        $config = $configRepository->get(static::class);
//
//
//
//        $serviceLocatorOptions = $config->get('serviceLocator', []);
//
//        if (is_a(static::class, Locator::class, true)) {
//            return static::getLocator();
//        }
//
//        if (is_a(static::class, Dto::class, true)) {
//            $dto = new static($options['id']); // todo: $options['id'] временно
//            // todo: возможно реализовать конфиг и контейнер
//            return $dto->init($params);
//        }
//
//        /** @var ServiceLocator $serviceLocator */
//        $serviceLocator = ServiceLocator::getInstance();
//
//
//        $service = $serviceLocator->getService(static::class, $options, $params, $services);
//
//        if ($service) {
//            return $service;
//        }
//
//        if (static::class === Container::class) {
//            $services['config'] = null;
//        } else if (static::class === Config::class && isset($options['id']) && $options['id'] === Config::class) {
//            $services['config'] = self::getSelfConfig();
//        } else if (ServiceLocator::is()) {
//            $services['config'] = Config::getInstance($options);
//        } else {
//            // todo: попробовать завести
//            $configContainer = null;
//
//            $services['config'] = new Config(static::class);
//
//            $services['config'] = $services['config']->init($options, $configContainer);
//        }
//
//        if (!$services['config']) {
//            unset($services['config']);
//        }
//
//        $service = new static(isset($services['config']) ? $services['config']->get('name', null) : null);
//
//        $container = new Container(static::class);
//
//        return $service->init($params, $container->init($services));
//
//        throw new \Exception('Victory');
////            /** @var ServiceLocator $serviceLocator */
////            $serviceLocator = ServiceLocator::getInstance();
////
////            $serviceId = static::buildId([
////                'class' => static::class,
////                'options' => $options,
////                'params' => $params
////            ]);
////
////            if ($service = $serviceLocator->get($serviceId)) {
////                return $service;
////            }
////
////            $service = new static();
////
////            $service->register($params);
////
////            $serviceLocator->add($serviceId, $service);
//
//        return $service;

    }
}