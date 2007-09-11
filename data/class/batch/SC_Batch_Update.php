<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

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
        $includeArray = explode(',', $this->includes);
        $excludeArray = explode(',', $this->excludes);
        $fileArrays = listdirs($target);

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
                    @include_once($fileName);
                }

                // 除外ファイルをスキップ
                if (in_array($fileName, $excludeArray)) {
                    echo "excludes by " . $path . "\n";
                    continue;
                }

                // sha1 を取得
                $sha1 = sha1_file($path);

                echo $sha1 . " => " . $path . "\n";


                // 変換対象を順に処理
                foreach ($includeArray as $include) {
                    if ($suffix == $include) {

                        // ファイル内容を取得
                        $contents = file_get_contents($path);

                        // 書き出し先を取得
                        if (!empty($distinfo[$sha1])) {
                            $out = $distinfo[$sha1];
                        } else {
                            die("ハッシュ値が一致しないため, コピー先が取得できません.");
                        }

                        // ファイルを書き出しモードで開く
                        $handle = fopen($out, "w");
                        if (!$handle) {
                            echo "Cannot open file (". $out . ")";
                            continue;
                        }

                        // 取得した内容を書き込む
                        if (fwrite($handle, $contents) === false) {
                            echo "Cannot write to file (" . $out . ")";
                            continue;
                        }

                        echo "copyed " . $out . "\n";
                        // ファイルを閉じる
                        fclose($handle);
                    }
                }
            }
        }
        echo "Finished Successful!\n";
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
}
?>
