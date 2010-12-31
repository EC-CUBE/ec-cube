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
// エラー捕捉用の出力バッファリング
ob_start('_fatal_error_handler');

// E_USER_ERROR を捕捉した場合にエラー画面を表示させるためのエラーハンドラ
set_error_handler('handle_error');

/**
 * エラーを捕捉するための関数.
 *
 * PHP4 では, try/catch が使用できず, かつ set_error_handler で Fatal Error は
 * 捕捉できないため, ob_start にこの関数を定義し, Fatal Error が発生した場合
 * に出力される HTML 出力を捕捉する.
 * この関数が実行され, エラーが捕捉されると, DEBUG_MODE が無効な場合,
 * エラーページへリダイレクトする.
 *
 * @param string $buffer 出力バッファリングの内容
 * @return string|void エラーが捕捉された場合は, エラーページへリダイレクトする;
 *                     エラーが捕捉されない場合は, 出力バッファリングの内容を返す
 */
function &_fatal_error_handler(&$buffer) {
    if (preg_match('/<b>(Fatal) error<\/b>: +(.+) in <b>(.+)<\/b> on line <b>(\d+)<\/b><br \/>/i', $buffer, $matches)) {
        error_log("FATAL Error: $matches[3]:$matches[4] $matches[2]\n", 3,
                  realpath(dirname(__FILE__) . "/" . HTML2DATA_DIR . "logs/site.log"));
        if (DEBUG_MODE !== true) {
            $url = HTTP_URL . "error.php";
            if (defined('ADMIN_FUNCTION') && ADMIN_FUNCTION) {
                $url .= "?admin";
            }
            header("Location: $url");
            exit;
        }
    }
    return $buffer;
}

/**
 * E_USER_ERROR を捕捉した場合にエラー画面を表示させるエラーハンドラ関数.
 *
 * この関数は, set_error_handler() 関数に登録するための関数である.
 * trigger_error にて E_USER_ERROR が生成されると, エラーログを出力した後,
 * エラー画面を表示させる.
 *
 * E_USER_ERROR 以外のエラーが生成された場合, この関数は true を返す.
 *
 * @param integer $errno エラーコード
 * @param string $errstr エラーメッセージ
 * @param string $errfile エラーが発生したファイル名
 * @param integer $errline エラーが発生した行番号
 * @return void|boolean E_USER_ERROR が発生した場合は, エラーページへリダイレクト;
 *                      E_USER_ERROR 以外の場合は true
 */
function handle_error($errno, $errstr, $errfile, $errline) {
    switch ($errno) {
    case E_USER_ERROR:
        error_log("FATAL Error($errno) $errfile:$errline $errstr", 3, realpath(dirname(__FILE__) . "/" . HTML2DATA_DIR . "logs/site.log"));

        displaySystemError($errstr);
        exit(1);
        break;

    case E_USER_WARNING:
    case E_USER_NOTICE:
    default:
    }
    return true;
}

/**
 * エラー画面を表示する
 *
 * @param string|null $errstr エラーメッセージ
 * @return void
 */
function displaySystemError($errstr = null) {
    if (SC_Utils_Ex::sfIsMobileSite()) {
        ob_clean();
    } else {
        // 最下層以外の出力用バッファをクリアし、出力のバッファリングを解除する
        // FIXME #811(出力バッファリングの利用を見直し)
        while (ob_get_level() >= 2) {
            ob_end_clean();
        }

        // 最下層の出力バッファをクリアする
        ob_clean();
    }

    require_once CLASS_EX_PATH . 'page_extends/error/LC_Page_Error_SystemError_Ex.php';
    $objPage = new LC_Page_Error_SystemError_Ex();
    register_shutdown_function(array($objPage, 'destroy'));
    $objPage->init();
    if (isset($errstr)) {
        $objPage->arrDebugMsg[]
            = "▼▼▼ エラーメッセージ ▼▼▼\n"
            . $errstr
            . "▲▲▲ エラーメッセージ ▲▲▲\n"
        ;
    }
    $objPage->process();
}
?>
