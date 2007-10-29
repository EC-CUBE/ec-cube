<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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

// {{{ requires
require_once(CLASS_PATH . "batch/SC_Batch.php");

/**
 * アップデート機能 のバッチクラス.
 *
 * XXX Singleton にするべき...
 *
 * @package Batch
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class SC_Batch_Update extends SC_Batch {

    /**
     * 変換したいファイルの拡張子をカンマ区切りで羅列.
     */
    var $includes = "php,inc,tpl,css,sql,js";

    /**
     * 除外するファイル名をカンマ区切りで羅列.
     */
    var $excludes = "distinfo.php,update.php";

    /**
     * バッチ処理を実行する.
     *
     * @param string $target アップデータファイルのディレクトリパス
     * @return void
     */
    function execute($target = ".") {
        $arrLog = array();
        $includeArray = explode(',', $this->includes);
        $excludeArray = explode(',', $this->excludes);
        $fileArrays = $this->listdirs($target);

        foreach ($fileArrays as $path) {
            if (is_file($path)) {

                // ファイル名を取得
                $fileName = pathinfo($path, PATHINFO_BASENAME);

                // 拡張子を取得
                $suffix = pathinfo($path, PATHINFO_EXTENSION);

                // distinfo の変数定義
                $distinfo = isset($distinfo) ? $distinfo : "";

                // distinfo.php を読み込む
                if ($fileName == "distinfo.php") {
                    include_once($path);
                }

                // 除外ファイルをスキップ
                if (in_array($fileName, $excludeArray)) {
                    $arrLog[] = "excludes by " . $path . "\n";
                    continue;
                }

                // sha1 を取得
                $sha1 = sha1_file($path);

                $arrLog[] = $sha1 . " => " . $path . "\n";


                // 変換対象を順に処理
                foreach ($includeArray as $include) {
                    if ($suffix == $include) {

                        // ファイル内容を取得
                        $contents = file_get_contents($path);

                        // 書き出し先を取得
                        if (!empty($distinfo[$sha1])) {
                            $out = $distinfo[$sha1];
                        } else {
                            $arrLog[] = "ハッシュ値が一致しないため, コピー先が取得できません.";
                            die();
                        }

                        // ファイルを書き出しモードで開く
                        $handle = @fopen($out, "w");
                        if (!$handle) {
                            // ディレクトリ作成を行ってリトライ
                            $this->mkdir_p($out);
                            $handle = @fopen($out, "w");
                            if (!$handle) {
                                $arrLog[] = "Cannot open file (". $out . ")\n";
                                continue;
                            }
                        }

                        // 取得した内容を書き込む
                        if (fwrite($handle, $contents) === false) {
                            $arrLog[] = "Cannot write to file (" . $out . ")\n";
                            continue;
                        }

                        $arrLog[] =  "copyed " . $out . "\n";
                        // ファイルを閉じる
                        fclose($handle);
                    }
                }
            }
        }
        $arrLog[] = "Finished Successful!\n";
        return $arrLog;
    }

    /**
     * $dir を再帰的に辿ってパス名を配列で返す.
     *
     * @param string 任意のパス名
     * @return array $dir より下層に存在するパス名の配列
     * @see http://www.php.net/glob
     */
    function listdirs($dir) {
        static $alldirs = array();
        $dirs = glob($dir . '/*');
        if (count($dirs) > 0) {
            foreach ($dirs as $d) $alldirs[] = $d;
        }
        foreach ($dirs as $dir) $this->listdirs($dir);
        return $alldirs;
    }

    /**
     * mkdir -p
     *
     * @param string $path 絶対パス
     */
    function mkdir_p($path){
        $path = dirname($path);
        $path = str_replace ('\\', '/', $path);

        $arrDirs = explode("/", $path);
        $dir = '';

        foreach($arrDirs as $n){
            $dir .= $n . '/';
            if(!file_exists($dir)) {
                if (!@mkdir($dir)) return;
            }
        }
}
}
?>
