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

namespace Eccube\Plugin\ProductReview\Page\Admin\Products;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\FormParam;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Util\Utils;
use Eccube\Plugin\ProductReview\Helper\ReviewHelper;

/**
 * レビュー編集 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class ReviewEdit extends Review
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'products/review_edit.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'review';
        // 両方選択可能
        $this->tpl_status_change = true;

        $masterData = Application::alias('eccube.db.master_data');
        $this->arrRECOMMEND = $masterData->getMasterData('mtb_recommend');
        $this->tpl_maintitle = '商品管理';
        $this->tpl_subtitle = 'レビュー管理';
        $this->arrSex = $masterData->getMasterData('mtb_sex');
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
        /* @var $objReview ReviewHelper */
        $objReview = Application::alias('eccube.helper.review');
        // パラメーター情報の初期化
        $objFormParam = Application::alias('eccube.form_param');
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();
        // 検索ワードの引き継ぎ
        $this->arrSearchHidden = $objFormParam->getSearchArray();
        $this->arrForm = $objFormParam->getHashArray();

        switch ($this->getMode()) {
            // 登録
            case 'complete':
                $this->arrErr = $objFormParam->checkError();
                // エラー無し
                if (Utils::isBlank($this->arrErr)) {
                    // レビュー情報の更新
                    $arrValues = $objFormParam->getDbArray();
                    $objReview->save($arrValues);
                    // レビュー情報のDB取得
                    $this->arrForm = $objReview->get($this->arrForm['review_id']);
                    $this->tpl_onload = "alert('登録が完了しました。');";
                }
                break;
            default:
                // レビュー情報のDB取得
                $this->arrForm = $objReview->get($this->arrForm['review_id']);
                break;
        }

    }

    /**
     * パラメーター情報の初期化を行う.
     *
     * @param  FormParam $objFormParam FormParam インスタンス
     * @return void
     */
    public function lfInitParam(&$objFormParam)
    {
        // 検索条件のパラメーターを初期化
        parent::lfInitParam($objFormParam);
        $objFormParam->addParam('レビューID', 'review_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('商品名', 'name', '', '', array(), '', false);
        $objFormParam->addParam('投稿日', 'create_date', '', '', array(), '', false);

        // 登録情報
        $objFormParam->addParam('レビュー表示', 'status', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('投稿者名', 'reviewer_name', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('投稿者URL', 'reviewer_url', URL_LEN, 'KVCa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('性別', 'sex', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('おすすめレベル', 'recommend_level', INT_LEN, 'n', array('SELECT_CHECK'));
        $objFormParam->addParam('タイトル', 'title', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('コメント', 'comment', LTEXT_LEN, 'KVa', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
    }
}
