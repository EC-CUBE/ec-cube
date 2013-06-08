<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/Common_TestCase.php");
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
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
 * SC_Utils::copyDirectory()のテストクラス.
 * TODO : 最後にスラッシュがないとうまくいかないのは良いのか？
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_copyDirectoryTest extends Common_TestCase
{

  static $TMP_DIR;

  protected function setUp()
  {
    // parent::setUp();
    self::$TMP_DIR = realpath(dirname(__FILE__)) . "/../../../tmp";
    SC_Helper_FileManager::deleteFile(self::$TMP_DIR);
    mkdir(self::$TMP_DIR, 0700, true);
  }

  protected function tearDown()
  {
    // parent::tearDown();
  }

  /////////////////////////////////////////
  public function testCopyDirectory_存在するパスの場合_指定したパス以下が再帰的にコピーされる()
  {
    /**
     * tests/tmp/src
     *             /dir10
     *             /dir20/dir21
     *                   /file22.txt
     */
    mkdir(self::$TMP_DIR . "/src", 0700, true);
    mkdir(self::$TMP_DIR . "/src/dir10", 0700, true);
    mkdir(self::$TMP_DIR . "/src/dir20", 0700, true);
    mkdir(self::$TMP_DIR . "/src/dir20/dir21", 0700, true);
    $fp = fopen(self::$TMP_DIR . "/src/dir20/file22.txt", "w");
    fwrite($fp, "ec-cube test");
    fclose($fp);
    mkdir(self::$TMP_DIR . "/dst");

    SC_Utils::copyDirectory(self::$TMP_DIR . "/src/", self::$TMP_DIR . "/dst/");

    $this->expected = array("dir10", "dir20", "dir21", "file22.txt");
    $this->actual = array();
    Test_Utils::array_append($this->actual, Test_Utils::mapCols(SC_Helper_FileManager::sfGetFileList(self::$TMP_DIR . "/dst"), "file_name"));
    Test_Utils::array_append($this->actual, Test_Utils::mapCols(SC_Helper_FileManager::sfGetFileList(self::$TMP_DIR . "/dst/dir20"), "file_name"));
    
    $this->verify('コピーされたファイル一覧');
  }

  public function testCopyDirectory_存在しないパスの場合_何も起こらない()
  {
    /**
     * tests/tmp/src
     *             /dir10
     *             /dir20/dir21
     *                   /file22.txt
     */
    // mkdir(self::$TMP_DIR . "/src", 0700, true);
    mkdir(self::$TMP_DIR . "/dst");

    SC_Utils::copyDirectory(self::$TMP_DIR . "/src/", self::$TMP_DIR . "/dst/");

    $this->expected = array();
    $this->actual = array();
    
    $this->verify('コピーされたファイル一覧');
  }

  public function testCopyDirectory_コピー先のディレクトリが元々存在する場合_上書きされる()
  {
    /**
     * tests/tmp/src
     *             /dir10
     *             /dir20/dir21
     *                   /file22.txt
     */
    mkdir(self::$TMP_DIR . "/src", 0700, true);
    mkdir(self::$TMP_DIR . "/src/dir10", 0700, true);
    mkdir(self::$TMP_DIR . "/src/dir20", 0700, true);
    mkdir(self::$TMP_DIR . "/src/dir20/dir21", 0700, true);
    $fp = fopen(self::$TMP_DIR . "/src/dir20/file22.txt", "w");
    fwrite($fp, "ec-cube test");
    fclose($fp);
    mkdir(self::$TMP_DIR . "/dst");
    mkdir(self::$TMP_DIR . "/dst/dir20/dir23", 0700, true);

    SC_Utils::copyDirectory(self::$TMP_DIR . "/src/", self::$TMP_DIR . "/dst/");

    $this->expected = array("dir10", "dir20", "dir21", "dir23", "file22.txt");
    $this->actual = array();
    Test_Utils::array_append($this->actual, Test_Utils::mapCols(SC_Helper_FileManager::sfGetFileList(self::$TMP_DIR . "/dst"), "file_name"));
    Test_Utils::array_append($this->actual, Test_Utils::mapCols(SC_Helper_FileManager::sfGetFileList(self::$TMP_DIR . "/dst/dir20"), "file_name"));
    
    $this->verify('コピー先のファイル一覧');
  }

  public function testCopyDirectory_コピー先のファイルが元々存在する場合_上書きされる()
  {
    /**
     * tests/tmp/src
     *             /dir10
     *             /dir20/dir21
     *                   /file22.txt
     */
    mkdir(self::$TMP_DIR . "/src", 0700, true);
    mkdir(self::$TMP_DIR . "/src/dir10", 0700, true);
    mkdir(self::$TMP_DIR . "/src/dir20", 0700, true);
    mkdir(self::$TMP_DIR . "/src/dir20/dir21", 0700, true);
    $fp = fopen(self::$TMP_DIR . "/src/dir20/file22.txt", "w");
    fwrite($fp, "ec-cube test");
    fclose($fp);
    mkdir(self::$TMP_DIR . "/dst");
    mkdir(self::$TMP_DIR . "/dst/dir20");
    $fp_dist = fopen(self::$TMP_DIR . "/dst/dir20/file22.txt", "w");
    fwrite($fp_dist, "hello");
    fclose($fp_dist);

    SC_Utils::copyDirectory(self::$TMP_DIR . "/src/", self::$TMP_DIR . "/dst/");

    $this->expected = array("dir10", "dir20", "dir21", "file22.txt", "ec-cube test");
    $this->actual = array();
    Test_Utils::array_append($this->actual, Test_Utils::mapCols(SC_Helper_FileManager::sfGetFileList(self::$TMP_DIR . "/dst"), "file_name"));
    Test_Utils::array_append($this->actual, Test_Utils::mapCols(SC_Helper_FileManager::sfGetFileList(self::$TMP_DIR . "/dst/dir20"), "file_name"));
    $fp_final = fopen(self::$TMP_DIR . "/dst/dir20/file22.txt", "r");
    $read_result = fread($fp_final, 100);
    fclose($fp_final);
    $this->actual[] = $read_result;

    $this->verify('コピー先のファイル一覧');
  }

  //////////////////////////////////////////
}

