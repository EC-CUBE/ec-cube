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
 * 休日を管理するヘルパークラス.
 *
 * @package Helper
 * @author pineray
 */
class HolidayHelper
{
    /**
     * 休日の情報を取得.
     *
     * @param  integer $holiday_id  休日ID
     * @param  boolean $has_deleted 削除された休日も含む場合 true; 初期値 false
     * @return array
     */
    public function get($holiday_id, $has_deleted = false)
    {
        $objQuery = Application::alias('eccube.query');
        $where = 'holiday_id = ?';
        if (!$has_deleted) {
            $where .= ' AND del_flg = 0';
        }
        $arrRet = $objQuery->select('*', 'dtb_holiday', $where, array($holiday_id));

        return $arrRet[0];
    }

    /**
     * 休日一覧の取得.
     *
     * @param  boolean $has_deleted 削除された休日も含む場合 true; 初期値 false
     * @return array
     */
    public function getList($has_deleted = false)
    {
        $objQuery = Application::alias('eccube.query');
        $col = 'holiday_id, title, month, day';
        $where = '';
        if (!$has_deleted) {
            $where .= 'del_flg = 0';
        }
        $table = 'dtb_holiday';
        $objQuery->setOrder('rank DESC');
        $arrRet = $objQuery->select($col, $table, $where);

        return $arrRet;
    }

    /**
     * 休日の登録.
     *
     * @param  array    $sqlval
     * @return multiple 登録成功:休日ID, 失敗:FALSE
     */
    public function save($sqlval)
    {
        $objQuery = Application::alias('eccube.query');

        $holiday_id = $sqlval['holiday_id'];
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        // 新規登録
        if ($holiday_id == '') {
            // INSERTの実行
            $sqlval['rank'] = $objQuery->max('rank', 'dtb_holiday') + 1;
            $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
            $sqlval['holiday_id'] = $objQuery->nextVal('dtb_holiday_holiday_id');
            $ret = $objQuery->insert('dtb_holiday', $sqlval);
        // 既存編集
        } else {
            unset($sqlval['creator_id']);
            unset($sqlval['create_date']);
            $where = 'holiday_id = ?';
            $ret = $objQuery->update('dtb_holiday', $sqlval, $where, array($holiday_id));
        }

        return ($ret) ? $sqlval['holiday_id'] : FALSE;
    }

    /**
     * 休日の削除.
     *
     * @param  integer $holiday_id 休日ID
     * @return void
     */
    public function delete($holiday_id)
    {
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        // ランク付きレコードの削除
        $objDb->deleteRankRecord('dtb_holiday', 'holiday_id', $holiday_id, '', true);
    }

    /**
     * 休日の表示順をひとつ上げる.
     *
     * @param  integer $holiday_id 休日ID
     * @return void
     */
    public function rankUp($holiday_id)
    {
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        $objDb->rankUp('dtb_holiday', 'holiday_id', $holiday_id);
    }

    /**
     * 休日の表示順をひとつ下げる.
     *
     * @param  integer $holiday_id 休日ID
     * @return void
     */
    public function rankDown($holiday_id)
    {
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        $objDb->rankDown('dtb_holiday', 'holiday_id', $holiday_id);
    }

    /**
     * 同じ日付の休日が存在するか確認.
     *
     * @param  integer $month
     * @param  integer $day
     * @param  integer $holiday_id
     * @return boolean 同日付の休日が存在:true
     */
    public function isDateExist($month, $day, $holiday_id = NULL)
    {
        $objQuery = Application::alias('eccube.query');
        $where = 'del_flg = 0 AND month = ? AND day = ?';
        $arrVal = array($month, $day);
        if (!Utils::isBlank($holiday_id)) {
            $where .= ' AND holiday_id <> ?';
            $arrVal[] = $holiday_id;
        }
        $arrRet = $objQuery->select('holiday_id, title', 'dtb_holiday', $where, $arrVal);

        return !Utils::isBlank($arrRet);
    }
}
