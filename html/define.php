<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
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

/** HTMLディレクトリからのDATAディレクトリの相対パス */
define('HTML2DATA_DIR', '../data/');

/** data/module 以下の PEAR ライブラリを優先的に使用する */
set_include_path(realpath(dirname(__FILE__) . '/' . HTML2DATA_DIR . 'module') . PATH_SEPARATOR . get_include_path());

/**
 * DIR_INDEX_FILE にアクセスするときにファイル名を使用するか
 *
 * true: 使用する, false: 使用しない, null: 自動(IIS は true、それ以外は false)
 * ※ IIS は、POST 時にファイル名を使用しないと不具合が発生する。(http://support.microsoft.com/kb/247536/ja)
 */
define('USE_FILENAME_DIR_INDEX', null);

// bufferを初期化する
while (ob_get_level() > 0 && ob_get_level() > 0) {
    ob_end_clean();
}
