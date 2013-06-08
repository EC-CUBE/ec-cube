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
 * SC_Utils::sfCopyDir()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfCopyDirTest extends Common_TestCase
{

  static $TMP_DIR;

  protected function setUp()
  {
    // parent::setUp();
    self::$TMP_DIR = realpath(dirname(__FILE__)) . "/../../../tmp";
    SC_Helper_FileManager::deleteFile(self::$TMP_DIR);
    mkdir(self::$TMP_DIR, 0777, true);
  }

  protected function tearDown()
  {
    // parent::tearDown();
  }

  /////////////////////////////////////////
  public function testSfCopyDir_ディレクトリでない場合_falseを返し何もしない()
  {
    mkdir(self::$TMP_DIR . "/src", 0777, true);
    $fp = fopen(self::$TMP_DIR . "/src/test.txt", "w");
    fwrite($fp, "hello");
    fclose($fp);

    $src = self::$TMP_DIR . "/src/test.txt"; // ディレクトリではなくファイルを指定
    $dst = self::$TMP_DIR . "/dst/";

    $this->expected = array(
      'result' => FALSE,
      'file_exists' => FALSE
    );
    $this->actual['result'] = SC_Utils::sfCopyDir($src, $dst);
    $this->actual['file_exists'] = file_exists($dst);

    $this->verify();
  }

  public function testSfCopyDir_コピー先のディレクトリが存在しない場合_新たに作成する()
  {
    mkdir(self::$TMP_DIR . "/src", 0777, true);
    $fp = fopen(self::$TMP_DIR . "/src/test.txt", "w");
    fwrite($fp, "hello");
    fclose($fp);

    $src = self::$TMP_DIR . "/src/";
    $dst = self::$TMP_DIR . "/dst/";

    $this->expected = array(
      'dir_exists' => TRUE,
      'files' => array('test.txt') 
    );
    SC_Utils::sfCopyDir($src, $dst);
    $this->actual['dir_exists'] = is_dir($dst);
    $this->actual['files'] = Test_Utils::mapCols(SC_Helper_FileManager::sfGetFileList($dst), 'file_name');

    $this->verify();
  }

  // TODO CVS以下のEntriesなどはコピーされないが、CVSという親ディレクトリはコピーされてしまう。
  // そもそも、CVSだけ特別扱いする意味がないような…
  public function testSfCopyDir_コピー先のディレクトリが存在する場合_そのままコピーする()
  {
    mkdir(self::$TMP_DIR . "/src", 0777, true);
    mkdir(self::$TMP_DIR . "/dst", 0777, true); // コピー先も作成しておく
    $fp = fopen(self::$TMP_DIR . "/src/test.txt", "w");
    fwrite($fp, "hello");
    fclose($fp);

    // CVS関連のディレクトリ
    mkdir(self::$TMP_DIR . "/src/CVS/Entries", 0777, true);
    mkdir(self::$TMP_DIR . "/src/CVS/Repository", 0777, true);
    mkdir(self::$TMP_DIR . "/src/CVS/Root", 0777, true);

    // 入れ子になったディレクトリ
    mkdir(self::$TMP_DIR . "/src/dir1/dir12/dir123", 0777, true);

    // 上書きされないファイル
    $fp = fopen(self::$TMP_DIR . "/dst/test.txt", "w");
    fwrite($fp, "good morning");
    fclose($fp);

    $src = self::$TMP_DIR . "/src/";
    $dst = self::$TMP_DIR . "/dst/";

    $this->expected = array(
      'dir_exists' => TRUE,
      'files' => array('CVS', 'dir1', 'test.txt'),
      'files_2' => array('dir12'),
      'files_3' => array('dir123'),
      'file_content' => 'good morning'
    );
    SC_Utils::sfCopyDir($src, $dst);
    $this->actual['dir_exists'] = is_dir($dst);
    $this->actual['files'] = Test_Utils::mapCols(SC_Helper_FileManager::sfGetFileList($dst), 'file_name');
    $this->actual['files_2'] = Test_Utils::mapCols(SC_Helper_FileManager::sfGetFileList($dst . "dir1/"), 'file_name');
    $this->actual['files_3'] = Test_Utils::mapCols(SC_Helper_FileManager::sfGetFileList($dst . "dir1/dir12/"), 'file_name');
    $fp = fopen(self::$TMP_DIR . "/dst/test.txt", "r");
    $this->actual['file_content'] = fread($fp, 100);

    $this->verify();
  }

  public function testSfCopyDir_上書きフラグがONの場合_同名ファイルが上書きされる()
  {
    mkdir(self::$TMP_DIR . "/src", 0777, true);
    mkdir(self::$TMP_DIR . "/dst", 0777, true); // コピー先も作成しておく
    $fp = fopen(self::$TMP_DIR . "/src/test.txt", "w");
    fwrite($fp, "hello");
    fclose($fp);

    // 上書きされるファイル
    $fp = fopen(self::$TMP_DIR . "/dst/test.txt", "w");
    fwrite($fp, "good morning");
    fclose($fp);

    $src = self::$TMP_DIR . "/src/";
    $dst = self::$TMP_DIR . "/dst/";

    $this->expected = array(
      'dir_exists' => TRUE,
      'files' => array('test.txt'),
      'file_content' => 'hello'
    );
    SC_Utils::sfCopyDir($src, $dst, '', TRUE);
    $this->actual['dir_exists'] = is_dir($dst);
    $this->actual['files'] = Test_Utils::mapCols(SC_Helper_FileManager::sfGetFileList($dst), 'file_name');
    $fp = fopen(self::$TMP_DIR . "/dst/test.txt", "r");
    $this->actual['file_content'] = fread($fp, 100);

    $this->verify();
  }

  public function testSfCopyDir_上書きフラグがONかつ書き込み権限がない場合_同名ファイルが上書きされない()
  {
    mkdir(self::$TMP_DIR . "/src", 0777, true);
    mkdir(self::$TMP_DIR . "/dst", 0777, true); // コピー先も作成しておく
    $fp = fopen(self::$TMP_DIR . "/src/test.txt", "w");
    fwrite($fp, "hello");
    fclose($fp);

    // 上書きされないファイル
    $test_file = self::$TMP_DIR . "/dst/test.txt";
    $fp = fopen($test_file, "w");
    fwrite($fp, "good morning");
    fclose($fp);
    chmod($test_file, 0444); // いったん読取専用にする

    $src = self::$TMP_DIR . "/src/";
    $dst = self::$TMP_DIR . "/dst/";

    $this->expected = array(
      'dir_exists' => TRUE,
      'files' => array('test.txt'),
      'file_content' => 'good morning'
    );
    SC_Utils::sfCopyDir($src, $dst, '', TRUE);
    $this->actual['dir_exists'] = is_dir($dst);
    $this->actual['files'] = Test_Utils::mapCols(SC_Helper_FileManager::sfGetFileList($dst), 'file_name');
    $fp = fopen($test_file, "r");
    $this->actual['file_content'] = fread($fp, 100);

    chmod($test_file, 0777); // verifyする前にパーミッションを戻す
    $this->verify();
  }

  //////////////////////////////////////////
}

