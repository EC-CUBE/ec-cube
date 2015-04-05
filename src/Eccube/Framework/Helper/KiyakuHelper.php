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
use Eccube\Framework\Util\Utils;

/**
 * 会員規約を管理するヘルパークラス.
 *
 * @package Helper
 * @author pineray
 */
class KiyakuHelper
{
    /**
     * 会員規約の情報を取得.
     *
     * @param  integer $kiyaku_id   会員規約ID
     * @param  boolean $has_deleted 削除された会員規約も含む場合 true; 初期値 false
     * @return array
     */
    public function getKiyaku($kiyaku_id, $has_deleted = false)
    {
        $objQuery = Application::alias('eccube.query');
        $where = 'kiyaku_id = ?';
        if (!$has_deleted) {
            $where .= ' AND del_flg = 0';
        }
        $arrRet = $objQuery->select('*', 'dtb_kiyaku', $where, array($kiyaku_id));

        return $arrRet[0];
    }

    /**
     * 会員規約一覧の取得.
     *
     * @param  boolean $has_deleted 削除された会員規約も含む場合 true; 初期値 false
     * @return array
     */
    public function getList($has_deleted = false)
    {
        $objQuery = Application::alias('eccube.query');
        $col = 'kiyaku_id, kiyaku_title, kiyaku_text';
        $where = '';
        if (!$has_deleted) {
            $where .= 'del_flg = 0';
        }
        $table = 'dtb_kiyaku';
        $objQuery->setOrder('rank DESC');
        $arrRet = $objQuery->select($col, $table, $where);

        return $arrRet;
    }

    /**
     * 会員規約の登録.
     *
     * @param  array    $sqlval
     * @return multiple 登録成功:会員規約ID, 失敗:FALSE
     */
    public function saveKiyaku($sqlval)
    {
        $objQuery = Application::alias('eccube.query');

        $kiyaku_id = $sqlval['kiyaku_id'];
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        // 新規登録
        if ($kiyaku_id == '') {
            // INSERTの実行
            $sqlval['rank'] = $objQuery->max('rank', 'dtb_kiyaku') + 1;
            $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
            $sqlval['kiyaku_id'] = $objQuery->nextVal('dtb_kiyaku_kiyaku_id');
            $ret = $objQuery->insert('dtb_kiyaku', $sqlval);
        // 既存編集
        } else {
            unset($sqlval['creator_id']);
            unset($sqlval['create_date']);
            $where = 'kiyaku_id = ?';
            $ret = $objQuery->update('dtb_kiyaku', $sqlval, $where, array($kiyaku_id));
        }

        return ($ret) ? $sqlval['kiyaku_id'] : FALSE;
    }

    /**
     * 会員規約の削除.
     *
     * @param  integer $kiyaku_id 会員規約ID
     * @return void
     */
    public function deleteKiyaku($kiyaku_id)
    {
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        // ランク付きレコードの削除
        $objDb->deleteRankRecord('dtb_kiyaku', 'kiyaku_id', $kiyaku_id);
    }

    /**
     * 会員規約の表示順をひとつ上げる.
     *
     * @param  integer $kiyaku_id 会員規約ID
     * @return void
     */
    public function rankUp($kiyaku_id)
    {
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        $objDb->rankUp('dtb_kiyaku', 'kiyaku_id', $kiyaku_id);
    }

    /**
     * 会員規約の表示順をひとつ下げる.
     *
     * @param  integer $kiyaku_id 会員規約ID
     * @return void
     */
    public function rankDown($kiyaku_id)
    {
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        $objDb->rankDown('dtb_kiyaku', 'kiyaku_id', $kiyaku_id);
    }

    /**
     * 同じタイトルの規約が存在するか確認.
     *
     * @param  string  $title     規約タイトル
     * @param  integer $kiyaku_id 会員規約ID
     * @return boolean 同名のタイトルが存在:TRUE
     */
    public function isTitleExist($title, $kiyaku_id = NULL)
    {
        $objQuery = Application::alias('eccube.query');

        $where  = 'del_flg = 0 AND kiyaku_title = ?';
        $arrVal = array($title);

        if (!Utils::isBlank($kiyaku_id)) {
            $where   .= ' AND kiyaku_id <> ?';
            $arrVal[] = $kiyaku_id;
        }

        $arrRet = $objQuery->select('kiyaku_id, kiyaku_title', 'dtb_kiyaku', $where, $arrVal);

        return !Utils::isBlank($arrRet);
    }
}
