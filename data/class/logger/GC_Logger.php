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

require_once (realpath(dirname(__FILE__)) . '/log/GC_Log_Log4php.php');

/**
 * loggerクラス
 *
 * ロギングを行うクラス
 *
 * @package Logger
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */

class GC_Logger {
    /** @var GC_Log_Interface ログインスタンス */
    var $objLog;

    /**
     * コンストラクタ
     *
     * @param string logger名
     * @return void
     *
     */
    function GC_Logger($loggerName) {
            $this->objLog = new GC_Log_Log4php($loggerName);
    }

    /**
     * インスタンスの取得
     *
     *
     * @param string logger名
     * @return GC_Logger GC_Loggerインスタンス
     *
     */
    function &gfGetInstance($loggerName='EC_CUBE') {
        static $instances = array();

        if(empty($loggerName)) {
            $loggerName = 'EC_CUBE';
        }

        if(!array_key_exists($loggerName, $instances)) {
            $instances[$loggerName] = new GC_Logger($loggerName);
        }

        return $instances[$loggerName];
    }

    /**
     * ログ整形
     *
     * 文字列以外は、print_r形式に整形する
     *
     * @param mixed 出力したいログ
     * @return mixed 整形されたログ
     *
     */
    function lfCastLog($obj) {
        if(!is_string($obj)) {
            ob_start();
            print_r($obj);
            $obj = ob_get_contents();
            ob_end_clean();
        }

        return $obj;
    }

    /**
     * DEBUGログ
     *
     * @param string 出力したいログ
     * @param mixed ダンプしたい変数
     * @return void
     *
     */
    function gfDebug($str, $dump='') {
        $this->objLog->gfDebug($str . $this->lfCastLog($dump));
    }

    /**
     * INFOログ
     *
     * @param string 出力したいログ
     * @param mixed ダンプしたい変数
     * @return void
     *
     */
    function gfInfo($str, $dump='') {
        $this->objLog->gfInfo($str . $this->lfCastLog($dump));
    }

    /**
     * WARNログ
     *
     * @param string 出力したいログ
     * @param mixed ダンプしたい変数
     * @return void
     *
     */
    function gfWarn($str, $dump='') {
        $this->objLog->gfWarn($str . $this->lfCastLog($dump));
    }

    /**
     * ERRORログ
     *
     * @param string 出力したいログ
     * @param mixed ダンプしたい変数
     * @return void
     *
     */
    function gfError($str, $dump='') {
        $this->objLog->gfError($str . $this->lfCastLog($dump));
    }

    /**
     * FATALログ
     *
     * @param string 出力したいログ
     * @param mixed ダンプしたい変数
     * @return void
     *
     */
    function gfFatal($str, $dump='') {
        $this->objLog->gfFatal($str . $this->lfCastLog($dump));
    }
}
?>