<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Eccube\Log\Monolog\Helper;

use Eccube\Entity\Customer;
use Eccube\Entity\Member;
use Eccube\Log\Monolog\Processor\IntrospectionProcessor;
use Eccube\Log\Monolog\Processor\WebProcessor;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;

/**
 * Handler生成クラス
 *
 * @package Eccube\Log\Monolog\Helper
 */
class LogHelper
{

    /** @var  \Eccube\Application */
    protected $app;

    /**
     * EccubeMonologHelper constructor.
     *
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * log.ymlの内容に応じたHandlerの設定を行う
     *
     * @param array $channelValues
     * @return FingersCrossedHandler
     */
    public function getHandler(array $channelValues)
    {
        $app = $this->app;

        $levels = Logger::getLevels();

        // ファイル名などの設定を行い、設定がなければデフォルト値を設定
        $logFileName = isset($channelValues['filename']) ? $channelValues['filename'] : $app['config']['log']['filename'];
        $delimiter = isset($channelValues['delimiter']) ? $channelValues['delimiter'] : $app['config']['log']['delimiter'];
        $dateFormat = isset($channelValues['dateformat']) ? $channelValues['dateformat'] : $app['config']['log']['dateformat'];
        $logLevel = isset($channelValues['log_level']) ? $channelValues['log_level'] : $app['config']['log']['log_level'];
        $actionLevel = isset($channelValues['action_level']) ? $channelValues['action_level'] : $app['config']['log']['action_level'];
        $passthruLevel = isset($channelValues['passthru_level']) ? $channelValues['passthru_level'] : $app['config']['log']['passthru_level'];
        $maxFiles = isset($channelValues['max_files']) ? $channelValues['max_files'] : $app['config']['log']['max_files'];
        $logDateFormat = isset($channelValues['log_dateformat']) ? $channelValues['log_dateformat'] : $app['config']['log']['log_dateformat'];
        $logFormat = isset($channelValues['log_format']) ? $channelValues['log_format'] : $app['config']['log']['log_format'];

        if ($app['debug']) {
            $level = Logger::DEBUG;
        } else {
            $level = $logLevel;
        }


        // RotateHandlerの設定
        $filename = $app['config']['root_dir'].'/app/log/'.$logFileName.'.log';
        $RotateHandler = new RotatingFileHandler($filename, $maxFiles, $level);
        $RotateHandler->setFilenameFormat(
            $logFileName.$delimiter.'{date}'.$app['config']['log']['suffix'],
            $dateFormat
        );

        // ログフォーマットの設定(設定ファイルで定義)
        $RotateHandler->setFormatter(new LineFormatter($logFormat.PHP_EOL, $logDateFormat, true, true));

        // FingerCossedHandlerの設定
        $FingerCrossedHandler = new FingersCrossedHandler(
            $RotateHandler,
            new ErrorLevelActivationStrategy($levels[$actionLevel]),
            0,
            true,
            true,
            $levels[$passthruLevel]
        );


        // Processorの内容をログ出力
        $webProcessor = new WebProcessor();
        $uidProcessor = new UidProcessor(8);

        $FingerCrossedHandler->pushProcessor(function ($record) use ($app, $uidProcessor, $webProcessor) {
            // ログフォーマットに出力する値を独自に設定

            $record['level_name'] = sprintf("%-5s", $record['level_name']);

            // セッションIDと会員IDを設定
            $record['session_id'] = null;
            $record['user_id'] = null;
            if ($app->isBooted()) {
                if (isset($app['session'])) {
                    $sessionId = $app['session']->getId();
                    if ($sessionId) {
                        $record['session_id'] = substr(sha1($sessionId), 0, 8);
                    }
                }
                if (isset($app['user'])) {
                    $user = $app->user();
                    if ($user instanceof Customer || $user instanceof Member) {
                        $record['user_id'] = $user->getId();
                    }
                }
            }

            $record['uid'] = $uidProcessor->getUid();

            $record['url'] = $webProcessor->getRequestUri();
            $record['ip'] = $webProcessor->getClientIp();
            $record['referrer'] = $webProcessor->getReferer();
            $record['method'] = $webProcessor->getMethod();
            $record['user_agent'] = $webProcessor->getUserAgent();

            // クラス名などを一旦保持し、不要な情報は削除
            $line = $record['extra']['line'];
            $functionName = $record['extra']['function'];
            // php5.3だとclass名が取得できないため、ファイル名を元に出力
            // $className = $record['extra']['class'];
            $className = $record['extra']['file'];

            // 不要な情報を削除
            unset($record['extra']['file']);
            unset($record['extra']['line']);
            unset($record['extra']['class']);
            unset($record['extra']['function']);

            $record['class'] = pathinfo($className, PATHINFO_FILENAME);
            $record['function'] = $functionName;
            $record['line'] = $line;

            return $record;
        });

        // クラス名等を取得するProcessor、ログ出力時にクラス名/関数名を無視するための設定を行っている
        $skipClasses = array('Psr\\Log\\', 'Eccube\\Log\\');
        $skipFunctions = array(
            'log_info',
            'log_notice',
            'log_warning',
            'log_error',
            'log_critical',
            'log_alert',
            'log_emergency'
        );
        $intro = new IntrospectionProcessor(Logger::DEBUG, $skipClasses, $skipFunctions);
        $FingerCrossedHandler->pushProcessor($intro);

        return $FingerCrossedHandler;

    }

}
