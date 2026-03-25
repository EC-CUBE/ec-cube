<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\ErrorHandler;

require dirname(__DIR__) . '/vendor/autoload.php';

if (file_exists(dirname(__DIR__) . '/config/bootstrap.php')) {
    require dirname(__DIR__) . '/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
}

// Pre-register Symfony's ErrorHandler so that subsequent calls to
// ErrorHandler::register(null, false) in FrameworkBundle::boot() are idempotent.
// Without this, each kernel boot pushes a new exception handler onto PHP's stack,
// and PHPUnit 11.4+ detects the leaked handler as a "risky test".
$handler = ErrorHandler::register();
// Match the behaviour of framework.php_errors.throw=false so that E_WARNING etc.
// are logged but never converted to ErrorException during the bootstrap phase
// (before the kernel configures the handler via ErrorHandlerConfigurator).
$handler->throwAt(0);
