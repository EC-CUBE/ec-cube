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

namespace Eccube\Framework;

use Eccube\Application;
use Eccube\Framework\SelectSql;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\DB\DBFactory;
use Eccube\Framework\Util\Utils;

/**
 * 会員検索用クラス
 */
class CustomerList extends SelectSql
{
    public $arrColumnCSV;

    public function __construct($array, $mode = '')
    {
        parent::__construct($array);

        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        /* @var $dbFactory DBFactory */
        $dbFactory = Application::alias('eccube.db.factory');

        if ($mode == '') {
            // 会員本登録会員で削除していない会員
            $this->setWhere('status = 2 AND del_flg = 0 ');
            // 登録日を示すカラム
            $regdate_col = 'dtb_customer.update_date';
        }

        if ($mode == 'customer') {
            $this->setWhere(' del_flg = 0 ');
            // 登録日を示すカラム
            $regdate_col = 'dtb_customer.update_date';
        }

        // 会員ID
        if (!isset($this->arrSql['search_customer_id'])) $this->arrSql['search_customer_id'] = '';
        if (strlen($this->arrSql['search_customer_id']) > 0) {
            $this->setWhere('customer_id =  ?');
            $this->arrVal[] = $this->arrSql['search_customer_id'];
        }

        // 名前
        if (!isset($this->arrSql['search_name'])) $this->arrSql['search_name'] = '';
        if (strlen($this->arrSql['search_name']) > 0) {
            $this->setWhere('(' . $dbFactory->concatColumn(array('name01', 'name02')) . ' LIKE ?)');
            $searchName = $this->addSearchStr($this->arrSql['search_name']);
            $this->arrVal[] = preg_replace('/[ 　]+/u', '', $searchName);
        }

        // 名前(フリガナ)
        if (!isset($this->arrSql['search_kana'])) $this->arrSql['search_kana'] = '';
        if (strlen($this->arrSql['search_kana']) > 0) {
            $this->setWhere('(' . $dbFactory->concatColumn(array('kana01', 'kana02')) . ' LIKE ?)');
            $searchKana = $this->addSearchStr($this->arrSql['search_kana']);
            $this->arrVal[] = preg_replace('/[ 　]+/u', '', $searchKana);
        }

        // 都道府県
        if (!isset($this->arrSql['search_pref'])) $this->arrSql['search_pref'] = '';
        if (strlen($this->arrSql['search_pref']) > 0) {
            $this->setWhere('pref = ?');
            $this->arrVal[] = $this->arrSql['search_pref'];
        }

        // 電話番号
        if (!isset($this->arrSql['search_tel'])) $this->arrSql['search_tel'] = '';
        if (is_numeric($this->arrSql['search_tel'])) {
            $this->setWhere('(' . $dbFactory->concatColumn(array('tel01', 'tel02', 'tel03')) . ' LIKE ?)');
            $searchTel = $this->addSearchStr($this->arrSql['search_tel']);
            $this->arrVal[] = str_replace('-', '', $searchTel);
        }

        // 性別
        if (!isset($this->arrSql['search_sex'])) $this->arrSql['search_sex'] = '';
        if (is_array($this->arrSql['search_sex'])) {
            $arrSexVal = $this->setItemTerm($this->arrSql['search_sex'], 'sex');
            foreach ($arrSexVal as $data) {
                $this->arrVal[] = $data;
            }
        }

        // 職業
        if (!isset($this->arrSql['search_job'])) $this->arrSql['search_job'] = '';
        if (is_array($this->arrSql['search_job'])) {
            if (in_array('不明', $this->arrSql['search_job'])) {
                $arrJobVal = $this->setItemTermWithNull($this->arrSql['search_job'], 'job');
            } else {
                $arrJobVal = $this->setItemTerm($this->arrSql['search_job'], 'job');
            }
            if (is_array($arrJobVal)) {
                foreach ($arrJobVal as $data) {
                    $this->arrVal[] = $data;
                }
            }
        }

        // E-MAIL
        if (!isset($this->arrSql['search_email'])) $this->arrSql['search_email'] = '';
        if (strlen($this->arrSql['search_email']) > 0) {
            //カンマ区切りで複数の条件指定可能に
            $this->arrSql['search_email'] = explode(',', $this->arrSql['search_email']);
            $sql_where = '';
            foreach ($this->arrSql['search_email'] as $val) {
                $val = trim($val);
                //検索条件を含まない
                if ($this->arrSql['not_emailinc'] == '1') {
                    if ($sql_where == '') {
                        $sql_where .= 'dtb_customer.email NOT ILIKE ? ';
                    } else {
                        $sql_where .= 'AND dtb_customer.email NOT ILIKE ? ';
                    }
                } else {
                    if ($sql_where == '') {
                        $sql_where .= 'dtb_customer.email ILIKE ? ';
                    } else {
                        $sql_where .= 'OR dtb_customer.email ILIKE ? ';
                    }
                }
                $searchEmail = $this->addSearchStr($val);
                $this->arrVal[] = $searchEmail;
            }
            $this->setWhere($sql_where);
        }

        // E-MAIL(mobile)
        if (!isset($this->arrSql['search_email_mobile'])) $this->arrSql['search_email_mobile'] = '';

        if (strlen($this->arrSql['search_email_mobile']) > 0) {
            //カンマ区切りで複数の条件指定可能に
            $this->arrSql['search_email_mobile'] = explode(',', $this->arrSql['search_email_mobile']);
            $sql_where = '';
            foreach ($this->arrSql['search_email_mobile'] as $val) {
                $val = trim($val);
                //検索条件を含まない
                if ($this->arrSql['not_email_mobileinc'] == '1') {
                    if ($sql_where == '') {
                        $sql_where .= 'dtb_customer.email_mobile NOT ILIKE ? ';
                    } else {
                        $sql_where .= 'AND dtb_customer.email_mobile NOT ILIKE ? ';
                    }
                } else {
                    if ($sql_where == '') {
                        $sql_where .= 'dtb_customer.email_mobile ILIKE ? ';
                    } else {
                        $sql_where .= 'OR dtb_customer.email_mobile ILIKE ? ';
                    }
                }
                $searchemail_mobile = $this->addSearchStr($val);
                $this->arrVal[] = $searchemail_mobile;
            }
            $this->setWhere($sql_where);
        }

        // メールマガジンの場合
        if ($mode == 'customer') {
            // メルマガ受け取りの選択項目がフォームに存在する場合
            if (isset($this->arrSql['search_htmlmail'])) {
                $this->setWhere('status = 2');
                if (Utils::sfIsInt($this->arrSql['search_htmlmail'])) {
                    // メルマガ拒否している会員も含む場合は、条件を付加しない
                    if ($this->arrSql['search_htmlmail'] != 99) {
                        $this->setWhere('mailmaga_flg = ?');
                        $this->arrVal[] = $this->arrSql['search_htmlmail'];
                    }
                } else {
                    //　メルマガ購読拒否は省く
                    $this->setWhere('mailmaga_flg <> 3');
                }
            }
        }

        // 配信メールアドレス種別
        if ($mode == 'customer') {
            if (isset($this->arrSql['search_mail_type'])) {
                $sqlEmailMobileIsEmpty = "(dtb_customer.email_mobile IS NULL OR dtb_customer.email_mobile = '')";
                switch ($this->arrSql['search_mail_type']) {
                    // PCメールアドレス
                    case 1:
                        $this->setWhere("(dtb_customer.email <> dtb_customer.email_mobile OR $sqlEmailMobileIsEmpty)");
                        break;
                    // 携帯メールアドレス
                    case 2:
                        $this->setWhere("NOT $sqlEmailMobileIsEmpty");
                        break;
                    // PCメールアドレス (携帯メールアドレスを登録している会員は除外)
                    case 3:
                        $this->setWhere($sqlEmailMobileIsEmpty);
                        break;
                    // 携帯メールアドレス (PCメールアドレスを登録している会員は除外)
                    case 4:
                        $this->setWhere('dtb_customer.email = dtb_customer.email_mobile');
                        break;
                }
            }
        }

        // 購入金額指定
        if (!isset($this->arrSql['search_buy_total_from'])) $this->arrSql['search_buy_total_from'] = '';
        if (!isset($this->arrSql['search_buy_total_to'])) $this->arrSql['search_buy_total_to'] = '';
        if (is_numeric($this->arrSql['search_buy_total_from']) || is_numeric($this->arrSql['search_buy_total_to'])) {
            $arrBuyTotal = $this->selectRange($this->arrSql['search_buy_total_from'], $this->arrSql['search_buy_total_to'], 'buy_total');
            foreach ($arrBuyTotal as $data) {
                $this->arrVal[] = $data;
            }
        }

        // 購入回数指定
        if (!isset($this->arrSql['search_buy_times_from'])) $this->arrSql['search_buy_times_from'] = '';
        if (!isset($this->arrSql['search_buy_times_to'])) $this->arrSql['search_buy_times_to'] = '';
        if (is_numeric($this->arrSql['search_buy_times_from']) || is_numeric($this->arrSql['search_buy_times_to'])) {
            $arrBuyTimes = $this->selectRange($this->arrSql['search_buy_times_from'], $this->arrSql['search_buy_times_to'], 'buy_times');
            foreach ($arrBuyTimes as $data) {
                $this->arrVal[] = $data;
            }
        }

        // 誕生日期間指定
        if (!isset($this->arrSql['search_b_start_year'])) $this->arrSql['search_b_start_year'] = '';
        if (!isset($this->arrSql['search_b_start_month'])) $this->arrSql['search_b_start_month'] = '';
        if (!isset($this->arrSql['search_b_start_day'])) $this->arrSql['search_b_start_day'] = '';
        if (!isset($this->arrSql['search_b_end_year'])) $this->arrSql['search_b_end_year'] = '';
        if (!isset($this->arrSql['search_b_end_month'])) $this->arrSql['search_b_end_month'] = '';
        if (!isset($this->arrSql['search_b_end_day'])) $this->arrSql['search_b_end_day'] = '';
        if ((strlen($this->arrSql['search_b_start_year']) > 0 && strlen($this->arrSql['search_b_start_month']) > 0 && strlen($this->arrSql['search_b_start_day']) > 0)
            || strlen($this->arrSql['search_b_end_year']) > 0 && strlen($this->arrSql['search_b_end_month']) > 0 && strlen($this->arrSql['search_b_end_day']) > 0) {
            $arrBirth = $this->selectTermRange($this->arrSql['search_b_start_year'], $this->arrSql['search_b_start_month'], $this->arrSql['search_b_start_day'],
                                               $this->arrSql['search_b_end_year'], $this->arrSql['search_b_end_month'], $this->arrSql['search_b_end_day'], 'birth');
            foreach ($arrBirth as $data) {
                $this->arrVal[] = $data;
            }
        }

        // 誕生月の検索
        if (!isset($this->arrSql['search_birth_month'])) $this->arrSql['search_birth_month'] = '';
        if (is_numeric($this->arrSql['search_birth_month'])) {
            $this->setWhere(' EXTRACT(month from birth) = ?');
            $this->arrVal[] = $this->arrSql['search_birth_month'];
        }

        // 登録期間指定
        if (!isset($this->arrSql['search_start_year'])) $this->arrSql['search_start_year'] = '';
        if (!isset($this->arrSql['search_start_month'])) $this->arrSql['search_start_month'] = '';
        if (!isset($this->arrSql['search_start_day'])) $this->arrSql['search_start_day'] = '';
        if (!isset($this->arrSql['search_end_year'])) $this->arrSql['search_end_year'] = '';
        if (!isset($this->arrSql['search_end_month'])) $this->arrSql['search_end_month'] = '';
        if (!isset($this->arrSql['search_end_day'])) $this->arrSql['search_end_day'] = '';
        if ( (strlen($this->arrSql['search_start_year']) > 0 && strlen($this->arrSql['search_start_month']) > 0 && strlen($this->arrSql['search_start_day']) > 0) ||
                (strlen($this->arrSql['search_end_year']) > 0 && strlen($this->arrSql['search_end_month']) >0 && strlen($this->arrSql['search_end_day']) > 0)) {
            $arrRegistTime = $this->selectTermRange($this->arrSql['search_start_year'], $this->arrSql['search_start_month'], $this->arrSql['search_start_day'],
                            $this->arrSql['search_end_year'], $this->arrSql['search_end_month'], $this->arrSql['search_end_day'], $regdate_col);
            foreach ($arrRegistTime as $data) {
                $this->arrVal[] = $data;
            }
        }

        // 最終購入日指定
        if (!isset($this->arrSql['search_buy_start_year'])) $this->arrSql['search_buy_start_year'] = '';
        if (!isset($this->arrSql['search_buy_start_month'])) $this->arrSql['search_buy_start_month'] = '';
        if (!isset($this->arrSql['search_buy_start_day'])) $this->arrSql['search_buy_start_day'] = '';
        if (!isset($this->arrSql['search_buy_end_year'])) $this->arrSql['search_buy_end_year'] = '';
        if (!isset($this->arrSql['search_buy_end_month'])) $this->arrSql['search_buy_end_month'] = '';
        if (!isset($this->arrSql['search_buy_end_day'])) $this->arrSql['search_buy_end_day'] = '';

        if ( (strlen($this->arrSql['search_buy_start_year']) > 0 && strlen($this->arrSql['search_buy_start_month']) > 0 && strlen($this->arrSql['search_buy_start_day']) > 0) ||
                (strlen($this->arrSql['search_buy_end_year']) > 0 && strlen($this->arrSql['search_buy_end_month']) >0 && strlen($this->arrSql['search_buy_end_day']) > 0)) {
            $arrRegistTime = $this->selectTermRange($this->arrSql['search_buy_start_year'], $this->arrSql['search_buy_start_month'], $this->arrSql['search_buy_start_day'],
                            $this->arrSql['search_buy_end_year'], $this->arrSql['search_buy_end_month'], $this->arrSql['search_buy_end_day'], 'last_buy_date');
            foreach ($arrRegistTime as $data) {
                $this->arrVal[] = $data;
            }
        }

        // 購入商品コード
        if (!isset($this->arrSql['search_buy_product_code'])) $this->arrSql['search_buy_product_code'] = '';
        if (strlen($this->arrSql['search_buy_product_code']) > 0) {
            $this->setWhere('customer_id IN (SELECT customer_id FROM dtb_order WHERE order_id IN (SELECT order_id FROM dtb_order_detail WHERE product_code LIKE ?) AND del_flg = 0)');
            $search_buyproduct_code = $this->addSearchStr($this->arrSql['search_buy_product_code']);
            $this->arrVal[] = $search_buyproduct_code;
        }

        // 購入商品名称
        if (!isset($this->arrSql['search_buy_product_name'])) $this->arrSql['search_buy_product_name'] = '';
        if (strlen($this->arrSql['search_buy_product_name']) > 0) {
            $this->setWhere('customer_id IN (SELECT customer_id FROM dtb_order WHERE order_id IN (SELECT order_id FROM dtb_order_detail WHERE product_name LIKE ?) AND del_flg = 0)');
            $search_buyproduct_name = $this->addSearchStr($this->arrSql['search_buy_product_name']);
            $this->arrVal[] = $search_buyproduct_name;
        }

        // カテゴリを選択している場合のみ絞込検索を行う
        if (!isset($this->arrSql['search_category_id'])) $this->arrSql['search_category_id'] = '';
        if (strlen($this->arrSql['search_category_id']) > 0) {
            // カテゴリで絞込検索を行うSQL文生成
            list($tmp_where, $tmp_arrval) = $objDb->getCatWhere($this->arrSql['search_category_id']);

            // カテゴリで絞込みが可能の場合
            if ($tmp_where != '') {
                $this->setWhere(' customer_id IN (SELECT distinct customer_id FROM dtb_order WHERE order_id IN (SELECT distinct order_id FROM dtb_order_detail WHERE product_id IN (SELECT product_id FROM dtb_product_categories WHERE '.$tmp_where.') AND del_flg = 0)) ');
                $this->arrVal = array_merge((array) $this->arrVal, (array) $tmp_arrval);
            }
        }

        // 会員状態
        if (!isset($this->arrSql['search_status'])) $this->arrSql['search_status'] = '';
        if (is_array($this->arrSql['search_status'])) {
            $arrStatusVal = $this->setItemTerm($this->arrSql['search_status'], 'status');
            foreach ($arrStatusVal as $data) {
                $this->arrVal[] = $data;
            }
        }

        $this->setOrder('customer_id DESC');
    }

    // 検索用SQL
    public function getList()
    {
        $this->select = 'SELECT customer_id,name01,name02,kana01,kana02,sex,email,email_mobile,tel01,tel02,tel03,pref,status,update_date,mailmaga_flg FROM dtb_customer ';

        return $this->getSql(2);
    }

    public function getListMailMagazine($is_mobile = false)
    {
        $colomn = $this->getMailMagazineColumn($is_mobile);
        $this->select = "
            SELECT
                $colomn
            FROM
                dtb_customer";

        return $this->getSql(0);
    }

    // 検索総数カウント用SQL
    public function getListCount()
    {
        $this->select = 'SELECT COUNT(customer_id) FROM dtb_customer ';

        return $this->getSql(1);
    }

    // CSVダウンロード用SQL
    public function getListCSV($arrColumnCSV)
    {
        $this->arrColumnCSV = $arrColumnCSV;
        $i = 0;
        foreach ($this->arrColumnCSV as $val) {
            if ($i != 0) $state .= ', ';
            $state .= $val['sql'];
            $i ++;
        }

        $this->select = 'SELECT ' .$state. ' FROM dtb_customer ';

        return $this->getSql(2);
    }

    public function getWhere()
    {
        return array(parent::getWhere(), $this->arrVal);
    }
}
