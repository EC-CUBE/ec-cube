<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/**
 * マスタデータを扱うクラス.
 *
 * @package DB
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class SC_DB_MasterData {

    // }}}
    // {{{ functions

    /**
     * マスタデータを取得する.
     *
     * 以下の順序でマスタデータを取得する.
     * 1. data/conf/cache のマスタデータキャッシュを include_once() で読み込む
     * 2. 1 で読み込んだ値をチェックし, 値が変数定義されていれば値を返す.
     *    されていなければ, 次の処理を行う.
     * 3. 値が未定義の場合は, DBからマスタデータを取得する.
     * 4. 取得した後, マスタデータのキャッシュを生成し, 値を返す.
     *
     * @param string $name マスタデータ名
     * @return array マスタデータ
     */
    function getMasterData($name) {
        // TODO
    }

    /**
     * マスタデータをDBに追加する.
     *
     * 引数 $value をマスタデータとしてDBに追加し,
     * キャッシュを生成する.
     * 既存のキャッシュが存在する場合は上書きする.
     * $value は key => value 形式の配列である必要がある.
     *
     * @param string $name マスタデータ名
     * @param array $value マスタデータ
     * @return integer マスタデータの登録数
     */
    function registMasterData($name, $value) {
        // TODO
    }

    /**
     * マスタデータを更新する.
     *
     * 引数 $value の値でマスタデータを更新し,
     * キャッシュを更新する.
     * $value は key => value 形式の配列である必要がある.
     *
     * @param string $name マスタデータ名
     * @param array $value マスタデータ
     * @return integer マスタデータの更新数
     */
    function updateMasterData($name, $value) {
        // TODO
    }

    /**
     * マスタデータを削除する.
     *
     * 引数 $name のマスタデータを削除し,
     * キャッシュも削除する.
     *
     * @param string $name マスタデータ名
     * @return integer マスタデータの削除数
     */
    function deleteMasterData($name) {
        // TODO
    }

    /**
     * マスタデータのキャッシュを消去する.
     *
     * @param string $name マスタデータ名
     * @return void
     */
    function clearCache($name) {
        // TODO
    }

    /**
     * マスタデータのキャッシュを生成する.
     *
     * 引数 $name のマスタデータキャッシュを生成する.
     * 既存のキャッシュが存在する場合は上書きする.
     *
     * @param string $name マスタデータ名
     * @return void
     */
    function createCache($name) {
        // TODO
    }

    // }}}
    // {{{ private functions

    /**
     * DBからマスタデータを取得する.
     *
     * キャッシュの有無に関係なく, DBからマスタデータを検索し, 取得する.
     *
     * @param string $name マスタデータ名
     * @return array マスタデータ
     */
    function getDbMasterData($name) {
        // TODO
    }
}
?>
