<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2009 LOCKON CO.,LTD. All Rights Reserved.
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
 * logインタフェース
 *
 * サブクラスでロギングを実装する
 *
 * @package Logger
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */

class GC_Log_Interface {
    /**
     * DEBUGログ
     *
     * @param string 出力したいログ
     * @return void
     *
     */
    function gfDebug($log) {
        die('gfDebugメソッドを実装してください。');
    }

    /**
     * INFOログ
     *
     * @param string 出力したいログ
     * @return void
     *
     */
    function gfInfo($log) {
        die('gfInfoを実装してください。');
    }

    /**
     * WARNログ
     *
     * @param string 出力したいログ
     * @return void
     *
     */
    function gfWarn($log) {
        die('gfWarnを実装してください。');
    }

    /**
     * ERRORログ
     *
     * @param string 出力したいログ
     * @return void
     *
     */
    function gfError($log) {
        die('gfErrorを実装してください。');
    }

    /**
     * FATALログ
     *
     * @param string 出力したいログ
     * @return void
     *
     */
    function gfFatal($log) {
        die('gfFatalを実装してください。');
    }
}
?>