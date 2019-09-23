<?php

namespace Ifacesoft\Ice\Core\V2\Application;

abstract class Action extends Service
{
    abstract public function run();
}