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

// エラー画面を表示させるためのエラーハンドラ
set_error_handler('handle_error');

/**
 * エラーを捕捉するための関数.
 *
 * PHP4 では, try/catch が使用できず, かつ set_error_handler で Fatal Error は
 * 捕捉できないため, ob_start にこの関数を定義し, Fatal Error が発生した場合
 * に出力される HTML 出力を捕捉する.
 * この関数が実行され, エラーが捕捉されると, エラーページへリダイレクトする.
 *
 * @param string $buffer 出力バッファリングの内容
 * @return string|void エラーが捕捉された場合は, エラーページへリダイレクトする;
 *                     エラーが捕捉されない場合は, 出力バッファリングの内容を返す
 */
function &_fatal_error_handler(&$buffer) {
    if (preg_match('/<b>(Fatal) error<\/b>: +(.+) in <b>(.+)<\/b> on line <b>(\d+)<\/b><br \/>/i', $buffer, $matches)) {

        $admin = "";
        if (defined('ADMIN_FUNCTION') && ADMIN_FUNCTION) {
            $admin = "?admin";
        }
        header("Location: " . SITE_URL . "error.php" . $admin);
        exit;
    }
    return $buffer;
}

/**
 * エラー画面を表示させるための関数.
 *
 * この関数は, set_error_handler() 関数に登録するための関数である.
 * trigger_error にて E_USER_ERROR が生成されると, ob_end_clean() 関数によって
 * 出力バッファリングが無効にされ, エラーログを出力した後, エラーページへ
 * リダイレクトする.
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
        ob_end_clean();
        error_log("FATAL Error($errno) $errfile:$errline $errstr");

        $admin = "";
        if (defined('ADMIN_FUNCTION') && ADMIN_FUNCTION) {
            $admin = "?admin";
        }
        header("Location: " . SITE_URL . "error.php" . $admin);
        exit(1);
        break;

    case E_USER_WARNING:
    case E_USER_NOTICE:
    default:
    }
    return true;
}
?>
