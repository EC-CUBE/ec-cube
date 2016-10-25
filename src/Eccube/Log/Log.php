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

namespace Eccube\Log;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * EC-CUBE専用ログ出力クラス
 * namespaceは設定せず、各プログラムからはクラス名::関数名(\EccubeLog::info())で利用できるようにしている。
 * ログ出力時はレベルに応じた関数を使用する。
 *
 * ログ出力は実際にはLoggerクラスで行っている。
 */
class Log implements LoggerAwareInterface
{
    protected $logger;

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * emergencyレベル用
     *
     * @param $message
     * @param array $context
     */
    public function emergency($message, array $context = array())
    {
        $this->logger->emergency($message, $context);
    }

    /**
     * alertレベル用
     *
     * @param $message
     * @param array $context
     */
    public function alert($message, array $context = array())
    {
        $this->logger->alert($message, $context);
    }

    /**
     * criticalレベル用
     *
     * @param $message
     * @param array $context
     */
    public function critical($message, array $context = array())
    {
        $this->logger->critical($message, $context);
    }

    /**
     * errorレベル用
     *
     * @param $message
     * @param array $context
     */
    public function error($message, array $context = array())
    {
        $this->logger->error($message, $context);
    }

    /**
     * warningレベル用
     *
     * @param $message
     * @param array $context
     */
    public function warning($message, array $context = array())
    {
        $this->logger->warning($message, $context);
    }

    /**
     * noticeレベル用
     *
     * @param $message
     * @param array $context
     */
    public function notice($message, array $context = array())
    {
        $this->logger->notice($message, $context);
    }

    /**
     * infoレベル用
     *
     * @param $message
     * @param array $context
     */
    public function info($message, array $context = array())
    {
        $this->logger->info($message, $context);
    }

    /**
     * debugレベル用
     *
     * @param $message
     * @param array $context
     */
    public function debug($message, array $context = array())
    {
        $this->logger->debug($message, $context);
    }

}
