<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Framework\Batch;

use Eccube\Application;
use Eccube\Framework\Batch\AbstractBatch;
use Eccube\Framework\Util\GcUtils;

/**
 * アップデート機能 のバッチクラス.
 *
 * XXX Singleton にするべき...
 *
 * @package Batch
 * @author LOCKON CO.,LTD.
 */
class BatchUpdate extends AbstractBatch
{
    /**
     * 変換したいファイルの拡張子をカンマ区切りで羅列.
     */
    public $includes = 'php,inc,tpl,css,sql,js,png,jpg,gif,swf,txt,doc,pdf';

    /**
     * 除外するファイル名をカンマ区切りで羅列.
     */
    public $excludes = 'distinfo.php';

    /**
     * バッチ処理を実行する.
     *
     * @param  string $target アップデータファイルのディレクトリパス
     * @return void
     */
    public function execute($target = '.')
    {
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
            $msg = 'バックアップディレクトリの作成に失敗しました';
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
                    $msg = '次のファイルは除外されました: ' . $path;
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
                            $msg = 'ハッシュ値が一致しないため, コピー先が取得できません: ' . $path;
                            $arrLog['err'][] = $msg;
                            $this->printLog($msg);
                            break 2;
                        }

                        if (file_exists($out) && $sha1 == sha1_file($out)) {
                            $msg = '同じ内容のファイルをスキップしました: ' . $out;
                            $this->printLog($msg);
                            continue;
                        }

                        // バックアップを作成
                        if (file_exists($out)) {
                            $bkupTo = $bkupPathFile . pathinfo($out, PATHINFO_BASENAME);
                            $bkupDistInfoArray[sha1_file($out)] = $out;

                            if (!@copy($out, $bkupTo)) {
                                $msg = 'バックアップファイルの作成に失敗しました: ' . $out . ' -> ' . $bkupTo;
                                $arrLog['err'][] = $msg;
                                $this->printLog($msg);
                                break 2;
                            }
                            $msg = 'バックアップファイルの作成に成功しました: ' . $out . ' -> ' . $bkupTo;
                            $this->printLog($msg);
                        }

                        // ファイルを書き出しモードで開く
                        $handle = @fopen($out, 'w');
                        if (!$handle) {
                            // ディレクトリ作成を行ってリトライ
                            $this->lfMkdirRecursive($out);
                            $handle = @fopen($out, 'w');
                            if (!$handle) {
                                $msg = 'コピー先に書き込み権限がありません: ' . $out;
                                $arrLog['err'][] = $msg;
                                $this->printLog($msg);
                                continue;
                            }
                        }

                        // 取得した内容を書き込む
                        if (fwrite($handle, $contents) === false) {
                            $msg = 'コピー先に書き込み権限がありません: ' . $out;
                            $arrLog['err'][] = $msg;
                            $this->printLog($msg);
                            continue;
                        }

                        $msg =  'ファイルのコピーに成功しました: ' . $out;
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
            $msg = 'distinfoファイルの作成に成功しました: ' . $bkupPath . 'distinfo.php';
            $this->printLog($msg);
        } else {
            $msg = 'distinfoファイルの作成に失敗しました: ' . $bkupPath . 'distinfo.php';
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
     * @param string $dir
     * @return array $dir より下層に存在するパス名の配列
     * @see http://www.php.net/glob
     */
    public function listdirs($dir)
    {
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
    public function lfMkdirRecursive($path)
    {
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

    public function makeDistInfo($bkupDistInfoArray)
    {
        $src = "<?php\n"
             . '$distifo = array(' . "\n";

        foreach ($bkupDistInfoArray as $key => $value) {
            $src .= "'${key}' => '${value}',\n";
        }
        $src .= ");\n?>";

        return $src;
    }

    /**
     * @param string $msg
     */
    public function printLog($msg)
    {
        GcUtils::gfPrintLog($msg, DATA_REALDIR . 'logs/ownersstore_batch_update.log');
    }
}
