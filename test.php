<?php

namespace Ifacesoft\Ice\Core\V2;

use Exception;
use Ifacesoft\Ice\Core\V2\Application\Response;
use Throwable;

require __DIR__ . '/vendor/autoload.php';

try {
    Response::getInstance()->send();
} catch (Exception $e) {
    terminated($e);
} catch (Throwable $e) {
    terminated($e);
}
/**
 * @param Exception $e
 */
function terminated($e)
{
    echo "\033[0;31m" . $e->getMessage() . "\n" . "\033[0m";
    debug_print_backtrace();
    exit;
}