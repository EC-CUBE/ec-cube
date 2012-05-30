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
 * オーナーズストア管理用ログクラス
 *
 */
class LC_Upgrade_Helper_Log {
    /**
     * 開始メッセージを出力
     *
     * @param string $mode
     */
    function start($mode) {
        $message = "##### $mode start #####";
        $this->log($message);
    }
    /**
     * 終了メッセージを出力
     *
     * @param string $mode
     */
    function end() {
        $message = '##### end #####';
        $this->log($message);
    }
    /**
     * メッセージを出力
     *
     * @param string $message
     */
    function log($message) {
        GC_Utils_Ex::gfPrintLog($message, OSTORE_LOG_REALFILE);
    }
    /**
     * エラーメッセージを出力
     *
     * @param string $code
     * @param mixed $val
     */
    function error($code, $val = null) {
        $format = '* error! code:%s / debug:%s';
        $message = sprintf($format, $code, serialize($val));
        $this->log($message);
        $this->end();
    }
}
