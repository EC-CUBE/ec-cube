<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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
    var $includes = 'php,inc,tpl,css,sql,js,png,jpg,gif,swf,txt,doc,pdf';

    /**
     * 除外するファイル名をカンマ区切りで羅列.
     */
    var $excludes = 'distinfo.php';

    /**
     * バッチ処理を実行する.
     *
     * @param string $target アップデータファイルのディレクトリパス
     * @return void
     */
    function execute($target = '.') {
        $msg = '';
        $oldMask = umask(0);
        $bkupDistInfoArray = array(); //バックアップファイル用のdistinfoファイル内容
        $bkupPath = DATA_REALDIR . 'downloads/backup/update_' . time() . '/';
        $bkupPathFile = $bkupPath . 'files/';
        $this->lfMkdirRecursive($bkupPathFile . 'dummy');

        $arrLog = array(
            'err' =>  array(),
            'ok'  => array(),
            'buckup_path' => $bkupPath
        );

        if (!is_writable($bkupPath) || !is_writable($bkupPathFile)) {
            $msg = t('c_Creation of backup directory failed_01');
            $arrLog['err'][] = $msg;
            $this->printLog($msg);
            return $arrLog;
        }

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
                $distinfo = isset($distinfo) ? $distinfo : '';

                // distinfo.php を読み込む
                if ($fileName == 'distinfo.php') {
                    include_once $path;
                }

                // 除外ファイルをスキップ
                if (in_array($fileName, $excludeArray)) {
                    //$arrLog['ok'][] = '次のファイルは除外されました: ' . $path;
                    $msg = t('c_The following file was excluded: _01') . $path;
                    $this->printLog($msg);
                    continue;
                }

                // sha1 を取得
                $sha1 = sha1_file($path);

                //$arrLog[] = $sha1 . ' => ' . $path;

                // 変換対象を順に処理
                foreach ($includeArray as $include) {
                    if ($suffix == $include) {

                        // ファイル内容を取得
                        $contents = file_get_contents($path);

                        // 書き出し先を取得
                        if (!empty($distinfo[$sha1])) {
                            $out = $distinfo[$sha1];
                        } else {
                            $msg = t('c_The hash value does not match and the copy destination cannot be retrieved: _01') . $path;
                            $arrLog['err'][] = $msg;
                            $this->printLog($msg);
                            break 2;
                        }

                        if (file_exists($out) && $sha1 == sha1_file($out)) {
                            $msg = t('c_Files with the same contents were skipped: _01') . $out;
                            $this->printLog($msg);
                            continue;
                        }

                        // バックアップを作成
                        if (file_exists($out)) {
                            $bkupTo = $bkupPathFile . pathinfo($out, PATHINFO_BASENAME);
                            $bkupDistInfoArray[sha1_file($out)] = $out;

                            if (!@copy($out, $bkupTo)) {
                                $msg = t('c_Creation of backup file failed: _01') . $out . ' -> ' . $bkupTo;
                                $arrLog['err'][] = $msg;
                                $this->printLog($msg);
                                break 2;
                            }
                            $msg = t('c_Creation of backup file failed: _01') . $out . ' -> ' . $bkupTo;
                            $this->printLog($msg);
                        }

                        // ファイルを書き出しモードで開く
                        $handle = @fopen($out, 'w');
                        if (!$handle) {
                            // ディレクトリ作成を行ってリトライ
                            $this->lfMkdirRecursive($out);
                            $handle = @fopen($out, 'w');
                            if (!$handle) {
                                $msg = t('c_The copying destination does not have write access: _01') . $out;
                                $arrLog['err'][] = $msg;
                                $this->printLog($msg);
                                continue;
                            }
                        }

                        // 取得した内容を書き込む
                        if (fwrite($handle, $contents) === false) {
                            $msg = t('c_The copying destination does not have write access: _01') . $out;
                            $arrLog['err'][] = $msg;
                            $this->printLog($msg);
                            continue;
                        }

                        $msg = t('c_File copying was successful: _01') . $out;
                        $arrLog['ok'][] = $msg;
                        $this->printLog($msg);
                        // ファイルを閉じる
                        fclose($handle);
                    }
                }
            }
        }
        $src = $this->makeDistInfo($bkupDistInfoArray);
        if (is_writable($bkupPath)) {
            $handle = @fopen($bkupPath . 'distinfo.php', 'w');
            @fwrite($handle, $src);
            @fclose($handle);
            $msg = t('c_distinfo file creation was successful: _01') . $bkupPath . 'distinfo.php';
            $this->printLog($msg);
        } else {
            $msg = t('c_distinfo file creation failed: _01') . $bkupPath . 'distinfo.php';
            $arrLog['err'][] = $msg;
            $this->printLog($msg);
        }
        umask($oldMask);
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
        if (is_array($dirs) && count($dirs) > 0) {
            foreach ($dirs as $d) $alldirs[] = $d;
        }
        if (is_array($dirs)) {
            foreach ($dirs as $dir) $this->listdirs($dir);
        }
        return $alldirs;
    }

    /**
     * mkdir -p
     *
     * @param string $path 絶対パス
     */
    function lfMkdirRecursive($path) {
        $path = dirname($path);

        // HTML_REALDIR/DATA_REALDIRの判別
        if (preg_match("@\Q".HTML_REALDIR."\E@", $path) > 0) {
            $dir = str_replace('\\', '/', HTML_REALDIR);
            $path = preg_replace("@\Q".HTML_REALDIR."\E@", '', $path);
        } elseif (preg_match("@\Q".DATA_REALDIR."\E@", $path) > 0) {
            $dir = str_replace('\\', '/', DATA_REALDIR);
            $path = preg_replace("@\Q".DATA_REALDIR."\E@", '', $path);
        } else {
            $dir = '';
        }
        $arrDirs = explode('/', str_replace('\\', '/', $path));

        foreach ($arrDirs as $n) {
            $dir .= $n . '/';
            if (!file_exists($dir)) {
                if (!@mkdir($dir)) break;
            }
        }
    }

    function makeDistInfo($bkupDistInfoArray) {
        $src = "<?php\n"
             . '$distifo = array(' . "\n";

        foreach ($bkupDistInfoArray as $key => $value) {
            $src .= "'${key}' => '${value}',\n";
        }
        $src .= ");\n?>";

        return $src;
    }

    function printLog($msg) {
        GC_Utils_Ex::gfPrintLog($msg, DATA_REALDIR . 'logs/ownersstore_batch_update.log');
    }
}
