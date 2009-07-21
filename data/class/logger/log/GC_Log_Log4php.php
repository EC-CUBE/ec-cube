<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2009 LOCKON CO.,LTD. All Rights Reserved.
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

if(!defined('LOG4PHP_CONFIGURATION')){
    define('LOG4PHP_CONFIGURATION', realpath(dirname(__FILE__)) . '/../../../conf/log4php.properties');
}

if(phpversion() >= '5.0.0') {
    require_once (realpath(dirname(__FILE__)) . '/../../../module/log4php/php5/log4php/LoggerManager.php');
} else {
    require_once (realpath(dirname(__FILE__)) . '/../../../module/log4php/php4/log4php/LoggerManager.php');
}

require_once (realpath(dirname(__FILE__)) . '/GC_Log_Interface.php');

/**
 * GC_Log_Log4phpクラス
 *
 * ロギングをlog4phpで行うクラス
 * 
 *
 * @package Logger
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */

class GC_Log_Log4php extends GC_Log_Interface{
    /** @var logger loggerインスタンス*/
    var $logger;

    /**
     * コンストラクタ
     *
     * @param void
     * @return void
     *
     */
    function GC_Log_Log4php($loggerName='EC_CUBE') {
        $this->logger =& LoggerManager::getLogger($loggerName);
    }

    /**
     * DEBUGログ
     *
     * @param string 出力したいログ
     * @return void
     *
     */
    function gfDebug($log) {
        $this->logger->debug($log);
    }

    /**
     * INFOログ
     *
     * @param string 出力したいログ
     * @return void
     *
     */
    function gfInfo($log) {
        $this->logger->info($log);
    }

    /**
     * WARNログ
     *
     * @param string 出力したいログ
     * @return void
     *
     */
    function gfWarn($log) {
        $this->logger->warn($log);
    }

    /**
     * ERRORログ
     *
     * @param string 出力したいログ
     * @return void
     *
     */
    function gfError($log) {
        $this->logger->error($log);
    }

    /**
     * FATALログ
     *
     * @param string 出力したいログ
     * @return void
     *
     */
    function gfFatal($log) {
        $this->logger->fatal($log);
    }
}
?>