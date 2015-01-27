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
use Eccube\Framework\FormParam;
use Eccube\Framework\Query;
use Eccube\Framework\Helper\DbHelper;

/**
 * ポイント設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Point extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'basis/point.tpl';
        $this->tpl_subno = 'point';
        $this->tpl_mainno = 'basis';
        $this->tpl_maintitle = '基本情報管理';
        $this->tpl_subtitle = 'ポイント設定';
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
        // パラメーター管理クラス
        $objFormParam = Application::alias('eccube.form_param');
        // パラメーター情報の初期化
        $this->lfInitParam($objFormParam);
        // POST値の取得
        $objFormParam->setParam($_POST);

        if ($objDb->getBasisExists()) {
            $this->tpl_mode = 'update';
        } else {
            $this->tpl_mode = 'insert';
        }

        if (!empty($_POST)) {
            // 入力値の変換
            $objFormParam->convParam();
            $this->arrErr = $objFormParam->checkError();

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
                $this->tpl_onload = "window.alert('ポイント設定が完了しました。');";
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
        $objFormParam->addParam('ポイント付与率', 'point_rate', PERCENTAGE_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('会員登録時付与ポイント', 'welcome_point', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
    }

    public function lfUpdateData($post)
    {
        // 入力データを渡す。
        $sqlval = $post;
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $objQuery = Application::alias('eccube.query');
        // UPDATEの実行
        $objQuery->update('dtb_baseinfo', $sqlval);
    }

    public function lfInsertData($post)
    {
        // 入力データを渡す。
        $sqlval = $post;
        $sqlval['id'] = 1;
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $objQuery = Application::alias('eccube.query');
        // INSERTの実行
        $objQuery->insert('dtb_baseinfo', $sqlval);
    }
}
