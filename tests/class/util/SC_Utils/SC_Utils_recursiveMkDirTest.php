<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/Common_TestCase.php");
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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

/**
 * SC_Utils::recursiveMkDir()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_recursiveMkdirTest extends Common_TestCase
{

    static $TMP_DIR;

    protected function setUp()
    {
        self::$TMP_DIR = realpath(dirname(__FILE__)) . "/../../../tmp";
        SC_Helper_FileManager::deleteFile(self::$TMP_DIR);
        mkdir(self::$TMP_DIR, 0777, true);
        // parent::setUp();
    }

    protected function tearDown()
    {
        // parent::tearDown();
    }

    /////////////////////////////////////////
    public function testRecursiveMkdir_パーミッションを指定した場合_指定のパーミッションでディレクトリが作られる()
    {
        $path = realpath(dirname(__FILE__)) . "/../../../tmp/dir1/dir2/dir3/";
        $mode = 0755;

        $result = SC_Utils::recursiveMkdir($path, $mode);
        if (DIRECTORY_SEPARATOR == '\\') {
            // Windows環境ではパーミッションを指定したディレクトリ作成が出来ない
            $this->expected = true;
            $this->actual = file_exists($path);
            $this->verify('作成したディレクトリがあるかどうか');
        } else {
            $this->expected = '0755';
            $this->actual = substr(sprintf('%o', fileperms($path)), -4);
            $this->verify('作成したディレクトリのパーミッション');
        }

    }

    public function testRecursiveMkdir_パーミッションを指定しない場合_0777でディレクトリが作られる()
    {
        $path = realpath(dirname(__FILE__)) . "/../../../tmp/dir1/dir2/dir3/";

        $result = SC_Utils::recursiveMkdir($path);
        if (DIRECTORY_SEPARATOR == '\\') {
            // Windows環境ではパーミッションを指定したディレクトリ作成が出来ない
            $this->expected = true;
            $this->actual = file_exists($path);
            $this->verify('作成したディレクトリがあるかどうか');
        } else {
            $this->expected = '0777';
            $this->actual = substr(sprintf('%o', fileperms($path)), -4);
            $this->verify('作成したディレクトリのパーミッション');
        }

    }

    //////////////////////////////////////////
}

