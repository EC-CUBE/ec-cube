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
require_once(realpath(dirname(__FILE__)) . "/../../../data/class/logger/GC_Logger.php");

/**
 * GC_Loggerのテスト
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
class GC_Logger_Test extends PHPUnit_Framework_TestCase
{

    /**
     * インスタンス化のテスト
     *
     *
     */
    function testGfGetInstance(){
        $object1 = GC_Logger::gfGetInstance();
        $object2 = GC_Logger::gfGetInstance();

        $this->assertSame($object1, $object2);

        $object3 = GC_Logger::gfGetInstance('logger3');
        $object4 = GC_Logger::gfGetInstance('logger4');

        $this->assertNotSame($object3, $object4);
    }

    /**
     * DEBUGログのテスト
     *
     *
     */
    function testGfDebug(){
        $object = GC_Logger::gfGetInstance();

        ob_start();
        $object->gfDebug('DEBUGログ');
        $buff = ob_get_contents();
        ob_end_clean();

        $output = "[DEBUG] DEBUGログ\n";

        $this->assertEquals($buff,$output);
    }

    /**
     * DEBUGログ、ダンプテスト
     *
     *
     */
    function testGfDebugDump(){
        $object = GC_Logger::gfGetInstance();
        $a = array('test');
        ob_start();
        $object->gfDebug('a => ', $a);
        $buff = ob_get_contents();
        ob_end_clean();

        $output = 
"[DEBUG] a => Array
(
    [0] => test
)

";
        $this->assertEquals($buff,$output);
    }

    /**
     * INFOログのテスト
     *
     *
     */
    function testGfInfo(){
        $object = GC_Logger::gfGetInstance();

        ob_start();
        $object->gfInfo('INFOログ');
        $buff = ob_get_contents();
        ob_end_clean();

        $output = "[INFO] INFOログ\n";

        $this->assertEquals($buff, $output);
    }

    /**
     * INFOログ、ダンプのテスト
     *
     *
     */
    function testGfInfoDump(){
        $object = GC_Logger::gfGetInstance();
        $a = array('test');
        ob_start();
        $object->gfInfo('a => ', $a);
        $buff = ob_get_contents();
        ob_end_clean();

        $output = 
"[INFO] a => Array
(
    [0] => test
)

";
        $this->assertEquals($buff, $output);
    }

    /**
     * ERRORログのテスト
     *
     *
     */
    function testGfError(){
        $object = GC_Logger::gfGetInstance();

        ob_start();
        $object->gfError('ERRORログ');
        $buff = ob_get_contents();
        ob_end_clean();

        $output = "[ERROR] ERRORログ\n";

        $this->assertEquals($buff, $output);
    }

    /**
     * ERRORログ、ダンプのテスト
     *
     *
     */
    function testGfErrorDump(){
        $object = GC_Logger::gfGetInstance();
        $a = array('test');
        ob_start();
        $object->gfError('a => ', $a);
        $buff = ob_get_contents();
        ob_end_clean();

        $output = 
"[ERROR] a => Array
(
    [0] => test
)

";
        $this->assertEquals($buff, $output);
    }

    /**
     * FATALログのテスト
     *
     *
     */
    function testGfFatal(){
        $object = GC_Logger::gfGetInstance();

        ob_start();
        $object->gfFatal('FATALログ');
        $buff = ob_get_contents();
        ob_end_clean();

        $output = "[FATAL] FATALログ\n";

        $this->assertEquals($buff, $output);
    }

    /**
     * FATALログ、ダンプのテスト
     *
     *
     */
    function testGfFatalDump(){
        $object = GC_Logger::gfGetInstance();
        $a = array('test');
        ob_start();
        $object->gfFatal('a => ', $a);
        $buff = ob_get_contents();
        ob_end_clean();

        $output = 
"[FATAL] a => Array
(
    [0] => test
)

";
        $this->assertEquals($buff, $output);
    }
}
?>
