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
 * SC_Utils::sfEncodeFile()のテストクラス.
 * TODO $out_dirで最後のスラッシュまで必ず指定しなければいけないのが少し気になる
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfEncodeFileTest extends Common_TestCase
{


  protected function setUp()
  {
    // parent::setUp();
  }

  protected function tearDown()
  {
    // parent::tearDown();
  }

  /////////////////////////////////////////
  public function testSfEncodeFile_ファイルが正常に開けた場合_ファイルがエンコードされ出力先のパスが取得できる()
  {
    $outdir = realpath(dirname(__FILE__)) . "/../../../tmp/enc_output/";
    SC_Helper_FileManager::deleteFile($outdir);
    mkdir($outdir, 0777, TRUE);
    $indir = realpath(dirname(__FILE__)) . "/../../../tmp/enc_input/";
    SC_Helper_FileManager::deleteFile($indir);
    mkdir($indir, 0777, TRUE);

    $filepath = $indir . 'test.txt';
    $fp_out = fopen($filepath, 'w');
    fwrite($fp_out, 'こんにちは');
    fclose($fp_out);

    $this->expected = array(
      'filename' => $outdir . 'enc_test.txt',
      'content' => 'こんにちは'
    );

    $this->actual['filename'] = SC_Utils::sfEncodeFile($filepath, 'euc-jp', $outdir);

    $fp_in = fopen($outdir . 'enc_test.txt', 'r');
    $this->actual['content'] = mb_convert_encoding(fread($fp_in, 100), 'utf8', 'euc-jp');
    fclose($fp_in);

    $this->verify();
  }

  //TODO ファイルが開けなかった場合はexitするためテスト不可

  //////////////////////////////////////////
}

