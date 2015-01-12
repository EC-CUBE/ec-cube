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

namespace Eccube\Framework\Helper;

use Eccube\Application;
use Eccube\Framework\Query;
use Eccube\Framework\Helper\DbHelper;

/**
 * メーカーを管理するヘルパークラス.
 *
 * @package Helper
 * @author pineray
 */
class MakerHelper
{
    /**
     * メーカーの情報を取得.
     *
     * @param  integer $maker_id    メーカーID
     * @param  boolean $has_deleted 削除されたメーカーも含む場合 true; 初期値 false
     * @return array
     */
    public function getMaker($maker_id, $has_deleted = false)
    {
        $objQuery = Application::alias('eccube.query');
        $where = 'maker_id = ?';
        if (!$has_deleted) {
            $where .= ' AND del_flg = 0';
        }
        $arrRet = $objQuery->getRow('*', 'dtb_maker', $where, array($maker_id));

        return $arrRet;
    }

    /**
     * 名前からメーカーの情報を取得.
     *
     * @param  integer $name        メーカー名
     * @param  boolean $has_deleted 削除されたメーカーも含む場合 true; 初期値 false
     * @return array
     */
    public function getByName($name, $has_deleted = false)
    {
        $objQuery = Application::alias('eccube.query');
        $where = 'name = ?';
        if (!$has_deleted) {
            $where .= ' AND del_flg = 0';
        }
        $arrRet = $objQuery->getRow('*', 'dtb_maker', $where, array($name));

        return $arrRet;
    }

    /**
     * メーカー一覧の取得.
     *
     * @param  boolean $has_deleted 削除されたメーカーも含む場合 true; 初期値 false
     * @return array
     */
    public function getList($has_deleted = false)
    {
        $objQuery = Application::alias('eccube.query');
        $col = 'maker_id, name';
        $where = '';
        if (!$has_deleted) {
            $where .= 'del_flg = 0';
        }
        $table = 'dtb_maker';
        $objQuery->setOrder('rank DESC');
        $arrRet = $objQuery->select($col, $table, $where);

        return $arrRet;
    }

    /**
     * メーカーの登録.
     *
     * @param  array    $sqlval
     * @return multiple 登録成功:メーカーID, 失敗:FALSE
     */
    public function saveMaker($sqlval)
    {
        $objQuery = Application::alias('eccube.query');

        $maker_id = $sqlval['maker_id'];
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        // 新規登録
        if ($maker_id == '') {
            // INSERTの実行
            $sqlval['rank'] = $objQuery->max('rank', 'dtb_maker') + 1;
            $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
            $sqlval['maker_id'] = $objQuery->nextVal('dtb_maker_maker_id');
            $ret = $objQuery->insert('dtb_maker', $sqlval);
        // 既存編集
        } else {
            unset($sqlval['creator_id']);
            unset($sqlval['create_date']);
            $where = 'maker_id = ?';
            $ret = $objQuery->update('dtb_maker', $sqlval, $where, array($maker_id));
        }

        return ($ret) ? $sqlval['maker_id'] : FALSE;
    }

    /**
     * メーカーの削除.
     *
     * @param  integer $maker_id メーカーID
     * @return void
     */
    public function delete($maker_id)
    {
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        // ランク付きレコードの削除
        $objDb->deleteRankRecord('dtb_maker', 'maker_id', $maker_id, '', true);
    }

    /**
     * メーカーの表示順をひとつ上げる.
     *
     * @param  integer $maker_id メーカーID
     * @return void
     */
    public function rankUp($maker_id)
    {
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        $objDb->rankUp('dtb_maker', 'maker_id', $maker_id);
    }

    /**
     * メーカーの表示順をひとつ下げる.
     *
     * @param  integer $maker_id メーカーID
     * @return void
     */
    public function rankDown($maker_id)
    {
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        $objDb->rankDown('dtb_maker', 'maker_id', $maker_id);
    }

    /**
     * メーカーIDをキー, 名前を値とする配列を取得.
     *
     * @return array
     */
    public function getIDValueList()
    {
        return Application::alias('eccube.helper.db')->getIDValueList('dtb_maker', 'maker_id', 'name');
    }
}
