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
 * 支払方法を管理するヘルパークラス.
 *
 * @package Helper
 * @author pineray
 */
class PaymentHelper
{
    /**
     * 支払方法の情報を取得.
     *
     * @param  integer $payment_id  支払方法ID
     * @param  boolean $has_deleted 削除された支払方法も含む場合 true; 初期値 false
     * @return array
     */
    public function get($payment_id, $has_deleted = false)
    {
        $objQuery = Application::alias('eccube.query');
        $where = 'payment_id = ?';
        if (!$has_deleted) {
            $where .= ' AND del_flg = 0';
        }
        $arrRet = $objQuery->select('*', 'dtb_payment', $where, array($payment_id));

        return $arrRet[0];
    }

    /**
     * 支払方法一覧の取得.
     *
     * @param  boolean $has_deleted 削除された支払方法も含む場合 true; 初期値 false
     * @return array
     */
    public function getList($has_deleted = false)
    {
        $objQuery = Application::alias('eccube.query');
        $col = 'payment_id, payment_method, payment_image, charge, rule_max, upper_rule, note, fix, charge_flg';
        $where = '';
        if (!$has_deleted) {
            $where .= 'del_flg = 0';
        }
        $table = 'dtb_payment';
        $objQuery->setOrder('rank DESC');
        $arrRet = $objQuery->select($col, $table, $where);

        return $arrRet;
    }

    /**
     * 購入金額に応じた支払方法を取得する.
     *
     * @param  integer $total 購入金額
     * @return array   購入金額に応じた支払方法の配列
     */
    public function getByPrice($total)
    {
        // 削除されていない支払方法を取得
        $payments = $this->getList();
        $arrPayment = array();
        foreach ($payments as $data) {
            // 下限と上限が設定されている
            if (strlen($data['rule_max']) != 0 && strlen($data['upper_rule']) != 0) {
                if ($data['rule_max'] <= $total && $data['upper_rule'] >= $total) {
                    $arrPayment[] = $data;
                }
            // 下限のみ設定されている
            } elseif (strlen($data['rule_max']) != 0) {
                if ($data['rule_max'] <= $total) {
                    $arrPayment[] = $data;
                }
            // 上限のみ設定されている
            } elseif (strlen($data['upper_rule']) != 0) {
                if ($data['upper_rule'] >= $total) {
                    $arrPayment[] = $data;
                }
            // いずれも設定なし
            } else {
                $arrPayment[] = $data;
            }
        }

        return $arrPayment;
    }

    /**
     * 支払方法の登録.
     *
     * @param  array $sqlval
     * @return void
     */
    public function save($sqlval)
    {
        $objQuery = Application::alias('eccube.query');

        $payment_id = $sqlval['payment_id'];
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        // 新規登録
        if ($payment_id == '') {
            // INSERTの実行
            $sqlval['rank'] = $objQuery->max('rank', 'dtb_payment') + 1;
            $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
            $sqlval['payment_id'] = $objQuery->nextVal('dtb_payment_payment_id');
            $objQuery->insert('dtb_payment', $sqlval);
        // 既存編集
        } else {
            unset($sqlval['creator_id']);
            unset($sqlval['create_date']);
            $where = 'payment_id = ?';
            $objQuery->update('dtb_payment', $sqlval, $where, array($payment_id));
        }
    }

    /**
     * 支払方法の削除.
     *
     * @param  integer $payment_id 支払方法ID
     * @return void
     */
    public function delete($payment_id)
    {
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        // ランク付きレコードの削除
        $objDb->deleteRankRecord('dtb_payment', 'payment_id', $payment_id);
    }

    /**
     * 支払方法の表示順をひとつ上げる.
     *
     * @param  integer $payment_id 支払方法ID
     * @return void
     */
    public function rankUp($payment_id)
    {
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        $objDb->rankUp('dtb_payment', 'payment_id', $payment_id);
    }

    /**
     * 支払方法の表示順をひとつ下げる.
     *
     * @param  integer $payment_id 支払方法ID
     * @return void
     */
    public function rankDown($payment_id)
    {
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        $objDb->rankDown('dtb_payment', 'payment_id', $payment_id);
    }

    /**
     * 決済モジュールを使用するかどうか.
     *
     * dtb_payment.memo03 に値が入っている場合は決済モジュールと見なす.
     *
     * @param  integer $payment_id 支払い方法ID
     * @return boolean 決済モジュールを使用する支払い方法の場合 true
     */
    public static function useModule($payment_id)
    {
        $objQuery = Application::alias('eccube.query');
        $memo03 = $objQuery->get('memo03', 'dtb_payment', 'payment_id = ?', array($payment_id));

        return !Utils::isBlank($memo03);
    }

    /**
     * 支払方法IDをキー, 名前を値とする配列を取得.
     *
     * @param  string $type 値のタイプ
     * @return array
     */
    public static function getIDValueList($type = 'payment_method')
    {
        return Application::alias('eccube.helper.db')->getIDValueList('dtb_payment', 'payment_id', $type);
    }
}
