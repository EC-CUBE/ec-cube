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

use Eccube\DependencyInjection\Facade\LoggerFacade;

/**
 * @param array<mixed> $context
 *
 * @return void
 *
 * @throws Exception
 */
function log_emergency(string $message, array $context = [])
{
    $logger = LoggerFacade::create();
    $logger->emergency($message, $context);
}

/**
 * @param array<mixed> $context
 *
 * @return void
 *
 * @throws Exception
 */
function log_alert(string $message, array $context = [])
{
    $logger = LoggerFacade::create();
    $logger->alert($message, $context);
}

/**
 * @param array<mixed> $context
 *
 * @return void
 *
 * @throws Exception
 */
function log_critical(string $message, array $context = [])
{
    $logger = LoggerFacade::create();
    $logger->critical($message, $context);
}

/**
 * @param array<mixed> $context
 *
 * @return void
 *
 * @throws Exception
 */
function log_error(string $message, array $context = [])
{
    $logger = LoggerFacade::create();
    $logger->error($message, $context);
}

/**
 * @param array<mixed> $context
 *
 * @return void
 *
 * @throws Exception
 */
function log_warning(string $message, array $context = [])
{
    $logger = LoggerFacade::create();
    $logger->warning($message, $context);
}

/**
 * @param array<mixed> $context
 *
 * @return void
 *
 * @throws Exception
 */
function log_notice(string $message, array $context = [])
{
    $logger = LoggerFacade::create();
    $logger->notice($message, $context);
}

/**
 * @param array<mixed> $context
 *
 * @return void
 *
 * @throws Exception
 */
function log_info(string $message, array $context = [])
{
    $logger = LoggerFacade::create();
    $logger->info($message, $context);
}

/**
 * @param array<mixed> $context
 *
 * @return void
 *
 * @throws Exception
 */
function log_debug(string $message, array $context = [])
{
    $logger = LoggerFacade::create();
    $logger->debug($message, $context);
}

/**
 * プラグイン用ログ出力関数
 *
 * @param string $channel 設定されたchannel名
 *
 * @return Monolog\Logger
 */
function logs(string $channel)
{
    return LoggerFacade::getLoggerBy($channel);
}
