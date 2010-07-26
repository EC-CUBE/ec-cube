<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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

define('LOG4PHP_CONFIGURATION', realpath(dirname(__FILE__)) . '/conf/log4php.properties');
require_once(realpath(dirname(__FILE__)) . "/../../require.php");
require_once(realpath(dirname(__FILE__)) . "/../../../data/class/logger/log/GC_Log_Log4php.php");

/**
 * GC_Log_Log4phpのテスト
 *
 * プロパティファイルの内容は以下
 * +---------------------------------------------------------
 * | log4php.rootLogger=DEBUG, R
 * | log4php.appender.R=LoggerAppenderEcho
 * | log4php.appender.R.layout=LoggerPatternLayout
 * | log4php.appender.R.layout.ConversionPattern="[%p] %m%n"
 * +---------------------------------------------------------
 *
 * @package Logger
 * @author LOCKON CO.,LTD.
 * @version $Id$
 *
 */
class GC_Log_Log4php_Test extends PHPUnit_Framework_TestCase
{
    /**
     * DEBUGログのテスト
     *
     *
     */
    function testGfDebug(){
        $object = new GC_Log_Log4php();

        ob_start();
        $object->gfDebug('DEBUGログ');
        $buff = ob_get_contents();
        ob_end_clean();

        $output = "[DEBUG] DEBUGログ\n";

        $this->assertEquals($buff, $output);
    }

    /**
     * INFOログのテスト
     *
     *
     */
    function testGfInfo(){
        $object = new GC_Log_Log4php();

        ob_start();
        $object->gfInfo('INFOログ');
        $buff = ob_get_contents();
        ob_end_clean();

        $output = "[INFO] INFOログ\n";

        $this->assertEquals($buff, $output);
    }

    /**
     * ERRORログのテスト
     *
     *
     */
    function testGfError(){
        $object = new GC_Log_Log4php();

        ob_start();
        $object->gfError('ERRORログ');
        $buff = ob_get_contents();
        ob_end_clean();

        $output = "[ERROR] ERRORログ\n";

        $this->assertEquals($buff, $output);
    }

    /**
     * FATALログのテスト
     *
     *
     */
    function testGfFatal(){
        $object = new GC_Log_Log4php();

        ob_start();
        $object->gfFatal('FATALログ');
        $buff = ob_get_contents();
        ob_end_clean();

        $output = "[FATAL] FATALログ\n";

        $this->assertEquals($buff, $output);
    }
}
?>
