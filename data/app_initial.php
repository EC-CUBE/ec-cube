<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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

if (!defined('CLASS_REALDIR')) {
    /** クラスパス */
    define('CLASS_REALDIR', DATA_REALDIR . "class/");
}

if (!defined('CLASS_EX_REALDIR')) {
    /** クラスパス */
    define('CLASS_EX_REALDIR', DATA_REALDIR . "class_extends/");
}

if (!defined('CACHE_REALDIR')) {
    /** キャッシュ生成ディレクトリ */
    define('CACHE_REALDIR', DATA_REALDIR . "cache/");
}

// クラスのオートローディングを定義する
setClassAutoloader();

SC_Helper_HandleError_Ex::load();

// アプリケーション初期化処理
$objInit = new SC_Initial_Ex();
$objInit->init();

/**
 * クラスのオートローディングを定義する
 *
 * LC_* には対応していない。
 */
function setClassAutoloader() {
    function __autoload($class) {
        $arrClassNamePart = explode('_', $class);
        $is_ex = end($arrClassNamePart) === 'Ex';
        $count = count($arrClassNamePart);
        $classpath = $is_ex ? CLASS_EX_REALDIR : CLASS_REALDIR;

        if (($arrClassNamePart[0] === 'GC' || $arrClassNamePart[0] === 'SC') && $arrClassNamePart[1] === 'Utils') {
            $classpath .= $is_ex ? 'util_extends/' : 'util/';
        }
        elseif ($arrClassNamePart[0] === 'SC' && $is_ex === true && $count >= 4) {
            $arrClassNamePartTemp = $arrClassNamePart;
            // FIXME クラスファイルのディレクトリ命名が変。変な現状に合わせて強引な処理をしてる。
            $arrClassNamePartTemp[1] = $arrClassNamePartTemp[1] . '_extends';
            $classpath .= strtolower(implode('/', array_slice($arrClassNamePartTemp, 1, -2))) . '/';
        }
        elseif ($arrClassNamePart[0] === 'SC' && $is_ex === false && $count >= 3) {
            $classpath .= strtolower(implode('/', array_slice($arrClassNamePart, 1, -1))) . '/';
        }
        elseif ($arrClassNamePart[0] === 'SC') {
            // 処理なし
        }
        // PEAR用
        // FIXME トリッキー
        else {
            $classpath = '';
            $class = str_replace('_', '/', $class);
        }

        $classpath .= "$class.php";
        require($classpath);
    }
}
