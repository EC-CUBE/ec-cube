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

namespace Eccube\Page\Admin\Basis;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\CheckError;
use Eccube\Framework\FormParam;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Util\Utils;

/**
 * 店舗基本情報 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Index extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'basis/index.tpl';
        $this->tpl_subno = 'index';
        $this->tpl_mainno = 'basis';
        $masterData = Application::alias('eccube.db.master_data');
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrTAXRULE = $masterData->getMasterData('mtb_taxrule');
        $this->tpl_maintitle = '基本情報管理';
        $this->tpl_subtitle = 'SHOPマスター';

        //定休日用配列
        $this->arrRegularHoliday[0] = '日';
        $this->arrRegularHoliday[1] = '月';
        $this->arrRegularHoliday[2] = '火';
        $this->arrRegularHoliday[3] = '水';
        $this->arrRegularHoliday[4] = '木';
        $this->arrRegularHoliday[5] = '金';
        $this->arrRegularHoliday[6] = '土';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    public function action()
    {
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        $objFormParam = Application::alias('eccube.form_param');
        $this->lfInitParam($objFormParam, $_POST);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();
        $this->arrForm = $objFormParam->getHashArray();

        $this->tpl_onload = "eccube.checkLimit('downloadable_days', 'downloadable_days_unlimited', '" . DISABLED_RGB . "');";

        switch ($this->getMode()) {
            case 'confirm':
                $this->arrErr = $this->lfCheckError($objFormParam);
                if (!empty($this->arrErr)) {
                    return;
                }
                $this->tpl_subtitle = 'SHOPマスター(確認)';
                $this->tpl_mainpage = 'basis/confirm.tpl';
                break;

            case 'return':
                break;

            case 'complete':
                $arrData = $objFormParam->getDbArray();
                Application::alias('eccube.helper.db')->registerBasisData($arrData);

                // キャッシュファイル更新
                $objDb->createBasisDataCache();
                $this->tpl_onload .= "window.alert('SHOPマスターの登録が完了しました。');";
            // breakはつけない
            default:
                $arrRet = $objDb->getBasisData(true);
                $objFormParam->setParam($arrRet);
                $this->arrForm = $objFormParam->getHashArray();
                $this->arrForm['regular_holiday_ids'] = explode('|', $this->arrForm['regular_holiday_ids']);
                break;
        }
    }

    /**
     * 前方互換用
     *
     * @deprecated 2.12.4
     */
    public function lfUpdateData($arrData)
    {
        trigger_error('前方互換用メソッドが使用されました。', E_USER_WARNING);
        Application::alias('eccube.helper.db')->registerBasisData($arrData);
    }

    /**
     * 前方互換用
     *
     * @deprecated 2.12.4
     */
    public function lfInsertData($arrData)
    {
        trigger_error('前方互換用メソッドが使用されました。', E_USER_WARNING);
        Application::alias('eccube.helper.db')->registerBasisData($arrData);
    }

    /**
     * @param FormParam $objFormParam
     */
    public function lfInitParam(&$objFormParam, $post)
    {
        $objFormParam->addParam('会社名', 'company_name', STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('会社名(フリガナ)', 'company_kana', STEXT_LEN, 'KVC', array('KANA_CHECK', 'MAX_LENGTH_CHECK'));

        $objFormParam->addParam('店名', 'shop_name', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('店名(フリガナ)', 'shop_kana', STEXT_LEN, 'KVC', array('KANA_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('店名(英語表記)', 'shop_name_eng', MTEXT_LEN, 'a', array('GRAPH_CHECK', 'MAX_LENGTH_CHECK'));
        // 郵便番号チェック
        $objFormParam->addParam('郵便番号1', 'zip01', ZIP01_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'NUM_COUNT_CHECK'));
        $objFormParam->addParam('郵便番号2', 'zip02', ZIP02_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'NUM_COUNT_CHECK'));
        // 所在地チェック
        $objFormParam->addParam('都道府県', 'pref', '', 'n', array('EXIST_CHECK'));
        $objFormParam->addParam('所在地1', 'addr01', MTEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('所在地2', 'addr02', MTEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        // メールチェック
        $objFormParam->addParam('商品注文受付メールアドレス', 'email01', null, 'a', array('EXIST_CHECK', 'EMAIL_CHECK', 'EMAIL_CHAR_CHECK'));
        $objFormParam->addParam('問い合わせ受付メールアドレス', 'email02', null, 'a', array('EXIST_CHECK', 'EMAIL_CHECK', 'EMAIL_CHAR_CHECK'));
        $objFormParam->addParam('メール送信元メールアドレス', 'email03', null, 'a', array('EXIST_CHECK', 'EMAIL_CHECK', 'EMAIL_CHAR_CHECK'));
        $objFormParam->addParam('送信エラー受付メールアドレス', 'email04', null, 'a', array('EXIST_CHECK', 'EMAIL_CHECK', 'EMAIL_CHAR_CHECK'));

        // 電話番号
        $objFormParam->addParam('電話番号1', 'tel01', TEL_ITEM_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('電話番号2', 'tel02', TEL_ITEM_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('電話番号3', 'tel03', TEL_ITEM_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));

        // FAX番号
        $objFormParam->addParam('FAX番号1', 'fax01', TEL_ITEM_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('FAX番号2', 'fax02', TEL_ITEM_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('FAX番号3', 'fax03', TEL_ITEM_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));

        // その他
        $objFormParam->addParam('送料無料条件', 'free_rule', PRICE_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('店舗営業時間', 'business_hour', STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));

        $objFormParam->addParam('取扱商品', 'good_traded', LLTEXT_LEN, '', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('メッセージ', 'message', LLTEXT_LEN, '', array('MAX_LENGTH_CHECK'));

        if (!isset($post['downloadable_days_unlimited']) && $post['downloadable_days_unlimited'] != '1') {
            $objFormParam->addParam('ダウンロード可能日数', 'downloadable_days', DOWNLOAD_DAYS_LEN, 'n', array('EXIST_CHECK', 'ZERO_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        } else {
            $objFormParam->addParam('ダウンロード無制限', 'downloadable_days_unlimited', array('EXIST_CHECK'));
        }
        $objFormParam->addParam('緯度', 'latitude', STEXT_LEN, '', array('MAX_LENGTH_CHECK', 'NUM_POINT_CHECK'));
        $objFormParam->addParam('軽度', 'longitude', STEXT_LEN, '', array('MAX_LENGTH_CHECK', 'NUM_POINT_CHECK'));

        $objFormParam->addParam('定休日', 'regular_holiday_ids', INT_LEN, 'n', array('MAX_LENGTH_CHECK'));
    }

    // 入力エラーチェック

    /**
     * @param FormParam $objFormParam
     */
    public function lfCheckError(&$objFormParam)
    {
        $arrErr = $objFormParam->checkError();
        $post = $objFormParam->getHashArray();

        /* @var $objErr CheckError */
        $objErr = Application::alias('eccube.check_error', $post);
        $objErr->doFunc(array('郵便番号', 'zip01', 'zip02'), array('ALL_EXIST_CHECK'));

        // 電話番号チェック
        $objErr->doFunc(array('TEL', 'tel01', 'tel02', 'tel03'), array('TEL_CHECK'));
        $objErr->doFunc(array('FAX', 'fax01', 'fax02', 'fax03'), array('TEL_CHECK'));

        return array_merge((array) $arrErr, (array) $objErr->arrErr);
    }
}
