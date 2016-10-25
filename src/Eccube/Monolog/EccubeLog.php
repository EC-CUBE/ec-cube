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

use Eccube\Monolog\EccubeLogger;

/**
 * EC-CUBE専用ログ出力クラス
 * namespaceは設定せず、各プログラムからはクラス名::関数名(\EccubeLog::info())で利用できるようにしている。
 * ログ出力時はレベルに応じた関数を使用する。
 *
 * ログ出力は実際にはEccubeLoggerクラスで行っている。
 */
class EccubeLog
{

    /**
     * emergencyレベル用
     *
     * @param $message
     * @param array $context
     */
    public static function emergency($message, array $context = array())
    {
        $logger = EccubeLogger::getInstance();

        $logger->emergency($message, $context);
    }

    /**
     * alertレベル用
     *
     * @param $message
     * @param array $context
     */
    public static function alert($message, array $context = array())
    {
        $logger = EccubeLogger::getInstance();

        $logger->alert($message, $context);
    }

    /**
     * criticalレベル用
     *
     * @param $message
     * @param array $context
     */
    public static function critical($message, array $context = array())
    {
        $logger = EccubeLogger::getInstance();

        $logger->critical($message, $context);
    }

    /**
     * errorレベル用
     *
     * @param $message
     * @param array $context
     */
    public static function error($message, array $context = array())
    {
        $logger = EccubeLogger::getInstance();

        $logger->error($message, $context);
    }

    /**
     * warningレベル用
     *
     * @param $message
     * @param array $context
     */
    public static function warning($message, array $context = array())
    {
        $logger = EccubeLogger::getInstance();

        $logger->warning($message, $context);
    }

    /**
     * noticeレベル用
     *
     * @param $message
     * @param array $context
     */
    public static function notice($message, array $context = array())
    {
        $logger = EccubeLogger::getInstance();

        $logger->notice($message, $context);
    }

    /**
     * infoレベル用
     *
     * @param $message
     * @param array $context
     */
    public static function info($message, array $context = array())
    {
        $logger = EccubeLogger::getInstance();

        $logger->info($message, $context);
    }

    /**
     * debugレベル用
     *
     * @param $message
     * @param array $context
     */
    public static function debug($message, array $context = array())
    {
        $logger = EccubeLogger::getInstance();

        $logger->debug($message, $context);
    }

}
