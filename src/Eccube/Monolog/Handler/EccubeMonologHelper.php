<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
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

namespace Eccube\Monolog\Handler;

use Eccube\Application;
use Eccube\Monolog\Processor\EccubeWebProcessor;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\UidProcessor;

class EccubeMonologHelper
{

    protected $app;

    /**
     * EccubeMonologHandler constructor.
     *
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function getHandler($channelName, $values)
    {
        $app = $this->app;

        $levels = Logger::getLevels();

        $logFileName = $values['filename'];
        $delimiter = $values['delimiter'];
        $dateFormat  = $values['dateformat'];
        $logLevel = $values['log_level'];
        $actionLevel = $values['action_level'];
        $maxFiles = $values['max_files'];
        $logDateFormate = $values['log_dateformat'];

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

        // ログフォーマットの設定
        $format = "[%datetime%] %channel%.%level_name% [%token%] [%uid%] [%class%:%function%:%line%] - %message% %context% %extra% [%url%, %ip%, %referrer%]\n";
        $RotateHandler->setFormatter(new LineFormatter($format, $logDateFormate, true, true));

        // FingerCossedHandlerの設定
        $FingerCrossedHandler = new FingersCrossedHandler(
            $RotateHandler,
            new ErrorLevelActivationStrategy($levels[$actionLevel])
        );


        // Processorの設定
        $web = new EccubeWebProcessor();
        $uid = new UidProcessor(8);

        $FingerCrossedHandler->pushProcessor(function ($record) use ($app, $uid, $web) {

            $record['level_name'] = sprintf("%-5s", $record['level_name']);

            $sessionId = substr(sha1($app['session']->getId()), 0, 8);
            $record['token'] = $sessionId;
            $record['uid'] = $uid->getUid();

            $record['url'] = $web->serverData['REQUEST_URI'];
            $record['ip'] = $web->serverData['REMOTE_ADDR'];
            $record['referrer'] = isset($web->serverData['HTTP_REFERER']) ? $web->serverData['HTTP_REFERER'] : '';

            $line = $record['extra']['line'];
            // $className = $record['extra']['class'];
            $functionName = $record['extra']['function'];
            $className = $record['extra']['file'];

            // 不要な情報を削除
            unset($record['extra']['file']);
            unset($record['extra']['line']);
            unset($record['extra']['class']);
            unset($record['extra']['function']);

            // php5.3だとclass名が取得できないため、ファイル名を出力
            //  $className = substr(strrchr($className, '\\'), 1);
            $className = strstr(substr(strrchr($className, '/'), 1), '.', true);

            $record['class'] = $className;
            $record['function'] = $functionName;
            $record['line'] = $line;

            return $record;
        });

        $intro = new IntrospectionProcessor();
        $FingerCrossedHandler->pushProcessor($intro);

        return $FingerCrossedHandler;

    }

}
