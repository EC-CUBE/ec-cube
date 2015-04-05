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
use Eccube\Framework\Query;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Helper\DbHelper;

/**
 * 特定商取引法 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Tradelaw extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'basis/tradelaw.tpl';
        $this->tpl_subno = 'tradelaw';
        $this->tpl_mainno = 'basis';
        $masterData = Application::alias('eccube.db.master_data');
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrTAXRULE = $masterData->getMasterData('mtb_taxrule');
        $this->tpl_maintitle = '基本情報管理';
        $this->tpl_subtitle = '特定商取引法';
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
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);

        if ($objDb->getBasisExists()) {
            $this->tpl_mode = 'update';
        } else {
            $this->tpl_mode = 'insert';
        }

        if (!empty($_POST)) {
            // 入力値の変換
            $objFormParam->convParam();
            $this->arrErr = $this->lfCheckError($objFormParam);

            if (count($this->arrErr) == 0) {
                switch ($this->getMode()) {
                    case 'update':
                        $this->lfUpdateData($objFormParam->getHashArray()); // 既存編集
                        break;
                    case 'insert':
                        $this->lfInsertData($objFormParam->getHashArray()); // 新規作成
                        break;
                    default:
                        break;
                }
                // 再表示
                $this->tpl_onload = "window.alert('特定商取引法の登録が完了しました。');";
            }
        } else {
            $arrRet = $objDb->getBasisData();
            $objFormParam->setParam($arrRet);
        }
        $this->arrForm = $objFormParam->getFormParamList();
    }

    /* パラメーター情報の初期化 */

    /**
     * @param FormParam $objFormParam
     */
    public function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('販売業者', 'law_company', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('運営責任者', 'law_manager', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('郵便番号1', 'law_zip01', ZIP01_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'NUM_COUNT_CHECK'));
        $objFormParam->addParam('郵便番号2', 'law_zip02', ZIP02_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'NUM_COUNT_CHECK'));
        $objFormParam->addParam('都道府県', 'law_pref', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('所在地1', 'law_addr01', MTEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('所在地2', 'law_addr02', MTEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('電話番号1', 'law_tel01', TEL_ITEM_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam('電話番号2', 'law_tel02', TEL_ITEM_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam('電話番号3', 'law_tel03', TEL_ITEM_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam('FAX番号1', 'law_fax01', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam('FAX番号2', 'law_fax02', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam('FAX番号3', 'law_fax03', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam('メールアドレス', 'law_email', null, 'KVa', array('EXIST_CHECK', 'EMAIL_CHECK', 'EMAIL_CHAR_CHECK'));
        $objFormParam->addParam('URL', 'law_url', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'URL_CHECK'));
        $objFormParam->addParam('必要料金', 'law_term01', MLTEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('注文方法', 'law_term02', MLTEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('支払方法', 'law_term03', MLTEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('支払期限', 'law_term04', MLTEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('引き渡し時期', 'law_term05', MLTEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('返品・交換について', 'law_term06', MLTEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
    }

    public function lfUpdateData($sqlval)
    {
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $objQuery = Application::alias('eccube.query');
        // UPDATEの実行
        $objQuery->update('dtb_baseinfo', $sqlval);
    }

    public function lfInsertData($sqlval)
    {
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $objQuery = Application::alias('eccube.query');
        // INSERTの実行
        $objQuery->insert('dtb_baseinfo', $sqlval);
    }

    /* 入力内容のチェック */

    /**
     * @param FormParam $objFormParam
     */
    public function lfCheckError(&$objFormParam)
    {
        // 入力データを渡す。
        $arrRet =  $objFormParam->getHashArray();
        /* @var $objErr CheckError */
        $objErr = Application::alias('eccube.check_error', $arrRet);
        $objErr->arrErr = $objFormParam->checkError();

        // 電話番号チェック
        $objErr->doFunc(array('TEL', 'law_tel01', 'law_tel02', 'law_tel03'), array('TEL_CHECK'));
        $objErr->doFunc(array('FAX', 'law_fax01', 'law_fax02', 'law_fax03'), array('TEL_CHECK'));
        $objErr->doFunc(array('郵便番号', 'law_zip01', 'law_zip02'), array('ALL_EXIST_CHECK'));

        return $objErr->arrErr;
    }
}
