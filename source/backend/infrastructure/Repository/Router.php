<?php

namespace Ifacesoft\Ice\Core\V2\Infrastructure\Repository;

use Exception;
use Ifacesoft\Ice\Core\V2\Application\Action\HelloWorld;
use Ifacesoft\Ice\Core\V2\Application\Request;
use Ifacesoft\Ice\Core\V2\Domain\Dto;
use Ifacesoft\Ice\Core\V2\Domain\Route;
use Ifacesoft\Ice\Core\V2\Infrastructure\SingletonRepository;

class Router extends SingletonRepository
{
    protected static function config()
    {
        return array_merge_recursive(
            [
                'services' => [
                    'request' => [
                        'class' => Request::class
                    ],
                ]
            ],
            parent::config()
        );
    }

    /**
     * @param Request|null $request
     * @return Route|Dto
     * @throws Exception
     */
    public function getRoute(Request $request = null)
    {
        if (!$request) {
            $request = $this->getService('request');
        }

        $uri = $request->getParam('uri', '/');
        $method = $request->getParam('method', 'cli');

        return Route::create([
            'uri' => $uri,
            'method' => $method,
            'actionClass' => HelloWorld::class
        ]);
    }
}
