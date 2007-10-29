#!/usr/local/bin/php
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

/**
 * ファイルのエンコーディングを $fromEncoding から $toEncoding へ変換します.
 *
 * @author  Kentaro Ohkouchi<ohkouchi@loop-az.jp>
 * @since   PHP4.3.0(cli)
 * @version $Id:convert.php 15079 2007-07-20 07:20:36Z nanasess $
 */

/**
 * 変換したいファイルの拡張子をカンマ区切りで羅列.
 */
$includes = "php,inc,tpl,css,sql,js";

/**
 * 除外するファイル名をカンマ区切りで羅列.
 */
$excludes = "convert.php";

/**
 * 変換元エンコーディング.
 */
$fromEncoding = "EUC-JP";

/**
 * 変換先エンコーディング.
 */
$toEncoding = "UTF-8";

$includeArray = explode(',', $includes);
$excludeArray = explode(',', $excludes);
$fileArrays = listdirs('.');

foreach ($fileArrays as $path) {
    if (is_file($path)) {

        // ファイル名を取得
        $fileName = pathinfo($path, PATHINFO_BASENAME);

        // 拡張子を取得
        $suffix = pathinfo($path, PATHINFO_EXTENSION);

        // 除外ファイルをスキップ
        if (in_array($fileName, $excludeArray)) {
            echo "excludes by " . $path . "\n";
            continue;
        }

        // 変換対象を順に処理
        foreach ($includeArray as $include) {
            if ($suffix == $include) {

                // ファイル内容を取得し, エンコーディング変換
                $contents = file_get_contents($path);
                $convertedContents = mb_convert_encoding($contents,
                                                         $toEncoding,
                                                         $fromEncoding);

                // 書き込みできるか？
                if (is_writable($path)) {

                    // ファイルを書き出しモードで開く
                    $handle = fopen($path, "w");
                    if (!$handle) {
                        echo "Cannot open file (". $path . ")";
                        continue;
                    }

                    // コード変換した内容を書き込む
                    if (fwrite($handle, $convertedContents) === false) {
                        echo "Cannot write to file (" . $path . ")";
                        continue;
                    }

                    echo "converted " . $path . "\n";
                    // ファイルを閉じる
                    fclose($handle);
                } else {

                    echo "The file " . $filename . "is not writable";
                }
            }
        }
    }
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
    foreach ($dirs as $dir) listdirs($dir);
    return $alldirs;
}
?>
