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
 * @param string $message
 * @param array<mixed> $context
 *
 * @return void
 *
 * @throws Exception
 */
function log_emergency($message, array $context = [])
{
    $logger = LoggerFacade::create();
    $logger->emergency($message, $context);
}

/**
 * @param string $message
 * @param array<mixed> $context
 *
 * @return void
 *
 * @throws Exception
 */
function log_alert($message, array $context = [])
{
    $logger = LoggerFacade::create();
    $logger->alert($message, $context);
}

/**
 * @param string $message
 * @param array<mixed> $context
 *
 * @return void
 *
 * @throws Exception
 */
function log_critical($message, array $context = [])
{
    $logger = LoggerFacade::create();
    $logger->critical($message, $context);
}

/**
 * @param string $message
 * @param array<mixed> $context
 *
 * @return void
 *
 * @throws Exception
 */
function log_error($message, array $context = [])
{
    $logger = LoggerFacade::create();
    $logger->error($message, $context);
}

/**
 * @param string $message
 * @param array<mixed> $context
 *
 * @return void
 *
 * @throws Exception
 */
function log_warning($message, array $context = [])
{
    $logger = LoggerFacade::create();
    $logger->warning($message, $context);
}

/**
 * @param string $message
 * @param array<mixed> $context
 *
 * @return void
 *
 * @throws Exception
 */
function log_notice($message, array $context = [])
{
    $logger = LoggerFacade::create();
    $logger->notice($message, $context);
}

/**
 * @param string $message
 * @param array<mixed> $context
 *
 * @return void
 *
 * @throws Exception
 */
function log_info($message, array $context = [])
{
    $logger = LoggerFacade::create();
    $logger->info($message, $context);
}

/**
 * @param string $message
 * @param array<mixed> $context
 *
 * @return void
 *
 * @throws Exception
 */
function log_debug($message, array $context = [])
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
function logs($channel)
{
    return LoggerFacade::getLoggerBy($channel);
}
