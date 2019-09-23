<?php

namespace Ifacesoft\Ice\Core\V2\Application;

use Exception;
use Ifacesoft\Ice\Core\V2\Infrastructure\Repository\Router;

class Response extends Service
{
    protected static function config()
    {
        return array_merge_recursive(
            [
                'services' => [
                    'action' => [
                        'class' => [
                            'param' => [
                                'service' => ['class' => Router::class],
                                'paramName' => 'route'
                            ],
                            'paramName' => 'actionClass'
                        ]
                    ]
                ]
            ],
            parent::config()
        );
    }

    /**
     * @throws Exception
     */
    public function send()
    {
        echo $this->getService('action')->run();
    }
}
