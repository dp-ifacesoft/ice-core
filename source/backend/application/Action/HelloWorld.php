<?php

namespace Ifacesoft\Ice\Core\V2\Application\Action;

use Ifacesoft\Ice\Core\V2\Application\Action;

class HelloWorld extends Action
{
    public function run()
    {
        echo 'Hello World!';
    }
}