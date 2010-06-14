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

// {{{ requires
require_once '../../require.php';
require_once DATA_PATH . 'module/Services/JSON.php';

/**
 * モジュール一覧を出力するテストスクリプト
 *
 * 新しいモジュールを管理画面に表示する場合は、下の配列の値を書き換える.
 * 追加する際には、新しく配列を追加すればよい.
 *
 * installed_flgとdownload_flgを1にすると「設定」ボタンが出るので、
 * data/downloads/module/mdl_***にモジュールが設置してあれば、
 * ダウンロードしなくてもすぐに使用可能な状態となります.
 * (codeがモジュールファイル設定先になります)
 *
 * product_idは重複しない値を設定してください.
 * 本番商品との重複を避けるためにも大きめの値を設定しておくとよいかもしれません.
 */
$arrProductsList = array(
    // サンプルモジュール
    array(
        'name' => 'サンプルモジュール',
        'code' => 'mdl_epsilon',
        'main_list_comment' => 'モジュール開発テスト用です。',
        'main_list_image' => 'no_image.jpg',
        'version' => '開発版',
        'last_update_date' => '9999/99/99 00:00:00',
        'product_id' => '100',
        'status' => '使用可能です',
        'installed_flg' => '1',
        'installed_version' => '開発版',
        'download_flg' => '1',
        'version_up_flg' => '0'
    ),
);

switch(getMode()) {

case 'products_list':
    displayProductsList();
    break;

default:
    displayProductsList();
    break;
}

/**
 * モード取得.
 *
 * @return string
 */
function getMode() {
    if (isset($_GET['mode'])) {
        return $_GET['mode'];
    } elseif (isset($_POST['mode'])) {
        return $_POST['mode'];
    }
    return '';
}

/**
 * モジュールリスト一覧をjson出力する
 *
 */
function displayProductsList() {
    global $arrProductsList;
    $arrRet = array(
        'status' => 'SUCCESS',
        'data'   => $arrProductsList
    );

    // FIXME 一覧を取得するたびに更新されるのは微妙かも..
    updateModuleTable($arrProductsList);

    $objJson = new Services_JSON();
    echo $objJson->encode($arrRet);
}

/**
 * dtb_moduleを更新する.
 *
 * @param array $arrProductsList
 */
function updateModuleTable($arrProductsList) {
    $table = 'dtb_module';
    $where = 'module_id = ?';
    $objQuery = new SC_Query;

    foreach ($arrProductsList as $arrProduct) {
        $count = $objQuery->count($table, $where, array($arrProduct['product_id']));
        if ($count) {
            $arrUpdate = array(
                'module_code' => $arrProduct['code'],
                'module_name' => $arrProduct['name'],
                'auto_update_flg' => '0',
                'del_flg' => '0',
                'update_date' => 'NOW()',
            );
            $objQuery->update($table, $arrUpdate, $where, array($arrProduct['product_id']));
        } else {
            $arrInsert = array(
                'module_id'   => $arrProduct['product_id'],
                'module_code' => $arrProduct['code'],
                'module_name' => $arrProduct['name'],
                'auto_update_flg' => '0',
                'del_flg' => '0',
                'update_date' => 'NOW()',
                'create_date' => 'NOW()',
            );
            $objQuery->insert($table, $arrInsert);
        }
    }
}
?>
